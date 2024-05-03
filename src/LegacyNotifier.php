<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif;

use Joli\JoliNotif\Driver\DriverInterface;

trigger_deprecation('jolicode/jolinotif', '2.7', 'The "%s" class is deprecated and will be removed in 3.0. Use %s', LegacyNotifier::class, DefaultNotifier::class);

/**
 * @deprecated since 2.7, will be removed in 3.0. Use DefaultNotifier instead.
 */
class LegacyNotifier extends DefaultNotifier implements Notifier
{
    /** @param list<DriverInterface> $drivers */
    public function __construct(array $drivers)
    {
        parent::__construct(null, $drivers, true);

        $this->loadDriver();
    }

    public function isSupported(): bool
    {
        return true;
    }

    public function getPriority(): int
    {
        return Notifier::PRIORITY_HIGH;
    }
}
