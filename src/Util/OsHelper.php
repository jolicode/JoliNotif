<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\Util;

use Symfony\Component\Process\Process;

class OsHelper
{
    /**
     * @var string
     */
    private static $kernelName;

    /**
     * @var string
     */
    private static $kernelVersion;

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
    public static function isWindows()
    {
        return '\\' === DIRECTORY_SEPARATOR;
    }

    /**
     * @return bool
     */
    public static function isWindowsSeven()
    {
        if (null === self::$kernelVersion) {
            self::$kernelVersion = php_uname('r');
        }

        return '6.1' === self::$kernelVersion;
    }

    /**
     * @return bool
     */
    public static function isWindowsEightOrHigher()
    {
        if (null === self::$kernelVersion) {
            self::$kernelVersion = php_uname('r');
        }

        return version_compare(self::$kernelVersion, '6.2', '>=');
    }

    /**
     * @return bool
     */
    public static function isMacOS()
    {
        if (null === self::$kernelName) {
            self::$kernelName = php_uname('s');
        }

        return false !== strpos(self::$kernelName, 'Darwin');
    }

    /**
     * @return string
     */
    public static function getMacOSVersion()
    {
        if (null === self::$macOSVersion) {
            $process = new Process('sw_vers -productVersion');
            $process->run();
            self::$macOSVersion = (string) trim($process->getOutput());
        }

        return self::$macOSVersion;
    }
}
