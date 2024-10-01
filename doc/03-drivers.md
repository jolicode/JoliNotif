# Drivers

JoliNotif's default notifier uses drivers to delegate the notification sending
to the right executable available on your system.

## Interface

All drivers implement the `Joli\JoliNotif\Driver\DriverInterface` interface.

`Driver#send()` will return true if the command was successfully executed,
false otherwise.

## Supported drivers

JoliNotif supports different kinds of drivers. Some driver load a C library
while most of the others execute a binary (whether it is available on your
system or provided by JoliNotif directly).

Here is the full list of supported drivers, grouped by platform:

### Linux

#### LibNotifyDriver

This driver uses the FFI PHP extension to interact with the `libnotify` C
library. This library should be installed by default on most Linux
distributions wih graphical interface.

LibNotifyDriver can display notification with a body, a title and an icon.

#### NotifySendDriver

This driver uses the executable `notify-send` (available in the
`libnotify-bin` package) which should be installed by default on most Linux
distributions.

notify-send can display notification with a body, a title and an icon.

#### KDialogDriver

This driver uses the executable `kdialog` (part of the standard KDE 5 Plasma
Desktop installation) which should be installed by default on most Linux
distributions which use the KDE 5 Plasma Desktop such as KUbuntu.

kdialog can display notifications with a body and a title. It does not support
icons. A default timeout of 5 seconds is hard-coded for the notification as it
needs to be part of the command line.

### Mac OS

#### GrowlNotifyDriver

This driver uses the `growlnotify` executable. It can be used when available
alongside growl, which can be installed on Mac OS X.

growl can display notification with a body, a title and an icon.

#### TerminalNotifierDriver

This driver uses the `terminal-notifier` executable and works on Mac OS X 10.8
and higher.

terminal-notifier can display notification with a body and a title. An icon can
only be displayed on Mac OS X 10.9 and higher.

#### AppleScriptDriver

This driver is based on AppleScript and uses the `osascript` binary.
AppleScript can display notification since Mac OS X 10.9, so this driver
requires this version or higher.

AppleScript can display notification with only a body and a title. AppleScript
don't support to set an icon and will always use instead the icon of the
application sending the notification, in our case, the terminal.

### Windows

#### SnoreToastDriver

This driver uses the Windows application called SnoreToastDriver. It works on
Windows 8 and higher. Because SnoreToastDriver is probably not installed on
your system, JoliNotif embed the binaries inside the [bin/snoreToast](bin/snoreToast)
directory.

When you use JoliNotif inside a phar archive, we take care to extract those
binaries in the system temp directory to be able to execute them.

SnoreToastDriver can display notification with a body, a title and an icon.

#### ToasterDriver

This driver uses the Windows application called Toaster. It works on Windows 8
and higher. Because Toaster is probably not installed on your system, JoliNotif
embed the binaries inside the [bin/toaster](bin/toaster) directory.

When you use JoliNotif inside a phar archive, we take care to extract those
binaries in the system temp directory to be able to execute them.

Toaster can display notification with a body, a title and an icon.

#### WslNotifySendDriver

This driver uses the executable `wsl-notify-send`.
It permits to send notification from Windows Subsystem for Linux to Windows.

wsl-notify-send can display notification with a body and a title.

Icon is partially supported by `wsl-notify-send`, but it's not possible to set
an icon for now.

## Using custom drivers

If you created your own driver, you can pass it in the `$additionnalDrivers`
parameter of the `DefaultNotifier` constructor:

```php
use Joli\JoliNotif\DefaultNotifier;
use Joli\JoliNotif\Notification;

$notifier = new DefaultNotifier(null, [new MyCustomDriver()]);

$notifier->send(new Notification());
```

If the driver is supported, it will be used in priority. If not, the native
drivers of JoliNotif will be looked for. You can totally disable the native
drivers by also passing `false` in the `$useOnlyAdditionalDrivers` parameter of
the constructor:

```php
use Joli\JoliNotif\DefaultNotifier;
use Joli\JoliNotif\Notification;

$notifier = new DefaultNotifier(null, [new MyCustomDriver()], false);

// If MyCustomDriver is not supported, no native drivers will not be used
// and the send method will always return false.
$notifier->send(new Notification());
```

> [!NOTE]
> If you created a driver that could be useful for others, feel free to open a
> pull request, so we can consider adding it natively in the library!

## Next readings

* [CRON usage](04-cron-usage.md)
* [CLI usage](05-cli-usage.md)

Previous pages:

* [Notification](02-notification.md)
* [Basic usage](01-basic-usage.md)
* [README](../README.md)
