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

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\Util\OsHelper;
use Symfony\Component\Process\ProcessBuilder;

class NotifuNotifier extends CliBasedNotifier implements BinaryProvider
{
    /**
     * {@inheritdoc}
     */
    public function getBinary()
    {
        return 'notifu';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return static::PRIORITY_LOW;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureProcess(ProcessBuilder $processBuilder, Notification $notification)
    {
        $processBuilder->add('/m');
        $processBuilder->add($notification->getBody());

        if ($notification->getTitle()) {
            $processBuilder->add('/p');
            $processBuilder->add($notification->getTitle());
        }

        if ($notification->getIcon()) {
            $processBuilder->add('/i');
            $processBuilder->add($notification->getIcon());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function canBeUsed()
    {
        return OsHelper::isWindows() && OsHelper::isWindowsSeven();
    }

    /**
     * {@inheritdoc}
     */
    public function getRootDir()
    {
        return dirname(dirname(dirname(__FILE__))).'/bin/notifu';
    }

    /**
     * {@inheritdoc}
     */
    public function getEmbeddedBinary()
    {
        return 'notifu.exe';
    }

    /**
     * {@inheritdoc}
     */
    public function getExtraFiles()
    {
        return [];
    }
}
