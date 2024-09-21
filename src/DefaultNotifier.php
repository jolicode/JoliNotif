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

use Joli\JoliNotif\Driver\AppleScriptDriver;
use Joli\JoliNotif\Driver\DriverInterface;
use Joli\JoliNotif\Driver\GrowlNotifyDriver;
use Joli\JoliNotif\Driver\KDialogDriver;
use Joli\JoliNotif\Driver\LibNotifyDriver;
use Joli\JoliNotif\Driver\NotifuDriver;
use Joli\JoliNotif\Driver\NotifySendDriver;
use Joli\JoliNotif\Driver\SnoreToastDriver;
use Joli\JoliNotif\Driver\TerminalNotifierDriver;
use Joli\JoliNotif\Driver\WslNotifySendDriver;
use JoliCode\PhpOsHelper\OsHelper;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class DefaultNotifier implements NotifierInterface
{
    private readonly LoggerInterface $logger;
    private ?DriverInterface $driver;

    public function __construct(
        ?LoggerInterface $logger = null,
        /** @var list<DriverInterface> $additionalDrivers */
        private readonly array $additionalDrivers = [],
        private readonly bool $useOnlyAdditionalDrivers = false,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function getDriver(): ?DriverInterface
    {
        $this->loadDriver();

        return $this->driver;
    }

    public function send(Notification $notification): bool
    {
        $this->loadDriver();

        if (!$this->driver) {
            $this->logger->warning('No driver available to display a notification on your system.');

            return false;
        }

        return $this->driver->send($notification);
    }

    protected function loadDriver(): void
    {
        if ($this->additionalDrivers) {
            $this->doLoadDriver($this->additionalDrivers);
        }

        if ($this->additionalDrivers && $this->useOnlyAdditionalDrivers) {
            $this->driver ??= null;

            return;
        }

        $this->doLoadDriver($this->getDefaultDrivers());
    }

    /**
     * @param list<DriverInterface> $drivers
     */
    private function doLoadDriver(array $drivers): void
    {
        if (isset($this->driver)) {
            return;
        }

        /** @var ?DriverInterface $bestDriver */
        $bestDriver = null;

        foreach ($drivers as $driver) {
            if (!$driver->isSupported()) {
                continue;
            }

            if (null !== $bestDriver && $bestDriver->getPriority() >= $driver->getPriority()) {
                continue;
            }

            $bestDriver = $driver;
        }

        $this->driver = $bestDriver;
    }

    /**
     * @return list<DriverInterface>
     */
    private function getDefaultDrivers(): array
    {
        // Don't retrieve notifiers which are certainly not supported on this
        // system. This helps to lower the number of process to run.
        if (OsHelper::isUnix() && !OsHelper::isWindowsSubsystemForLinux()) {
            return $this->getUnixDrivers();
        }

        return $this->getWindowsDrivers();
    }

    /**
     * @return list<DriverInterface>
     */
    private function getUnixDrivers(): array
    {
        return [
            new LibNotifyDriver(),
            new GrowlNotifyDriver(),
            new TerminalNotifierDriver(),
            new AppleScriptDriver(),
            new KDialogDriver(),
            new NotifySendDriver(),
        ];
    }

    /**
     * @return list<DriverInterface>
     */
    private function getWindowsDrivers(): array
    {
        return [
            new SnoreToastDriver(),
            new NotifuDriver(),
            new WslNotifySendDriver(),
        ];
    }
}
