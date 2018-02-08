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
    /**
     * @var bool
     */
    private $supported;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var bool
     */
    private $sendReturn;

    /**
     * @param $supported
     * @param $priority
     * @param $sendReturn
     */
    public function __construct(bool $supported, int $priority = Notifier::PRIORITY_MEDIUM, bool $sendReturn = true)
    {
        $this->supported = $supported;
        $this->priority = $priority;
        $this->sendReturn = $sendReturn;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(): bool
    {
        return $this->supported;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Notification $notification): bool
    {
        return $this->sendReturn;
    }
}
