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
use Symfony\Component\Finder\Finder;

class PharExtractorTest extends TestCase
{
    public function testIsLocatedInsideAPhar()
    {
        $this->assertFalse(PharExtractor::isLocatedInsideAPhar('/var/www/my_file'));
        $this->assertFalse(PharExtractor::isLocatedInsideAPhar('http://example.com/index.php'));
        $this->assertFalse(PharExtractor::isLocatedInsideAPhar('/var/www/phar://test.phar/my_file'));
        $this->assertTrue(PharExtractor::isLocatedInsideAPhar('phar:///var/www/test.phar/my_file'));
    }

    public function testExtractFile()
    {
        $key = uniqid('', true);
        $pharPath = $this->getTestDir() . '/phar-extractor-' . $key . '.phar';
        $relativeFilePath = 'path/to/file-' . $key . '.txt';
        $extractedFilePath = sys_get_temp_dir() . '/jolinotif/' . $relativeFilePath;

        $this->generatePhar($pharPath, $relativeFilePath, $key, false);
        $this->assertFileExists($pharPath);
        exec('php ' . $pharPath);
        \Phar::unlinkArchive($pharPath);

        $this->assertFileExists($extractedFilePath);
        $this->assertSame($key, file_get_contents($extractedFilePath));
        unlink($extractedFilePath);
    }

    public function testExtractFileDoesntOverwriteExistingFileIfNotSpecified()
    {
        $key = uniqid('', true);
        $pharPath = $this->getTestDir() . '/phar-extractor-no-overwrite-' . $key . '.phar';
        $relativeFilePath = 'path/to/file-' . $key . '.txt';
        $extractedFilePath = sys_get_temp_dir() . '/jolinotif/' . $relativeFilePath;

        $this->generatePhar($pharPath, $relativeFilePath, $key, false);
        $this->assertFileExists($pharPath);
        exec('php ' . $pharPath);
        \Phar::unlinkArchive($pharPath);

        $this->generatePhar($pharPath, $relativeFilePath, 'new content', false);
        $this->assertFileExists($pharPath);
        exec('php ' . $pharPath);
        \Phar::unlinkArchive($pharPath);

        $this->assertFileExists($extractedFilePath);
        $this->assertSame($key, file_get_contents($extractedFilePath));
        unlink($extractedFilePath);
    }

    public function testExtractFileOverwritesExistingFileIfSpecified()
    {
        $key = uniqid('', true);
        $pharPath = $this->getTestDir() . '/phar-extractor-overwrite-' . $key . '.phar';
        $relativeFilePath = 'path/to/file-' . $key . '.txt';
        $extractedFilePath = sys_get_temp_dir() . '/jolinotif/' . $relativeFilePath;

        $this->generatePhar($pharPath, $relativeFilePath, $key, false);
        $this->assertFileExists($pharPath);
        exec('php ' . $pharPath);
        \Phar::unlinkArchive($pharPath);

        $this->generatePhar($pharPath, $relativeFilePath, 'new content', true);
        $this->assertFileExists($pharPath);
        exec('php ' . $pharPath);
        \Phar::unlinkArchive($pharPath);

        $this->assertFileExists($extractedFilePath);
        $this->assertSame('new content', file_get_contents($extractedFilePath));
        unlink($extractedFilePath);
    }

    private function getTestDir(): string
    {
        $testDir = sys_get_temp_dir() . '/test-jolinotif';

        if (!is_dir($testDir)) {
            mkdir($testDir);
        }

        return $testDir;
    }

    private function generatePhar(string $pharPath, string $fileRelativePath, string $fileContent, bool $overwrite)
    {
        $rootPackage = \dirname(__DIR__, 2);
        $bootstrap = <<<'PHAR_BOOTSTRAP'
            <?php

            require __DIR__.'/vendor/autoload.php';

            $filePath = '/{{ THE_FILE }}';
            $overwrite = {{ OVERWRITE }};

            \Joli\JoliNotif\Util\PharExtractor::extractFile(__DIR__.$filePath, $overwrite);

            ?>
            PHAR_BOOTSTRAP;

        $files = (new Finder())
            ->in("{$rootPackage}/src")
            ->in("{$rootPackage}/tests/fixtures")
            ->in("{$rootPackage}/vendor")
            ->notPath('vendor/symfony/phpunit-bridge/bin/simple-phpunit')
            ->files()
        ;

        $phar = new \Phar($pharPath);
        $phar->buildFromIterator($files, $rootPackage);
        $phar->addFromString('bootstrap.php', str_replace(
            [
                '{{ THE_FILE }}',
                '{{ OVERWRITE }}',
            ],
            [
                $fileRelativePath,
                $overwrite ? 'true' : 'false',
            ],
            $bootstrap
        ));
        $phar->addFromString($fileRelativePath, $fileContent);
        $phar->setStub($phar->createDefaultStub('bootstrap.php'));
    }
}
