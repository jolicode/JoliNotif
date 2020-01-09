<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\tests\Util;

use Joli\JoliNotif\Util\OsHelper;
use PHPUnit\Framework\TestCase;

class OsHelperTest extends TestCase
{
    public function testIsUnix()
    {
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $this->assertFalse(OsHelper::isUnix());
        }

        if ('/' === \DIRECTORY_SEPARATOR) {
            $this->assertTrue(OsHelper::isUnix());
        }
    }

    public function testIsWindows()
    {
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $this->assertTrue(OsHelper::isWindows());
        }

        if ('/' === \DIRECTORY_SEPARATOR) {
            $this->assertFalse(OsHelper::isWindows());
        }
    }

    public function testIsWindowsSeven()
    {
        if (!OsHelper::isWindows()) {
            $this->markTestSkipped('Can only be run on Windows');
        }

        $isSeven = '6.1' === php_uname('r');

        $this->assertSame($isSeven, OsHelper::isWindowsSeven());
    }

    public function testIsWindowsEightOrHigher()
    {
        if (!OsHelper::isWindows()) {
            $this->markTestSkipped('Can only be run on Windows');
        }

        $eightOrHigher = [
            '6.2', // 8
            '6.3', // 8.1
            '6.4', // 10
        ];
        $isEightOrHigher = \in_array(php_uname('r'), $eightOrHigher, true);

        $this->assertSame($isEightOrHigher, OsHelper::isWindowsEightOrHigher());
    }

    public function testIsMacOS()
    {
        $uname = php_uname();
        $isDarwin = 'Darwin' === substr($uname, 0, 6);

        $this->assertSame($isDarwin, OsHelper::isMacOS());
    }

    public function testGetMacOSVersion()
    {
        if (!OsHelper::isMacOS()) {
            $this->markTestSkipped('Can only be run on MacOS');
        }

        $expectedMacOsVersion = exec('sw_vers -productVersion', $output);

        $macOsVersion = OsHelper::getMacOSVersion();

        $this->assertRegExp('#\d{1,2}\.\d{1,2}(\.\d{1,2})?#', $macOsVersion);
        $this->assertSame($expectedMacOsVersion, $macOsVersion);
    }
}
