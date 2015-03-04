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
use JoliNotif\Driver\TerminalNotifierDriver;
use JoliNotif\Util\OsHelper;

class TerminalNotifierDriverTest extends DriverTestCase
{
    const BINARY = 'terminal-notifier';

    use CliBasedDriverTestTrait;

    protected function getDriver()
    {
        return new TerminalNotifierDriver();
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
        return "'terminal-notifier' '-message' 'I'\\''m the notification body'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithATitle()
    {
        return "'terminal-notifier' '-message' 'I'\\''m the notification body' '-title' 'I'\\''m the notification title'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAnIcon()
    {
        if (OsHelper::isMacOS() && version_compare(OsHelper::getMacOSVersion(), '10.9.0', '>=')) {
            return "'terminal-notifier' '-message' 'I'\\''m the notification body' '-appIcon' '/home/toto/Images/my-icon.png'";
        }

        return "'terminal-notifier' '-message' 'I'\\''m the notification body'";
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAllOptions()
    {
        if (OsHelper::isMacOS() && version_compare(OsHelper::getMacOSVersion(), '10.9.0', '>=')) {
            return "'terminal-notifier' '-message' 'I'\\''m the notification body' '-title' 'I'\\''m the notification title' '-appIcon' '/home/toto/Images/my-icon.png'";
        }

        return "'terminal-notifier' '-message' 'I'\\''m the notification body' '-title' 'I'\\''m the notification title'";
    }
}
