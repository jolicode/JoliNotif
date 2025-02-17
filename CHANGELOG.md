# Changes between versions

## Not released yet

## 3.1.0 (2025-02-17)

* Added support for sound option on TerminalNotifier notifier
* Add support for PHP 8.4
* Drop support for PHP 8.1
* Drop support for Symfony 6.0 - 6.3, 7.0

## 3.0.0 (2024-10-02)

* Remove deprecated code:
  * Joli\JoliNotif\NotifierFactory
  * Joli\JoliNotif\Notifier interface and all implementations
  * Toaster notifier and binary
  * Joli\JoliNotif\Util\OsHelper class

## 2.7.3 (2024-09-30)

* Fixed SnoreToast and Notifu Windows drivers to be non blocking

## 2.7.2 (2024-06-01)

* Changed requirement on psr/log to allow all versions from 1.0 to 3.0

## 2.7.1 (2024-05-03)

* Fixed phar for Windows drivers
* Fixed executable in verbose mode when no driver is available
* Fixed executable to better handle option passed several times

## 2.7.0 (2024-05-03)

* Added a new NotifierInterface and DefaultNotifier as the main public API of this package
* Added wsl-notify-send notifier for Windows Subsystem for Linux
* Added libnotify based notifier for Linux through FFI
* Changed TerminalNotifier to use contentImage option for icon instead of appIcon
* Fixed phar missing some dependencies
* Marked most of the classes as internal
* Deprecated all the notifiers classes in favor of the new internal DriverInterface implementations
* Deprecated the NotifierFactory in favor of the new DefaultNotifier class that hide driver implementation details

## 2.6.0 (2023-12-03)

* Deprecated Joli\JoliNotif\Util\OsHelper in favor of jolicode/php-os-helper package
* Added support for Symfony 7.x
* Added support for PHP 8.3
* Dropped support for PHP 8.0

## 2.5.2 (2023-05-24)

* Added PHAR to GitHub releases

## 2.5.1 (2023-05-24)

* Fixed permissions on Windows notifiers binaries

## 2.5.0 (2022-12-24)

* Dropped support for PHP 7.4
* Dropped support for Symfony 4.x
* Added support for PHP 8.2

## 2.4.0 (2021-12-01)

* Dropped support for PHP < 7.4
* Dropped support for Symfony 3.x
* Added support for Symfony 6.x

## 2.3.0 (2021-03-07)

* Added SnoreToastNotifier to fix notification on Windows 8+
* Deprecated ToasterNotifier in favour of SnoreToastNotifier
* Added support for PHP 8

## 2.2.0 (2020-06-17)

* Added support for kdialog - the native notifier on KDE Desktop

## 2.1.0 (2020-01-10)

* Added support for Symfony > 5.0
* Dropped support for PHP < 7.2

## 2.0.2 (2019-02-26)

* Fixed compatibility with Windows Subsystem for Linux
* Fixed compatibility with Symfony 4.2

## 2.0.1 (2018-04-04)

* Fixed autoloader in CLI script

## 2.0.0 (2018-02-12)

* Dropped support for Symfony < 3.3
* Dropped support for PHP < 7 & HHVM
* Fixed compatibility with Symfony 4
* Added better CI configuration
* Added typehints everywhere

## 1.3.0 (2018-01-25)

* Added binary script to run jolinotif in CLI

## 1.2.0 (2017-07-17)

* Added support for sound and subtitle option on AppleScript notifier
* Added support for url option on TerminalNotifier notifier

## 1.1.2 (2017-07-13)

* Fixed compatibility with symfony/process 3.3

## 1.1.1 (2017-06-03)

* Fixed Notification icon to always use a canonical absolute path

## 1.1.0 (2017-04-04)

* Added NotifierFactory::createOrThrowException() method
* Added NullNotifier to get ride of null value
* Updated php-cs-fixer to v2.0 and fixed CS

## 1.0.5 (2016-03-22)

* Allowed Symfony 3
* Fixed quote escaping in AppleScriptNotifier

## 1.0.4 (2015-06-20)

* Fixed Mac OSX version detection
* Removed allowed failure for PHP 7 in Travis
* Added documentation for usage inside cronjobs

## 1.0.3 (2015-06-03)

* Fixed documentation typo
* Added easy example debugging

## 1.0.2 (2015-04-13)

* Fixed NotifierFactory phpdoc
* Added precision about factory returning null

## 1.0.1 (2015-04-01)

* Fixed Symfony version to 2.3
* Fixed Notifier phpdoc
* Added brand new documentation

## 1.0 (2015-03-20)

* Initial release
