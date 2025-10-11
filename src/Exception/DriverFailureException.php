<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\Exception;

use Joli\JoliNotif\Notification;

class DriverFailureException extends \RuntimeException implements ExceptionInterface
{
    public function __construct(
        public readonly Notification $notification,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getNotification(): Notification
    {
        return $this->notification;
    }
}
