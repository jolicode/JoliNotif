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
    use CliBasedNotifierTestTrait;

    const BINARY = 'terminal-notifier';

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

    protected function getNotifier(): Notifier
    {
        return new TerminalNotifierNotifier();
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotification(): string
    {
        return <<<CLI
'terminal-notifier' '-message' 'I'\''m the notification body'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithATitle(): string
    {
        return <<<CLI
'terminal-notifier' '-message' 'I'\''m the notification body' '-title' 'I'\''m the notification title'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAnUrl(): string
    {
        return <<<CLI
'terminal-notifier' '-message' 'I'\''m the notification body' '-open' 'https://google.com'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAnIcon(): string
    {
        if (OsHelper::isMacOS() && version_compare(OsHelper::getMacOSVersion(), '10.9.0', '>=')) {
            $iconDir = $this->getIconDir();

            return <<<CLI
'terminal-notifier' '-message' 'I'\''m the notification body' '-appIcon' '${iconDir}/image.gif'
CLI;
        }

        return <<<CLI
'terminal-notifier' '-message' 'I'\''m the notification body'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAllOptions(): string
    {
        if (OsHelper::isMacOS() && version_compare(OsHelper::getMacOSVersion(), '10.9.0', '>=')) {
            $iconDir = $this->getIconDir();

            return <<<CLI
'terminal-notifier' '-message' 'I'\''m the notification body' '-title' 'I'\''m the notification title' '-appIcon' '${iconDir}/image.gif' '-open' 'https://google.com'
CLI;
        }

        return <<<CLI
'terminal-notifier' '-message' 'I'\''m the notification body' '-title' 'I'\''m the notification title' '-open' 'https://google.com'
CLI;
    }
}
