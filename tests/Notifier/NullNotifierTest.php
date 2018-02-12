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

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\Notifier;
use Joli\JoliNotif\Notifier\NullNotifier;

class NullNotifierTest extends NotifierTestCase
{
    public function testGetPriority()
    {
        $notifier = $this->getNotifier();

        $this->assertSame(Notifier::PRIORITY_LOW, $notifier->getPriority());
    }

    public function testIsSupported()
    {
        $this->assertTrue($this->getNotifier()->isSupported());
    }

    public function testSendReturnsFalse()
    {
        $notifier = $this->getNotifier();

        $notification = new Notification();
        $notification->setBody('The notification body');

        $this->assertFalse($notifier->send($notification));
    }

    protected function getNotifier(): Notifier
    {
        return new NullNotifier();
    }
}
