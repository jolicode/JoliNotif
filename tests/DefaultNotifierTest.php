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

use Joli\JoliNotif\DefaultNotifier;
use Joli\JoliNotif\Driver\AppleScriptDriver;
use Joli\JoliNotif\Driver\GrowlNotifyDriver;
use Joli\JoliNotif\Driver\KDialogDriver;
use Joli\JoliNotif\Driver\LibNotifyDriver;
use Joli\JoliNotif\Driver\NotifuDriver;
use Joli\JoliNotif\Driver\NotifySendDriver;
use Joli\JoliNotif\Driver\SnoreToastDriver;
use Joli\JoliNotif\Driver\TerminalNotifierDriver;
use Joli\JoliNotif\Driver\WslNotifySendDriver;
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\tests\fixtures\ConfigurableDriver;
use JoliCode\PhpOsHelper\OsHelper;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DefaultNotifierTest extends TestCase
{
    public function testCreateDefaultNotifier()
    {
        $notifier = new DefaultNotifier();

        if (OsHelper::isUnix()) {
            $expectedDriverClasses = [
                LibNotifyDriver::class,
                GrowlNotifyDriver::class,
                TerminalNotifierDriver::class,
                AppleScriptDriver::class,
                KDialogDriver::class,
                NotifySendDriver::class,
            ];
        } else {
            $expectedDriverClasses = [
                SnoreToastDriver::class,
                NotifuDriver::class,
                WslNotifySendDriver::class,
            ];
        }

        $driver = $notifier->getDriver();

        $this->assertContains($driver::class, $expectedDriverClasses);
    }

    public function testUsesGivenDrivers()
    {
        $notifier = new DefaultNotifier(null, [
            new ConfigurableDriver(true),
        ]);

        $driver = $notifier->getDriver();

        $this->assertInstanceOf(ConfigurableDriver::class, $driver);
    }

    public function testWithNoSupportedDriversReturnsANativeNotifier()
    {
        $notifier = new DefaultNotifier(null, [
            new ConfigurableDriver(false),
            new ConfigurableDriver(false),
        ], false);

        $driver = $notifier->getDriver();

        $this->assertNotNull($driver);
    }

    public function testWithNoSupportedDriversReturnsANullDriverIfConfiguredWithOnlyAdditionalDrivers()
    {
        $notifier = new DefaultNotifier(null, [
            new ConfigurableDriver(false),
            new ConfigurableDriver(false),
        ], true);

        $driver = $notifier->getDriver();

        $this->assertNull($driver);
    }

    public function testItUsesTheOnlySupportedDriver()
    {
        $expectedDriver = new ConfigurableDriver(true);

        $notifier = new DefaultNotifier(null, [
            $expectedDriver,
        ]);

        $this->assertSame($expectedDriver, $notifier->getDriver());
    }

    public function testItUsesTheFirstSupportedDriverWhenNoPrioritiesAreGiven()
    {
        $driver1 = new ConfigurableDriver(false);
        $driver2 = new ConfigurableDriver(true);
        $driver3 = new ConfigurableDriver(true);
        $driver4 = new ConfigurableDriver(true);

        $notifier = new DefaultNotifier(null, [
            $driver1,
            $driver2,
            $driver3,
            $driver4,
        ]);

        $this->assertSame($driver2, $notifier->getDriver());
    }

    public function testItUsesTheBestSupportedDriver()
    {
        $driver1 = new ConfigurableDriver(false);
        $driver2 = new ConfigurableDriver(true, 5);
        $driver3 = new ConfigurableDriver(true, 8);
        $driver4 = new ConfigurableDriver(false);
        $driver5 = new ConfigurableDriver(true, 6);

        $notifier = new DefaultNotifier(null, [
            $driver1,
            $driver2,
            $driver3,
            $driver4,
            $driver5,
        ]);

        $this->assertSame($driver3, $notifier->getDriver());
    }

    public function testItUsesTheFirstOfTheBestSupportedDrivers()
    {
        $driver1 = new ConfigurableDriver(false);
        $driver2 = new ConfigurableDriver(true, 5);
        $driver3 = new ConfigurableDriver(true, 8);
        $driver4 = new ConfigurableDriver(false);
        $driver5 = new ConfigurableDriver(true, 8);

        $notifier = new DefaultNotifier(null, [
            $driver1,
            $driver2,
            $driver3,
            $driver4,
            $driver5,
        ]);

        $this->assertSame($driver3, $notifier->getDriver());
    }

    public function testItLogsWhenNoDriverAvailable()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('warning')
            ->with($this->equalTo('No driver available to display a notification on your system.'))
        ;

        $notifier = new DefaultNotifier($logger, [
            new ConfigurableDriver(false),
        ], true);

        $this->assertNull($notifier->getDriver());

        $result = $notifier->send(new Notification());

        $this->assertFalse($result);
    }
}
