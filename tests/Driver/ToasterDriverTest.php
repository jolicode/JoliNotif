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
use JoliNotif\Driver\ToasterDriver;

class ToasterDriverTest extends DriverTestCase
{
    const BINARY = 'toast';

    use CliBasedDriverTestTrait;

    protected function getDriver()
    {
        return new ToasterDriver();
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
        return "'toast' '-m' 'I'\\''m the notification body'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithATitle()
    {
        return "'toast' '-m' 'I'\\''m the notification body' '-t' 'I'\\''m the notification title'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAnIcon()
    {
        return "'toast' '-m' 'I'\\''m the notification body' '-p' '/home/toto/Images/my-icon.png'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAllOptions()
    {
        return "'toast' '-m' 'I'\\''m the notification body' '-t' 'I'\\''m the notification title' '-p' '/home/toto/Images/my-icon.png'";
    }
}
