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

trigger_deprecation('jolicode/jolinotif', '2.7', 'The "%s" class is deprecated and will be removed in 3.0.', NoSupportedNotifierException::class);

/**
 * @deprecated since 2.7, will be removed in 3.0
 */
class NoSupportedNotifierException extends \RuntimeException implements Exception
{
    public function __construct(
        string $message = 'No supported notifier',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
