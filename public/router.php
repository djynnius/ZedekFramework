<?php

/**
* @package Zedek Framework
* @subpackage ZController zedek super controller class
* @version 4
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedekframework
* @link https://github.com/djynnius/zedekframework.git
*/

require_once("zettings");

Router::webRoot();
Router::webSubFolder();
/**
* you may override default by entering path to core
* eg /path/to/my/zedek or on windows c:\\path\\to\\my\\zedek
*/
Router::anchor(); 