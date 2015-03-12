<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JoliNotif;

use JoliNotif\Driver\Driver;
use JoliNotif\Exception\InvalidNotificationException;
use JoliNotif\Exception\SystemNotSupportedException;
use JoliNotif\Util\PharExtractor;

class Notifier
{
    const STATUS_SENT         = 1;
    const STATUS_ERROR_DRIVER = 2;
    const STATUS_NO_DRIVER    = 3;

    /**
     * @var Driver[]
     */
    private $drivers = [];

    /**
     * @var Driver
     */
    private $driverInUse;

    /**
     * @param Driver[] $drivers
     *
     * @throw SystemNotSupportedException if no drivers are supported
     */
    public function __construct(array $drivers)
    {
        $this->drivers = $drivers;

        $this->driverInUse = $this->chooseBestDriver();
    }

    /**
     * @return Driver
     */
    public function getDriverInUse()
    {
        return $this->driverInUse;
    }

    /**
     * @param Notification $notification
     *
     * @return int
     */
    public function send(Notification $notification)
    {
        if (!$this->driverInUse) {
            return self::STATUS_NO_DRIVER;
        }

        if (!$notification->getBody()) {
            throw new InvalidNotificationException($notification, 'Notification body can not be empty');
        }

        // This makes the icon accessible for native commands when it's embedded inside a phar
        if (($icon = $notification->getIcon()) && PharExtractor::isLocatedInsideAPhar($icon)) {
            $notification->setIcon(
                PharExtractor::extractFile($icon)
            );
        }

        return $this->driverInUse->send($notification) ? self::STATUS_SENT : self::STATUS_ERROR_DRIVER;
    }

    /**
     * @return Driver
     *
     * @throw SystemNotSupportedException if no drivers are supported
     */
    private function chooseBestDriver()
    {
        $bestDriver = null;

        foreach ($this->drivers as $driver) {
            if (!$driver->isSupported()) {
                continue;
            }

            if (null !== $bestDriver && $bestDriver->getPriority() >= $driver->getPriority()) {
                continue;
            }

            $bestDriver = $driver;
        }

        return $bestDriver;
    }
}
