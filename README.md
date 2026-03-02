Zedek 6
========

Zedek Web Development Framework version 6

A lightweight PHP MVC web development framework modernized for PHP 8.1+.

Features
--------

1. Model-View-Controller architecture
2. Object-oriented design
3. Encourages agile development
4. Built-in Object Relational Mapper (ZORM) with PDO support for MySQL, SQLite, PostgreSQL, and MSSQL
5. Jinja-like templating with Twig 3.x integration
6. Native ZView templating engine with conditionals, loops, and data iteration
7. Clean URL routing via URL rewriting -- no routing files required
8. Subfolder installation support
9. Internal helper classes for forms, authentication, image processing, sessions, and more
10. Works on Linux, macOS, and Windows

Requirements
------------

- PHP 8.1 or higher
- Composer
- Nginx or Apache with mod_rewrite (for production)
- Some knowledge of PHP (expert knowledge is not required)

Installation
------------

Clone the repository:

    git clone https://github.com/djynnius/ZedekFramework.git

Or install with Composer:

    composer create-project openimo/zedekframework

Then install dependencies:

    cd zedekframework
    composer install

Set permissions to allow reading and writing to the zedekframework folder.

### Development Server

Start the built-in PHP development server:

    php zedek 8585

You can now view your application at http://localhost:8585

You can specify any port:

    php zedek 3000

### Production Deployment (Nginx)

Point your Nginx root to the `public/` directory. Ensure `anchor.php` and the rest of the framework remain outside the web-accessible directory for security.

Example Nginx configuration:

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

### Production Deployment (Apache)

Move the contents of the `public/` folder to your Apache web directory. Update the path to `anchor.php` accordingly. Ensure mod_rewrite is enabled.

For subdirectory installation, set the `zsub` constant in `anchor.php`:

```php
def("zsub", "/subfolder/");
```

Hello World
-----------

Zedek maps URLs to engine directories and controller methods:

    http://localhost:8585/controller/method/id/?arg1=val1&arg2=val2

No routing files are required.

### Steps

1. Create a folder named `foo` inside the `engines/` directory.
2. Create `engines/foo/views/` directory.
3. Create `engines/foo/controller.php` with the following code:

```php
<?php
namespace __zf__;
class CController extends ZController {
    function bar(){
        print "Hello World";
    }
}
```

4. Browse to http://localhost:8585/foo/bar

You should see your Hello World message.

Project Structure
-----------------

```
zedekframework/
    config/         - Configuration files (JSON)
    core/           - Framework core classes
    engines/        - Application controllers and views (MVC)
        default/    - Default landing controller
    internals/      - Helper classes (forms, auth, image, etc.)
    models/         - Data models
    public/         - Web-accessible directory (document root)
        themes/     - Theme folders with header.html and footer.html
    databases/      - SQLite database files
    errors/         - Error log files
    sessions/       - Session storage
    vendor/         - Composer dependencies
    composer.json   - Composer configuration
    initializer.php - Framework bootstrap
    zedek           - CLI development server script
```

Documentation
-------------

Full documentation is available in the [docs/](docs/) directory:

- [00 - Introduction](docs/00-introduction.md)
- [01 - Installation](docs/01-installation.md)
- [02 - Engines](docs/02-engines.md)
- [03 - Controllers](docs/03-controllers.md)
- [04 - Views and Templating](docs/04-views.md)
- [05 - Theming](docs/05-theming.md)
- [06 - ZORM](docs/06-zorm.md)
- [07 - Models](docs/07-models.md)
- [08 - Internals](docs/08-internals.md)
- [09 - Configuration](docs/09-configuration.md)

What's New in Version 6
-----------------------

- **PHP 8.1+ required** -- fully compatible with PHP 8.1 through 8.4
- **Twig 3.x** -- modern Twig templating via Composer with namespaced classes
- **Composer autoloading** -- dependencies managed through Composer
- **Removed deprecated functions** -- replaced `strftime()`, `utf8_encode()`, deprecated filter constants, and `(integer)` casts
- **Bug fixes** -- fixed `rollbackTransaction()`, `func_num_args()` misuse, image quality parameters
- **Declared class properties** -- eliminates PHP 8.2 dynamic property deprecation warnings

License
-------

Openimo
