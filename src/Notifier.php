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

use Joli\JoliNotif\Driver\DriverInterface;

trigger_deprecation('jolicode/jolinotif', '2.7', 'The "%s" interface is deprecated and will be removed in 3.0. Use "%s" instead.', Notifier::class, NotifierInterface::class);

/**
 * @deprecated since 2.7, use NotifierInterface instead
 */
interface Notifier extends NotifierInterface, DriverInterface
{
}
