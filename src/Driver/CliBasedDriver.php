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
     * Configure the process to run in order to send the notification.
     *
     * @param ProcessBuilder $processBuilder
     * @param Notification   $notification
     */
    abstract protected function configureProcess(ProcessBuilder $processBuilder, Notification $notification);

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
        $builder = new ProcessBuilder();
        $builder->setPrefix($this->getBinary());

        $this->configureProcess($builder, $notification);

        $process = $builder->getProcess();
        $process->run();

        return $process->isSuccessful();
    }
}
