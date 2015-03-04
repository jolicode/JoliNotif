<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JoliNotif\Util;

use Symfony\Component\Process\Process;

class OsHelper
{
    /**
     * @var bool
     */
    private static $isMacOS;

    /**
     * @var string
     */
    private static $macOSVersion;

    /**
     * @return bool
     */
    public static function isUnix()
    {
        return '/' === DIRECTORY_SEPARATOR;
    }

    /**
     * @return bool
     */
    public static function isMacOS()
    {
        if (null === self::$isMacOS) {
            self::$isMacOS = false !== strpos(php_uname('s'), 'Darwin');
        }

        return self::$isMacOS;
    }

    /**
     * @return string
     */
    public static function getMacOSVersion()
    {
        if (null === self::$macOSVersion) {
            $process = new Process('sw_vers -productVersion');
            $process->run();
            self::$macOSVersion = (string)$process->getOutput();
        }

        return self::$macOSVersion;
    }
}
