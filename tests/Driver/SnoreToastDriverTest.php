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
use Joli\JoliNotif\Driver\SnoreToastDriver;

class SnoreToastDriverTest extends AbstractDriverTestCase
{
    use AbstractCliBasedDriverTestTrait;
    use BinaryProviderTestTrait;

    private const BINARY = 'snoretoast';

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

    protected function getDriver(): SnoreToastDriver
    {
        return new SnoreToastDriver();
    }

    protected function getExpectedCommandLineForNotification(): string
    {
        return <<<'CLI'
            'snoretoast' '-m' 'I'\''m the notification body'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithATitle(): string
    {
        return <<<'CLI'
            'snoretoast' '-m' 'I'\''m the notification body' '-t' 'I'\''m the notification title'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAnIcon(): string
    {
        $iconDir = $this->getIconDir();

        return <<<CLI
            'snoretoast' '-m' 'I'\\''m the notification body' '-p' '{$iconDir}/image.gif'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAllOptions(): string
    {
        $iconDir = $this->getIconDir();

        return <<<CLI
            'snoretoast' '-m' 'I'\\''m the notification body' '-t' 'I'\\''m the notification title' '-p' '{$iconDir}/image.gif'
            CLI;
    }
}
