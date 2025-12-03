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
use Joli\JoliNotif\Notification;
use JoliCode\PhpOsHelper\OsHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Process\Process;

/**
 * Classes using this trait should define a BINARY constant and extend
 * AbstractDriverTestCase.
 */
trait AbstractCliBasedDriverTestTrait
{
    public function testIsSupported()
    {
        if (OsHelper::isUnix()) {
            $commandLine = 'command -v ' . static::BINARY . ' >/dev/null 2>&1';
        } else {
            $commandLine = 'where ' . static::BINARY;
        }

        passthru($commandLine, $return);
        $supported = 0 === $return;

        $this->assertSame($supported, $this->getDriver()->isSupported());
    }

    #[DataProvider('provideValidNotifications')]
    public function testConfigureProcessAcceptAnyValidNotification(Notification $notification, string $expectedCommandLine)
    {
        try {
            $arguments = $this->invokeMethod($this->getDriver(), 'getCommandLineArguments', [$notification]);

            $this->assertSame($expectedCommandLine, (new Process(array_merge([self::BINARY], $arguments)))->getCommandLine());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public static function provideValidNotifications(): iterable
    {
        $iconDir = self::getIconDir();

        yield self::BINARY . '_getExpectedCommandLineForNotification' => [
            (new Notification())
                ->setBody('I\'m the notification body'),
            self::getExpectedCommandLineForNotification(),
        ];
        yield self::BINARY . '_getExpectedCommandLineForNotificationWithATitle' => [
            (new Notification())
                ->setBody('I\'m the notification body')
                ->setTitle('I\'m the notification title'),
            self::getExpectedCommandLineForNotificationWithATitle(),
        ];
        yield self::BINARY . '_getExpectedCommandLineForNotificationWithASubtitle' => [
            (new Notification())
                ->setBody('I\'m the notification body')
                ->addOption('subtitle', 'I\'m the notification subtitle'),
            self::getExpectedCommandLineForNotificationWithASubtitle(),
        ];
        yield self::BINARY . '_getExpectedCommandLineForNotificationWithASound' => [
            (new Notification())
                ->setBody('I\'m the notification body')
                ->addOption('sound', 'Frog'),
            self::getExpectedCommandLineForNotificationWithASound(),
        ];
        yield self::BINARY . '_getExpectedCommandLineForNotificationWithAnUrl' => [
            (new Notification())
                ->setBody('I\'m the notification body')
                ->addOption('url', 'https://google.com'),
            self::getExpectedCommandLineForNotificationWithAnUrl(),
        ];
        yield self::BINARY . '_getExpectedCommandLineForNotificationWithAnIcon' => [
            (new Notification())
                ->setBody('I\'m the notification body')
                ->setIcon($iconDir . '/image.gif'),
            self::getExpectedCommandLineForNotificationWithAnIcon(),
        ];
        yield self::BINARY . '_getExpectedCommandLineForNotificationWithAllOptions' => [
            (new Notification())
                ->setBody('I\'m the notification body')
                ->setTitle('I\'m the notification title')
                ->addOption('subtitle', 'I\'m the notification subtitle')
                ->addOption('sound', 'Frog')
                ->addOption('url', 'https://google.com')
                ->setIcon($iconDir . '/image.gif'),
            self::getExpectedCommandLineForNotificationWithAllOptions(),
        ];
    }

    public function testSendThrowsExceptionWhenNotificationDoesntHaveBody()
    {
        $driver = $this->getDriver();

        $notification = new Notification();

        try {
            $driver->send($notification);
            $this->fail('Expected a InvalidNotificationException');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Joli\JoliNotif\Exception\InvalidNotificationException', $e);
        }
    }

    public function testSendThrowsExceptionWhenNotificationHasAnEmptyBody()
    {
        $driver = $this->getDriver();

        $notification = new Notification();
        $notification->setBody('');

        try {
            $driver->send($notification);
            $this->fail('Expected a InvalidNotificationException');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Joli\JoliNotif\Exception\InvalidNotificationException', $e);
        }
    }

    abstract protected function getDriver(): DriverInterface;

    abstract protected static function getExpectedCommandLineForNotification(): string;

    abstract protected static function getExpectedCommandLineForNotificationWithATitle(): string;

    /**
     * Subtitle is supported only on few driver.
     */
    protected static function getExpectedCommandLineForNotificationWithASubtitle(): string
    {
        return static::getExpectedCommandLineForNotification();
    }

    /**
     * Sound is supported only on few driver.
     */
    protected static function getExpectedCommandLineForNotificationWithASound(): string
    {
        return static::getExpectedCommandLineForNotification();
    }

    /**
     * Sound is supported only on few driver.
     */
    protected static function getExpectedCommandLineForNotificationWithAnUrl(): string
    {
        return static::getExpectedCommandLineForNotification();
    }

    abstract protected static function getExpectedCommandLineForNotificationWithAnIcon(): string;

    abstract protected static function getExpectedCommandLineForNotificationWithAllOptions(): string;
}
