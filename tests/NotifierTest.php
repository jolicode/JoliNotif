<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JoliNotif\tests;

use JoliNotif\Notification;
use JoliNotif\Notifier;
use JoliNotif\tests\fixtures\ConfigurableDriver;

class NotifierTest extends \PHPUnit_Framework_TestCase
{
    public function testNoDriverOrNoSupportedDriverThrowsException()
    {
        try {
            new Notifier([]);
            $this->fail('Expected a SystemNotSupportedException');
        } catch (\Exception $e) {
            $this->assertInstanceOf('JoliNotif\Exception\SystemNotSupportedException', $e);
        }

        try {
            new Notifier([
                new ConfigurableDriver(false),
                new ConfigurableDriver(false),
            ]);
            $this->fail('Expected a SystemNotSupportedException');
        } catch (\Exception $e) {
            $this->assertInstanceOf('JoliNotif\Exception\SystemNotSupportedException', $e);
        }
    }

    public function testTheBestSupportedDriverIsUsed()
    {
        // test case
        $driver = new ConfigurableDriver(true);

        $notifier = new Notifier([
            $driver,
        ]);
        $this->assertSame($driver, $notifier->getDriverInUse());

        // test case
        $driver1 = new ConfigurableDriver(false);
        $driver2 = new ConfigurableDriver(true);
        $driver3 = new ConfigurableDriver(true);
        $driver4 = new ConfigurableDriver(true);

        $notifier = new Notifier([
            $driver1,
            $driver2,
            $driver3,
            $driver4,
        ]);
        $this->assertSame($driver2, $notifier->getDriverInUse());

        // test case
        $driver1 = new ConfigurableDriver(false);
        $driver2 = new ConfigurableDriver(true, 5);
        $driver3 = new ConfigurableDriver(false);
        $driver4 = new ConfigurableDriver(true, 8);
        $driver5 = new ConfigurableDriver(true, 6);

        $notifier = new Notifier([
            $driver1,
            $driver2,
            $driver3,
            $driver4,
            $driver5,
        ]);
        $this->assertSame($driver4, $notifier->getDriverInUse());

        // test case
        $driver1 = new ConfigurableDriver(false);
        $driver2 = new ConfigurableDriver(true, 5);
        $driver3 = new ConfigurableDriver(true, 8);
        $driver4 = new ConfigurableDriver(false);
        $driver5 = new ConfigurableDriver(true, 8);

        $notifier = new Notifier([
            $driver1,
            $driver2,
            $driver3,
            $driver4,
            $driver5,
        ]);
        $this->assertSame($driver3, $notifier->getDriverInUse());
    }

    public function testSendThrowsExceptionWhenNotificationHasAnEmptyBody()
    {
        $notifier = new Notifier([new ConfigurableDriver(true)]);

        // test case
        $notification = new Notification();

        try {
            $notifier->send($notification);
            $this->fail('Expected a InvalidNotificationException');
        } catch (\Exception $e) {
            $this->assertInstanceOf('JoliNotif\Exception\InvalidNotificationException', $e);
        }

        // test case
        $notification = new Notification();
        $notification->setBody('');

        try {
            $notifier->send($notification);
            $this->fail('Expected a InvalidNotificationException');
        } catch (\Exception $e) {
            $this->assertInstanceOf('JoliNotif\Exception\InvalidNotificationException', $e);
        }
    }

    public function testSendUsesTheBestDriverAndReturnsItsReturn()
    {
        $notification = new Notification();
        $notification->setBody('My notification');

        // test case
        $driver = new ConfigurableDriver(true, 2, false);

        $notifier = new Notifier([
            $driver,
        ]);
        $this->assertFalse($notifier->send($notification));

        // test case
        $driver1 = new ConfigurableDriver(false, 0, false);
        $driver2 = new ConfigurableDriver(true, 0, true);
        $driver3 = new ConfigurableDriver(true, 0, false);
        $driver4 = new ConfigurableDriver(true, 0, false);

        $notifier = new Notifier([
            $driver1,
            $driver2,
            $driver3,
            $driver4,
        ]);
        $this->assertTrue($notifier->send($notification));

        // test case
        $driver1 = new ConfigurableDriver(false, 0, false);
        $driver2 = new ConfigurableDriver(true, 0, false);
        $driver3 = new ConfigurableDriver(true, 5, true);
        $driver4 = new ConfigurableDriver(true, 2, false);

        $notifier = new Notifier([
            $driver1,
            $driver2,
            $driver3,
            $driver4,
        ]);
        $this->assertTrue($notifier->send($notification));
    }

    public function testItExtractsIconFromPhar()
    {
        $key              = uniqid();
        $iconContent      = $key;
        $rootPackage      = dirname(dirname(__FILE__));
        $iconRelativePath = 'Resources/notification/icon-'.$key.'.png';
        $pharPath         = sys_get_temp_dir().'/testjolinotif-'.$key.'.phar';
        $tmpIconPath      = sys_get_temp_dir().'/jolinotif/'.$iconRelativePath;

        $stub = <<<'PHAR_STUB'
<?php

require __DIR__.'/vendor/autoload.php';

$notification = new \JoliNotif\Notification();
$notification->setBody('My notification');
$notification->setIcon(__DIR__.YOUR_ICON);

$notifier = new \JoliNotif\Notifier([
    new \JoliNotif\tests\fixtures\ConfigurableDriver(true),
]);
$notifier->send($notification);

__HALT_COMPILER();
?>
PHAR_STUB;

        $phar = new \Phar($pharPath);
        $phar->buildFromDirectory($rootPackage, '#(src|tests/fixtures|vendor/composer)#');
        $phar->addFromString($iconRelativePath, $iconContent);
        $phar->addFile('vendor/autoload.php');
        $phar->setStub(
            str_replace(
                [
                    '__DIR__',
                    'YOUR_ICON'
                ],
                [
                    '\'phar://'.$pharPath.'\'',
                    '\'/'.$iconRelativePath.'\'',
                ],
                $stub
            )
        );

        $this->assertTrue(is_file($pharPath));

        exec('php '.$pharPath);

        $this->assertTrue(is_file($tmpIconPath));
        $this->assertEquals($iconContent, file_get_contents($tmpIconPath));
    }
}
