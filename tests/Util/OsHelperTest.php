<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Util;

use JoliNotif\Util\OsHelper;

class OsHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testIsUnix()
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->assertFalse(OsHelper::isUnix());
        }

        if ('/' === DIRECTORY_SEPARATOR) {
            $this->assertTrue(OsHelper::isUnix());
        }
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

        $this->assertStringMatchesFormat('%d.%d.%d', $macOsVersion);
        $this->assertSame($expectedMacOsVersion, $macOsVersion);
    }
}
