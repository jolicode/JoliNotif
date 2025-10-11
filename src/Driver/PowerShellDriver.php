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

use Joli\JoliNotif\Exception\DriverFailureException;
use Joli\JoliNotif\Notification;
use JoliCode\PhpOsHelper\OsHelper;

/**
 * This driver allows native notification in Windows and WSL using PowerShell.
 * No Windows binary needed.
 *
 * @internal
 */
class PowerShellDriver extends AbstractCliBasedDriver
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
        return (OsHelper::isWindows() && OsHelper::isWindowsTenOrHigher()) || OsHelper::isWindowsSubsystemForLinux();
    }

    protected function getCommandLineArguments(Notification $notification): array
    {
        // Avoid escaping issues
        $body = str_replace('"', '""', (string) $notification->getBody());
        $title = str_replace('"', '""', (string) $notification->getTitle());
        $icon = str_replace('"', '""', (string) $notification->getIcon());

        if ($icon && OsHelper::isWindowsSubsystemForLinux() && !preg_match('@^[a-z]:@i', $icon) && !preg_match('@^/mnt/[a-z]@i', $icon)) {
            $this->logger->warning('Only images from Windows volume can be used for PowerShell notification icon inside WSL. Please use a path starting by something like "c:\" or "/mnt/c/".', [
                'path' => $icon,
            ]);
            $icon = '';
        }

        if ($icon) {
            $icon = 'file:///' . str_replace('\\', '/', $icon);
        }

        $command = <<<POWERSHELL
            [Windows.UI.Notifications.ToastNotificationManager, Windows.UI.Notifications, ContentType = WindowsRuntime] | Out-Null;

            \$template = [Windows.UI.Notifications.ToastNotificationManager]::GetTemplateContent([Windows.UI.Notifications.ToastTemplateType]::ToastImageAndText01);

            \$textNodes = \$template.GetElementsByTagName("text");
            \$textNodes.Item(0).AppendChild(
                \$template.CreateTextNode("{$body}")
            ) | Out-Null;

            \$imageNodes = \$template.GetElementsByTagName("image");

            if ("{$icon}" -ne "") {
                \$imageNodes.Item(0).SetAttribute("src", "{$icon}") | Out-Null;
            }

            \$notifier = [Windows.UI.Notifications.ToastNotificationManager]::CreateToastNotifier("{$title}");
            \$notifier.Show([Windows.UI.Notifications.ToastNotification]::new(\$template))
            POWERSHELL;

        // Encode the PowerShell script to UTF-16LE, then Base64.
        // This is the recommended and safest way to use -EncodedCommand to prevent injection attacks and avoid shell escaping issues.
        $command = iconv('UTF-8', 'UTF-16LE', $command);

        if (false === $command) {
            throw new DriverFailureException($notification, 'Failed to convert PowerShell command to UTF-16LE encoding.');
        }

        $command = base64_encode($command);

        return [
            '-NoProfile',
            '-NonInteractive',
            '-EncodedCommand',
            $command,
        ];
    }
}
