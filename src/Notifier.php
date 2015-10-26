<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Joli\JoliNotif;

interface Notifier
{
    const PRIORITY_LOW = 0;
    const PRIORITY_MEDIUM = 50;
    const PRIORITY_HIGH = 100;

    /**
     * This method is called to check whether the notifier can be used on the
     * current system or not.
     *
     * @return bool
     */
    public function isSupported();

    /**
     * The supported notifier with the higher priority will be preferred.
     *
     * @return int
     */
    public function getPriority();

    /**
     * Send the given notification.
     *
     * @param Notification $notification
     *
     * @throws Exception\InvalidNotificationException if the notification is invalid
     *
     * @return bool
     */
    public function send(Notification $notification);
}
