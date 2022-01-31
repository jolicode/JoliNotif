# Notifier

## Interface

All notifiers implement the `Joli\JoliNotif\Notifier` interface. The main
method you will use is `Notifier#send()`:

```php
interface Notifier
{
    /**
     * Send the given notification.
     *
     * @param Notification $notification
     *
     * @throws Exception\InvalidNotificationException if the notification is invalid
     *
     * @return bool
     */
    public function send(Notification $notification);
}
```

`Notifier#send()` will return true if the command was successfully executed,
false otherwise.

## Supported notifiers

Currently, JoliNotif only provides notifiers that use an executable available
on your system. But nothing prevents to add network based notifiers later! :)

Here is the full list of supported notifiers, grouped by platform:

### Linux

#### KDialogNotifier

This notifier uses the executable `kdialog` (part of the standard KDE 5 Plasma
Desktop installation) which should be installed by default on most Linux
distributions which use the KDE 5 Plasma Desktop such as KUbuntu.

kdialog can display notifications with a body and a title. It does not support
icons. A default timeout of 5 seconds is hard-coded for the notification as it
needs to be part of the command line.

#### NotifySendNotifier

This notifier uses the executable `notify-send` (available in the
`libnotify-bin` package) which should be installed by default on most Linux
distributions.

notify-send can display notification with a body, a title and an icon.

### Mac OS

#### GrowlNotifyNotifier

This notifier uses the `growlnotify` executable. It can be used when available
alongside growl, which can be installed on Mac OS X.

growl can display notification with a body, a title and an icon.

#### TerminalNotifierNotifier

This notifier uses the `terminal-notifier` executable and works on Mac OS X
10.8 and higher.

terminal-notifier can display notification with a body and a title. An icon
can only be displayed on Mac OS X 10.9 and higher.

#### AppleScriptNotifier

This notifier is based on AppleScript and uses the `osascript` binary.
AppleScript can display notification since Mac OS X 10.9, so this notifier
requires this version or higher.

AppleScript can display notification with only a body and a title. AppleScript
don't support to set an icon and will always use instead the icon of the
application sending the notification, in our case, the terminal.

### Windows

#### SnoreToastNotifier

This notifier uses the Windows application called SnoreToastNotifier. It works on Windows
8 and higher. Because SnoreToastNotifier is probably not installed on your system,
JoliNotif embed the binaries inside the [bin/snoreToast](bin/snoreToast) directory.

When you use JoliNotif inside a phar archive, we take care to extract those
binaries in the system temp directory to be able to execute them.

SnoreToastNotifier can display notification with a body, a title and an icon.

#### ToasterNotifier

This notifier uses the Windows application called Toaster. It works on Windows
8 and higher. Because Toaster is probably not installed on your system,
JoliNotif embed the binaries inside the [bin/toaster](bin/toaster) directory.

When you use JoliNotif inside a phar archive, we take care to extract those
binaries in the system temp directory to be able to execute them.

Toaster can display notification with a body, a title and an icon.

#### NotifuNotifier

This notifier uses the Windows application called Notifu. It works on Windows
7. Because Notifu is probably not installed on your system, JoliNotif embed the
binary inside the [bin/notifu](bin/notifu) directory.

When you use JoliNotif inside a phar archive, we take care to extract this
binary in the system temp directory to be able to execute it.

Notifu can display notification with a body, a title and an icon. Sadly, Notifu
can only display icon with the .ico format.

## Next readings

* [CRON usage](04-cron-usage.md)
* [CLI usage](05-cli-usage.md)

Previous pages:

* [Notification](02-notification.md)
* [Basic usage](01-basic-usage.md)
* [README](../README.md)
