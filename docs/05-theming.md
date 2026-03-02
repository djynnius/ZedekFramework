# Theming

## Overview

Themes control the common header and footer that wrap around your views when using `render()`. Themes are stored in the `public/themes/` directory.

## Theme Structure

```
public/themes/
    default/
        header.html
        footer.html
        css/
            style.css
        js/
            app.js
        images/
            logo.png
    dark/
        header.html
        footer.html
        css/
        js/
        images/
```

Each theme folder must contain at least:

- **header.html** -- HTML rendered before the view content
- **footer.html** -- HTML rendered after the view content

Additional asset folders (css, js, images) are organized as you see fit.

## Setting the Active Theme

Configure the active theme in `config/global.conf`:

```json
{
    "theme": "default"
}
```

Change `"default"` to the name of any theme folder in `public/themes/`.

## Linking Theme Assets

Use template variables in your `header.html` and `footer.html` to reference assets:

### Current Theme Assets

Use `{{ theme }}` to reference assets from the active theme:

```html
<link rel="stylesheet" href="{{ theme }}/css/style.css">
<script src="{{ theme }}/js/app.js"></script>
<img src="{{ theme }}/images/logo.png" alt="Logo">
```

### Common Assets

Use `{{ common }}` to reference assets shared across all themes:

```html
<link rel="stylesheet" href="{{ common }}/css/shared.css">
```

This points to `public/themes/common/`.

### Cross-Theme Assets

Use `{{ dir }}` to reference assets from a specific theme:

```html
<link rel="stylesheet" href="{{ dir }}/themes/dark/css/dark-mode.css">
```

## Per-Request Theme Override

You can specify a different theme for a specific render call by passing the theme name as the third argument:

```php
self::render("page", ["title" => "Hello"], "dark");
```

This uses `public/themes/dark/header.html` and `footer.html` instead of the default theme.

## Custom Template Variables in Themes

You can define additional template variables in `config/tpl.conf` as a JSON object:

```json
{
    "site_name": "My Application",
    "copyright": "2026 My Company"
}
```

These become available as `{{ site_name }}` and `{{ copyright }}` in all views and theme files.

## Example Theme

**header.html:**

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ app }} - {{ method }}</title>
    <link rel="stylesheet" href="{{ theme }}/css/style.css">
</head>
<body>
    <nav>
        <a href="{{ dir }}/">{{ app }}</a>
    </nav>
    <main>
```

**footer.html:**

```html
    </main>
    <footer>
        <p>{{ sub_version }} &copy; {{ this_year }}</p>
    </footer>
    <script src="{{ theme }}/js/app.js"></script>
</body>
</html>
```
