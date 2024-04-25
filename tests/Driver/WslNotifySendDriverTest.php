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
use Joli\JoliNotif\Driver\WslNotifySendDriver;

class WslNotifySendDriverTest extends AbstractDriverTestCase
{
    use AbstractCliBasedDriverTestTrait;
    use BinaryProviderTestTrait;

    private const BINARY = 'wsl-notify-send';

    public function testGetBinary()
    {
        $driver = $this->getDriver();

        $this->assertSame(self::BINARY, $driver->getBinary());
    }

    public function testGetPriority()
    {
        $driver = $this->getDriver();

        $this->assertSame(DriverInterface::PRIORITY_HIGH, $driver->getPriority());
    }

    protected function getDriver(): WslNotifySendDriver
    {
        return new WslNotifySendDriver();
    }

    protected function getExpectedCommandLineForNotification(): string
    {
        return <<<'CLI'
            'wsl-notify-send' '--appId' 'JoliNotif' 'I'\''m the notification body'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithATitle(): string
    {
        return <<<'CLI'
            'wsl-notify-send' '--appId' 'JoliNotif' 'I'\''m the notification body' '-c' 'I'\''m the notification title'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAnIcon(): string
    {
        return <<<'CLI'
            'wsl-notify-send' '--appId' 'JoliNotif' 'I'\''m the notification body'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAllOptions(): string
    {
        return <<<'CLI'
            'wsl-notify-send' '--appId' 'JoliNotif' 'I'\''m the notification body' '-c' 'I'\''m the notification title'
            CLI;
    }
}
