<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use JoliNotif\Driver\AppleScriptDriver;
use JoliNotif\Driver\GrowlNotifyDriver;
use JoliNotif\Driver\NotifySendDriver;
use JoliNotif\Driver\TerminalNotifierDriver;
use JoliNotif\Notification;
use JoliNotif\Notifier;

require __DIR__.'/../vendor/autoload.php';

$notifier = new Notifier([
    new AppleScriptDriver(),
    new TerminalNotifierDriver(),
    new GrowlNotifyDriver(),
    new NotifySendDriver(),
]);

$notification = new Notification();
$notification->setTitle('I\'m a notification title');
$notification->setBody('And this is the body');
$notification->setIcon(__DIR__.'/notification-icon.png');

$notifier->send($notification);
