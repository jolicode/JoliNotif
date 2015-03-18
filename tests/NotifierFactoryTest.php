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

use JoliNotif\Notifier;
use JoliNotif\NotifierFactory;
use JoliNotif\tests\fixtures\ConfigurableNotifier;
use JoliNotif\Util\OsHelper;

class NotifierFactoryTest extends \PHPUnit_Framework_TestCase
{
    private function assertNotifierClasses($expectedNotifierClasses, $notifiers)
    {
        $expectedCount = count($expectedNotifierClasses);
        $this->assertSame($expectedCount, count($notifiers));

        for ($i = 0; $i<$expectedCount; $i++) {
            $this->assertInstanceOf($expectedNotifierClasses[$i], $notifiers[$i]);
        }
    }

    public function testGetDefaultNotifiers()
    {
        $notifiers = NotifierFactory::getDefaultNotifiers();

        if (OsHelper::isUnix()) {
            $expectedNotifierClasses = [
                'JoliNotif\\Notifier\\GrowlNotifyNotifier',
                'JoliNotif\\Notifier\\TerminalNotifierNotifier',
                'JoliNotif\\Notifier\\AppleScriptNotifier',
                'JoliNotif\\Notifier\\NotifySendNotifier',
            ];
        } else {
            $expectedNotifierClasses = [
                'JoliNotif\\Notifier\\ToasterNotifier',
                'JoliNotif\\Notifier\\NotifuNotifier',
            ];
        }

        $this->assertNotifierClasses($expectedNotifierClasses, $notifiers);
    }

    public function testCreateUsesDefaultNotifiers()
    {
        $notifier = NotifierFactory::create();

        if (null === $notifier) {
            $this->markTestSkipped('This test needs that at least one notifier is supported');
        }

        $this->assertInstanceOf('JoliNotif\\Notifier', $notifier);

        if (OsHelper::isUnix()) {
            $expectedNotifierClasses = [
                'JoliNotif\\Notifier\\GrowlNotifyNotifier',
                'JoliNotif\\Notifier\\TerminalNotifierNotifier',
                'JoliNotif\\Notifier\\AppleScriptNotifier',
                'JoliNotif\\Notifier\\NotifySendNotifier',
            ];
        } else {
            $expectedNotifierClasses = [
                'JoliNotif\\Notifier\\ToasterNotifier',
                'JoliNotif\\Notifier\\NotifuNotifier',
            ];
        }

        $this->assertContains(get_class($notifier), $expectedNotifierClasses);
    }

    public function testCreateUsesGivenNotifiers()
    {
        $notifier = NotifierFactory::create([
            new ConfigurableNotifier(true),
        ]);

        $this->assertInstanceOf('JoliNotif\\tests\\fixtures\\ConfigurableNotifier', $notifier);
    }

    public function testCreateWithNoSupportedNotifiersReturnsNull()
    {
        $notifier = NotifierFactory::create([
            new ConfigurableNotifier(false),
            new ConfigurableNotifier(false),
        ]);

        $this->assertNull($notifier);
    }

    public function testCreateUsesTheBestSupportedNotifier()
    {
        // test case

        $expectedNotifier = new ConfigurableNotifier(true);

        $notifier = NotifierFactory::create([
            $expectedNotifier,
        ]);

        $this->assertSame($expectedNotifier, $notifier);

        // test case

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

        // test case

        $notifier1 = new ConfigurableNotifier(false);
        $notifier2 = new ConfigurableNotifier(true, 5);
        $notifier3 = new ConfigurableNotifier(false);
        $notifier4 = new ConfigurableNotifier(true, 8);
        $notifier5 = new ConfigurableNotifier(true, 6);

        $notifier = NotifierFactory::create([
            $notifier1,
            $notifier2,
            $notifier3,
            $notifier4,
            $notifier5,
        ]);

        $this->assertSame($notifier4, $notifier);

        // test case

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
}
