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

use JoliNotif\Driver\AppleScriptDriver;
use JoliNotif\Driver\Driver;
use JoliNotif\Driver\GrowlNotifyDriver;
use JoliNotif\Driver\NotifuDriver;
use JoliNotif\Driver\NotifySendDriver;
use JoliNotif\Driver\TerminalNotifierDriver;
use JoliNotif\Driver\ToasterDriver;
use JoliNotif\Util\OsHelper;

class NotifierFactory
{
    /**
     * @return Notifier
     */
    public static function make()
    {
        if (OsHelper::isUnix()) {
            $drivers = self::makeUnixDrivers();
        } else {
            $drivers = self::makeWindowsDrivers();
        }

        return new Notifier($drivers);
    }

    /**
     * @return Driver[]
     */
    private static function makeUnixDrivers()
    {
        return [
            new GrowlNotifyDriver(),
            new TerminalNotifierDriver(),
            new AppleScriptDriver(),
            new NotifySendDriver(),
        ];
    }

    /**
     * @return Driver[]
     */
    private static function makeWindowsDrivers()
    {
        return [
            new ToasterDriver(),
            new NotifuDriver(),
        ];
    }
}
