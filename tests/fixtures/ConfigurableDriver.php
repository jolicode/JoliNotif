<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JoliNotif\tests\fixtures;

use JoliNotif\Driver\Driver;
use JoliNotif\Notification;

class ConfigurableDriver implements Driver
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
    public function __construct($supported, $priority = Driver::PRIORITY_MEDIUM, $sendReturn = true)
    {
        $this->supported = $supported;
        $this->priority = $priority;
        $this->sendReturn = $sendReturn;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported()
    {
        return $this->supported;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Notification $notification)
    {
        return $this->sendReturn;
    }
}
