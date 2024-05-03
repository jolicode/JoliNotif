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

    private \FFI $ffi;

    public function __destruct()
    {
        if (isset($this->ffi)) {
            $this->ffi->notify_uninit();
        }
    }

    public static function isLibraryExists(): bool
    {
        return file_exists('/lib64/libnotify.so.4')
            || file_exists('/lib/x86_64-linux-gnu/libnotify.so.4');
    }

    public function isSupported(): bool
    {
        return OsHelper::isUnix()
            && !OsHelper::isMacOS()
            && class_exists(\FFI::class)
            && self::isLibraryExists();
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

        $this->initialize();
        $notification = $this->ffi->notify_notification_new(
            $notification->getTitle() ?? '',
            $notification->getBody(),
            $notification->getIcon()
        );
        $value = $this->ffi->notify_notification_show($notification, null);
        $this->ffi->g_object_unref($notification);

        return $value;
    }

    private function initialize(): void
    {
        if (isset($this->ffi)) {
            return;
        }

        $headerFile = __DIR__ . '/FFI/ffi-libnotify.h';

        if (PharExtractor::isLocatedInsideAPhar($headerFile)) {
            $headerFile = PharExtractor::extractFile($headerFile);
        }

        $ffi = \FFI::load($headerFile);

        if (!$ffi) {
            throw new FFIRuntimeException('Unable to load libnotify');
        }

        $this->ffi = $ffi;

        if (!$this->ffi->notify_init(self::APP_NAME)) {
            throw new FFIRuntimeException('Unable to initialize libnotify');
        }

        if (!$this->ffi->notify_is_initted()) {
            throw new FFIRuntimeException('Libnotify has not been initialized');
        }
    }
}
