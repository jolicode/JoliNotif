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

use JoliNotif\Notifier\AppleScriptNotifier;
use JoliNotif\Notifier\GrowlNotifyNotifier;
use JoliNotif\Notifier\NotifuNotifier;
use JoliNotif\Notifier\NotifySendNotifier;
use JoliNotif\Notifier\TerminalNotifierNotifier;
use JoliNotif\Notifier\ToasterNotifier;
use JoliNotif\Util\OsHelper;

class NotifierFactory
{
    /**
     * @param Notifier[] $notifiers
     *
     * @return Notifier
     */
    public static function create(array $notifiers = [])
    {
        if (empty($notifiers)) {
            $notifiers = static::getDefaultNotifiers();
        }

        $bestNotifier = self::chooseBestNotifier($notifiers);

        return $bestNotifier;
    }

    /**
     * @return Notifier[]
     */
    public static function getDefaultNotifiers()
    {
        // Don't retrieve notifiers which are certainly not supported on this
        // system. This helps to lower the number of process to run.
        if (OsHelper::isUnix()) {
            $notifiers = self::getUnixNotifiers();
        } else {
            $notifiers = self::getWindowsNotifiers();
        }

        return $notifiers;
    }

    /**
     * @return Notifier[]
     */
    private static function getUnixNotifiers()
    {
        return [
            new GrowlNotifyNotifier(),
            new TerminalNotifierNotifier(),
            new AppleScriptNotifier(),
            new NotifySendNotifier(),
        ];
    }

    /**
     * @return Notifier[]
     */
    private static function getWindowsNotifiers()
    {
        return [
            new ToasterNotifier(),
            new NotifuNotifier(),
        ];
    }

    /**
     * @param Notifier[] $notifiers
     *
     * @return Notifier|null
     */
    private static function chooseBestNotifier($notifiers)
    {
        /** @var Notifier|null $bestNotifier */
        $bestNotifier = null;

        foreach ($notifiers as $notifier) {
            if (!$notifier->isSupported()) {
                continue;
            }

            if (null !== $bestNotifier && $bestNotifier->getPriority() >= $notifier->getPriority()) {
                continue;
            }

            $bestNotifier = $notifier;
        }

        return $bestNotifier;
    }
}
