<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JoliNotif\Driver;

use JoliNotif\Notification;

interface Driver
{
    const PRIORITY_LOW    = 0;
    const PRIORITY_MEDIUM = 50;
    const PRIORITY_HIGH   = 100;

    /**
     * This method is called to check whether the driver can be used on the current system or not.
     *
     * @return bool
     */
    public function isSupported();

    /**
     * The supported driver with the higher priority will be preferred.
     *
     * @return int
     */
    public function getPriority();

    /**
     * Send a the given notification.
     *
     * @param Notification $notification
     *
     * @return bool
     */
    public function send(Notification $notification);
}
