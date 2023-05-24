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

use Joli\JoliNotif\Exception\NoSupportedNotifierException;
use Joli\JoliNotif\Notifier\AppleScriptNotifier;
use Joli\JoliNotif\Notifier\GrowlNotifyNotifier;
use Joli\JoliNotif\Notifier\KDialogNotifier;
use Joli\JoliNotif\Notifier\NotifuNotifier;
use Joli\JoliNotif\Notifier\NotifySendNotifier;
use Joli\JoliNotif\Notifier\NullNotifier;
use Joli\JoliNotif\Notifier\SnoreToastNotifier;
use Joli\JoliNotif\Notifier\TerminalNotifierNotifier;
use Joli\JoliNotif\NotifierFactory;
use Joli\JoliNotif\tests\fixtures\ConfigurableNotifier;
use Joli\JoliNotif\Util\OsHelper;
use PHPUnit\Framework\TestCase;

class NotifierFactoryTest extends TestCase
{
    public function testGetDefaultNotifiers()
    {
        $notifiers = NotifierFactory::getDefaultNotifiers();

        if (OsHelper::isUnix()) {
            $expectedNotifierClasses = [
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
            ];
        }

        $this->assertNotifierClasses($expectedNotifierClasses, $notifiers);
    }

    public function testCreateUsesDefaultNotifiers()
    {
        $notifier = NotifierFactory::create();

        if ($notifier instanceof NullNotifier) {
            $this->markTestSkipped('This test needs that at least one notifier is supported');
        }

        $this->assertInstanceOf('Joli\\JoliNotif\\Notifier', $notifier);

        if (OsHelper::isUnix()) {
            $expectedNotifierClasses = [
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
            ];
        }

        $this->assertContains($notifier::class, $expectedNotifierClasses);
    }

    public function testCreateUsesGivenNotifiers()
    {
        $notifier = NotifierFactory::create([
            new ConfigurableNotifier(true),
        ]);

        $this->assertInstanceOf('Joli\\JoliNotif\\tests\\fixtures\\ConfigurableNotifier', $notifier);
    }

    public function testCreateWithNoSupportedNotifiersReturnsANullNotifier()
    {
        $notifier = NotifierFactory::create([
            new ConfigurableNotifier(false),
            new ConfigurableNotifier(false),
        ]);

        $this->assertInstanceOf(NullNotifier::class, $notifier);
    }

    public function testCreateUsesTheOnlySupportedNotifier()
    {
        $expectedNotifier = new ConfigurableNotifier(true);

        $notifier = NotifierFactory::create([
            $expectedNotifier,
        ]);

        $this->assertSame($expectedNotifier, $notifier);
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

        $this->assertSame($notifier2, $notifier);
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

        $this->assertSame($notifier3, $notifier);
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

        $this->assertSame($notifier3, $notifier);
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
