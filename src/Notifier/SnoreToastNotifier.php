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
use Symfony\Component\Process\Process;

/**
 * This notifier can be used on Windows Eight and higher and provides its own
 * binaries if not natively available.
 */
class SnoreToastNotifier extends CliBasedNotifier implements BinaryProvider
{
    /**
     * {@inheritdoc}
     */
    public function getBinary(): string
    {
        return 'snoretoast';
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
        return \dirname(__DIR__, 2).'/bin/snoreToast';
    }

    /**
     * {@inheritdoc}
     */
    public function getEmbeddedBinary(): string
    {
        return 'snoretoast-x86.exe';
    }

    /**
     * {@inheritdoc}
     */
    public function getExtraFiles(): array
    {
        return [];
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

    /**
     * {@inheritdoc}
     */
    protected function handleExitCode(Process $process): bool
    {
        return 0 < $process->getExitCode();
    }
}
