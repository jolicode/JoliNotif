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
use JoliNotif\Driver\NotifySendDriver;

class NotifySendDriverTest extends DriverTestCase
{
    const BINARY = 'notify-send';

    use CliBasedDriverTestTrait;
    use UnixBasedDriverTestTrait;

    protected function getDriver()
    {
        return new NotifySendDriver();
    }

    public function testGetBinary()
    {
        $driver = $this->getDriver();

        $this->assertSame(self::BINARY, $driver->getBinary());
    }

    public function testGetPriority()
    {
        $driver = $this->getDriver();

        $this->assertSame(Driver::PRIORITY_MEDIUM, $driver->getPriority());
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotification()
    {
        return "'notify-send' 'I'\\''m the notification body'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithATitle()
    {
        return "'notify-send' 'I'\\''m the notification title' 'I'\\''m the notification body'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAnIcon()
    {
        return "'notify-send' '--icon' '/home/toto/Images/my-icon.png' 'I'\\''m the notification body'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAllOptions()
    {
        return "'notify-send' '--icon' '/home/toto/Images/my-icon.png' 'I'\\''m the notification title' 'I'\\''m the notification body'";
    }
}
