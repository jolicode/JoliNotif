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
use JoliNotif\Util\OsHelper;
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
    public function isSupported()
    {
        return $this->isBinaryAvailable();
    }

    /**
     * Check whether a binary is available.
     *
     * @return bool
     */
    protected function isBinaryAvailable()
    {
        if (OsHelper::isUnix()) {
            // Do not use the 'which' programm to check if a binary exists.
            // See also http://stackoverflow.com/questions/592620/check-if-a-program-exists-from-a-bash-script
            $builder = new ProcessBuilder([
                'command',
                '-v',
                $this->getBinary(),
                '>/dev/null',
                '2>&1',
            ]);
        } else {
            // 'where' is available on Windows since Server 2003
            $builder = new ProcessBuilder([
                'where',
                $this->getBinary(),
            ]);
        }

        $process = $builder->getProcess();
        $process->run();

        return $process->isSuccessful();
    }

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
