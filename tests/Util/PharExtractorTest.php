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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class PharExtractorTest extends TestCase
{
    private string $testDir;
    private string $homeDir;
    private string $tmpDir;

    /**
     * @var list<string>
     */
    private array $pharPaths = [];

    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir() . '/jolinotif-' . bin2hex(random_bytes(8));
        $this->homeDir = $this->testDir . '/home';
        $this->tmpDir = $this->testDir . '/tmp';
        mkdir($this->homeDir, 0o700, true);
        mkdir($this->tmpDir, 0o700);
    }

    protected function tearDown(): void
    {
        foreach ($this->pharPaths as $pharPath) {
            \Phar::unlinkArchive($pharPath);
        }

        (new Filesystem())->remove($this->testDir);
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

    public function testUsesXdgCacheHome(): void
    {
        if ('Windows' === \PHP_OS_FAMILY) {
            self::markTestSkipped('The temporary directory is always used on Windows.');
        }

        $pharPath = $this->generatePhar('contents');
        $process = $this->getProcess($pharPath, [], ['XDG_CACHE_HOME' => $this->testDir . '/xdg']);
        $process->mustRun();

        $this->assertSame($this->getExtractedFilePath($pharPath, $this->testDir . '/xdg/jolinotif'), $process->getOutput());
    }

    public function testFallsBackToTheTemporaryDirectoryWithoutHome(): void
    {
        if ('Windows' === \PHP_OS_FAMILY) {
            self::markTestSkipped('The temporary directory is always used on Windows.');
        }

        $pharPath = $this->generatePhar('contents');
        $process = $this->getProcess($pharPath, [], ['HOME' => false]);
        $process->mustRun();
        $extractedFilePath = $this->getExtractedFilePath($pharPath, $this->getFallbackCacheDirectory());

        $this->assertSame($extractedFilePath, $process->getOutput());
        $this->assertSame('contents', file_get_contents($extractedFilePath));
    }

    public function testFallsBackToTheTemporaryDirectoryWithUnusableHome(): void
    {
        if ('Windows' === \PHP_OS_FAMILY) {
            self::markTestSkipped('The temporary directory is always used on Windows.');
        }

        mkdir($this->getCacheDirectory(), 0o777, true);
        chmod($this->getCacheDirectory(), 0o777);
        $pharPath = $this->generatePhar('contents');
        $process = $this->getProcess($pharPath);
        $process->mustRun();

        $this->assertSame($this->getExtractedFilePath($pharPath, $this->getFallbackCacheDirectory()), $process->getOutput());
    }

    public function testRejectsASymlinkCacheDirectory(): void
    {
        if ('Windows' === \PHP_OS_FAMILY) {
            self::markTestSkipped('Creating symbolic links requires additional privileges on Windows.');
        }

        $targetDir = $this->testDir . '/untrusted';
        mkdir($targetDir, 0o700);
        symlink($targetDir, $this->getFallbackCacheDirectory());
        $process = $this->getProcess($this->generatePhar('contents'), [], ['HOME' => false]);

        $this->assertExtractionFails($process, 'not a real directory');
    }

    public function testRejectsAWorldWritableCacheDirectory(): void
    {
        if ('Windows' === \PHP_OS_FAMILY) {
            self::markTestSkipped('POSIX permissions are not available on Windows.');
        }

        $cacheDir = $this->getFallbackCacheDirectory();
        mkdir($cacheDir, 0o700);
        chmod($cacheDir, 0o777);
        $process = $this->getProcess($this->generatePhar('contents'), [], ['HOME' => false]);

        $this->assertExtractionFails($process, 'permissions 0700');
        $this->assertSame(0o777, fileperms($cacheDir) & 0o777);
    }

    public function testPrunesStaleDirectories(): void
    {
        $staleDir = $this->getCacheDirectory() . '/stale';
        $recentDir = $this->getCacheDirectory() . '/recent';
        mkdir($staleDir . '/path', 0o700, true);
        mkdir($recentDir, 0o700);
        touch($staleDir . '/path/file.txt');
        touch($staleDir . '/.lock', time() - 40 * 24 * 3600);
        touch($staleDir, time() - 40 * 24 * 3600);
        touch($recentDir . '/.lock', time() - 40 * 24 * 3600);
        touch($recentDir . '/.lock');

        $this->getProcess($this->generatePhar('contents'))->mustRun();

        $this->assertDirectoryDoesNotExist($staleDir);
        $this->assertDirectoryExists($recentDir);
    }

    private function getCacheDirectory(): string
    {
        if ('Windows' === \PHP_OS_FAMILY) {
            return $this->tmpDir . '/jolinotif';
        }

        return $this->homeDir . '/.cache/jolinotif';
    }

    private function getFallbackCacheDirectory(): string
    {
        return $this->tmpDir . '/jolinotif-' . fileowner($this->testDir);
    }

    private function assertExtractionFails(Process $process, string $expectedMessage): void
    {
        $process->run();
        $output = $process->getErrorOutput() . $process->getOutput();

        $this->assertFalse($process->isSuccessful(), $output);
        $this->assertStringContainsString($expectedMessage, $output);
    }

    private function getExtractedFilePath(string $pharPath, ?string $cacheDir = null): string
    {
        $hash = strtolower((new \Phar($pharPath))->getSignature()['hash']);

        return ($cacheDir ?? $this->getCacheDirectory()) . '/' . $hash . '/path/to/file.txt';
    }

    /**
     * @param list<string>                $arguments
     * @param array<string, string|false> $env
     */
    private function getProcess(string $pharPath, array $arguments = [], array $env = []): Process
    {
        return new Process(
            [\PHP_BINARY, $pharPath, ...$arguments],
            $this->testDir,
            $env + [
                'HOME' => $this->homeDir,
                'XDG_CACHE_HOME' => false,
                'TMPDIR' => $this->tmpDir,
                'TMP' => $this->tmpDir,
                'TEMP' => $this->tmpDir,
            ],
        );
    }

    private function generatePhar(string $fileContent): string
    {
        $pharPath = $this->testDir . '/archive-' . bin2hex(random_bytes(8)) . '.phar';
        $bootstrap = <<<'PHAR_BOOTSTRAP'
            <?php

            require __DIR__.'/vendor/symfony/filesystem/Filesystem.php';
            require __DIR__.'/src/Exception/ExceptionInterface.php';
            require __DIR__.'/src/Exception/PharExtractionException.php';
            require __DIR__.'/src/Util/PharExtractor.php';

            // The cache must remain private even with a permissive umask.
            umask(0);

            echo \Joli\JoliNotif\Util\PharExtractor::extractFile(
                __DIR__.'/path/to/file.txt',
                in_array('--overwrite', $argv, true),
            );
            PHAR_BOOTSTRAP;

        $phar = new \Phar($pharPath);

        $files = [
            'vendor/symfony/filesystem/Filesystem.php',
            'src/Exception/ExceptionInterface.php',
            'src/Exception/PharExtractionException.php',
            'src/Util/PharExtractor.php',
        ];

        foreach ($files as $file) {
            $phar->addFile(\dirname(__DIR__, 2) . '/' . $file, $file);
        }

        $phar->addFromString('bootstrap.php', $bootstrap);
        $phar->addFromString('path/to/file.txt', $fileContent);
        $phar->setStub($phar->createDefaultStub('bootstrap.php'));
        $this->pharPaths[] = $pharPath;

        return $pharPath;
    }
}
