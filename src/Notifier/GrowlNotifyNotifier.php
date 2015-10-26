<?php

/**
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
 * This notifier can be used on Mac OS X when growlnotify command is available.
 */
class GrowlNotifyNotifier extends CliBasedNotifier
{
    /**
     * {@inheritdoc}
     */
    public function getBinary()
    {
        return 'growlnotify';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return static::PRIORITY_HIGH;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureProcess(ProcessBuilder $processBuilder, Notification $notification)
    {
        $processBuilder->add('--message');
        $processBuilder->add($notification->getBody());

        if ($notification->getTitle()) {
            $processBuilder->add('--title');
            $processBuilder->add($notification->getTitle());
        }

        if ($notification->getIcon()) {
            $processBuilder->add('--image');
            $processBuilder->add($notification->getIcon());
        }
    }
}
