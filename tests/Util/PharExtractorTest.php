<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Util;

use JoliNotif\Util\PharExtractor;

class PharExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsLocatedInsideAPhar()
    {
        $this->assertFalse(PharExtractor::isLocatedInsideAPhar('/var/www/my_file'));
        $this->assertFalse(PharExtractor::isLocatedInsideAPhar('http://exammple.com/index.php'));
        $this->assertFalse(PharExtractor::isLocatedInsideAPhar('/var/www/phar://test.phar/my_file'));
        $this->assertTrue(PharExtractor::isLocatedInsideAPhar('phar:///var/www/test.phar/my_file'));
    }

    public function testExtractFile()
    {
        $key               = uniqid();
        $fileContent       = $key;
        $rootPackage       = dirname(dirname(dirname(__FILE__)));
        $fileRelativePath  = 'path/to/file-'.$key.'.txt';
        $testDir           = sys_get_temp_dir().'/test-jolinotif';
        $pharPath          = $testDir.'/phar-extractor-'.$key.'.phar';
        $extractedFilePath = sys_get_temp_dir().'/jolinotif/'.$fileRelativePath;

        if (!is_dir($testDir)) {
            mkdir($testDir);
        }

        $bootstrap = <<<'PHAR_BOOTSTRAP'
<?php

require __DIR__.'/vendor/autoload.php';

$filePath = THE_FILE;

\JoliNotif\Util\PharExtractor::extractFile(__DIR__.$filePath);

?>
PHAR_BOOTSTRAP;

        $phar = new \Phar($pharPath);
        $phar->buildFromDirectory($rootPackage, '#(src|vendor/composer)#');
        $phar->addFromString('bootstrap.php', str_replace(
            'THE_FILE',
            '\'/'.$fileRelativePath.'\'',
            $bootstrap
        ));
        $phar->addFromString($fileRelativePath, $fileContent);
        $phar->addFile('vendor/autoload.php');
        $phar->setStub($phar->createDefaultStub('bootstrap.php'));

        $this->assertTrue(is_file($pharPath));

        exec('php '.$pharPath);

        $this->assertTrue(is_file($extractedFilePath));
        $this->assertEquals($fileContent, file_get_contents($extractedFilePath));
    }
}
