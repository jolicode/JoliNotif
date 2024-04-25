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

use Joli\JoliNotif\Driver\AppleScriptDriver;
use Joli\JoliNotif\Driver\DriverInterface;
use JoliCode\PhpOsHelper\OsHelper;

class AppleScriptDriverTest extends AbstractDriverTestCase
{
    use AbstractCliBasedDriverTestTrait;

    private const BINARY = 'osascript';

    public function testIsSupported()
    {
        $driver = $this->getDriver();

        if (OsHelper::isMacOS() && version_compare(OsHelper::getMacOSVersion(), '10.9.0', '>=')) {
            $this->assertTrue($driver->isSupported());
        } else {
            $this->assertFalse($driver->isSupported());
        }
    }

    public function testGetBinary()
    {
        $driver = $this->getDriver();

        $this->assertSame(self::BINARY, $driver->getBinary());
    }

    public function testGetPriority()
    {
        $driver = $this->getDriver();

        $this->assertSame(DriverInterface::PRIORITY_LOW, $driver->getPriority());
    }

    protected function getDriver(): AppleScriptDriver
    {
        return new AppleScriptDriver();
    }

    protected function getExpectedCommandLineForNotification(): string
    {
        return <<<'CLI'
            'osascript' '-e' 'display notification "I'\''m the notification body"'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithATitle(): string
    {
        return <<<'CLI'
            'osascript' '-e' 'display notification "I'\''m the notification body" with title "I'\''m the notification title"'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithASubtitle(): string
    {
        return <<<'CLI'
            'osascript' '-e' 'display notification "I'\''m the notification body" subtitle "I'\''m the notification subtitle"'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithASound(): string
    {
        return <<<'CLI'
            'osascript' '-e' 'display notification "I'\''m the notification body" sound name "Frog"'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAnIcon(): string
    {
        return <<<'CLI'
            'osascript' '-e' 'display notification "I'\''m the notification body"'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAllOptions(): string
    {
        return <<<'CLI'
            'osascript' '-e' 'display notification "I'\''m the notification body" with title "I'\''m the notification title" subtitle "I'\''m the notification subtitle" sound name "Frog"'
            CLI;
    }
}
