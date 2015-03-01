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

use JoliNotif\Notification;

/**
 * The class using this trait should extend DriverTestCase.
 */
trait CliBasedDriverTestTrait
{
    /**
     * @param \JoliNotif\Notification $notification
     *
     * @dataProvider provideValidNotifications
     */
    public function testSendAcceptAnyValidNotification(Notification $notification)
    {
        try {
            $arguments = $this->invokeMethod($this->getDriver(), 'getProcessArguments', [$notification]);
            $this->assertInternalType('array', $arguments);
            $this->assertGreaterThan(1, count($arguments));
        } catch(\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public static function provideValidNotifications()
    {
        return [
            [(new Notification())
                ->setBody('The notification body')],
            [(new Notification())
                ->setBody('The notification body')
                ->setTitle('The notification title')],
            [(new Notification())
                ->setBody('The notification body')
                ->setIcon('example/notification-icon.png')],
            [(new Notification())
                ->setBody('The notification body')
                ->setTitle('The notification title')
                ->setIcon('example/notification-icon.png')],
        ];
    }
}
