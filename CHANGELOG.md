# Changes between versions

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
