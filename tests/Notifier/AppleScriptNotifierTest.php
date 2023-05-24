<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\tests\Notifier;

use Joli\JoliNotif\Notifier;
use Joli\JoliNotif\Notifier\AppleScriptNotifier;
use Joli\JoliNotif\Util\OsHelper;

class AppleScriptNotifierTest extends NotifierTestCase
{
    use CliBasedNotifierTestTrait;

    private const BINARY = 'osascript';

    public function testIsSupported()
    {
        $notifier = $this->getNotifier();

        if (OsHelper::isMacOS() && version_compare(OsHelper::getMacOSVersion(), '10.9.0', '>=')) {
            $this->assertTrue($notifier->isSupported());
        } else {
            $this->assertFalse($notifier->isSupported());
        }
    }

    public function testGetBinary()
    {
        $notifier = $this->getNotifier();

        $this->assertSame(self::BINARY, $notifier->getBinary());
    }

    public function testGetPriority()
    {
        $notifier = $this->getNotifier();

        $this->assertSame(Notifier::PRIORITY_LOW, $notifier->getPriority());
    }

    protected function getNotifier(): Notifier
    {
        return new AppleScriptNotifier();
    }

    protected function getExpectedCommandLineForNotification(): string
    {
        return <<<'CLI'
            'osascript' '-e' 'display notification "I'\''m the notification body"'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithATitle(): string
    {
        return <<<'CLI'
            'osascript' '-e' 'display notification "I'\''m the notification body" with title "I'\''m the notification title"'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithASubtitle(): string
    {
        return <<<'CLI'
            'osascript' '-e' 'display notification "I'\''m the notification body" subtitle "I'\''m the notification subtitle"'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithASound(): string
    {
        return <<<'CLI'
            'osascript' '-e' 'display notification "I'\''m the notification body" sound name "Frog"'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAnIcon(): string
    {
        return <<<'CLI'
            'osascript' '-e' 'display notification "I'\''m the notification body"'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAllOptions(): string
    {
        return <<<'CLI'
            'osascript' '-e' 'display notification "I'\''m the notification body" with title "I'\''m the notification title" subtitle "I'\''m the notification subtitle" sound name "Frog"'
            CLI;
    }
}
