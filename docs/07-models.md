# Models

## Overview

Models in Zedek extend the abstract `ZModel` class and provide a structured way to interact with database tables. Models live in the `models/` directory and are automatically loaded by the framework.

## Creating a Model

Create a PHP file in the `models/` directory:

**models/Post.php:**

```php
<?php
namespace __zf__;
class Post extends ZModel {

    public $table = "posts";

    function __construct(){
        parent::__construct();
    }

    function all(){
        ZORM::table($this->table);
        return ZORM::read();
    }

    function getById($id){
        return $this->findOne($id);
    }

    function getByAuthor($authorId){
        ZORM::table($this->table);
        return ZORM::find("author_id", $authorId);
    }
}
```

## Inherited Properties

| Property | Type | Description |
|----------|------|-------------|
| `$this->table` | `string` | The database table this model represents |
| `$this->uri` | `ZURI` | URL parameter access |

## Inherited Methods

### CRUD Operations

```php
// Add a record
$this->add(["title" => "New Post", "body" => "Content"]);

// Alias for add
$this->create(["title" => "New Post", "body" => "Content"]);

// Find one record by ID
$record = $this->findOne(1);

// Find one record by column
$record = $this->findOne("slug", "my-post");

// Update a record by ID
$this->update(1, ["title" => "Updated Title"]);

// Delete a record
$this->remove(1);

// Alias for remove
$this->delete(1);
```

### Existence Checks

```php
// Check if a record exists by value and column
$this->exists("john@example.com", "email");

// Check if a pair of values exist
$this->pairExists($userId, "user_id", $postId, "post_id");
```

### Navigation

```php
$prev = $this->findPrev($currentId);
$next = $this->findNext($currentId);
```

### Row Count

```php
$total = $this->rowCount();
```

### Template Integration

Models can push their properties directly into the template array:

```php
$post = new Post();
$post->title = "Hello World";
$post->body = "Some content";

$template = [];
$post->appendToTemplate($template);
// $template now contains ["title" => "Hello World", "body" => "Some content"]
```

### UTF-8 Encoding

For records with ISO-8859-1 encoded data:

```php
$records = $this->UTF8EncodeRecords($records);
```

## Using Models in Controllers

```php
<?php
namespace __zf__;
class CController extends ZController {

    function index(){
        $post = new Post();
        $all = $post->all();
        self::render("index", ["posts" => $all]);
    }

    function show(){
        $post = new Post();
        $record = $post->getById($this->uri->id);
        self::render("show", ["post" => $record]);
    }
}
```
