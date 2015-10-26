<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\tests;

class NotificationTest extends \PHPUnit_Framework_TestCase
{
    public function testItExtractsIconFromPhar()
    {
        $key               = uniqid();
        $iconContent       = $key;
        $rootPackage       = dirname(dirname(__FILE__));
        $iconRelativePath  = 'Resources/notification/icon-'.$key.'.png';
        $testDir           = sys_get_temp_dir().'/test-jolinotif';
        $pharPath          = $testDir.'/notification-extract-icon-'.$key.'.phar';
        $extractedIconPath = sys_get_temp_dir().'/jolinotif/'.$iconRelativePath;

        if (!is_dir($testDir)) {
            mkdir($testDir);
        }

        $bootstrap = <<<'PHAR_BOOTSTRAP'
<?php

require __DIR__.'/vendor/autoload.php';

$iconPath = THE_ICON;
$notification = new \Joli\JoliNotif\Notification();
$notification->setBody('My notification');
$notification->setIcon(__DIR__.$iconPath);
PHAR_BOOTSTRAP;

        $phar = new \Phar($pharPath);
        $phar->buildFromDirectory($rootPackage, '#(src|tests/fixtures|vendor/composer)#');
        $phar->addFromString('bootstrap.php', str_replace(
            'THE_ICON',
            '\'/'.$iconRelativePath.'\'',
            $bootstrap
        ));
        $phar->addFromString($iconRelativePath, $iconContent);
        $phar->addFile('vendor/autoload.php');
        $phar->setStub($phar->createDefaultStub('bootstrap.php'));

        $this->assertTrue(is_file($pharPath));

        exec('php '.$pharPath);

        $this->assertTrue(is_file($extractedIconPath));
        $this->assertSame($iconContent, file_get_contents($extractedIconPath));
    }
}
