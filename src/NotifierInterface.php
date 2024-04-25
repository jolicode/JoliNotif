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

interface NotifierInterface
{
    /**
     * Send the given notification.
     *
     * @throws Exception\InvalidNotificationException if the notification is invalid
     */
    public function send(Notification $notification): bool;
}
