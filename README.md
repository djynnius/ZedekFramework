Zedek2.1
========

Zedek Web Development Framework version 2.1

This is a PHP web development framework built as a very light framework while taking from some of the nicer modern ideas. 

The features include:

1. Model View Controller
2. Object Oriented
3. Encourages agile development
4. Has an Object Relational Model (ORM) built in called ZORM
5. Has a templating engine accessed from a class called ZView
6. Templating engines allows some logic in the html view file such as looping through an array 
7. URL rewriting allowing for clean urls

Requirements
=============

1. Apache
2. PHP5.3+
3. Knowledge of PHP
4. Knowledge of Object Oriented Programming (OOP)
5. PHPUnit and understanding of Test Driven Development (TDD) in PHP


Creating your first application follow these steps:
===================================================

1. Download this repo and extract so you have a folder named "zedek" or what ever else you want to call it in a non web accessible folder. This is one of the security features of Zedek2.0.
2. in your web accessible folder (web root) u will require 3 files and a folder being a ".htaccess" file, a router file named as you desire such as "router.php", a "favicon.ico" file and a folder for your public files. To get these you may copy them out of the /zedek/public folder or make this folder your web folder by creating a virtual host for this folder
3. The contents of the .htaccess file should redirect all traffic to the router file while excluding the public folder contents and any other folders you define.

* You may also copy the contents of the /zedek/public/ folder into your web root or create a virtual host that points to that folder. 

## .htaccess contents ##

    RewriteEngine On
    RewriteCond %{REQUEST_URI} !/images/.*$ 
    RewriteCond %{REQUEST_URI} !/fonts/.*$ 
    RewriteCond %{REQUEST_URI} !/stylesheets/.*$ 
    RewriteCond %{REQUEST_URI} !/scripts/.*$ 
    RewriteCond %{REQUEST_URI} !/favicon\.ico$
    RewriteRule ^(.*)$ router.php

*Ensure you have mod_rewrite enabled and properly configured


## router.php contents ##

    <?php
      require_once "/path/to/zedek/controller.php";
    ?>
    
and you are about done with the web parts.

on a windows machine it would look more like:

    <?php
        require_once "drive:\\path\\to\\controller.php";
    ?>

## initializer.php ##
Within the controller file on line 7 set the root constant to the path leading to the zedek app ending with a trailing slash

    const zroot="/path/to/zedek/";
    const zweb="/path/to/web/folder/";


Once done you should see your app on your website with a successful install message.


Hello World!
============

Zedek 2.1 is built to map urls to classes and methods in a style:
http://mysite.com/class/method/arguments
(this mapping is handled primarily by a class named URLMaper)

the MVC is made literal within the engine folder. 

1. To create a new app called foo create a folder with the name foo within the engines folder.
2. within this create a class file "model.php".
3. within the model file enter the following code inside your php tags

## ##

    class CModel extends ZModel{
      function bar(){
        echo "Hello World";
      }
    }

4. Browse to http://mysite.com/foo/bar

and you should see your hello world message!
