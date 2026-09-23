<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\tests\Util;

use Joli\JoliNotif\Util\PharExtractor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class PharExtractorTest extends TestCase
{
    private string $testDir;
    private string $homeDir;

    /**
     * @var list<string>
     */
    private array $pharPaths = [];

    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir() . '/jolinotif-' . bin2hex(random_bytes(8));
        $this->homeDir = $this->testDir . '/home';
        mkdir($this->homeDir, 0o700, true);
    }

    protected function tearDown(): void
    {
        foreach ($this->pharPaths as $pharPath) {
            \Phar::unlinkArchive($pharPath);
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->testDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($files as $file) {
            $this->assertInstanceOf(\SplFileInfo::class, $file);

            if ($file->isDir() && !$file->isLink()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }

        rmdir($this->testDir);
    }

    public function testIsLocatedInsideAPhar(): void
    {
        $this->assertFalse(PharExtractor::isLocatedInsideAPhar('/var/www/my_file'));
        $this->assertFalse(PharExtractor::isLocatedInsideAPhar('http://example.com/index.php'));
        $this->assertFalse(PharExtractor::isLocatedInsideAPhar('/var/www/phar://test.phar/my_file'));
        $this->assertTrue(PharExtractor::isLocatedInsideAPhar('phar:///var/www/test.phar/my_file'));
    }

    public function testExtractFile(): void
    {
        $pharPath = $this->generatePhar('contents');
        $process = $this->getProcess($pharPath);
        $process->mustRun();
        $extractedFilePath = $this->getExtractedFilePath($pharPath);

        $this->assertSame($extractedFilePath, $process->getOutput());
        $this->assertSame('contents', file_get_contents($extractedFilePath));

        if ('Windows' !== \PHP_OS_FAMILY) {
            $this->assertSame(0o700, fileperms($this->getCacheDirectory()) & 0o777);
            $this->assertSame(0o700, fileperms(\dirname($extractedFilePath, 3)) & 0o777);
        }
    }

    public function testExtractFileDoesntOverwriteExistingFileIfNotSpecified(): void
    {
        $pharPath = $this->generatePhar('contents');
        $this->getProcess($pharPath)->mustRun();
        $extractedFilePath = $this->getExtractedFilePath($pharPath);
        file_put_contents($extractedFilePath, 'cached contents');

        $this->getProcess($pharPath)->mustRun();

        $this->assertSame('cached contents', file_get_contents($extractedFilePath));
    }

    public function testExtractFileOverwritesExistingFileIfSpecified(): void
    {
        $pharPath = $this->generatePhar('contents');
        $this->getProcess($pharPath)->mustRun();
        $extractedFilePath = $this->getExtractedFilePath($pharPath);
        file_put_contents($extractedFilePath, 'cached contents');

        $this->getProcess($pharPath, ['--overwrite'])->mustRun();

        $this->assertSame('contents', file_get_contents($extractedFilePath));
    }

    public function testDifferentArchivesDoNotShareExtractedFiles(): void
    {
        $firstPhar = $this->generatePhar('first archive');
        $secondPhar = $this->generatePhar('second archive');
        $this->getProcess($firstPhar)->mustRun();
        $this->getProcess($secondPhar)->mustRun();

        $this->assertNotSame($this->getExtractedFilePath($firstPhar), $this->getExtractedFilePath($secondPhar));
        $this->assertSame('first archive', file_get_contents($this->getExtractedFilePath($firstPhar)));
        $this->assertSame('second archive', file_get_contents($this->getExtractedFilePath($secondPhar)));
    }

    public function testRejectsASymlinkCacheDirectory(): void
    {
        if ('Windows' === \PHP_OS_FAMILY) {
            self::markTestSkipped('Creating symbolic links requires additional privileges on Windows.');
        }

        $cacheDir = $this->getCacheDirectory();
        $targetDir = $this->testDir . '/untrusted';
        mkdir($targetDir, 0o700);
        symlink($targetDir, $cacheDir);
        $process = $this->getProcess($this->generatePhar('contents'));

        $this->assertExtractionFails($process, 'not a real directory');
    }

    public function testRejectsAWorldWritableCacheDirectory(): void
    {
        if ('Windows' === \PHP_OS_FAMILY) {
            self::markTestSkipped('POSIX permissions are not available on Windows.');
        }

        $cacheDir = $this->getCacheDirectory();
        mkdir($cacheDir, 0o700, true);
        chmod($cacheDir, 0o777);
        $process = $this->getProcess($this->generatePhar('contents'));

        $this->assertExtractionFails($process, 'permissions 0700');
        $this->assertSame(0o777, fileperms($cacheDir) & 0o777);
    }

    private function getCacheDirectory(): string
    {
        if ('Windows' === \PHP_OS_FAMILY) {
            return str_replace('\\', '/', $this->homeDir) . '/JoliNotif';
        }

        return $this->homeDir . '/.jolinotif';
    }

    private function assertExtractionFails(Process $process, string $expectedMessage): void
    {
        $process->run();
        $output = $process->getErrorOutput() . $process->getOutput();

        $this->assertFalse($process->isSuccessful(), $output);
        $this->assertStringContainsString($expectedMessage, $output);
    }

    private function getExtractedFilePath(string $pharPath): string
    {
        return $this->getCacheDirectory() . '/' . hash_file('sha256', $pharPath) . '/path/to/file.txt';
    }

    /**
     * @param list<string> $arguments
     */
    private function getProcess(string $pharPath, array $arguments = []): Process
    {
        return new Process(
            [\PHP_BINARY, $pharPath, ...$arguments],
            $this->testDir,
            ['HOME' => $this->homeDir, 'LOCALAPPDATA' => $this->homeDir],
        );
    }

    private function generatePhar(string $fileContent): string
    {
        $pharPath = $this->testDir . '/archive-' . bin2hex(random_bytes(8)) . '.phar';
        $bootstrap = <<<'PHAR_BOOTSTRAP'
            <?php

            require __DIR__.'/src/Util/PharExtractor.php';

            // The cache must remain private even with a permissive umask.
            umask(0);

            echo \Joli\JoliNotif\Util\PharExtractor::extractFile(
                __DIR__.'/path/to/file.txt',
                in_array('--overwrite', $argv, true),
            );
            PHAR_BOOTSTRAP;

        $phar = new \Phar($pharPath);
        $phar->addFile(\dirname(__DIR__, 2) . '/src/Util/PharExtractor.php', 'src/Util/PharExtractor.php');
        $phar->addFromString('bootstrap.php', $bootstrap);
        $phar->addFromString('path/to/file.txt', $fileContent);
        $phar->setStub($phar->createDefaultStub('bootstrap.php'));
        $this->pharPaths[] = $pharPath;

        return $pharPath;
    }
}
