<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\tests\Notifier;

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\Util\OsHelper;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Classes using this trait should define a BINARY constant and extend
 * NotifierTestCase.
 */
trait CliBasedNotifierTestTrait
{
    public function testIsSupported()
    {
        if (OsHelper::isUnix()) {
            $commandLine = 'command -v '.static::BINARY.' >/dev/null 2>&1';
        } else {
            $commandLine = 'where '.static::BINARY;
        }

        passthru($commandLine, $return);
        $supported = 0 === $return;

        $this->assertSame($supported, $this->getNotifier()->isSupported());
    }

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

            $this->invokeMethod($this->getNotifier(), 'configureProcess', [$processBuilder, $notification]);

            $this->assertSame($expectedCommandLine, $processBuilder->getProcess()->getCommandLine());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function provideValidNotifications()
    {
        $iconDir = $this->getIconDir();

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
                    ->setTitle('I\'m the notification title')
                    ->addOption('subtitle', 'I\'m the notification subtitle'),
                $this->getExpectedCommandLineForNotificationWithASubtitle(),
            ],
            [
                (new Notification())
                    ->setBody('I\'m the notification body')
                    ->setTitle('I\'m the notification title')
                    ->addOption('sound', 'Frog'),
                $this->getExpectedCommandLineForNotificationWithASound(),
            ],
            [
                (new Notification())
                    ->setBody('I\'m the notification body')
                    ->setIcon($iconDir.'/image.gif'),
                $this->getExpectedCommandLineForNotificationWithAnIcon(),
            ],
            [
                (new Notification())
                    ->setBody('I\'m the notification body')
                    ->setTitle('I\'m the notification title')
                    ->addOption('subtitle', 'I\'m the notification subtitle')
                    ->addOption('sound', 'Frog')
                    ->setIcon($iconDir.'/image.gif'),
                $this->getExpectedCommandLineForNotificationWithAllOptions(),
            ],
        ];
    }

    public function testSendThrowsExceptionWhenNotificationDoesntHaveBody()
    {
        $notifier = $this->getNotifier();

        $notification = new Notification();

        try {
            $notifier->send($notification);
            $this->fail('Expected a InvalidNotificationException');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Joli\JoliNotif\Exception\InvalidNotificationException', $e);
        }
    }

    public function testSendThrowsExceptionWhenNotificationHasAnEmptyBody()
    {
        $notifier = $this->getNotifier();

        $notification = new Notification();
        $notification->setBody('');

        try {
            $notifier->send($notification);
            $this->fail('Expected a InvalidNotificationException');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Joli\JoliNotif\Exception\InvalidNotificationException', $e);
        }
    }

    public function getIconDir()
    {
        return realpath(dirname(__DIR__).'/fixtures');
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
     * Subtitle is supported only on few notifier.
     *
     * @return string
     */
    protected function getExpectedCommandLineForNotificationWithASubtitle()
    {
        return $this->getExpectedCommandLineForNotificationWithATitle();
    }

    /**
     * Sound is supported only on few notifier.
     *
     * @return string
     */
    protected function getExpectedCommandLineForNotificationWithASound()
    {
        return $this->getExpectedCommandLineForNotificationWithATitle();
    }

    /**
     * @return string
     */
    abstract protected function getExpectedCommandLineForNotificationWithAnIcon();

    /**
     * @return string
     */
    abstract protected function getExpectedCommandLineForNotificationWithAllOptions();
}
