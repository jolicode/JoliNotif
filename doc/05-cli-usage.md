# CLI usage

## Installation

Install package globally:

```bash
$ composer global require jolicode/jolinotif
```

**Note!** Make sure to place the `~/.composer/vendor/bin` directory (or the equivalent directory for your OS) in your PATH so the transfer executable can be located by your system. Simply add this directory to your PATH in your `~/.bashrc` (or `~/.bash_profile`) like this:

```
$ echo "export PATH=~/.composer/vendor/bin:$PATH" >> ~/.bashrc
$ source ~/.bashrc
```

## Usage

```bash
jolinotif --title "Awesome notification" --body "This is quite a cool cross-platform notification!"
```

To get help just run:

```bash
jolinotif --help
```

In case of troubles use following format for passing the param: `--param="value"`.  
For required params (title, body) equality sign and quotes can be omitted. 

## Next readings

Previous pages:

* [Advanced usage](04-advanced-usage.md)
* [Notifier](03-notifier.md)
* [Notification](02-notification.md)
* [Basic usage](01-basic-usage.md)
* [README](../README.md)
