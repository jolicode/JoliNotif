<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Joli\JoliNotif\tests\Notifier;

use Joli\JoliNotif\Notifier\AppleScriptNotifier;
use Joli\JoliNotif\Notifier;
use Joli\JoliNotif\Util\OsHelper;

class AppleScriptNotifierTest extends NotifierTestCase
{
    const BINARY = 'osascript';

    use CliBasedNotifierTestTrait;

    protected function getNotifier()
    {
        return new AppleScriptNotifier();
    }

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

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotification()
    {
        return "'osascript' '-e' 'display notification \"I'\\''m the notification body\"'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithATitle()
    {
        return "'osascript' '-e' 'display notification \"I'\\''m the notification body\" with title \"I'\\''m the notification title\"'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAnIcon()
    {
        return "'osascript' '-e' 'display notification \"I'\\''m the notification body\"'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAllOptions()
    {
        return "'osascript' '-e' 'display notification \"I'\\''m the notification body\" with title \"I'\\''m the notification title\"'";
    }
}
