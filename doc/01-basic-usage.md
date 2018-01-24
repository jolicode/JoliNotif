# Basic usage

## Create a notifier

JoliNotif provides a `NotifierFactory` which creates the best supported
notifier according to your platform. You don't have to care if you're running
on Linux, Windows or Mac OS:

```php
use Joli\JoliNotif\NotifierFactory;

$notifier = NotifierFactory::create();
```

The factory use the notifier's priority to determine the best notifier to use.
For example some notifier has a low priority because they don't support some
notification options. The best notifier will then be returned.

> **Note**: The factory now returns a NullNotifier instead of null when no
> notifier is available. You then no longer have to check for null value.

If you really need to ensure a Notifier is available, you can use the
`createOrThrowException` method. It will return the best notifier available or
throw a `Joli\JoliNotif\Exception\NoSupportedNotifierException` if no one is
available on the current system.

## Create your notification

Create a notification is as simple as instantiating a `Notification` and
setting the option you want to use:

```php
use Joli\JoliNotif\Notification;

$notification =
    (new Notification())
    ->setBody('The notification body')
    ->setTitle('The notification title')
    ->addOption('subtitle', 'This is a subtitle') // Only works on macOS (AppleScriptNotifier)
    ->addOption('sound', 'Frog') // Only works on macOS (AppleScriptNotifier)
    ->addOption('url', 'https://google.com') // Only works on macOS (TerminalNotifierNotifier)
;
```

As you can see, the notification provides a fluent API.

## Sending the notification

Now that you get your notification, just send it via the notifier:

```php
$notifier->send($notification);
```

And you're done!


## Next readings

* [Notification](02-notification.md)
* [Notifier](03-notifier.md)
* [Advanced usage](04-advanced-usage.md)
* [CLI usage](05-cli-usage.md)

Previous pages:

* [README](../README.md)
