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

/**
 * This driver can be used on Mac OS X when growlnotify command is available.
 *
 * @internal
 */
class GrowlNotifyDriver extends AbstractCliBasedDriver
{
    public function getBinary(): string
    {
        return 'growlnotify';
    }

    public function getPriority(): int
    {
        return static::PRIORITY_HIGH;
    }

    protected function getCommandLineArguments(Notification $notification): array
    {
        $arguments = [
            '--message',
            $notification->getBody() ?? '',
        ];

        if ($notification->getTitle()) {
            $arguments[] = '--title';
            $arguments[] = $notification->getTitle();
        }

        if ($notification->getIcon()) {
            $arguments[] = '--image';
            $arguments[] = $notification->getIcon();
        }

        return $arguments;
    }
}
