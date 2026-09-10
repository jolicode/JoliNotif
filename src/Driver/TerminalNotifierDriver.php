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
 * This driver can be used on macOS using the terminal-notifier binary.
 *
 * @internal
 */
class TerminalNotifierDriver extends AbstractCliBasedDriver
{
    public function getBinary(): string
    {
        return 'terminal-notifier';
    }

    public function getPriority(): int
    {
        return static::PRIORITY_MEDIUM;
    }

    protected function getCommandLineArguments(Notification $notification): array
    {
        $arguments = [
            '-message',
            $notification->getBody() ?? '',
        ];

        if ($notification->getTitle()) {
            $arguments[] = '-title';
            $arguments[] = $notification->getTitle();
        }

        if ($notification->getIcon()) {
            $arguments[] = '-contentImage';
            $arguments[] = $notification->getIcon();
        }

        if ($notification->getOption('url')) {
            $arguments[] = '-open';
            $arguments[] = (string) $notification->getOption('url');
        }

        if ($notification->getOption('sound')) {
            $arguments[] = '-sound';
            $arguments[] = (string) $notification->getOption('sound');
        }

        return $arguments;
    }
}
