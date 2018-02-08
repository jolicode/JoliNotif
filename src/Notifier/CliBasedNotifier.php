<?php

/*
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
use Symfony\Component\Process\Process;

abstract class CliBasedNotifier implements Notifier
{
    const SUPPORT_NONE = -1;
    const SUPPORT_UNKNOWN = 0;
    const SUPPORT_NATIVE = 1;
    const SUPPORT_BINARY_PROVIDED = 2;

    /**
     * @var int One of the SUPPORT_XXX constants
     */
    private $support = self::SUPPORT_UNKNOWN;

    /**
     * {@inheritdoc}
     */
    public function isSupported(): bool
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
     * {@inheritdoc}
     */
    public function send(Notification $notification): bool
    {
        if (!$notification->getBody()) {
            throw new InvalidNotificationException($notification, 'Notification body can not be empty');
        }

        $arguments = $this->getCommandLineArguments($notification);

        if (self::SUPPORT_BINARY_PROVIDED === $this->support && $this instanceof BinaryProvider) {
            $dir = rtrim($this->getRootDir(), '/').'/';
            $embeddedBinary = $dir.$this->getEmbeddedBinary();

            if (PharExtractor::isLocatedInsideAPhar($embeddedBinary)) {
                $embeddedBinary = PharExtractor::extractFile($embeddedBinary);

                foreach ($this->getExtraFiles() as $file) {
                    PharExtractor::extractFile($dir.$file);
                }
            }

            $binary = $embeddedBinary;
        } else {
            $binary = $this->getBinary();
        }

        $process = new Process(array_merge([$binary], $arguments));
        $process->run();

        return $process->isSuccessful();
    }

    /**
     * Configure the process to run in order to send the notification.
     */
    abstract protected function getCommandLineArguments(Notification $notification): array;

    /**
     * Get the binary to check existence.
     */
    abstract protected function getBinary(): string;

    /**
     * Check whether a binary is available.
     */
    protected function isBinaryAvailable(): bool
    {
        if (OsHelper::isUnix()) {
            // Do not use the 'which' program to check if a binary exists.
            // See also http://stackoverflow.com/questions/592620/check-if-a-program-exists-from-a-bash-script
            $process = new Process([
                'sh',
                '-c',
                'command -v $0',
                $this->getBinary(),
            ]);
        } else {
            // 'where' is available on Windows since Server 2003
            $process = new Process([
                'where',
                $this->getBinary(),
            ]);
        }

        $process->run();

        return $process->isSuccessful();
    }
}
