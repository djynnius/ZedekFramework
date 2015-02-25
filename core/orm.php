<?php
/**
* @package Zedek Framework
* @subpackage ZORM zedek ORM class
* @version 3
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

namespace __zf__;
use \PDO as PDO, \PDOException as PDOException, \Exception as Exception;
class ZORM extends Zedek{
	
	public $dbo;
	protected $host;
	protected $user;
	protected $pass;
	protected $name;
	protected $adapter;
	protected $db;
	const scaffold = 1;

	/**
	* @param PDO $dbo optionally takes a PDO object as argument or a boolean
	*/
	function __construct($dbo=false){
		if($dbo == false){
			$this->_dbConfig();
			$this->_adapterSelect();			
		} else {
			$this->dbo = $dbo;
		}
	}

	/**
	* database configuration settings from /config/db.json
	*/
	private function _dbConfig(){
		$db_config_file = file_get_contents(zroot."config/db.json");
		$db_config = json_decode($db_config_file);
		$this->adapter = $db_config->{'adapter'};
		$this->db = isset($_SESSION['__z__']['db']) ? $_SESSION['__z__']['db'] : $db_config->{'db'};
		$this->host = $db_config->{'host'};
		$this->user = $db_config->{'user'};
		$this->pass = $db_config->{'pass'};		
	}

	private function _adapterSelect(){
		switch($this->adapter){
			case "mysql":
				try{
					$this->dbo = new PDO(
						"mysql:host={$this->host};dbname={$this->db}", 
						$this->user, 
						$this->pass, 
						array(
							PDO::ATTR_ERRMODE, 
							PDO::ERRMODE_EXCEPTION
						)
					);
				} catch(PDOException $e){
					print "There was a problem connecting to the database";
				}
				break;
			default:
				$this->db = $this->db == "default" ? zroot."databases/zedek.db" : $this->db;
				try{
					$this->dbo = new PDO(
						"sqlite:{$this->db}", PDO::ERRMODE_EXCEPTION);	
				} catch(PDOException $e){
					print "There was a problem connecting to the database";
				}
		}		
	}

	/**
	* @return array of table column names
	*/

	protected function secureSelect($q){
		$q = addslashes($q);
		return $q;
	}

	public function getColumnNames(){
		$q = "SELECT * FROM {$this->table} LIMIT 1";
		//$q = self::secureSelect($q);
		$q = $this->dbo->query($q);
		$puts = array();
		while($r = $q->fetch(PDO::FETCH_ASSOC)){
			$puts = array_keys($r);
		}
		return $puts;
	}

	/**
	* @return int number of columns in table
	*/
	public function getColumnCount(){
		return count($this->getColumnNames());
	}

	/**
	* @param string $table database table name
	* @param array $attrs database definition
	* @return ZORMTable
	*/
	public function table($table=false, $attrs=false){
		return $table == false || gettype($table) == 'array' ? 
			false : new ZORMTable($table, $attrs, $this->dbo);
	}

	/**
	* @param string $view database view name
	* @param string $q string defining view
	* @return ZORMView
	*/
	public function view($view=false, $q=false){
		return $view == false || gettype($view) == 'array' ? 
			false : new ZORMView($view, $q, $this->dbo);
	}

	/**
	* @param string $q query
	* @return array multidimensional
	*/
	public function fetch($q){
		$q = strtolower($q);
		$q = str_replace("delete", "****", $q);
		$q = str_replace("insert", "****", $q);
		$q = str_replace("update", "****", $q);
		try{
			if($q = $this->dbo->query($q)){
				$a = array();
				while($r = $q->fetch(PDO::FETCH_ASSOC)){
					$a[] = $r;
				}
				return $a;
			} else {
				throw new PDOException("Invalid query.");
				return false;
			}

		} catch(PDOException $e){
			print $e->getMessage();
		}
	}

	/**
	* This sets a serial number column to db array
	* @param array $array to serialize
	* @return array serialized
	*/
	public function serialize($array, $k='sn'){
		if(gettype($array) != 'array') $array = array();
		foreach($array as $i=>$item){
			$array[$i]['sn'] = $i+1;
		}
		return $array;
	}

	/**
	* @param string $q query
	* @todo introspect and convert to prepare safe queries
	*/
	public function write($q){
		$q = strtolower($q);
		$q = str_replace("delete", "****", $q);
		$q = str_replace("select", "****", $q);
		$this->dbo->query($q);
	}

	/**
	* @param string $q query
	* @todo introspect and convert to prepare safe queries
	*/
	public function execute($q){

		$this->dbo->query($q);
	}

	/**
	* @param string $q query
	* @todo introspect and convert to prepare safe queries
	*/
	public function delete($q, $table=false){
		$q = strtolower($q);
		$q = str_replace("select", "****", $q);
		$q = str_replace("insert", "****", $q);
		$q = str_replace("update", "****", $q);
		$this->dbo->query($q);
	}

	/**
	* @param array $array array to convert
	* @return object 
	*/
	public function arrayToObject($array){
		$array = (object)$array;
		return $array;
	}	

}

/**
* @subpackage ZORM Table class
*/
class ZORMTable extends ZORM{
	public $dbo;
	public $table;

	/**
	* @param string $table table name
	* @param array $attrs table definition
	*/
	function __construct($table=false, $attrs=false, $dbo){
		$this->dbo = $dbo;
		$this->table = $table;

		if(is_array($attrs)) $this->create($table, $attrs);

	}

	/**
	* @param string $table table name
	* @param array $attrs table definition
	*/
	public function create($table, $attrs){
		$q = "CREATE TABLE IF NOT EXISTS `{$table}` (";
		$count = count($attrs);
		$i = 1;
		foreach($attrs as $k=>$v){
			if($k == 'primary key'){
				$q .= $i == $count ? "{$k} ({$v}) " : "{$k} ({$v}), ";
			} else {
				$q .= $i == $count ? "{$k} {$v} " : "{$k} {$v}, ";	
			}
				
			$i++;
		}
		$q .= ")";		
		$this->dbo->query($q);
		return $q;
	}

	/**
	* @return array
	*/
	public function fetch($x=false){
		$q = "SELECT * FROM `{$this->table}`";
		$q = self::secureSelect($q);
		try{
			if($q = $this->dbo->query($q)){
				$a = array();
				while($r = $q->fetch(PDO::FETCH_ASSOC)){
					$a[] = $r;
				}
				return $a;				
			} else {
				throw new ZException("The table ({$this->table}) does not exist. \r\n");
			}
		} catch(ZException $e){
			print $e->getMessage();
			return false;
		}
	}

	/**
	* @param array $a input array
	* @return array without empty value fields
	*/
	private function removeEmptyArrayInput($a){
		foreach($a as $k=>$v){
			if(empty($v)){
				unset($a[$k]);
			} elseif(strtolower(trim($k)) == "submit"){
				unset($a[$k]);
			} else {
				$a[$k] = trim($v);
				continue;
			}
		}		
		return $a;
	}

	/**
	* @param array $a array to be inserted in table
	* @return string query
	*/
	public function add($a){
		$a = $this->removeEmptyArrayInput($a);
		$count = count($a);
		if($count == 0){
			return false;
		} else {
			$keys = array_keys($a);
			$values = array_values($a);
			$q = "INSERT INTO {$this->table} (";
			for($i=1; $i<=$count; $i++){
				$q .= $i == $count ? "{$keys[($i-1)]} " : "{$keys[($i-1)]}, ";
			}
			$q .= ") VALUES (";
			for($i=1; $i<=$count; $i++){
				$q .= $i == $count ? "? " : "?, ";
			}
			$q .= ")";
			$insert = $this->dbo->prepare($q);
			$insert->execute($values);
		}
		return $q;
	}

	/**
	* May delete many rows
	* @param mixed $val value to remove
	* @param string $col column
	* @return string query
	*/
	public function remove($val, $col="id"){
		$q = "DELETE FROM {$this->table} WHERE {$col}=?";
		$stmt = $this->dbo->prepare($q);
		$stmt->execute(array($val,));
	}

	/**
	* @param mixed $val value
	* @param array $a array to insert
	* @param string $col column
	* @return string query
	*/
	public function update($val='*', $a=array(), $col="id"){
		$a = $this->removeEmptyArrayInput($a);
		$count = count($a);
		$q = "UPDATE {$this->table} SET ";
		$i = 1;
		foreach($a as $k=>$v){
			$q .= ($i == $count) ? "{$k}=? " : "{$k}=?, ";	
			$i++;
		}
		$q .= $val == "*" ? "" : " WHERE {$col}=?";
		$a = array_values($a);
		if($val == "*"){null;} else {array_push($a, $val);}
		$stmt = $this->dbo->prepare($q);
		$stmt->execute($a);
		return $q;
	}

	/**
	* @param mixed $val value to target
	* @param string $col column
	* @return ZORMRow
	*/
	public function row($val, $col='id'){
		try{
			$row = new ZORMRow($val, $col, $this->table, $this->dbo);
		} catch(PDOException $e){
			$row = false;
		}
		return $row;
	}

	/**
	* @return string query
	*/
	public function drop(){
		$q = "DROP TABLE {$this->table}";
		$this->dbo->query($q);
		return $q;
	}

	/**
	* @param string $col column 
	* @param int row count 
	*/
	public function size($col='id'){
		$q = "SELECT COUNT(`{$col}`) AS `count` FROM {$this->table}";
		$q = self::secureSelect($q);
		return (integer)$this->dbo->query($q)->fetchObject()->count;
	}

	/**
	* many to many relatipnship check ensures 
	* no duplicates are entered in many to many tables
	* @param mixed $arg1 value 1
	* @param string $col1 column 1
	* @param mixed $arg2 value 2
	* @param string $col2 column 2
	* @return boolean
	*/
	public function m2mExists($arg1, $col1, $arg2, $col2="id"){
		$sql = "SELECT COUNT(*) AS `count` 
				FROM {$this->table} 
				WHERE `{$col1}`= :arg1 
					AND `{$col2}`= :arg2"; 

		$stmt = $this->dbo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$stmt->execute(array(':arg1'=>$arg1, ':arg2'=>$arg2));
		return $stmt->fetchObject()->count > 0 ? true : false;
	}

	/**
	*Alias for m2mExists
	*/
	public function pairExists($arg1, $col1, $arg2, $col2="id"){
		return $this->m2mExists($arg1, $col1, $arg2, $col2);
	}

	/**
	* prevents duplicating of records
	* @param mixed $val value
	* @param string $col column 
	* @return object
	*/
	public function exists($val, $col="id"){
		$q = "SELECT COUNT({$col}) AS `count` 
				FROM {$this->table} 
			WHERE {$col}='{$val}'";
		//$q = self::secureSelect($q);
		return $this->dbo->query($q)->fetchObject()->count > 0 ? true : false;
	}
}

/**
* @subpackage ZORMView
*/
class ZORMView extends ZORMTable{
	function __construct($view=false, $q=false, $dbo){
		$this->dbo = $dbo;
		$this->table = $table;

		if(is_string($q)) $this->create($view, $q);
	}

	public function create($view, $q){
		$q = "CREATE VIEW `{$view}` AS {$q}";		
		$this->dbo->query($q);
	}

	public function drop(){
		$q = "DROP VIEW {$this->view}";
		$this->dbo->query($q);
	}	

	public function add($a=false){return false;}

	public function remove($a=false, $b=false){return false;}

	public function update($a=false, $b=false, $c=false){return false;}	
} 

/**
* @subpackage ZORMRow
*/
class ZORMRow extends ZORM{
	public $dbo;
	public $table;
	public $column;
	public $value;
	public $_row;

	/**
	* @param mixed $value
	* @param string $column column
	* @param string $table table
	* @param PDO object
	*/
	function __construct($value, $column, $table, $dbo){
		$this->column = $column;
		$this->value = $value;
		$this->table = $table;
		$this->dbo = $dbo;
		$q = "SELECT * FROM `{$table}` WHERE `{$column}`='{$value}' LIMIT 1";
		$this->_row = $this->dbo->query($q)->fetchObject();
	}

	/**
	* @return object
	*/
	function currentRow(){
		return $this->_row;
	}

	/**
	* @param mixed $val 
	* @param string $col column
	* @todo make prepare clean
	* @return string query
	*/
	function commit($val=false, $col=false){	
		
		$val = $val == false ? $this->currentRow()->id : $val;	
		$col = $col == false ? "id" : $col;

		foreach($this->currentRow() as $k=>$v){
			if($v == $this->$k){
				//continue;
			} else {
				$q = "	UPDATE `{$this->table}` 
						SET `{$k}`='{$this->$k}' 
						WHERE {$col}='{$val}'";
				$this->dbo->query($q);
			}
		}
		return $q;
	}

	function __get($attr){
		if(!property_exists($this, $attr) && in_array($attr, $this->getColumnNames())){
			return @$this->currentRow()->$attr;
		}
	}

	/**
	* @return int row count
	*/
	function size(){
		$i = 0;
		try{
			if($this->currentRow()){
				foreach((array)$this->currentRow() as $v){
					if(!empty($v)) $i++;
				}
				return $i;				
			} else {
				throw new PDOException;
			}
			
		} catch(PDOException $e){
			return $e->getMessage();
		}
	
	}

	/**
	* Does not delete where the table does not have an id column
	* @return string query
	*/
	function remove(){
		$q = "DELETE FROM `{$this->table}` WHERE `id`='{$this->currentRow()->id}'";
		$this->dbo->query($q);
		return $q;
	}
}
