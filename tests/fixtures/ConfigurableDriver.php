<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\tests\fixtures;

use Joli\JoliNotif\Driver\DriverInterface;
use Joli\JoliNotif\Notification;

class ConfigurableDriver implements DriverInterface
{
    public function __construct(
        private readonly bool $supported,
        private readonly int $priority = DriverInterface::PRIORITY_MEDIUM,
        private readonly bool $sendReturn = true,
    ) {
    }

    public function isSupported(): bool
    {
        return $this->supported;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function send(Notification $notification): bool
    {
        return $this->sendReturn;
    }
}
