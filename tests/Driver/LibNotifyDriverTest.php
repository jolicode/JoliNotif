<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\tests\Driver;

use Joli\JoliNotif\Driver\DriverInterface;
use Joli\JoliNotif\Driver\LibNotifyDriver;
use Joli\JoliNotif\Exception\InvalidNotificationException;
use Joli\JoliNotif\Notification;

class LibNotifyDriverTest extends AbstractDriverTestCase
{
    public function testGetPriority(): void
    {
        $driver = $this->getDriver();

        $this->assertSame(DriverInterface::PRIORITY_HIGH, $driver->getPriority());
    }

    public function testSendThrowsExceptionWhenNotificationDoesntHaveBody(): void
    {
        $this->expectException(InvalidNotificationException::class);

        $this->getDriver()->send(new Notification());
    }

    public function testSendThrowsExceptionWhenNotificationHasAnEmptyBody(): void
    {
        $this->expectException(InvalidNotificationException::class);

        $this->getDriver()->send((new Notification())->setBody(''));
    }

    /**
     * @requires extension ffi
     */
    public function testSendNotificationWithAllOptions(): void
    {
        $driver = $this->getDriver();

        if (!$driver->isSupported()) {
            $this->markTestSkipped('libnotify is not available');
        }

        $notification = (new Notification())
            ->setBody('I\'m the notification body')
            ->setTitle('I\'m the notification title')
            ->addOption('subtitle', 'I\'m the notification subtitle')
            ->addOption('sound', 'Frog')
            ->addOption('url', 'https://google.com')
            ->setIcon(self::getIconDir() . '/image.gif')
        ;

        $result = $driver->send($notification);

        if (!$result) {
            $this->markTestSkipped('Notification was not sent');
        }

        $this->assertTrue($driver->send($notification));
    }

    /**
     * @requires extension ffi
     */
    public function testWithMultipleInstance(): void
    {
        if (!$this->getDriver()->isSupported()) {
            $this->markTestSkipped('libnotify is not available');
        }

        $notification = (new Notification())
            ->setBody('I\'m the notification body')
            ->setTitle('I\'m the notification title')
        ;

        $result = (new LibNotifyDriver())->send($notification);

        if (!$result) {
            $this->markTestSkipped('Notification was not sent');
        }

        $this->assertTrue((new LibNotifyDriver())->send($notification));
        $this->assertTrue((new LibNotifyDriver())->send($notification));
    }

    protected function getDriver(): DriverInterface
    {
        static $driver;

        $driver ??= new LibNotifyDriver();

        return $driver;
    }
}
