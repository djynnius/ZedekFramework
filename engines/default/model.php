<?php
namespace __zf__;
use \PDO as PDO;
class CModel extends ZModel{
	function _default(){
		echo $this->template('index')->render();
	}
}
?>