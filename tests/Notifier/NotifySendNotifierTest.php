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
use Joli\JoliNotif\Notifier\NotifySendNotifier;

class NotifySendNotifierTest extends NotifierTestCase
{
    use CliBasedNotifierTestTrait;

    const BINARY = 'notify-send';

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
        return new NotifySendNotifier();
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotification(): string
    {
        return <<<CLI
'notify-send' 'I'\''m the notification body'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithATitle(): string
    {
        return <<<CLI
'notify-send' 'I'\''m the notification title' 'I'\''m the notification body'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAnIcon(): string
    {
        $iconDir = $this->getIconDir();

        return <<<CLI
'notify-send' '--icon' '${iconDir}/image.gif' 'I'\''m the notification body'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAllOptions(): string
    {
        $iconDir = $this->getIconDir();

        return <<<CLI
'notify-send' '--icon' '${iconDir}/image.gif' 'I'\''m the notification title' 'I'\''m the notification body'
CLI;
    }
}
