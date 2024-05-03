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

use Joli\JoliNotif\Driver\KDialogDriver;
use Joli\JoliNotif\Notifier;

trigger_deprecation('jolicode/jolinotif', '2.7', 'The "%s" class is deprecated and will be removed in 3.0.', KDialogNotifier::class);

/**
 * This notifier can be used on Linux distributions running KDE, using the command kdialog.
 * This command is shipped by default with KDE.
 *
 * @deprecated since 2.7, will be removed in 3.0
 */
class KDialogNotifier extends KDialogDriver implements Notifier
{
}
