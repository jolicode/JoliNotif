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

use Joli\JoliNotif\Driver\SnoreToastDriver;
use Joli\JoliNotif\Notifier;

trigger_deprecation('jolicode/jolinotif', '2.7', 'The "%s" class is deprecated and will be removed in 3.0.', SnoreToastNotifier::class);

/**
 * This notifier can be used on Windows Eight and higher and provides its own
 * binaries if not natively available.
 *
 * @deprecated since 2.7, will be removed in 3.0
 */
class SnoreToastNotifier extends SnoreToastDriver implements Notifier
{
}
