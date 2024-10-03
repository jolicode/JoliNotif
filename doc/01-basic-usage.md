# Basic usage

## Create a notifier and sending a notification

JoliNotif provides a `DefaultNotifier` class which is the main entrypoint of
the library. It's main goal is to provide a simple way to send a desktop
notification without having to care about the platform you're running on. It
will work whether you're on Linux, Windows or macOS.

```php
use Joli\JoliNotif\DefaultNotifier;
use Joli\JoliNotif\Notification;

$notifier = new DefaultNotifier();

$notifier->send(new Notification());
```

And you're done!

Internally, the notifier will use each driver's priority to determine the
best one available on your system.
For example, some driver have a low priority because they don't support some
notification options. So if a better driver is available, it will be used.

> [!NOTE]
> In case no driver is supported or if an error happens during notification
> sending, the send method will return false.

> [!TIP]
> If you want to log when an error happens or if no driver is supported, you
> can also pass an instance of `Psr\Log\LoggerInterface` as the first
> parameter of the `DefaultNotifier`'s constructor.

## Create and configure your notification

Create a notification is as simple as instantiating a `Notification` and
setting the option you want to use:

```php
use Joli\JoliNotif\Notification;

$notification =
    (new Notification())
    ->setBody('The notification body')
    ->setTitle('The notification title')
    ->addOption('subtitle', 'This is a subtitle') // Only works on macOS (AppleScriptDriver)
    ->addOption('sound', 'Frog') // Only works on macOS (AppleScriptDriver & TerminalNotifierDriver)
    ->addOption('url', 'https://google.com') // Only works on macOS (TerminalNotifierDriver)
;
```

As you can see, the notification class provides a fluent API.

## Next readings

* [Notification](02-notification.md)
* [Drivers](03-drivers.md)
* [CRON usage](04-cron-usage.md)
* [CLI usage](05-cli-usage.md)

Previous pages:

* [README](../README.md)
