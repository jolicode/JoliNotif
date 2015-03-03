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
use JoliNotif\Driver\GrowlNotifyDriver;

class GrowlNotifyDriverTest extends DriverTestCase
{
    const BINARY = 'growlnotify';

    use CliBasedDriverTestTrait;

    protected function getDriver()
    {
        return new GrowlNotifyDriver();
    }

    public function testGetBinary()
    {
        $driver = $this->getDriver();

        $this->assertSame(self::BINARY, $driver->getBinary());
    }

    public function testGetPriority()
    {
        $driver = $this->getDriver();

        $this->assertSame(Driver::PRIORITY_HIGH, $driver->getPriority());
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotification()
    {
        return "'growlnotify' '--message' 'I'\\''m the notification body'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithATitle()
    {
        return "'growlnotify' '--message' 'I'\\''m the notification body' '--title' 'I'\\''m the notification title'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAnIcon()
    {
        return "'growlnotify' '--message' 'I'\\''m the notification body' '--image' '/home/toto/Images/my-icon.png'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAllOptions()
    {
        return "'growlnotify' '--message' 'I'\\''m the notification body' '--title' 'I'\\''m the notification title' '--image' '/home/toto/Images/my-icon.png'";
    }
}
