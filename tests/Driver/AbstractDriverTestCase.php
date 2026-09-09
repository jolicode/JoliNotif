<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\tests\Driver;

use Joli\JoliNotif\Driver\DriverInterface;
use Joli\JoliNotif\Notification;
use PHPUnit\Framework\TestCase;

abstract class AbstractDriverTestCase extends TestCase
{
    public static function getIconDir(): string
    {
        $iconDir = sys_get_temp_dir() . '/jolinotif-tests-fixtures';

        if (!is_dir($iconDir)) {
            mkdir($iconDir);
            copy(\dirname(__DIR__) . '/fixtures/image.gif', $iconDir . '/image.gif');
        }

        // Resolve symlinks (e.g. /var -> /private/var on macOS) to match Notification::setIcon()
        return (string) realpath($iconDir);
    }

    abstract protected function getDriver(): DriverInterface;

    /**
     * @return list<string|int>
     */
    protected function getCommandLineArguments(DriverInterface $driver, Notification $notification): array
    {
        $method = new \ReflectionMethod($driver, 'getCommandLineArguments');

        $arguments = $method->invoke($driver, $notification);
        $this->assertIsList($arguments);

        return array_map(static fn (mixed $argument): string|int => match (true) {
            \is_int($argument), \is_string($argument) => $argument,
            default => self::fail('Command line arguments must be strings or integers'),
        }, $arguments);
    }
}
