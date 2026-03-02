# Configuration

## Overview

Zedek uses JSON configuration files stored in the `config/` directory. The `ZConfig` class provides a getter/setter interface to these files.

## Configuration Files

### config/global.conf

Main application settings:

```json
{
    "app": "zedek",
    "error": "On",
    "log_errors": "On",
    "webUnitTest": "On",
    "email": "app@zedek.io",
    "theme": "default",
    "templating": {
        "engine": "twig",
        "path": "engines"
    },
    "encryptionKey": "your_encryption"
}
```

| Setting | Description |
|---------|-------------|
| `app` | Application name |
| `error` | Display errors (`On`/`Off`). Use `Off` in production. |
| `log_errors` | Log errors to file (`On`/`Off`) |
| `webUnitTest` | Enable web-based unit testing |
| `email` | Default application email |
| `theme` | Active theme folder name |
| `templating.engine` | Templating engine: `twig` or `zview` |
| `templating.path` | Base path for templates (relative to framework root) |
| `encryptionKey` | Encryption key for `_Form::encrypt()`. Change this from the default. |

### config/db.conf

Database connection settings:

```json
{
    "adapter": "sqlite",
    "host": "localhost",
    "port": "3306",
    "db": "default",
    "user": "",
    "pass": ""
}
```

| Setting | Description |
|---------|-------------|
| `adapter` | Database driver: `mysql`, `sqlite`, `postgre`, `mssql` |
| `host` | Database host |
| `port` | Database port |
| `db` | Database name. For SQLite, `default` uses `databases/app.db` |
| `user` | Database username |
| `pass` | Database password |

### config/version.conf

Framework version metadata:

```json
{
    "version": "6",
    "sub_version": "6.0.0.01032026",
    "change_log": "Modernized framework for PHP 8.4 compatibility with Twig 3.x integration"
}
```

### config/tpl.conf

Custom template variables available in all views:

```json
{
    "site_name": "My Application",
    "copyright": "2026 My Company"
}
```

Any key-value pair added here becomes a `{{ key }}` variable in templates.

## Using ZConfig in Code

### Reading Settings

```php
$config = new ZConfig;                    // Loads global.conf
$theme = $config->get("theme");           // "default"
$engine = $config->get("templating");     // Object with ->engine and ->path

$version = new ZConfig("version");        // Loads version.conf
$ver = $version->get("version");          // "6"

$global = new ZConfig("global");          // Explicit global.conf
$app = $global->get("app");              // "zedek"
```

### Writing Settings

```php
$config = new ZConfig;
$config->set("theme", "dark");            // Updates global.conf
```

## Error Logging

Error display and logging are controlled by `global.conf` and applied in `initializer.php`:

- Errors are logged to `errors/errors.log`
- Set `"error": "Off"` in production to hide errors from users
- Set `"log_errors": "On"` to capture errors in the log file
