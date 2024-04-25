<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\Notifier;

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\Notifier;

trigger_deprecation('jolicode/jolinotif', '2.7', 'The "%s" class is deprecated and will be removed in 3.0.', NullNotifier::class);

/**
 * @deprecated since 2.7, will be removed in 3.0
 */
class NullNotifier implements Notifier
{
    public function isSupported(): bool
    {
        return true;
    }

    public function getPriority(): int
    {
        return static::PRIORITY_LOW;
    }

    public function send(Notification $notification): bool
    {
        return false;
    }
}
