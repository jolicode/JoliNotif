<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\Driver;

use Joli\JoliNotif\Notification;
use JoliCode\PhpOsHelper\OsHelper;

/**
 * This driver allows native notification from WSL to Windows using PowerShell.
 * No Windows binary needed.
 *
 * @internal
 */
class WslPowerShellDriver extends AbstractCliBasedDriver
{
    public function getBinary(): string
    {
        return 'powershell.exe';
    }

    public function getPriority(): int
    {
        return static::PRIORITY_HIGH;
    }

    public function isSupported(): bool
    {
        return OsHelper::isWindowsSubsystemForLinux();
    }

    protected function getCommandLineArguments(Notification $notification): array
    {
        $arguments = [
            '-Command',
        ];

        // For security reasons we clean newlines and double quote.
        $body = str_replace(["\n", '"'], '', (string) $notification->getBody());
        $title = str_replace(["\n", '"'], '', (string) $notification->getTitle());

        $command = <<<POWERSHELL
            [Windows.UI.Notifications.ToastNotificationManager, Windows.UI.Notifications, ContentType = WindowsRuntime] | Out-Null;
            \$template = [Windows.UI.Notifications.ToastNotificationManager]::GetTemplateContent([Windows.UI.Notifications.ToastTemplateType]::ToastText01);
            \$textNodes = \$template.GetElementsByTagName("text");
            \$textNodes.Item(0).AppendChild(
              \$template.CreateTextNode("{$body}")
            ) | Out-Null;
            \$notifier = [Windows.UI.Notifications.ToastNotificationManager]::CreateToastNotifier("{$title}");
            \$notifier.Show([Windows.UI.Notifications.ToastNotification]::new(\$template))
            POWERSHELL;

        $arguments[] = str_replace("\n", ' ', $command);

        return $arguments;
    }
}
