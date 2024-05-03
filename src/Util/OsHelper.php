<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\Util;

use JoliCode\PhpOsHelper\OsHelper as BaseOsHelper;

trigger_deprecation('jolicode/jolinotif', '2.6', 'The "%s" class is deprecated and will be removed in 3.0. Use "%s" from jolicode/php-os-helper instead.', OsHelper::class, BaseOsHelper::class);

/**
 * @deprecated since 2.6, use OsHelper from jolicode/php-os-helper instead
 */
class OsHelper extends BaseOsHelper
{
}
