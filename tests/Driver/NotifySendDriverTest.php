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
use Joli\JoliNotif\Driver\NotifySendDriver;

class NotifySendDriverTest extends AbstractDriverTestCase
{
    use AbstractCliBasedDriverTestTrait;

    private const BINARY = 'notify-send';

    public function testGetBinary()
    {
        $driver = $this->getDriver();

        $this->assertSame(self::BINARY, $driver->getBinary());
    }

    public function testGetPriority()
    {
        $driver = $this->getDriver();

        $this->assertSame(DriverInterface::PRIORITY_MEDIUM, $driver->getPriority());
    }

    protected function getDriver(): NotifySendDriver
    {
        return new NotifySendDriver();
    }

    protected function getExpectedCommandLineForNotification(): string
    {
        return <<<'CLI'
            'notify-send' 'I'\''m the notification body'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithATitle(): string
    {
        return <<<'CLI'
            'notify-send' 'I'\''m the notification title' 'I'\''m the notification body'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAnIcon(): string
    {
        $iconDir = $this->getIconDir();

        return <<<CLI
            'notify-send' '--icon' '{$iconDir}/image.gif' 'I'\\''m the notification body'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAllOptions(): string
    {
        $iconDir = $this->getIconDir();

        return <<<CLI
            'notify-send' '--icon' '{$iconDir}/image.gif' 'I'\\''m the notification title' 'I'\\''m the notification body'
            CLI;
    }
}
