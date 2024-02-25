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

use Joli\JoliNotif\Exception\InvalidNotificationException;
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\Notifier;
use Joli\JoliNotif\Notifier\LibNotifyNotifier;
use PHPUnit\Framework\TestCase;

class LibNotifyNotifierTest extends TestCase
{
    public function testGetPriority()
    {
        $notifier = $this->getNotifier();

        $this->assertSame(Notifier::PRIORITY_HIGH, $notifier->getPriority());
    }

    public function testSendWithEmptyBody()
    {
        $notifier = $this->getNotifier();

        $this->expectException(InvalidNotificationException::class);
        $this->expectExceptionMessage('Notification body can not be empty');
        $notifier->send(new Notification());
    }

    /**
     * @requires ffi
     */
    public function testInitialize()
    {
        $notifier = $this->getNotifier();

        if (!$notifier::isLibraryExists()) {
            $this->markTestSkipped('Looks like libnotify is not installed');
        }

        $closureToInitialize = \Closure::bind(static function (LibNotifyNotifier $notifier) {
            $notifier->initialize();
        }, null, LibNotifyNotifier::class);

        $this->assertTrue($notifier->isSupported());
        $closureToInitialize($notifier);
    }

    protected function getNotifier(): LibNotifyNotifier
    {
        return new LibNotifyNotifier();
    }
}
