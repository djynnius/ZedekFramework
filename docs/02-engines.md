# Engines

## Overview

Engines are the core building blocks of a Zedek application. Each engine is a self-contained MVC unit consisting of a controller and its views, stored in the `engines/` directory.

## Structure

```
engines/
    default/            - Default engine (landing page, fallback)
        controller.php
        views/
    foo/                - A custom engine named "foo"
        controller.php
        views/
            index.html
            bar.html
```

Each engine folder contains:

- **controller.php** -- A class named `CController` extending `ZController`
- **views/** -- A folder containing HTML view files or PHP view files

## URL Mapping

The engine folder name becomes the first segment of the URL:

```
http://example.com/foo/        -> engines/foo/ -> CController::index()
http://example.com/foo/bar     -> engines/foo/ -> CController::bar()
http://example.com/foo/bar/5   -> engines/foo/ -> CController::bar() with id=5
```

## Default Engine

The framework includes a `default` engine that handles:

- The application landing page (`http://example.com/`)
- Fallback for requests to non-existent controllers

## Creating Engines

### Manually

1. Create a folder in `engines/` with your engine name.
2. Create `controller.php` inside it.
3. Create a `views/` subfolder.
4. Define the `CController` class:

```php
<?php
namespace __zf__;
class CController extends ZController {
    function index(){
        print "Welcome to Foo";
    }
}
```

### Programmatically

Use the `create()` method from any existing controller:

```php
<?php
namespace __zf__;
class CController extends ZController {
    function index(){
        self::create("foo");
    }
}
```

This generates the engine folder, `controller.php` from a template, and the `views/` directory automatically.
