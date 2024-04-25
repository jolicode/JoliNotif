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
use Joli\JoliNotif\Notifier\WslNotifySendNotifier;

/**
 * @group legacy
 */
class WslNotifySendNotifierTest extends NotifierTestCase
{
    use BinaryProviderTestTrait;
    use CliBasedNotifierTestTrait;

    private const BINARY = 'wsl-notify-send';

    public function testGetBinary()
    {
        $notifier = $this->getNotifier();

        $this->assertSame(self::BINARY, $notifier->getBinary());
    }

    public function testGetPriority()
    {
        $notifier = $this->getNotifier();

        $this->assertSame(Notifier::PRIORITY_HIGH, $notifier->getPriority());
    }

    protected function getNotifier(): WslNotifySendNotifier
    {
        return new WslNotifySendNotifier();
    }

    protected function getExpectedCommandLineForNotification(): string
    {
        return <<<'CLI'
            'wsl-notify-send' '--appId' 'JoliNotif' 'I'\''m the notification body'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithATitle(): string
    {
        return <<<'CLI'
            'wsl-notify-send' '--appId' 'JoliNotif' 'I'\''m the notification body' '-c' 'I'\''m the notification title'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAnIcon(): string
    {
        return <<<'CLI'
            'wsl-notify-send' '--appId' 'JoliNotif' 'I'\''m the notification body'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAllOptions(): string
    {
        return <<<'CLI'
            'wsl-notify-send' '--appId' 'JoliNotif' 'I'\''m the notification body' '-c' 'I'\''m the notification title'
            CLI;
    }
}
