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

use JoliNotif\Driver\AppleScriptDriver;
use JoliNotif\Driver\Driver;
use JoliNotif\Notification;

class AppleScriptDriverTest extends DriverTestCase
{
    const BINARY = 'osascript';

    use CliBasedDriverTestTrait;
    use UnixBasedDriverTestTrait;

    protected function getDriver()
    {
        return new AppleScriptDriver();
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
        return "'osascript' '-e' 'display notification \"I'\\''m the notification body\"'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithATitle()
    {
        return "'osascript' '-e' 'display notification \"I'\\''m the notification body\" with title \"I'\\''m the notification title\"'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAnIcon()
    {
        return "'osascript' '-e' 'display notification \"I'\\''m the notification body\"'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAllOptions()
    {
        return "'osascript' '-e' 'display notification \"I'\\''m the notification body\" with title \"I'\\''m the notification title\"'";
    }
}
