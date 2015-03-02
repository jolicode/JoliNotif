<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JoliNotif\Driver;

use JoliNotif\Notification;

/**
 * This driver can be used on most Linux distributions, using the command notify-send.
 * This command is packaged in libnotify-bin.
 */
class NotifySendDriver extends UnixBasedDriver
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
    protected function getProcessArguments(Notification $notification)
    {
        $arguments = [
            $this->getBinary(),
        ];

        if ($notification->getIcon()) {
            $arguments[] = '--icon';
            $arguments[] = $notification->getIcon();
        }

        if ($notification->getTitle()) {
            $arguments[] = $notification->getTitle();
        }

        $arguments[] = $notification->getBody();

        return $arguments;
    }
}
