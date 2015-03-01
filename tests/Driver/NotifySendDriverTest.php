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
use JoliNotif\Driver\NotifySendDriver;
use JoliNotif\Notification;

class NotifySendDriverTest extends DriverTestCase
{
    const BINARY = 'notify-send';

    use CliBasedDriverTestTrait;
    use UnixBasedDriverTestTrait;

    protected function getDriver()
    {
        return new NotifySendDriver();
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
        } catch(\Exception $e) {
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
                    'The notification body',
                ],
            ],
            [
                (new Notification())
                    ->setBody('The notification body')
                    ->setTitle('The notification title'),
                [
                    self::BINARY,
                    'The notification title',
                    'The notification body',
                ],
            ],
            [
                (new Notification())
                    ->setBody('The notification body')
                    ->setIcon('/home/toto/Images/my-icon.png'),
                [
                    self::BINARY,
                    '--icon', '/home/toto/Images/my-icon.png',
                    'The notification body',
                ],
            ],
            [
                (new Notification())
                    ->setBody('The notification body')
                    ->setTitle('The notification title')
                    ->setIcon('/home/toto/Images/my-icon.png'),
                [
                    self::BINARY,
                    '--icon', '/home/toto/Images/my-icon.png',
                    'The notification title',
                    'The notification body'
                ],
            ],
        ];
    }
}
