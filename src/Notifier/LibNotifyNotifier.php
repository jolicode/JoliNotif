<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\Notifier;

use Joli\JoliNotif\Exception\FFIRuntimeException;
use Joli\JoliNotif\Exception\InvalidNotificationException;
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\Notifier;
use JoliCode\PhpOsHelper\OsHelper;

class LibNotifyNotifier implements Notifier
{
    private static string $APP_NAME = 'jolinotif';

    private ?\FFI $ffi;

    public function __destruct()
    {
        if (isset($this->ffi) && null !== $this->ffi) {
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

        $this->ffi = \FFI::load(__DIR__ . '/FFI/ffi-libnotify.h');
        if (!$this->ffi->notify_init(self::$APP_NAME)) {
            throw new FFIRuntimeException('Unable to initialize libnotify');
        }

        if (!$this->ffi->notify_is_initted()) {
            throw new FFIRuntimeException('Libnotify has not been initialized');
        }
    }
}
