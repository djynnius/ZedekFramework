# Views and Templating

## Overview

Views are HTML files stored in `engines/<controller>/views/`. The framework provides two templating systems:

1. **ZView** -- The built-in native templating engine with variable substitution, conditionals, and loops.
2. **Twig 3.x** -- A full-featured Jinja-style templating engine (enabled via configuration).

## ZView Templating

### Template Variables

Pass an associative array of data to `render()` or `display()`:

```php
self::render("profile", [
    "name" => "John",
    "role" => "admin",
    "items" => ["apple", "banana", "cherry"]
]);
```

In the HTML view file, use double curly braces to output variables:

```html
<h1>Hello, {{ name }}</h1>
<p>Role: {{ role }}</p>
```

### Built-in Template Variables

These variables are always available in every view:

| Variable | Description |
|----------|-------------|
| `{{ app }}` | Application name from config |
| `{{ controller }}` | Current controller name |
| `{{ method }}` | Current method name |
| `{{ version }}` | Framework version |
| `{{ dir }}` | Application base directory |
| `{{ theme }}` | Current theme directory path |
| `{{ common }}` | Common theme directory path |
| `{{ this_year }}` | Current year (e.g., 2026) |
| `{{ this_month }}` | Current month name (e.g., March) |
| `{{ today }}` | Formatted date (e.g., Sunday, March 01, 2026) |
| `{{ now }}` | Timestamp (e.g., 2026-03-01 14:30:00) |

### Conditionals

Use the `{%if ... %}` syntax for inline conditionals:

```html
{%if [role] == admin ? <span>Admin Panel</span> : <span>User Panel</span> %}
```

This checks if the template variable `role` equals `admin` and outputs the corresponding HTML.

### Simple Loops (for)

Iterate over a simple list (one-dimensional array):

```html
{%for item in items : <li>{{ item }}</li> %}
```

Given `"items" => ["apple", "banana", "cherry"]`, this outputs:

```html
<li>apple</li>
<li>banana</li>
<li>cherry</li>
```

### Data Loops (foreach)

Iterate over an array of associative arrays (e.g., database records):

```html
{%foreach students as s : <tr><td>{{ s.name }}</td><td>{{ s.grade }}</td></tr> %}
```

Given:
```php
"students" => [
    ["name" => "Alice", "grade" => "A"],
    ["name" => "Bob", "grade" => "B"],
]
```

This outputs:

```html
<tr><td>Alice</td><td>A</td></tr>
<tr><td>Bob</td><td>B</td></tr>
```

Use dot notation (`s.name`, `s.grade`) to access fields within each record.

## Twig Templating

When Twig is enabled, `render()` uses the Twig 3.x engine instead of ZView. Twig provides a more powerful templating language with inheritance, filters, macros, and more.

### Enabling Twig

In `config/global.conf`:

```json
{
    "templating": {
        "engine": "twig",
        "path": "engines"
    }
}
```

### Twig View Files

Twig views use standard Twig syntax:

```html
<h1>Hello, {{ name }}</h1>

{% if role == 'admin' %}
    <a href="/admin">Admin Panel</a>
{% endif %}

{% for item in items %}
    <li>{{ item }}</li>
{% endfor %}
```

All built-in template variables are available in Twig views as well.

For full Twig documentation, see https://twig.symfony.com/doc/3.x/

## Dynamic Views (PHP)

Use `self::dynamic()` to render PHP view files. These are stored as `.php` files in the views folder and have access to template data via the `$self` object:

**Controller:**

```php
self::dynamic("report", ["title" => "Sales Report", "total" => 1500]);
```

**views/report.php:**

```php
<h1><?= $self->title ?></h1>
<p>Total: $<?= $self->total ?></p>
```
