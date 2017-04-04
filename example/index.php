<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\Notifier\NullNotifier;
use Joli\JoliNotif\NotifierFactory;

require __DIR__.'/../vendor/autoload.php';

$notifier = NotifierFactory::create();

if (!($notifier instanceof NullNotifier)) {
    $notification =
        (new Notification())
        ->setTitle('Notification example')
        ->setBody('This is a notification example. Pretty cool isn\'t it?')
        ->setIcon(__DIR__.'/icon-success.png')
    ;

    $result = $notifier->send($notification);

    echo 'Notification ', $result ? 'successfully sent' : 'failed', ' with ', get_class($notifier), PHP_EOL;
} else {
    echo 'No supported notifier', PHP_EOL;
}
