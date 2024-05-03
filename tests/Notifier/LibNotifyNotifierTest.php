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

/**
 * @group legacy
 */
class LibNotifyNotifierTest extends NotifierTestCase
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
     * @requires extension ffi
     */
    public function testInitialize()
    {
        $notifier = $this->getNotifier();

        if (!$notifier::isLibraryExists()) {
            $this->markTestSkipped('Looks like libnotify is not installed');
        }

        $this->assertTrue($notifier->isSupported());
    }

    public function testSendThrowsExceptionWhenNotificationDoesntHaveBody()
    {
        $notifier = $this->getNotifier();

        $notification = new Notification();

        try {
            $notifier->send($notification);
            $this->fail('Expected a InvalidNotificationException');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Joli\JoliNotif\Exception\InvalidNotificationException', $e);
        }
    }

    public function testSendThrowsExceptionWhenNotificationHasAnEmptyBody()
    {
        $notifier = $this->getNotifier();

        $notification = new Notification();
        $notification->setBody('');

        try {
            $notifier->send($notification);
            $this->fail('Expected a InvalidNotificationException');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Joli\JoliNotif\Exception\InvalidNotificationException', $e);
        }
    }

    /**
     * @requires extension ffi
     */
    public function testSendNotificationWithAllOptions()
    {
        $notifier = $this->getNotifier();

        $notification = (new Notification())
            ->setBody('I\'m the notification body')
            ->setTitle('I\'m the notification title')
            ->addOption('subtitle', 'I\'m the notification subtitle')
            ->addOption('sound', 'Frog')
            ->addOption('url', 'https://google.com')
            ->setIcon($this->getIconDir() . '/image.gif')
        ;

        $result = $notifier->send($notification);

        if (!$result) {
            $this->markTestSkipped('Notification was not sent');
        }

        $this->assertTrue($notifier->send($notification));
    }

    protected function getNotifier(): LibNotifyNotifier
    {
        return new LibNotifyNotifier();
    }
}
