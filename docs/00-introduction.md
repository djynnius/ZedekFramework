# Introduction

## What is Zedek Framework?

Zedek Framework is a lightweight PHP MVC web development framework. It emphasizes simplicity, object-oriented design, and agile development practices. Originally created in 2011, the framework has evolved through several major versions, with version 6 bringing full PHP 8.4 compatibility.

## Core Features

- **MVC Architecture** -- Controllers, Views, and Models are organized into discrete "engines" that map directly to URL paths.
- **Object-Oriented Design** -- All framework components are classes within the `__zf__` namespace.
- **ZORM** -- A built-in Object Relational Mapper supporting MySQL, SQLite, PostgreSQL, and MSSQL via PDO.
- **Dual Templating** -- Choose between the native ZView engine (with conditionals and loops in HTML) or Twig 3.x for Jinja-style templates.
- **Clean URLs** -- URLs map directly to controllers and methods with no routing files needed.
- **Security by Design** -- The framework core lives outside the web-accessible directory. Only the `public/` folder is exposed.
- **Internal Helpers** -- Built-in classes for form handling, authentication, bcrypt hashing, image manipulation, CAPTCHA, LDAP, email, and sessions.

## Architecture

Zedek is split into two parts:

1. **Non-web component** -- The framework core (`core/`), engines (`engines/`), models (`models/`), internals (`internals/`), and configuration (`config/`). These live outside the document root and are never directly accessible via the web.

2. **Web component** -- The `public/` directory serves as the document root. It contains themes (CSS, JS, images), uploads, and the front controller (`zedek`/`app.php`) that bootstraps the framework.

## System Requirements

- PHP 8.1 or higher
- Composer (for dependency management)
- Nginx or Apache with mod_rewrite
- PDO extension (with drivers for your database of choice)
- mbstring extension
- GD extension (for image processing features)

## Version History

| Version | Highlights |
|---------|------------|
| 0.1 - 3.0 | Foundation, core MVC concepts |
| 5.x | Twig integration, subfolder installs, Windows support, internal helpers |
| **6.0** | PHP 8.1-8.4 compatibility, Twig 3.x, Composer autoloading, bug fixes |
