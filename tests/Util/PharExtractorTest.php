<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Joli\JoliNotif\tests\Util;

use Joli\JoliNotif\Util\PharExtractor;

class PharExtractorTest extends \PHPUnit_Framework_TestCase
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
        $key = uniqid();
        $pharPath = $this->getTestDir().'/phar-extractor-'.$key.'.phar';
        $relativeFilePath = 'path/to/file-'.$key.'.txt';
        $extractedFilePath = sys_get_temp_dir().'/jolinotif/'.$relativeFilePath;

        $this->generatePhar($pharPath, $relativeFilePath, $key, false);
        $this->assertTrue(is_file($pharPath));
        exec('php '.$pharPath);
        \Phar::unlinkArchive($pharPath);

        $this->assertTrue(is_file($extractedFilePath));
        $this->assertEquals($key, file_get_contents($extractedFilePath));
        unlink($extractedFilePath);
    }

    public function testExtractFileDoesntOverwriteExistingFileIfNotSpecified()
    {
        $key = uniqid();
        $pharPath = $this->getTestDir().'/phar-extractor-no-overwrite-'.$key.'.phar';
        $relativeFilePath = 'path/to/file-'.$key.'.txt';
        $extractedFilePath = sys_get_temp_dir().'/jolinotif/'.$relativeFilePath;

        $this->generatePhar($pharPath, $relativeFilePath, $key, false);
        $this->assertTrue(is_file($pharPath));
        exec('php '.$pharPath);
        \Phar::unlinkArchive($pharPath);

        $this->generatePhar($pharPath, $relativeFilePath, 'new content', false);
        $this->assertTrue(is_file($pharPath));
        exec('php '.$pharPath);
        \Phar::unlinkArchive($pharPath);

        $this->assertTrue(is_file($extractedFilePath));
        $this->assertEquals($key, file_get_contents($extractedFilePath));
        unlink($extractedFilePath);
    }

    public function testExtractFileOverwritesExistingFileIfSpecified()
    {
        $key = uniqid();
        $pharPath = $this->getTestDir().'/phar-extractor-overwrite-'.$key.'.phar';
        $relativeFilePath = 'path/to/file-'.$key.'.txt';
        $extractedFilePath = sys_get_temp_dir().'/jolinotif/'.$relativeFilePath;

        $this->generatePhar($pharPath, $relativeFilePath, $key, false);
        $this->assertTrue(is_file($pharPath));
        exec('php '.$pharPath);
        \Phar::unlinkArchive($pharPath);

        $this->generatePhar($pharPath, $relativeFilePath, 'new content', true);
        $this->assertTrue(is_file($pharPath));
        exec('php '.$pharPath);
        \Phar::unlinkArchive($pharPath);

        $this->assertTrue(is_file($extractedFilePath));
        $this->assertEquals('new content', file_get_contents($extractedFilePath));
        unlink($extractedFilePath);
    }

    /**
     * @return string
     */
    private function getTestDir()
    {
        $testDir = sys_get_temp_dir().'/test-jolinotif';

        if (!is_dir($testDir)) {
            mkdir($testDir);
        }

        return $testDir;
    }

    /**
     * @param string $pharPath
     * @param string $fileRelativePath
     * @param string $fileContent
     * @param bool   $overwrite
     */
    private function generatePhar($pharPath, $fileRelativePath, $fileContent, $overwrite)
    {
        $rootPackage = dirname(dirname(dirname(__FILE__)));
        $bootstrap = <<<'PHAR_BOOTSTRAP'
<?php

require __DIR__.'/vendor/autoload.php';

$filePath = THE_FILE;
$overwrite = OVERWRITE;

\Joli\JoliNotif\Util\PharExtractor::extractFile(__DIR__.$filePath, $overwrite);

?>
PHAR_BOOTSTRAP;

        $phar = new \Phar($pharPath);
        $phar->buildFromDirectory($rootPackage, '#(src|vendor/composer)#');
        $phar->addFromString('bootstrap.php', str_replace(
            [
                'THE_FILE',
                'OVERWRITE',
            ],
            [
                '\'/'.$fileRelativePath.'\'',
                $overwrite ? 'true' : 'false',

            ],
            $bootstrap
        ));
        $phar->addFromString($fileRelativePath, $fileContent);
        $phar->addFile('vendor/autoload.php');
        $phar->setStub($phar->createDefaultStub('bootstrap.php'));
    }
}
