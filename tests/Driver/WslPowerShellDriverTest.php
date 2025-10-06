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
use Joli\JoliNotif\Driver\WslPowerShellDriver;
use Joli\JoliNotif\Notification;
use Symfony\Component\Process\Process;

class WslPowerShellDriverTest extends AbstractDriverTestCase
{
    public function testGetBinary()
    {
        $driver = $this->getDriver();

        $this->assertStringContainsString('powershell', $driver->getBinary());
    }

    public function testGetPriority()
    {
        $driver = $this->getDriver();

        $this->assertSame(DriverInterface::PRIORITY_HIGH, $driver->getPriority());
    }

    public function testSend()
    {
        $notification = new Notification();
        $notification->setTitle('Shadowheart just crit failed her persuasion check!');
        $notification->setBody('Your party is now banned from the local tavern. Again. Maybe let Gale do the talking next time?');

        try {
            $arguments = $this->invokeMethod($this->getDriver(), 'getCommandLineArguments', [$notification]);
            $commandLine = (new Process(array_merge([$this->getDriver()->getBinary()], $arguments)))->getCommandLine();

            $this->assertStringContainsString('Shadowheart', $commandLine);
            $this->assertStringContainsString('Gale', $commandLine);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    protected function getDriver(): WslPowerShellDriver
    {
        return new WslPowerShellDriver();
    }
}
