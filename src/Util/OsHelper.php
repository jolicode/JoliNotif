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

use Symfony\Component\Process\Process;

class OsHelper
{
    private static string $kernelName;
    private static string $kernelVersion;
    private static string $macOSVersion;

    public static function isUnix(): bool
    {
        return '/' === \DIRECTORY_SEPARATOR;
    }

    public static function isWindowsSubsystemForLinux(): bool
    {
        return self::isUnix() && false !== mb_strpos(strtolower(php_uname()), 'microsoft');
    }

    public static function isWindows(): bool
    {
        return '\\' === \DIRECTORY_SEPARATOR;
    }

    public static function isWindowsSeven(): bool
    {
        if (!isset(self::$kernelVersion)) {
            self::$kernelVersion = php_uname('r');
        }

        return '6.1' === self::$kernelVersion;
    }

    public static function isWindowsEightOrHigher(): bool
    {
        if (!isset(self::$kernelVersion)) {
            self::$kernelVersion = php_uname('r');
        }

        return version_compare(self::$kernelVersion, '6.2', '>=');
    }

    public static function isMacOS(): bool
    {
        if (!isset(self::$kernelName)) {
            self::$kernelName = php_uname('s');
        }

        return false !== strpos(self::$kernelName, 'Darwin');
    }

    public static function getMacOSVersion(): string
    {
        if (!isset(self::$macOSVersion)) {
            $process = new Process(['sw_vers', '-productVersion']);
            $process->run();
            self::$macOSVersion = trim($process->getOutput());
        }

        return self::$macOSVersion;
    }
}
