<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\Notifier;

use Joli\JoliNotif\Exception\InvalidNotificationException;
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\Notifier;
use Joli\JoliNotif\Util\OsHelper;
use Joli\JoliNotif\Util\PharExtractor;
use Symfony\Component\Process\ProcessBuilder;

abstract class CliBasedNotifier implements Notifier
{
    const SUPPORT_NONE            = -1;
    const SUPPORT_UNKNOWN         = 0;
    const SUPPORT_NATIVE          = 1;
    const SUPPORT_BINARY_PROVIDED = 2;

    /**
     * @var int One of the SUPPORT_XXX constants
     */
    private $support = self::SUPPORT_UNKNOWN;

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
        if (self::SUPPORT_UNKNOWN !== $this->support) {
            return self::SUPPORT_NONE !== $this->support;
        }

        if ($this->isBinaryAvailable()) {
            $this->support = self::SUPPORT_NATIVE;

            return true;
        }

        if ($this instanceof BinaryProvider && $this->canBeUsed()) {
            $this->support = self::SUPPORT_BINARY_PROVIDED;

            return true;
        }

        $this->support = self::SUPPORT_NONE;

        return false;
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
        if (!$notification->getBody()) {
            throw new InvalidNotificationException($notification, 'Notification body can not be empty');
        }

        $builder = new ProcessBuilder();

        if (self::SUPPORT_BINARY_PROVIDED === $this->support && $this instanceof BinaryProvider) {
            $dir            = rtrim($this->getRootDir(), '/').'/';
            $embeddedBinary = $dir.$this->getEmbeddedBinary();

            if (PharExtractor::isLocatedInsideAPhar($embeddedBinary)) {
                $embeddedBinary = PharExtractor::extractFile($embeddedBinary);

                foreach ($this->getExtraFiles() as $file) {
                    PharExtractor::extractFile($dir.$file);
                }
            }

            $builder->setPrefix($embeddedBinary);
        } else {
            $builder->setPrefix($this->getBinary());
        }

        $this->configureProcess($builder, $notification);

        $process = $builder->getProcess();
        $process->run();

        return $process->isSuccessful();
    }
}
