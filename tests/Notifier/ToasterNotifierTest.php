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
use Joli\JoliNotif\Notifier\ToasterNotifier;

class ToasterNotifierTest extends NotifierTestCase
{
    use BinaryProviderTestTrait;
    use CliBasedNotifierTestTrait;

    private const BINARY = 'toast';

    public function testGetBinary()
    {
        $notifier = $this->getNotifier();

        $this->assertSame(self::BINARY, $notifier->getBinary());
    }

    public function testGetPriority()
    {
        $notifier = $this->getNotifier();

        $this->assertSame(Notifier::PRIORITY_MEDIUM, $notifier->getPriority());
    }

    protected function getNotifier(): Notifier
    {
        return new ToasterNotifier();
    }

    protected function getExpectedCommandLineForNotification(): string
    {
        return <<<'CLI'
            'toast' '-m' 'I'\''m the notification body'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithATitle(): string
    {
        return <<<'CLI'
            'toast' '-m' 'I'\''m the notification body' '-t' 'I'\''m the notification title'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAnIcon(): string
    {
        $iconDir = $this->getIconDir();

        return <<<CLI
            'toast' '-m' 'I'\\''m the notification body' '-p' '{$iconDir}/image.gif'
            CLI;
    }

    protected function getExpectedCommandLineForNotificationWithAllOptions(): string
    {
        $iconDir = $this->getIconDir();

        return <<<CLI
            'toast' '-m' 'I'\\''m the notification body' '-t' 'I'\\''m the notification title' '-p' '{$iconDir}/image.gif'
            CLI;
    }
}
