<?php

namespace djynnius\zedekframework;

use ZORM as ORM;

abstract class Zlibs implements ZIlib{
	
	function __construct(){
		$this->orm = new ORM();
		$this->_init();
	}

	function _init(){}
}

interface ZIlib{
	function _init();
}

?>