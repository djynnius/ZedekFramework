<?php

/**
* @package Zedek Framework
* @version 5
* @subpackage ZConfig zedek configuration class
* @author defestdude <defestdude@gmail.com> Donald Mkpanam
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

require_once("zettings");

Router::webRoot();
Router::webSubFolder();
/**
* you may override default by entering path to core
* eg /path/to/my/zedek or on windows c:\\path\\to\\my\\zedek
*/
Router::anchor(); 