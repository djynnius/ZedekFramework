# Controllers

## Overview

Every engine has a controller defined in `engines/<name>/controller.php`. The controller class must be named `CController` and extend `ZController`. All classes live in the `__zf__` namespace.

```php
<?php
namespace __zf__;
class CController extends ZController {
    function index(){
        self::render();
    }
}
```

## Inherited Properties

Controllers automatically have access to:

| Property | Type | Description |
|----------|------|-------------|
| `$this->uri` | `ZURI` | URL parameters and server info |
| `$this->app` | `App` | Application model instance |
| `$this->config` | `ZConfig` | Framework configuration |

## The `index()` Method

Every controller inherits an `index()` method. It serves as the default method when no method is specified in the URL. Override it to define your landing page:

```php
function index(){
    self::render("home", ["title" => "Welcome"]);
}
```

## Rendering Views

Controllers provide several methods for outputting views:

### `render($view, $data, $theme)`

Renders a themed view (wrapped in `header.html` and `footer.html` from the active theme). If Twig is enabled in configuration, this uses the Twig engine.

```php
self::render("dashboard", ["user" => $username]);
```

### `display($view, $data, $theme)`

Renders an unthemed view -- just the raw HTML template without header/footer.

```php
self::display("widget", ["items" => $items]);
```

### `dynamic($view, $data, $theme)`

Renders a PHP view file from the `views/` folder. This allows embedded PHP execution within the view.

```php
self::dynamic("report", ["data" => $records]);
```

Inside a dynamic view file (`views/report.php`), template variables are accessible via `$self`:

```php
<h1><?= $self->data ?></h1>
```

### Cross-Engine Views

Reference a view from another engine using the `@` syntax:

```php
self::render("shared_header@common");
// Loads engines/common/views/shared_header.html
```

### Specifying a Theme

Pass a theme name as the third argument to use a theme other than the default:

```php
self::render("page", ["title" => "Hello"], "dark");
```

## Redirects

The `redirect()` method handles navigation:

```php
self::redirect("foo");              // -> /foo
self::redirect("foo", "bar");       // -> /foo/bar
self::redirect("foo", "bar", 5);    // -> /foo/bar/5
self::redirect(0);                  // -> same page (self)
self::redirect("self");             // -> same page
self::redirect(-1);                 // -> previous page (referrer)
self::redirect("back");             // -> previous page
self::redirect();                   // -> home page
```

## ZURI -- URL Parameters

The `$this->uri` object gives access to URL components:

| Property | Description |
|----------|-------------|
| `$this->uri->controller` | Current controller name |
| `$this->uri->method` | Current method name |
| `$this->uri->id` | The ID segment from the URL |
| `$this->uri->gets` | Query string parameters |
| `$this->uri->dir` | Application base directory |

## Example Controller

```php
<?php
namespace __zf__;
class CController extends ZController {

    function index(){
        ZORM::table("posts");
        $posts = ZORM::read();
        self::render("index", ["posts" => $posts]);
    }

    function show(){
        $id = $this->uri->id;
        ZORM::table("posts");
        $post = ZORM::record($id);
        self::render("show", ["post" => $post]);
    }

    function create(){
        if(_Form::posted()){
            $data = _Form::prepareArray();
            ZORM::table("posts");
            ZORM::add($data);
            self::redirect("posts");
        }
        self::render("create");
    }
}
```
