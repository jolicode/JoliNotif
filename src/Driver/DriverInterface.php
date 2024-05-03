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

use Joli\JoliNotif\Exception\ExceptionInterface;
use Joli\JoliNotif\Exception\InvalidNotificationException;
use Joli\JoliNotif\Notification;

/**
 * @internal
 */
interface DriverInterface
{
    public const PRIORITY_LOW = 0;
    public const PRIORITY_MEDIUM = 50;
    public const PRIORITY_HIGH = 100;

    /**
     * This method is called to check whether the driver can be used on the
     * current system or not.
     */
    public function isSupported(): bool;

    /**
     * The supported driver with the higher priority will be preferred.
     */
    public function getPriority(): int;

    /**
     * Send the given notification.
     *
     * @throws InvalidNotificationException if the notification is invalid
     * @throws ExceptionInterface           if something goes wrong when sending the notification
     */
    public function send(Notification $notification): bool;
}
