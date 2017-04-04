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
use Joli\JoliNotif\Notifier\GrowlNotifyNotifier;

class GrowlNotifyNotifierTest extends NotifierTestCase
{
    use CliBasedNotifierTestTrait;
    const BINARY = 'growlnotify';

    public function testGetBinary()
    {
        $notifier = $this->getNotifier();

        $this->assertSame(self::BINARY, $notifier->getBinary());
    }

    public function testGetPriority()
    {
        $notifier = $this->getNotifier();

        $this->assertSame(Notifier::PRIORITY_HIGH, $notifier->getPriority());
    }

    protected function getNotifier()
    {
        return new GrowlNotifyNotifier();
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotification()
    {
        return <<<CLI
'growlnotify' '--message' 'I'\''m the notification body'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithATitle()
    {
        return <<<CLI
'growlnotify' '--message' 'I'\''m the notification body' '--title' 'I'\''m the notification title'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAnIcon()
    {
        return <<<CLI
'growlnotify' '--message' 'I'\''m the notification body' '--image' '/home/toto/Images/my-icon.png'
CLI;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommandLineForNotificationWithAllOptions()
    {
        return <<<CLI
'growlnotify' '--message' 'I'\''m the notification body' '--title' 'I'\''m the notification title' '--image' '/home/toto/Images/my-icon.png'
CLI;
    }
}
