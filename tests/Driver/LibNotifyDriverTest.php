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
    public function testGetPriority()
    {
        $driver = $this->getDriver();

        $this->assertSame(DriverInterface::PRIORITY_HIGH, $driver->getPriority());
    }

    public function testSendWithEmptyBody()
    {
        $driver = $this->getDriver();

        $this->expectException(InvalidNotificationException::class);
        $this->expectExceptionMessage('Notification body can not be empty');
        $driver->send(new Notification());
    }

    /**
     * @requires extension ffi
     */
    public function testInitialize()
    {
        $driver = $this->getDriver();

        if (!$driver::isLibraryExists()) {
            $this->markTestSkipped('Looks like libnotify is not installed');
        }

        $this->assertTrue($driver->isSupported());
    }

    public function testSendThrowsExceptionWhenNotificationDoesntHaveBody()
    {
        $driver = $this->getDriver();

        $notification = new Notification();

        try {
            $driver->send($notification);
            $this->fail('Expected a InvalidNotificationException');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Joli\JoliNotif\Exception\InvalidNotificationException', $e);
        }
    }

    public function testSendThrowsExceptionWhenNotificationHasAnEmptyBody()
    {
        $driver = $this->getDriver();

        $notification = new Notification();
        $notification->setBody('');

        try {
            $driver->send($notification);
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
        $driver = $this->getDriver();

        $notification = (new Notification())
            ->setBody('I\'m the notification body')
            ->setTitle('I\'m the notification title')
            ->addOption('subtitle', 'I\'m the notification subtitle')
            ->addOption('sound', 'Frog')
            ->addOption('url', 'https://google.com')
            ->setIcon($this->getIconDir() . '/image.gif')
        ;

        $result = $driver->send($notification);

        if (!$result) {
            $this->markTestSkipped('Notification was not sent');
        }

        $this->assertTrue($driver->send($notification));
    }

    protected function getDriver(): DriverInterface
    {
        static $driver;

        $driver ??= new LibNotifyDriver();

        return $driver;
    }
}
