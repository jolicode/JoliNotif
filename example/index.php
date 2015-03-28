<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;

require __DIR__.'/../vendor/autoload.php';

$notifier = NotifierFactory::create();

$notification =
    (new Notification())
    ->setTitle('Notification example')
    ->setBody('This is a notification example. Pretty cool isn\'t it?')
    ->setIcon(__DIR__.'/icon-success.png')
;

$notifier->send($notification);
