<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Joli\JoliNotif\DefaultNotifier;
use Joli\JoliNotif\Notification;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

require __DIR__ . '/../vendor/autoload.php';

$logger = new Logger('jolinotif');
$logger->pushHandler(new StreamHandler('php://stderr', Level::Warning));
$notifier = new DefaultNotifier($logger);

if (!$notifier->getDriver()) {
    echo 'No supported notifier', \PHP_EOL;
    exit(1);
}

$notification =
    (new Notification())
        ->setTitle('Notification example')
        ->setBody('This is a notification example. Pretty cool isn\'t it?')
        ->setIcon(__DIR__ . '/icon-success.png')
;

$result = $notifier->send($notification);

$driver = $notifier->getDriver();

echo 'Notification ', $result ? 'successfully sent' : 'failed', ' with ', str_replace('Joli\JoliNotif\Driver\\', '', $driver::class), \PHP_EOL;
