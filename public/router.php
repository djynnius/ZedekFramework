<?php

/**
* @package Zedek Framework
* @subpackage ZController zedek super controller class
* @version 3
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedekframework
* @link https://github.com/djynnius/zedekframework.git
*/

require_once("zettings");

Router::webRoot();
Router::webSubFolder();
/**
* you may override default by entering to core ending with trailing slash
* eg /path/to/my/zedek/ or on windows c:\\path\\to\\my\\zedek\\
*/
Router::anchor(); 