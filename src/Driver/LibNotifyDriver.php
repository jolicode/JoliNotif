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

use Joli\JoliNotif\Exception\FFIRuntimeException;
use Joli\JoliNotif\Exception\InvalidNotificationException;
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\Util\PharExtractor;
use JoliCode\PhpOsHelper\OsHelper;

/**
 * This driver can be used on Linux systems when libnotify and FFI are available.
 *
 * @internal
 */
class LibNotifyDriver implements DriverInterface
{
    private const APP_NAME = 'JoliNotif';

    private static \FFI $ffi;

    public function isSupported(): bool
    {
        if ('cli' !== \PHP_SAPI || !class_exists(\FFI::class) || !OsHelper::isUnix() || OsHelper::isMacOS()) {
            return false;
        }

        try {
            self::initialize();
        } catch (\Throwable) {
            return false;
        }

        return true;
    }

    public function getPriority(): int
    {
        return static::PRIORITY_HIGH;
    }

    public function send(Notification $notification): bool
    {
        if (!$notification->getBody()) {
            throw new InvalidNotificationException($notification, 'Notification body can not be empty');
        }

        self::initialize();
        $native = self::$ffi->notify_notification_new(
            $notification->getTitle() ?? '',
            $notification->getBody(),
            $notification->getIcon()
        );
        $value = self::$ffi->notify_notification_show($native, null);
        self::$ffi->g_object_unref($native);

        return $value;
    }

    private static function initialize(): void
    {
        if (isset(self::$ffi)) {
            return;
        }

        $headerFile = __DIR__ . '/FFI/ffi-libnotify.h';

        if (PharExtractor::isLocatedInsideAPhar($headerFile)) {
            $headerFile = PharExtractor::extractFile($headerFile);
        }

        try {
            $ffi = \FFI::load($headerFile);
        } catch (\FFI\Exception $e) {
            throw new FFIRuntimeException('Unable to load libnotify: ' . $e->getMessage(), 0, $e);
        }

        if (!$ffi) {
            throw new FFIRuntimeException('Unable to load libnotify');
        }

        self::$ffi = $ffi;

        if (!self::$ffi->notify_init(self::APP_NAME)) {
            throw new FFIRuntimeException('Unable to initialize libnotify');
        }

        if (!self::$ffi->notify_is_initted()) {
            throw new FFIRuntimeException('Libnotify has not been initialized');
        }
    }
}
