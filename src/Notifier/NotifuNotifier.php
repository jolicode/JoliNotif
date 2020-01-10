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
 * This notifier can be used on Windows Seven and provides its own binaries if
 * not natively available.
 */
class NotifuNotifier extends CliBasedNotifier implements BinaryProvider
{
    /**
     * {@inheritdoc}
     */
    public function getBinary(): string
    {
        return 'notifu';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return static::PRIORITY_LOW;
    }

    /**
     * {@inheritdoc}
     */
    public function canBeUsed(): bool
    {
        return OsHelper::isWindows() && OsHelper::isWindowsSeven();
    }

    /**
     * {@inheritdoc}
     */
    public function getRootDir(): string
    {
        return \dirname(\dirname(__DIR__)).'/bin/notifu';
    }

    /**
     * {@inheritdoc}
     */
    public function getEmbeddedBinary(): string
    {
        return 'notifu.exe';
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
            '/m',
            $notification->getBody(),
        ];

        if ($notification->getTitle()) {
            $arguments[] = '/p';
            $arguments[] = $notification->getTitle();
        }

        if ($notification->getIcon()) {
            $arguments[] = '/i';
            $arguments[] = $notification->getIcon();
        }

        return $arguments;
    }
}
