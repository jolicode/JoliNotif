<?php

/*
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
     */
    public function isSupported(): bool;

    /**
     * The supported notifier with the higher priority will be preferred.
     */
    public function getPriority(): int;

    /**
     * Send the given notification.
     *
     * @throws Exception\InvalidNotificationException if the notification is invalid
     */
    public function send(Notification $notification): bool;
}
