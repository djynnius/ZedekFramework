# Installation

## Requirements

- PHP 8.1+
- Composer
- A web server (Nginx or Apache) for production, or the built-in PHP server for development

## Getting the Framework

### Via Git

```bash
git clone https://github.com/djynnius/ZedekFramework.git
cd ZedekFramework
composer install
```

### Via Composer

```bash
composer create-project openimo/zedekframework
cd zedekframework
```

## File Permissions

Set read and write permissions on the framework folder:

```bash
chmod -R 755 zedekframework
chmod -R 777 zedekframework/sessions
chmod -R 777 zedekframework/errors
chmod -R 777 zedekframework/databases
```

## Development Server

Zedek includes a CLI script that starts PHP's built-in development server:

```bash
php zedek 8585
```

This serves the application at `http://localhost:8585`. You can specify any port number as the first argument.

## Production Deployment

### Nginx

Point the Nginx document root to the `public/` directory. The rest of the framework must remain outside the web root.

```nginx
server {
    listen 80;
    server_name example.com;
    root /path/to/zedekframework/public;
    index zedek;

    location / {
        try_files $uri $uri/ /zedek?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Apache (Web Root)

1. Copy the contents of `public/` to your Apache document root (e.g., `/var/www/html/`).
2. Update the path to `anchor.php` inside the front controller to point back to the framework directory.
3. Ensure `mod_rewrite` is enabled.

### Apache (Subdirectory)

1. Place the `public/` contents in a subfolder (e.g., `/var/www/html/myapp/`).
2. Set the `zsub` constant in `anchor.php`:

```php
def("zsub", "/myapp/");
```

3. Ensure `mod_rewrite` is enabled and `.htaccess` files are allowed.

## URL Mapping

Zedek maps URLs to controllers and methods automatically:

```
http://example.com/controller/method/id/?arg1=val1&arg2=val2
```

No routing files are required. The URL structure maps directly to the engine folder name, the method in `CController`, and optional parameters.

## Quick Test

After installation, visit `http://localhost:8585` to see the default landing page. If it loads without errors, the framework is working correctly.
