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

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\Notifier;

class ConfigurableNotifier implements Notifier
{
    private bool $supported;
    private int $priority;
    private bool $sendReturn;

    public function __construct(bool $supported, int $priority = Notifier::PRIORITY_MEDIUM, bool $sendReturn = true)
    {
        $this->supported = $supported;
        $this->priority = $priority;
        $this->sendReturn = $sendReturn;
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
