# Notification

`Notification` is the main model of JoliNotif. It contains all the options
that should be applied - if possible - by the notifier.

> **Note**
> Notifiers are designed to handle the options they support and discard not
> supported ones gracefully without throwing any exception.

Currently, only three options are supported but new ones could be added later.
Each option can be set via a setter on the `Notification` class.

## Body

The body is the main message of the notification that you want to display.

> **Note**
> This is the only required option. Notifiers will throw an`InvalidNotificationException`
> if the body is empty or null.

## Title

You can also optionally provide a title. Notifiers will certainly display the
title above the body, in bold.

## Icon

An icon can also be displayed inside the notification. You will need to set an
**absolute path**.

```php
$notification->setIcon(__DIR__.'/Resources/icons/success.png');
```

> **Note**
> If you use JoliNotif from a phar and add a notification icon, we will take
> care to extract this icon in your system temp directory to make it accessible
> from command line executable.

Not all notifiers support icon but again, you can safely add an icon to your
notification since the notifier will discard it if not supported.

## Options with restricted support

Some options are available only on few notifiers. They can be configured
through the `addOption` method.

### Subtitle

Only works with AppleScriptNotifier at the moment.

```php
$notification->addOption('subtitle', 'This is a subtitle');
```

### Sound

Only works with AppleScriptNotifier at the moment.

Non-exhaustive list of sounds: Basso, Frog, Hero, Pop, Submarine, Blow, Funk,
Morse, Purr, Tink, Bottle, Glass, Ping, Sosumi.

```php
$notification->addOption('sound', 'Frog');
```

### Url

Only works with TerminalNotifierNotifier at the moment.

```php
$notification->addOption('url', 'https://google.com');
```

## Next readings

* [Notifier](03-notifier.md)
* [CRON usage](04-cron-usage.md)
* [CLI usage](05-cli-usage.md)

Previous pages:

* [Basic usage](01-basic-usage.md)
* [README](../README.md)
