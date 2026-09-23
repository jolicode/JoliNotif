# CLI usage

## Installation

There is two ways to install JoliNotif for a CLI usage.

### Install package globally with Composer

```bash
$ composer global require jolicode/jolinotif
```

> **Note**
> Make sure to place the `~/.composer/vendor/bin` directory (or the equivalent
> directory for your OS) in your PATH so the transfer executable can be located
> by your system. Simply add this directory to your PATH in your `~/.bashrc`
> (or `~/.bash_profile`) like this:

```
$ echo "export PATH=~/.composer/vendor/bin:$PATH" >> ~/.bashrc
$ source ~/.bashrc
```

### Download the PHAR executable

You can download the latest version of JoliNotif as a PHAR file from the [releases
page](https://github.com/jolicode/JoliNotif/releases):

```bash
curl https://github.com/jolicode/JoliNotif/releases/latest/download/jolinotif.phar && sudo mv jolinotif.phar /usr/local/bin/jolinotif
```

### PHAR resource cache

When running from a PHAR, JoliNotif extracts embedded binaries and icons into a
per-user cache:

* Linux, macOS, and other Unix systems: `$HOME/.jolinotif`.
* Windows: `%LOCALAPPDATA%\JoliNotif`.

The base location must be trusted and writable by the current user.

## Usage

```bash
jolinotif --title "Awesome notification" --body "This is quite a cool cross-platform notification!"
```

To get help just run:

```bash
jolinotif --help
```

To output debug information, add the `--verbose` flag:

```bash
jolinotif --title "..." --body "..." --verbose
```

In case of troubles use following format for passing the param: `--param="value"`.  
For required params (title, body) equality sign and quotes can be omitted. 

## Next readings

Previous pages:

* [CRON usage](04-cron-usage.md)
* [Drivers](03-drivers.md)
* [Notification](02-notification.md)
* [Basic usage](01-basic-usage.md)
* [README](../README.md)
