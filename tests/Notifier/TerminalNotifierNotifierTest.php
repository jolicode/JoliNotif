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
        return <<<CLI
'terminal-notifier' '-message' 'I'\''m the notification body'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithATitle()
    {
        return <<<CLI
'terminal-notifier' '-message' 'I'\''m the notification body' '-title' 'I'\''m the notification title'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAnIcon()
    {
        if (OsHelper::isMacOS() && version_compare(OsHelper::getMacOSVersion(), '10.9.0', '>=')) {
            return <<<CLI
'terminal-notifier' '-message' 'I'\''m the notification body' '-appIcon' '/home/toto/Images/my-icon.png'
CLI;
        }

        return <<<CLI
'terminal-notifier' '-message' 'I'\''m the notification body'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAllOptions()
    {
        if (OsHelper::isMacOS() && version_compare(OsHelper::getMacOSVersion(), '10.9.0', '>=')) {
            return <<<CLI
'terminal-notifier' '-message' 'I'\''m the notification body' '-title' 'I'\''m the notification title' '-appIcon' '/home/toto/Images/my-icon.png'
CLI;
        }

        return <<<CLI
'terminal-notifier' '-message' 'I'\''m the notification body' '-title' 'I'\''m the notification title'
CLI;
    }
}
