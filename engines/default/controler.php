<?php
namespace __zf__;
use \PDO as PDO;
class CControler extends ZControler{
	function __call($method, $args){
		parent::__call($method, $args);
	}
}
?>