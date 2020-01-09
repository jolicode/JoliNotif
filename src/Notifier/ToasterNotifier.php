<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\Notifier;

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\Util\OsHelper;

/**
 * This notifier can be used on Windows Eight and higher and provides its own
 * binaries if not natively available.
 */
class ToasterNotifier extends CliBasedNotifier implements BinaryProvider
{
    /**
     * {@inheritdoc}
     */
    public function getBinary(): string
    {
        return 'toast';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return static::PRIORITY_MEDIUM;
    }

    /**
     * {@inheritdoc}
     */
    public function canBeUsed(): bool
    {
        return
            (OsHelper::isWindows() && OsHelper::isWindowsEightOrHigher())
            ||
            OsHelper::isWindowsSubsystemForLinux()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootDir(): string
    {
        return \dirname(\dirname(__DIR__)).'/bin/toaster';
    }

    /**
     * {@inheritdoc}
     */
    public function getEmbeddedBinary(): string
    {
        return 'toast.exe';
    }

    /**
     * {@inheritdoc}
     */
    public function getExtraFiles(): array
    {
        return [
            'Microsoft.WindowsAPICodePack.dll',
            'Microsoft.WindowsAPICodePack.Shell.dll',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommandLineArguments(Notification $notification): array
    {
        $arguments = [
            '-m',
            $notification->getBody(),
        ];

        if ($notification->getTitle()) {
            $arguments[] = '-t';
            $arguments[] = $notification->getTitle();
        }

        if ($notification->getIcon()) {
            $arguments[] = '-p';
            $arguments[] = $notification->getIcon();
        }

        return $arguments;
    }
}
