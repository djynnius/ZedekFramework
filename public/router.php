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
Router::anchor(); /*you may override default by entering a path to anchor file as argument*/