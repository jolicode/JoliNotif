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
use Joli\JoliNotif\Driver\PowerShellDriver;
use Joli\JoliNotif\Driver\SnoreToastDriver;
use Joli\JoliNotif\Driver\TerminalNotifierDriver;
use Joli\JoliNotif\Exception\DriverFailureException;
use JoliCode\PhpOsHelper\OsHelper;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class DefaultNotifier implements NotifierInterface
{
    private readonly LoggerInterface $logger;
    private ?DriverInterface $driver = null;
    private bool $driverLoaded = false;

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

        try {
            return $this->driver->send($notification);
        } catch (DriverFailureException $e) {
            $this->logger->error('The notification could not be sent.', ['exception' => $e]);

            return false;
        }
    }

    protected function loadDriver(): void
    {
        if ($this->driverLoaded) {
            return;
        }

        $this->driverLoaded = true;

        if ($this->additionalDrivers) {
            $this->driver = $this->findBestDriver($this->additionalDrivers);

            if ($this->driver || $this->useOnlyAdditionalDrivers) {
                return;
            }
        }

        $this->driver = $this->findBestDriver($this->getDefaultDrivers());
    }

    /**
     * @param list<DriverInterface> $drivers
     */
    private function findBestDriver(array $drivers): ?DriverInterface
    {
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

        if ($bestDriver) {
            $this->logger->debug(\sprintf('Selected %s as best driver.', $bestDriver::class));
        } else {
            $this->logger->debug('Could not find any suitable driver.');
        }

        return $bestDriver;
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
            new GrowlNotifyDriver($this->logger),
            new TerminalNotifierDriver($this->logger),
            new AppleScriptDriver($this->logger),
            new KDialogDriver($this->logger),
            new NotifySendDriver($this->logger),
        ];
    }

    /**
     * @return list<DriverInterface>
     */
    private function getWindowsDrivers(): array
    {
        return [
            new PowerShellDriver($this->logger),
            new SnoreToastDriver($this->logger),
            new NotifuDriver($this->logger),
        ];
    }
}
