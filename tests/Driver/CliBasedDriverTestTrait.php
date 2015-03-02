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
use Symfony\Component\Process\ProcessBuilder;

/**
 * The class using this trait should define a BINARY constant and extend DriverTestCase.
 */
trait CliBasedDriverTestTrait
{
    /**
     * @param Notification $notification
     * @param string       $expectedCommandLine
     *
     * @dataProvider provideValidNotifications
     */
    public function testConfigureProcessAcceptAnyValidNotification(Notification $notification, $expectedCommandLine)
    {
        try {
            $processBuilder = new ProcessBuilder();
            $processBuilder->setPrefix(self::BINARY);
            $this->invokeMethod($this->getDriver(), 'configureProcess', [$processBuilder, $notification]);

            $this->assertEquals($expectedCommandLine, $processBuilder->getProcess()->getCommandLine());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @return string
     */
    abstract protected function getExpectedCommandLineForNotification();

    /**
     * @return string
     */
    abstract protected function getExpectedCommandLineForNotificationWithATitle();

    /**
     * @return string
     */
    abstract protected function getExpectedCommandLineForNotificationWithAnIcon();

    /**
     * @return string
     */
    abstract protected function getExpectedCommandLineForNotificationWithAllOptions();

    /**
     * @return array
     */
    public function provideValidNotifications()
    {
        return [
            [
                (new Notification())
                    ->setBody('I\'m the notification body'),
                $this->getExpectedCommandLineForNotification(),
            ],
            [
                (new Notification())
                    ->setBody('I\'m the notification body')
                    ->setTitle('I\'m the notification title'),
                $this->getExpectedCommandLineForNotificationWithATitle(),
            ],
            [
                (new Notification())
                    ->setBody('I\'m the notification body')
                    ->setIcon('/home/toto/Images/my-icon.png'),
                $this->getExpectedCommandLineForNotificationWithAnIcon(),
            ],
            [
                (new Notification())
                    ->setBody('I\'m the notification body')
                    ->setTitle('I\'m the notification title')
                    ->setIcon('/home/toto/Images/my-icon.png'),
                $this->getExpectedCommandLineForNotificationWithAllOptions(),
            ],
        ];
    }
}
