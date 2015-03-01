<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JoliNotif\tests\Driver;

/**
 * The class using this trait should define a BINARY constant and extend DriverTestCase.
 */
trait UnixBasedDriverTestTrait
{
    public function testIsSupported()
    {
        $supported = DIRECTORY_SEPARATOR === '/';

        if ($supported) {
            $commandLine = 'command -v '.static::BINARY.' >/dev/null 2>&1 ';
            passthru($commandLine, $return);

            $supported = $return === 0;
        }

        $this->assertSame($supported, $this->getDriver()->isSupported());
    }
}
