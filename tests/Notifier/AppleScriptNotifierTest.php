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
    const BINARY = 'osascript';

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

    protected function getNotifier()
    {
        return new AppleScriptNotifier();
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotification()
    {
        return <<<CLI
'osascript' '-e' 'display notification "I'\''m the notification body"'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithATitle()
    {
        return <<<CLI
'osascript' '-e' 'display notification "I'\''m the notification body" with title "I'\''m the notification title"'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithASubtitle()
    {
        return <<<CLI
'osascript' '-e' 'display notification "I'\''m the notification body" with title "I'\''m the notification title" subtitle "I'\''m the notification subtitle"'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithASound()
    {
        return <<<CLI
'osascript' '-e' 'display notification "I'\''m the notification body" with title "I'\''m the notification title" sound name "Frog"'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAnIcon()
    {
        return <<<CLI
'osascript' '-e' 'display notification "I'\''m the notification body"'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAllOptions()
    {
        return <<<CLI
'osascript' '-e' 'display notification "I'\''m the notification body" with title "I'\''m the notification title" subtitle "I'\''m the notification subtitle" sound name "Frog"'
CLI;
    }
}
