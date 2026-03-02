# Internals -- Helper Classes

## Overview

Internals are helper classes stored in the `internals/` directory. They are automatically loaded by the framework and provide utility functions for common tasks. All internal classes live in the `__zf__` namespace.

## _Form -- Form Handling and Validation

### Input Preparation

```php
// Sanitize a single string (addslashes + trim)
$clean = _Form::prepare($input);

// Sanitize an array of strings
$clean = _Form::batchPrepare($array);

// Sanitize a $_POST array (removes "submit" key, encrypts password field)
$data = _Form::prepareArray();
$data = _Form::prepareArray($customArray);
```

### Form Submission Detection

```php
// Check if a form was submitted
if(_Form::posted()){
    // handle form
}

// Check for a specific submit button
if(_Form::submitted("save")){
    // handle save
}
```

### Field Comparison

```php
// Compare two values
_Form::compare($password, $confirmPassword);

// Compare array of values (all must match)
_Form::compare([$val1, $val2, $val3]);
```

### Date Utilities

```php
_Form::today();    // "2026-03-01"
_Form::now();      // "2026-03-01 14:30:00"

// Normalize date formats to Y-m-d
_Form::fixDate("01-03-2026");    // "2026-03-01"
_Form::fixDate("03/01/2026");    // "2026-03-01"
```

### Sanitization Filters

```php
_Form::asString($input);    // HTML entity encoding
_Form::asEmail($input);     // Email sanitization
_Form::asURL($input);       // URL encoding
_Form::asInt($input);       // Integer sanitization
_Form::noTags($input);      // Special character encoding
_Form::safeSQL($input);     // Addslashes for SQL
```

### Encryption

```php
// Long hash (SHA1 + MD5 + crypt)
$hash = _Form::encrypt($text);
$hash = _Form::encrypt($text, "long");

// Short hash
$hash = _Form::encrypt($text, "short");
```

### Pattern Matching

```php
_Form::matching($input, '/^[a-z]+$/');
```

## _Auth -- Authentication

### Configuration

```php
_Auth::table("users");          // Set the users table
_Auth::handle("email");          // Set the login handle field
_Auth::pwd("password");          // Set the password field
```

### Login and Logout

```php
// Login -- returns user object on success, false on failure
$user = _Auth::login($email, $password);

if($user){
    $_SESSION['user'] = $user->id;
    _Auth::setLastLogin();
}

// Logout -- destroys session
_Auth::logout();
```

### Authorization

```php
// Check if a session variable is set
if(_Auth::authorized($_SESSION['user'])){
    // user is logged in
}

// Restrict access -- redirects if not authorized
_Auth::restricted($_SESSION['user'], "login");
```

### User Management

```php
_Auth::addUser(["email" => "new@example.com", "password" => _Bcrypt::hash("secret")]);
_Auth::delUser($id);
_Auth::updateUser($id, ["email" => "updated@example.com"]);
```

### Password Management

```php
// Change password (validates old password)
_Auth::changePassword($oldPwd, $newPwd, $confirmPwd, $userId);

// Reset password (admin override)
_Auth::resetPassword($userId, $newPassword);
```

## _Bcrypt -- Password Hashing

```php
// Hash a password
$hash = _Bcrypt::hash("mypassword");

// Verify a password against a hash
if(_Bcrypt::compare("mypassword", $hash)){
    // password matches
}
```

## _Image -- Image Processing

### Get File Extension

```php
$ext = _Image::extension("photo.jpg");  // "jpg"
$ext = _Image::ext("photo.png");        // "png"
```

### Crop an Image

```php
_Image::crop($source, $target, $width, $height, $extension);

// Example: crop to 256x256
_Image::crop("/path/to/original.jpg", "/path/to/cropped.jpg", 256, 256, "jpeg");
```

### Resize an Image

```php
_Image::resize($source, $target, $width, $height, $mime);

// Example: resize to 512x512 (maintains aspect ratio)
_Image::resize("/path/to/original.jpg", "/path/to/resized.jpg", 512, 512, "jpeg");
```

Supported formats: `jpeg`, `png`, `gif`

## _Session -- Session Management

Sessions are automatically started by the framework. Session files are stored in the `sessions/` directory.

## _CAPTCHA -- CAPTCHA Verification

```php
// Verify CAPTCHA (compares POST input to session value)
if(_Form::captcha()){
    // CAPTCHA passed
}

// With custom values
if(_Form::captcha($requestValue, $sessionValue)){
    // CAPTCHA passed
}
```
