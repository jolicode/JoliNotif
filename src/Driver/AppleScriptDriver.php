<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\Driver;

use Joli\JoliNotif\Notification;
use JoliCode\PhpOsHelper\OsHelper;

/**
 * This driver can be used on macOS.
 *
 * @internal
 */
class AppleScriptDriver extends AbstractCliBasedDriver
{
    public function isSupported(): bool
    {
        return OsHelper::isMacOS() && parent::isSupported();
    }

    public function getBinary(): string
    {
        return 'osascript';
    }

    public function getPriority(): int
    {
        return static::PRIORITY_LOW;
    }

    protected function getCommandLineArguments(Notification $notification): array
    {
        $script = 'display notification ' . self::quote($notification->getBody() ?? '');

        if ($notification->getTitle()) {
            $script .= ' with title ' . self::quote($notification->getTitle());
        }

        if ($notification->getOption('subtitle')) {
            $script .= ' subtitle ' . self::quote((string) $notification->getOption('subtitle'));
        }

        if ($notification->getOption('sound')) {
            $script .= ' sound name ' . self::quote((string) $notification->getOption('sound'));
        }

        return [
            '-e',
            $script,
        ];
    }

    /**
     * Escape both backslashes and double quotes, otherwise a crafted value
     * could break out of the string and inject arbitrary AppleScript.
     */
    private static function quote(string $value): string
    {
        return '"' . addcslashes($value, '"\\') . '"';
    }
}
