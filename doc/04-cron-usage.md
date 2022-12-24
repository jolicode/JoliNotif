# CRON usage

## Configuration for CRON

Cronjobs are usually CLI scripts. But JoliNotif's Unix/Linux notifiers are GUI
applications. This means you need to specify the display where the notification
will be sent.

You can achieve this by setting the `XDG_RUNTIME_DIR` environment variable in your
cronjob:

```
* * * * * XDG_RUNTIME_DIR=/run/user/$(id -u) php /path/to/your/script
```

> **Note**
> On some system, you may also/instead need to set DISPLAY env var and/or to
> run `xhost +local:`. For more information, please have a look at
> [https://help.ubuntu.com/community/CronHowto#GUI_Applications](https://help.ubuntu.com/community/CronHowto#GUI_Applications)

## Next readings

* [CLI usage](05-cli-usage.md)

Previous pages:

* [Notifier](03-notifier.md)
* [Notification](02-notification.md)
* [Basic usage](01-basic-usage.md)
* [README](../README.md)
