<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use JoliNotif\Driver\GrowlNotifyDriver;
use JoliNotif\Driver\NotifySendDriver;
use JoliNotif\Driver\TerminalNotifierDriver;
use JoliNotif\Notification;
use JoliNotif\Notifier;

require __DIR__.'/../vendor/autoload.php';

$icon = __DIR__.'/notification-icon.png';

// This part make the icon accessible when JoliNotif is used inside a phar
if (0 === strpos($icon, 'phar://')) {
    $pharUri          = dirname(dirname(__FILE__));
    $pharPath         = str_replace('phar://', '', $pharUri);
    $iconRelativePath = substr($icon, strlen($pharUri)+1);
    $tmpDir           = sys_get_temp_dir().'/jolinotif';

    $phar = new Phar($pharPath);
    $phar->extractTo($tmpDir, $iconRelativePath, true);

    $icon = $tmpDir.'/'.$iconRelativePath;
}

$notifier = new Notifier([
    new TerminalNotifierDriver(),
    new GrowlNotifyDriver(),
    new NotifySendDriver(),
]);

$notification = new Notification();
$notification->setTitle('I\'m a notification title');
$notification->setBody('And this is the body');
$notification->setIcon($icon);

$notifier->send($notification);
