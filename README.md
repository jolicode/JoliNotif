<p align="center">
    <img src=doc/images/demo.gif alt="JoliNotif demo" />
</p>

<p align="center">
    <a href="https://packagist.org/packages/jolicode/jolinotif"><img src="https://poser.pugx.org/jolicode/jolinotif/downloads" alt="Total Downloads"></img></a>
    <a href="https://packagist.org/packages/jolicode/jolinotif"><img src="https://poser.pugx.org/jolicode/jolinotif/v/stable" alt="Latest Stable Version"></img></a>
    <a href="https://packagist.org/packages/jolicode/jolinotif"><img src="https://poser.pugx.org/jolicode/jolinotif/v/unstable" alt="Latest Unstable Version"></img></a>
</p>

# About JoliNotif

JoliNotif is a cross-platform PHP library to display desktop notifications.
It works on Linux, Windows or MacOS.

Requires PHP >= 7.4 (support for PHP 5 was available in version 1.x, for PHP 7.0
and 7.1 in version < 2.1.0, for PHP 7.2 and 7.3 in version < 2.4.0).

> [!NOTE]
> This library can not be used in a web context (FPM or equivalent). Use
> it in your CLI scripts or in a [CRON](doc/04-cron-usage.md)

## Installation

Use [Composer](http://getcomposer.org/) to install JoliNotif in your project:

```shell
composer require "jolicode/jolinotif"
```

## Usage

Use the `NotifierFactory` to create the correct `Notifier` (adapted to your OS),
then use it to send your notification:

```php
include __DIR__.'/vendor/autoload.php';

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;

// Create a Notifier
$notifier = NotifierFactory::create();

// Create your notification
$notification =
    (new Notification())
    ->setTitle('Notification title')
    ->setBody('This is the body of your notification')
    ->setIcon(__DIR__.'/path/to/your/icon.png')
    ->addOption('subtitle', 'This is a subtitle') // Only works on macOS (AppleScriptNotifier)
    ->addOption('sound', 'Frog') // Only works on macOS (AppleScriptNotifier)
;

// Send it
$notifier->send($notification);
```

A [shell executable](doc/05-cli-usage.md) is also provided to use JoliNotif from CLI:

```shell
jolinotif --title "Hello" --body "World"
```

## Further documentation

Discover more by reading the docs:

* [Basic usage](doc/01-basic-usage.md)
* [Notification](doc/02-notification.md)
* [Notifier](doc/03-notifier.md)
* [CRON usage](doc/04-cron-usage.md)
* [CLI usage](doc/05-cli-usage.md)

You can see the current and past versions using one of the following:

* the `git tag` command
* the [releases page on Github](https://github.com/jolicode/JoliNotif/releases)
* the file listing the [changes between versions](CHANGELOG.md)

And finally some meta documentation:

* [versioning and branching models](VERSIONING.md)
* [contribution instructions](CONTRIBUTING.md)

## Credits

* [All contributors](https://github.com/jolicode/JoliNotif/graphs/contributors)
* This project was originally inspired by [mikaelbr/node-notifier](https://github.com/mikaelbr/node-notifier)

## License

JoliNotif is licensed under the MIT License - see the [LICENSE](LICENSE) file
for details.
