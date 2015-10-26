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
use Symfony\Component\Process\ProcessBuilder;

/**
 * This notifier can be used on most Linux distributions, using the command notify-send.
 * This command is packaged in libnotify-bin.
 */
class NotifySendNotifier extends CliBasedNotifier
{
    /**
     * {@inheritdoc}
     */
    public function getBinary()
    {
        return 'notify-send';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return static::PRIORITY_MEDIUM;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureProcess(ProcessBuilder $processBuilder, Notification $notification)
    {
        if ($notification->getIcon()) {
            $processBuilder->add('--icon');
            $processBuilder->add($notification->getIcon());
        }

        if ($notification->getTitle()) {
            $processBuilder->add($notification->getTitle());
        }

        $processBuilder->add($notification->getBody());
    }
}
