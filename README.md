Zedek 4
========

Zedek Web Development Framework version 5

This is a PHP web development framework. 

The features include:

1. Model-View-Controller
2. Object Orientation
3. Encourages Agile development
4. Has an Object Relational Mapper (ORM) built in called ZORM
5. Has a templating engine accessed through a class called ZView and more recently implements Jinja like templating with PHP twig
6. Templating engines allows some logic in the html view file such as looping through an array with the option of including raw php in the markup 
7. URL rewriting allowing for clean urls, and sub folder installation with the inclusion now of Routes through the ZRoute class
8. Works on Unix, unix-like and Windows Operating Systems

Requirements
=============
1. Apache
2. PHP5.4+ preferrably PHP7
3. Some knowledge of PHP and MVC framework development (expert knowledge isnt required)

Creating your first application follow these steps (Simple as 1-2-3):
======================================================================

Download this repo and extract so you have a folder named "zedek" or what ever else you want to call it in a non web accessible folder (one of the security features of Zedek Framework). You can either download the zip file or easier clone 

	git clone https://github.com/djynnius/ZedekFramework.git

For those comfortable with composer you can install with the command:

    composer create-project openimo/zedekframework

set persmissions to allow reading and writing to zedekframework folder

Change directory into your zedekframework folder and run :

    php zedek start

on windows replace php with the path to the php binary - example:
	
	c:\xampp\php\php.exe zedek start


You are done! view your app on localhost:8585

You can now view your application on localhost:8585


Hello World!
============

Zedek 4 is built to map urls to engine directories and methods of the class CController (for current controller) in a style:

    http://localhost:8585/controller/method/id/?arg1=val1&arg2=val2...$argn=valn

(this mapping is handled primarily by a class named URLMaper) 

No routing files are required.

The MVC is made literal within the engine folder. 

1. To create a new app named foo create a folder with the name foo within the engines folder.
2. within this create a class file "controller.php".

next within the controller file enter the following code inside your php tags

    <?php
    namespace __zf__;
    class CController extends ZController{
        function bar(){
            print "Hello World";
        }
    }
    

3. Browse to http://localhost:8585/foo/bar

and you should see your hello world message!

Congratulations!
