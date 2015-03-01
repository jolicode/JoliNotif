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
 * This driver can be used on Mac OS X when growlnotify command is available.
 */
class GrowlNotifyDriver extends UnixBasedDriver
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
        return static::PRIORITY_LOW;
    }

    /**
     * {@inheritdoc}
     */
    protected function getProcessArguments(Notification $notification)
    {
        $arguments = [
            $this->getBinary(),
            '--message',
            $notification->getBody(),
        ];

        if ($notification->getTitle()) {
            $arguments[] = '--title';
            $arguments[] = $notification->getTitle();
        }

        // Require 10.9+
        if (0 < strlen($notification->getIcon())) {
            $arguments[] = '--image';
            $arguments[] = $notification->getIcon();
        }

        return $arguments;
    }
}
