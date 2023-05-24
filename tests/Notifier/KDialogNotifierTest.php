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

class KDialogNotifierTest extends NotifierTestCase
{
    use CliBasedNotifierTestTrait;

    private const BINARY = 'kdialog';

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

    protected function getNotifier(): Notifier
    {
        return new Notifier\KDialogNotifier();
    }

    protected function getExpectedCommandLineForNotification(): string
    {
        return <<<'CLI'
            'kdialog' '--passivepopup' 'I'\''m the notification body' '5'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithATitle(): string
    {
        return <<<'CLI'
            'kdialog' '--title' 'I'\''m the notification title' '--passivepopup' 'I'\''m the notification body' '5'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAnIcon(): string
    {
        return <<<'CLI'
            'kdialog' '--passivepopup' 'I'\''m the notification body' '5'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAllOptions(): string
    {
        return <<<'CLI'
            'kdialog' '--title' 'I'\''m the notification title' '--passivepopup' 'I'\''m the notification body' '5'
            CLI;
    }
}
