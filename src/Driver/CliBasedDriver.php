<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JoliNotif\Driver;

use JoliNotif\Notification;
use Symfony\Component\Process\ProcessBuilder;

abstract class CliBasedDriver implements Driver
{
    /**
     * Get the arguments that will be passed to the process in order to send the notification.
     *
     * @param Notification $notification
     *
     * @return array
     */
    abstract protected function getProcessArguments(Notification $notification);

    /**
     * Get the binary to check existence.
     *
     * @return string
     */
    abstract protected function getBinary();

    /**
     * {@inheritdoc}
     */
    public function send(Notification $notification)
    {
        $arguments = $this->getProcessArguments($notification);

        $builder = new ProcessBuilder($arguments);
        $process = $builder->getProcess();

        $process->run();

        return $process->isSuccessful();
    }
}
