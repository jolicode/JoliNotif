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
use Joli\JoliNotif\Driver\GrowlNotifyDriver;
use Psr\Log\NullLogger;

class GrowlNotifyDriverTest extends AbstractDriverTestCase
{
    use AbstractCliBasedDriverTestTrait;

    private const BINARY = 'growlnotify';

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

    protected function getDriver(): GrowlNotifyDriver
    {
        return new GrowlNotifyDriver(new NullLogger());
    }

    protected static function getExpectedCommandLineForNotification(): string
    {
        return <<<'CLI'
            'growlnotify' '--message' 'I'\''m the notification body'
            CLI;
    }

    protected static function getExpectedCommandLineForNotificationWithATitle(): string
    {
        return <<<'CLI'
            'growlnotify' '--message' 'I'\''m the notification body' '--title' 'I'\''m the notification title'
            CLI;
    }

    protected static function getExpectedCommandLineForNotificationWithAnIcon(): string
    {
        $iconDir = self::getIconDir();

        return <<<CLI
            'growlnotify' '--message' 'I'\\''m the notification body' '--image' '{$iconDir}/image.gif'
            CLI;
    }

    protected static function getExpectedCommandLineForNotificationWithAllOptions(): string
    {
        $iconDir = self::getIconDir();

        return <<<CLI
            'growlnotify' '--message' 'I'\\''m the notification body' '--title' 'I'\\''m the notification title' '--image' '{$iconDir}/image.gif'
            CLI;
    }
}
