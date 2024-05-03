<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\Driver;

use Joli\JoliNotif\Notification;
use JoliCode\PhpOsHelper\OsHelper;

/**
 * This driver can be used on Windows Subsystem for Linux and provides notifications using the `wsl-notify-send` binary.
 *
 * @see https://github.com/stuartleeks/wsl-notify-send the source code of the `wsl-notify-send` binary
 *
 * @internal
 */
class WslNotifySendDriver extends AbstractCliBasedDriver implements BinaryProviderInterface
{
    private const APP_NAME = 'JoliNotif';

    public function getBinary(): string
    {
        return 'wsl-notify-send';
    }

    public function getPriority(): int
    {
        return static::PRIORITY_HIGH;
    }

    public function canBeUsed(): bool
    {
        return OsHelper::isWindowsSubsystemForLinux();
    }

    public function getRootDir(): string
    {
        return \dirname(__DIR__, 2) . '/bin/wsl-notify-send';
    }

    public function getEmbeddedBinary(): string
    {
        return 'wsl-notify-send.exe';
    }

    public function getExtraFiles(): array
    {
        return [];
    }

    protected function getCommandLineArguments(Notification $notification): array
    {
        $arguments = [
            '--appId',
            self::APP_NAME,
            $notification->getBody() ?? '',
        ];

        if ($notification->getTitle()) {
            $arguments[] = '-c';
            $arguments[] = $notification->getTitle();
        }

        return $arguments;
    }
}
