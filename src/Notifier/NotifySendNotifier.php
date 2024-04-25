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

use Joli\JoliNotif\Driver\NotifySendDriver;
use Joli\JoliNotif\Notifier;

trigger_deprecation('jolicode/jolinotif', '2.7', 'The "%s" class is deprecated and will be removed in 3.0.', NotifySendNotifier::class);

/**
 * This notifier can be used on most Linux distributions, using the command notify-send.
 * This command is packaged in libnotify-bin.
 *
 * @deprecated since 2.7, will be removed in 3.0
 */
class NotifySendNotifier extends NotifySendDriver implements Notifier
{
}
