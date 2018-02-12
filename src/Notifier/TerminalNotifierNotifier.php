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
 * This notifier can be used on Mac OS X 10.8, or higher, using the
 * terminal-notifier binary.
 */
class TerminalNotifierNotifier extends CliBasedNotifier
{
    /**
     * {@inheritdoc}
     */
    public function getBinary(): string
    {
        return 'terminal-notifier';
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
    protected function getCommandLineArguments(Notification $notification): array
    {
        $arguments = [
            '-message',
            $notification->getBody(),
        ];

        if ($notification->getTitle()) {
            $arguments[] = '-title';
            $arguments[] = $notification->getTitle();
        }

        if ($notification->getIcon() && version_compare(OsHelper::getMacOSVersion(), '10.9.0', '>=')) {
            $arguments[] = '-appIcon';
            $arguments[] = $notification->getIcon();
        }

        if ($notification->getOption('url')) {
            $arguments[] = '-open';
            $arguments[] = $notification->getOption('url');
        }

        return $arguments;
    }
}
