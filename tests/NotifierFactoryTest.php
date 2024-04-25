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

use Joli\JoliNotif\Driver\AppleScriptDriver;
use Joli\JoliNotif\Driver\GrowlNotifyDriver;
use Joli\JoliNotif\Driver\KDialogDriver;
use Joli\JoliNotif\Driver\LibNotifyDriver;
use Joli\JoliNotif\Driver\NotifuDriver;
use Joli\JoliNotif\Driver\NotifySendDriver;
use Joli\JoliNotif\Driver\SnoreToastDriver;
use Joli\JoliNotif\Driver\TerminalNotifierDriver;
use Joli\JoliNotif\Driver\WslNotifySendDriver;
use Joli\JoliNotif\Exception\NoSupportedNotifierException;
use Joli\JoliNotif\LegacyNotifier;
use Joli\JoliNotif\Notifier\AppleScriptNotifier;
use Joli\JoliNotif\Notifier\GrowlNotifyNotifier;
use Joli\JoliNotif\Notifier\KDialogNotifier;
use Joli\JoliNotif\Notifier\LibNotifyNotifier;
use Joli\JoliNotif\Notifier\NotifuNotifier;
use Joli\JoliNotif\Notifier\NotifySendNotifier;
use Joli\JoliNotif\Notifier\SnoreToastNotifier;
use Joli\JoliNotif\Notifier\TerminalNotifierNotifier;
use Joli\JoliNotif\Notifier\WslNotifySendNotifier;
use Joli\JoliNotif\NotifierFactory;
use Joli\JoliNotif\tests\fixtures\ConfigurableNotifier;
use JoliCode\PhpOsHelper\OsHelper;
use PHPUnit\Framework\TestCase;

/**
 * @group legacy
 */
class NotifierFactoryTest extends TestCase
{
    public function testGetDefaultNotifiers()
    {
        $notifiers = NotifierFactory::getDefaultNotifiers();

        if (OsHelper::isUnix()) {
            $expectedNotifierClasses = [
                LibNotifyNotifier::class,
                GrowlNotifyNotifier::class,
                TerminalNotifierNotifier::class,
                AppleScriptNotifier::class,
                KDialogNotifier::class,
                NotifySendNotifier::class,
            ];
        } else {
            $expectedNotifierClasses = [
                SnoreToastNotifier::class,
                NotifuNotifier::class,
                WslNotifySendNotifier::class,
            ];
        }

        $this->assertNotifierClasses($expectedNotifierClasses, $notifiers);
    }

    public function testCreateUsesDefaultNotifiers()
    {
        $notifier = NotifierFactory::create();

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

        $this->assertInstanceOf(LegacyNotifier::class, $notifier);

        $driver = $notifier->getDriver();

        $this->assertContains($driver::class, $expectedDriverClasses);
    }

    public function testCreateUsesGivenNotifiers()
    {
        $notifier = NotifierFactory::create([
            new ConfigurableNotifier(true),
        ]);

        $this->assertInstanceOf(LegacyNotifier::class, $notifier);

        $driver = $notifier->getDriver();

        $this->assertInstanceOf(ConfigurableNotifier::class, $driver);
    }

    public function testCreateWithNoSupportedNotifiersReturnsANullNotifier()
    {
        $notifier = NotifierFactory::create([
            new ConfigurableNotifier(false),
            new ConfigurableNotifier(false),
        ]);

        $this->assertInstanceOf(LegacyNotifier::class, $notifier);

        $driver = $notifier->getDriver();

        $this->assertNull($driver);
    }

    public function testCreateUsesTheOnlySupportedNotifier()
    {
        $expectedNotifier = new ConfigurableNotifier(true);

        $notifier = NotifierFactory::create([
            $expectedNotifier,
        ]);

        $this->assertInstanceOf(LegacyNotifier::class, $notifier);

        $driver = $notifier->getDriver();

        $this->assertSame($expectedNotifier, $driver);
    }

    public function testCreateUsesTheFirstSupportedNotifierWhenNoPrioritiesAreGiven()
    {
        $notifier1 = new ConfigurableNotifier(false);
        $notifier2 = new ConfigurableNotifier(true);
        $notifier3 = new ConfigurableNotifier(true);
        $notifier4 = new ConfigurableNotifier(true);

        $notifier = NotifierFactory::create([
            $notifier1,
            $notifier2,
            $notifier3,
            $notifier4,
        ]);

        $this->assertInstanceOf(LegacyNotifier::class, $notifier);

        $driver = $notifier->getDriver();

        $this->assertSame($notifier2, $driver);
    }

    public function testCreateUsesTheBestSupportedNotifier()
    {
        $notifier1 = new ConfigurableNotifier(false);
        $notifier2 = new ConfigurableNotifier(true, 5);
        $notifier3 = new ConfigurableNotifier(true, 8);
        $notifier4 = new ConfigurableNotifier(false);
        $notifier5 = new ConfigurableNotifier(true, 6);

        $notifier = NotifierFactory::create([
            $notifier1,
            $notifier2,
            $notifier3,
            $notifier4,
            $notifier5,
        ]);

        $this->assertInstanceOf(LegacyNotifier::class, $notifier);

        $driver = $notifier->getDriver();

        $this->assertSame($notifier3, $driver);
    }

    public function testCreateUsesTheFirstOfTheBestSupportedNotifiers()
    {
        $notifier1 = new ConfigurableNotifier(false);
        $notifier2 = new ConfigurableNotifier(true, 5);
        $notifier3 = new ConfigurableNotifier(true, 8);
        $notifier4 = new ConfigurableNotifier(false);
        $notifier5 = new ConfigurableNotifier(true, 8);

        $notifier = NotifierFactory::create([
            $notifier1,
            $notifier2,
            $notifier3,
            $notifier4,
            $notifier5,
        ]);

        $this->assertInstanceOf(LegacyNotifier::class, $notifier);

        $driver = $notifier->getDriver();

        $this->assertSame($notifier3, $driver);
    }

    public function testCreateOrThrowExceptionWithNoSupportedNotifiersThrowsException()
    {
        $this->expectException(NoSupportedNotifierException::class);

        NotifierFactory::createOrThrowException([
            new ConfigurableNotifier(false),
            new ConfigurableNotifier(false),
        ]);
    }

    private function assertNotifierClasses(array $expectedNotifierClasses, array $notifiers)
    {
        $expectedCount = \count($expectedNotifierClasses);
        $this->assertSame($expectedCount, \count($notifiers));

        for ($i = 0; $i < $expectedCount; ++$i) {
            $this->assertInstanceOf($expectedNotifierClasses[$i], $notifiers[$i]);
        }
    }
}
