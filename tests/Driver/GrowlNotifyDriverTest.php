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
use JoliNotif\Driver\GrowlNotifyDriver;
use JoliNotif\Notification;

class GrowlNotifyDriverTest extends DriverTestCase
{
    const BINARY = 'growlnotify';

    use CliBasedDriverTestTrait;
    use UnixBasedDriverTestTrait;

    protected function getDriver()
    {
        return new GrowlNotifyDriver();
    }

    public function testGetBinary()
    {
        $driver = $this->getDriver();

        $this->assertSame(self::BINARY, $driver->getBinary());
    }

    public function testGetPriority()
    {
        $driver = $this->getDriver();

        $this->assertSame(Driver::PRIORITY_LOW, $driver->getPriority());
    }

    /**
     * @param Notification $notification
     * @param array        $expectedArguments
     *
     * @dataProvider provideNotifications
     */
    public function testGetProcessArguments(Notification $notification, array $expectedArguments)
    {
        try {
            $arguments = $this->invokeMethod($this->getDriver(), 'getProcessArguments', [$notification]);
            $this->assertInternalType('array', $arguments);
            $this->assertEquals($expectedArguments, $arguments);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public static function provideNotifications()
    {
        return [
            [
                (new Notification())
                    ->setBody('The notification body'),
                [
                    self::BINARY,
                    '--message', 'The notification body',
                ],
            ],
            [
                (new Notification())
                    ->setBody('The notification body')
                    ->setTitle('The notification title'),
                [
                    self::BINARY,
                    '--message', 'The notification body',
                    '--title', 'The notification title',
                ],
            ],
            [
                (new Notification())
                    ->setBody('The notification body')
                    ->setIcon('/home/toto/Images/my-icon.png'),
                [
                    self::BINARY,
                    '--message', 'The notification body',
                    '--image', '/home/toto/Images/my-icon.png',
                ],
            ],
            [
                (new Notification())
                    ->setBody('The notification body')
                    ->setTitle('The notification title')
                    ->setIcon('/home/toto/Images/my-icon.png'),
                [
                    self::BINARY,
                    '--message', 'The notification body',
                    '--title', 'The notification title',
                    '--image', '/home/toto/Images/my-icon.png',
                ],
            ],
        ];
    }
}
