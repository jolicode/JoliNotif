<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JoliNotif\tests\Driver;

use JoliNotif\Driver\Driver;
use JoliNotif\Driver\NotifuDriver;

class NotifuDriverTest extends DriverTestCase
{
    const BINARY = 'notifu';

    use CliBasedDriverTestTrait;

    protected function getDriver()
    {
        return new NotifuDriver();
    }

    public function testGetBinary()
    {
        $driver = $this->getDriver();

        $this->assertSame(self::BINARY, $driver->getBinary());
    }

    public function testGetPriority()
    {
        $driver = $this->getDriver();

        $this->assertSame(Driver::PRIORITY_LOW, $driver->getPriority());
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotification()
    {
        return "'notifu' '/m' 'I'\\''m the notification body'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithATitle()
    {
        return "'notifu' '/m' 'I'\\''m the notification body' '/p' 'I'\\''m the notification title'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAnIcon()
    {
        return "'notifu' '/m' 'I'\\''m the notification body' '/i' '/home/toto/Images/my-icon.png'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAllOptions()
    {
        return "'notifu' '/m' 'I'\\''m the notification body' '/p' 'I'\\''m the notification title' '/i' '/home/toto/Images/my-icon.png'";
    }
}
