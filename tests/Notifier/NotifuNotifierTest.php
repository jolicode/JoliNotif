<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\tests\Notifier;

use Joli\JoliNotif\Notifier;
use Joli\JoliNotif\Notifier\NotifuNotifier;

class NotifuNotifierTest extends NotifierTestCase
{
    use CliBasedNotifierTestTrait;
    use BinaryProviderTestTrait;
    const BINARY = 'notifu';

    public function testGetBinary()
    {
        $notifier = $this->getNotifier();

        $this->assertSame(self::BINARY, $notifier->getBinary());
    }

    public function testGetPriority()
    {
        $notifier = $this->getNotifier();

        $this->assertSame(Notifier::PRIORITY_LOW, $notifier->getPriority());
    }

    protected function getNotifier()
    {
        return new NotifuNotifier();
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotification()
    {
        return <<<CLI
'notifu' '/m' 'I'\''m the notification body'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithATitle()
    {
        return <<<CLI
'notifu' '/m' 'I'\''m the notification body' '/p' 'I'\\''m the notification title'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithASubtitle()
    {
        return $this->getExpectedCommandLineForNotificationWithATitle();
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithASound()
    {
        return $this->getExpectedCommandLineForNotificationWithATitle();
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAnIcon()
    {
        $iconDir = $this->getIconDir();

        return <<<CLI
'notifu' '/m' 'I'\''m the notification body' '/i' '${iconDir}/image.gif'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAllOptions()
    {
        $iconDir = $this->getIconDir();

        return <<<CLI
'notifu' '/m' 'I'\''m the notification body' '/p' 'I'\''m the notification title' '/i' '${iconDir}/image.gif'
CLI;
    }
}
