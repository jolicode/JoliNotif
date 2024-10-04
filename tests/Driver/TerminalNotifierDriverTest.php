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
use Joli\JoliNotif\Driver\TerminalNotifierDriver;
use JoliCode\PhpOsHelper\OsHelper;

class TerminalNotifierDriverTest extends AbstractDriverTestCase
{
    use AbstractCliBasedDriverTestTrait;

    private const BINARY = 'terminal-notifier';

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

    protected function getDriver(): TerminalNotifierDriver
    {
        return new TerminalNotifierDriver();
    }

    protected function getExpectedCommandLineForNotification(): string
    {
        return <<<'CLI'
            'terminal-notifier' '-message' 'I'\''m the notification body'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithATitle(): string
    {
        return <<<'CLI'
            'terminal-notifier' '-message' 'I'\''m the notification body' '-title' 'I'\''m the notification title'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAnUrl(): string
    {
        return <<<'CLI'
            'terminal-notifier' '-message' 'I'\''m the notification body' '-open' 'https://google.com'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithASound(): string
    {
        return <<<'CLI'
            'terminal-notifier' '-message' 'I'\''m the notification body' '-sound' 'Frog'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAnIcon(): string
    {
        if (OsHelper::isMacOS() && version_compare(OsHelper::getMacOSVersion(), '10.9.0', '>=')) {
            $iconDir = $this->getIconDir();

            return <<<CLI
                'terminal-notifier' '-message' 'I'\\''m the notification body' '-contentImage' '{$iconDir}/image.gif'
                CLI;
        }

        return <<<'CLI'
            'terminal-notifier' '-message' 'I'\''m the notification body'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAllOptions(): string
    {
        if (OsHelper::isMacOS() && version_compare(OsHelper::getMacOSVersion(), '10.9.0', '>=')) {
            $iconDir = $this->getIconDir();

            return <<<CLI
                'terminal-notifier' '-message' 'I'\\''m the notification body' '-title' 'I'\\''m the notification title' '-contentImage' '{$iconDir}/image.gif' '-open' 'https://google.com' '-sound' 'Frog'
                CLI;
        }

        return <<<'CLI'
            'terminal-notifier' '-message' 'I'\''m the notification body' '-title' 'I'\''m the notification title' '-open' 'https://google.com' '-sound' 'Frog'
            CLI;
    }
}
