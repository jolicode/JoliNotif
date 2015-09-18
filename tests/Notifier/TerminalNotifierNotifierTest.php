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

use Joli\JoliNotif\Notifier;
use Joli\JoliNotif\Notifier\TerminalNotifierNotifier;
use Joli\JoliNotif\Util\OsHelper;

class TerminalNotifierNotifierTest extends NotifierTestCase
{
    const BINARY = 'terminal-notifier';

    use CliBasedNotifierTestTrait;

    protected function getNotifier()
    {
        return new TerminalNotifierNotifier();
    }

    public function testGetBinary()
    {
        $notifier = $this->getNotifier();

        $this->assertSame(self::BINARY, $notifier->getBinary());
    }

    public function testGetPriority()
    {
        $notifier = $this->getNotifier();

        $this->assertSame(Notifier::PRIORITY_MEDIUM, $notifier->getPriority());
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotification()
    {
        return "'terminal-notifier' '-message' 'I'\\''m the notification body'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithATitle()
    {
        return "'terminal-notifier' '-message' 'I'\\''m the notification body' '-title' 'I'\\''m the notification title'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAnUrl()
    {
        return "'terminal-notifier' '-message' 'I'\\''m the notification body' '-open' 'https://github.com/jolicode/JoliNotif'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAnIcon()
    {
        if (OsHelper::isMacOS() && version_compare(OsHelper::getMacOSVersion(), '10.9.0', '>=')) {
            return "'terminal-notifier' '-message' 'I'\\''m the notification body' '-appIcon' '/home/toto/Images/my-icon.png'";
        }

        return "'terminal-notifier' '-message' 'I'\\''m the notification body'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAllOptions()
    {
        if (OsHelper::isMacOS() && version_compare(OsHelper::getMacOSVersion(), '10.9.0', '>=')) {
            return "'terminal-notifier' '-message' 'I'\\''m the notification body' '-title' 'I'\\''m the notification title' '-appIcon' '/home/toto/Images/my-icon.png' '-open' 'https://github.com/jolicode/JoliNotif'";
        }

        return "'terminal-notifier' '-message' 'I'\\''m the notification body' '-title' 'I'\\''m the notification title' '-open' 'https://github.com/jolicode/JoliNotif'";
    }
}
