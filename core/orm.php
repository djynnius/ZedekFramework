<?php
#ORM for zedek
class ZORM extends Zedek{
	
	public $dbo;
	protected $host;
	protected $user;
	protected $pass;
	protected $name;
	protected $engine;
	protected $db;

	function __construct(){
		$db_config_file = file_get_contents(zroot."config/db.json");
		$db_config = json_decode($db_config_file);

		$this->engine = $db_config->{'engine'};
		$this->db = $db_config->{'db'};
		$this->host = $db_config->{'host'};
		$this->user = $db_config->{'user'};
		$this->pass = $db_config->{'pass'};

		switch($this->engine){
			case "mysql":
				$this->dbo = new PDO(
					"mysql: host={$this->host}; dbname={$this->db}", 
					$this->user, 
					$this->pass, 
					array(
						PDO::ATTR_ERRMODE, 
						PDO::ERRMODE_EXCEPTION
					)
				);
				break;
			default:
				$this->dbo = new PDO(
					"sqlite:{$this->db}", 
					array(
						PDO::ATTR_ERRMODE, 
						PDO::ERRMODE_EXCEPTION
					)
				);
		}		
	}

	function table($table=false, $attrs=false){
		return $table == false ? 0 : new ZORMTable($table, $attrs, $this->db, $this->dbo);
	}

	function fetch($q){
		try{
			if($q = $this->dbo->query($q)){
				$a = array();
				while($r = $q->fetch(PDO::FETCH_ASSOC)){
					$a[] = $r;
				}
				return $a;
			} else {
				throw new Exception("Invalid query.");
				return false;
			}
		} catch(Exception $e){
			//echo $e->getMessage();
		}
	}

	function write($q){
		$this->dbo->query($q);
	}

	function delete($q, $table=false){
		$this->dbo->query($q);
	}

}

#Table mapper
class ZORMTable extends ZORM{
	public $dbo;
	public $table;

	function __construct($table=false, $attrs=false, $db, $dbo){
		$this->dbo = $dbo;
		$this->table = $table;

		if(!is_array($attrs)){
			$this->fetch();
		} else {
			$this->create($table, $attrs);
		}
	}

	function create($table, $attrs){
		$q = "CREATE TABLE IF NOT EXISTS {$table} (";
		$count = count($attrs);
		$i = 1;
		foreach($attrs as $k=>$v){
			$q .= $i == $count ? "{$k} {$v} " : "{$k} {$v}, ";	
			$i++;
		}
		$q .= ")";		
		$this->dbo->query($q);
	}

	function fetch(){
		$table = $this->table;
		$q = "SELECT COUNT(*) AS count FROM {$table}";
		$q = $this->dbo->query($q);
		$a = array();
		while($r = $q->fetch(PDO::FETCH_ASSOC)){
			$a[] = $r;
		}
		return $a;
	}

	function add($a){
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
	}

	function remove($val, $col="id"){
		$q = "DELETE FROM {$this->table} WHERE {$col}=?";
		$stmt = $this->dbo->prepare($q);
		$stmt->execute(array($val,));
	}

	function update($a=array(), $val='*', $col="id"){
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
	}

	function row($val, $col='id'){
		return new ZORMRow($val, $col, $this->table, $this->dbo);
	}

	function scafold(){}

	function drop(){
		$q = "DROP TABLE {$this->table}";
		$this->dbo->query($q);
	}

	function size(){
		$q = "SELECT COUNT(`id`) AS `count` FROM {$this->table}";
		return $this
				->dbo
				->query($q)
				->fetchObject()
				->count;
	}
}

#purely for the purpose of replicatin the table actions except for the creation of a view
class ZORMView extends ZORMTable{
} 

#Row mapper
class ZORMRow extends ZORM{
	public $dbo;
	public $table;
	public $column;
	public $value;
	public $row;

	function __construct($value, $column, $table, $dbo){
		$this->column = $column;
		$this->value = $value;
		$this->table = $table;
		$this->dbo = $dbo;

		$q = "SELECT * FROM {$table} WHERE {$column}='{$value}' LIMIT 1";
		$this->row = $dbo->query($q)->fetchObject();
	}

	function commit(){		
		foreach($this->row as $k=>$v){
			if($v == $this->$k){
				continue;
			} else {
				$q = "UPDATE {$this->table} SET {$k}='{$this->$k}' WHERE id='{$this->row->id}'";		
				$this->dbo->query($q);
			}
		}
	}

	function __get($attr){
		if(!property_exists($this, $attr)){
			return $this->row->$attr;
		}
	}

	function size(){
		$i = 0;
		try{
			if($this->row){
				foreach($this->row as $v){
					$i++;
				}
				return $i;				
			} else {
				throw new Exception();
			}
			
		} catch(Exception $e){
			return $e->getMessage();
		}
	
	}

	function remove(){
		$q = "DELETE FROM {$this->table} WHERE id='{$this->row->id}'";
		$this->dbo->query($q);
	}

}

?>