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
use Joli\JoliNotif\Driver\KDialogDriver;
use Psr\Log\NullLogger;

class KDialogDriverTest extends AbstractDriverTestCase
{
    use AbstractCliBasedDriverTestTrait;

    private const BINARY = 'kdialog';

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

    protected function getDriver(): KDialogDriver
    {
        return new KDialogDriver(new NullLogger());
    }

    protected static function getExpectedCommandLineForNotification(): string
    {
        return <<<'CLI'
            'kdialog' '--passivepopup' 'I'\''m the notification body' '5'
            CLI;
    }

    protected static function getExpectedCommandLineForNotificationWithATitle(): string
    {
        return <<<'CLI'
            'kdialog' '--title' 'I'\''m the notification title' '--passivepopup' 'I'\''m the notification body' '5'
            CLI;
    }

    protected static function getExpectedCommandLineForNotificationWithAnIcon(): string
    {
        return <<<'CLI'
            'kdialog' '--passivepopup' 'I'\''m the notification body' '5'
            CLI;
    }

    protected static function getExpectedCommandLineForNotificationWithAllOptions(): string
    {
        return <<<'CLI'
            'kdialog' '--title' 'I'\''m the notification title' '--passivepopup' 'I'\''m the notification body' '5'
            CLI;
    }
}
