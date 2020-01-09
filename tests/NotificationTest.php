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

use Joli\JoliNotif\Notification;
use PHPUnit\Framework\TestCase;

class NotificationTest extends TestCase
{
    public function testItExtractsIconFromPhar()
    {
        $key = uniqid();
        $iconContent = $key;
        $rootPackage = \dirname(__DIR__);
        $iconRelativePath = 'Resources/notification/icon-'.$key.'.png';
        $testDir = sys_get_temp_dir().'/test-jolinotif';
        $pharPath = $testDir.'/notification-extract-icon-'.$key.'.phar';
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
        $phar->buildFromDirectory($rootPackage, '#(src|tests/fixtures|vendor)#');
        $phar->addFromString('bootstrap.php', str_replace(
            'THE_ICON',
            '\'/'.$iconRelativePath.'\'',
            $bootstrap
        ));
        $phar->addFromString($iconRelativePath, $iconContent);
        $phar->setStub($phar->createDefaultStub('bootstrap.php'));

        $this->assertFileExists($pharPath);

        exec('php '.$pharPath);

        $this->assertFileExists($extractedIconPath);
        $this->assertSame($iconContent, file_get_contents($extractedIconPath));
    }

    public function testItResolvesRealPathToIcon()
    {
        $notification = new Notification();
        $notification->setIcon(__DIR__.'/../tests/fixtures/image.gif');

        $this->assertFileEquals(__DIR__.'/fixtures/image.gif', $notification->getIcon());
    }
}
