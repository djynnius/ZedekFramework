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
namespace __zf__;
use \PDO as PDO;

if(phpversion() >= "5.4"){

	/*Class definition begins*/
	Class ZORM{

		static public $table;
		static public $config;

		/**
		* @param [string] $file full path to config file
		*/
		static public function config($file=false){
			if($file == false){
				$file = zroot."config/db.conf";
			}
			self::$config = new ORMConfig($file);
		}

		/**
		* counts number of records returned
		* @return int;
		*/
		static function count(){
			return count(self::read());
		}

		/**
		* alias for ORM::count
		*/
		static function length(){
			return self::count();
		}

		/**
		* PDO beginTransaction
		* @return PDO::beginTransaction()
		*/
		static function beginTransaction(){
			return self::cxn()->beginTransaction();
		}

		/**
		* PDO commit transaction
		* @return PDO::commit()
		*/
		static function commitTransaction(){
			return self::cxn()->commit();
		}

		/**
		* PDO rollback transaction
		* @return PDO::rollback()
		*/
		static function rollbackTransaction(){
			return self::cxn()->commit();
		}


		/**
		*
		*/
		static function tableData($table){
			self::table($table);
			return self::rows();
		}

		static public function cxn(){
			if(is_null(self::$config)) self::config();
			$config = self::$config;
			$adapter = $config->setting("adapter");
			$db = $config->setting("db");
			$user = $config->setting("user");
			$pass = $config->setting("pass");
			$host = $config->setting("host");
			$port = $config->setting("port");

			$cxn = new ORMCxn($adapter, $db, $user, $pass, $host, $port);
			return $cxn;
		}

		/**
		* Sets cursor to point to a DB table
		*/
		static function table($table){
			self::$table = $table;
		}

		/**
		* basic SQL intection attack prevention
		* @param mixed $val
		* @return string
		*/
		static function prepare($val){
			return addslashes($val);
		}

		/**
		* Executes SQL statement
		* @param string $sql
		* @return PDO::query() object
		*/
		static function execute($sql){
			return self::cxn()->query($sql);
		}

		/****/
		static function singleRecord($sql){
			$id = self::execute($sql)->fetchObject()->id;
			return self::row($id); 
		}

		/**
		* Creates new table if the table doesnt already exist
		* @param string $table table name
		* @param array $description description of table to be created
		* $description ['email'=>"varchar(30)", 'dob'=>"date", 'address'=>"text"... ]
		*/
		static function create($table, $description=[]){
			$adapter = self::$config->setting("adapter");
			$mthd = "create_".$adapter;
			$sql = self::$mthd($table, $description);
			self::execute($sql);
		}

		/**
		* MySQL table generator
		*/
		private function create_mysql($table, $description){
			$id["id"] = !isset($description["id"]) ?  
				"INT PRIMARY KEY AUTO_INCREMENT NOT  NULL" : $description["id"];
			$description = array_merge($id, $description); /*ensures ID is first column in DB*/
			$description["created_by"] = !isset($description["created_by"]) ? 
				"INT" : $description["created_by"];
			$description["created_at"] = !isset($description["created_at"]) ? 
				"DATETIME" : $description["created_at"];
			$description["updated_by"] = !isset($description["updated_by"]) ? 
				"INT" : $description["updated_by"];
			$description["updated_at"] = !isset($description["updated_at"]) ? 
				"TIMESTAMP DEFAULT NOW() ON UPDATE NOW()" : $description["updated_at"];

			$sql = "CREATE TABLE IF NOT EXISTS `{$table}` (";
			
			$pair = [];
			foreach($description as $col=>$val){
				$pair[] = "{$col} {$val}";
			}

			$sql .= join(", ", $pair);
			$sql .= ")";		

			return $sql;
		}

		/**
		* SQLite3 table generator
		*/
		private function create_sqlite($table, $description){
			$id["id"] = !isset($description["id"]) ?  
				"INTEGER PRIMARY KEY " : $description["id"];
			$description = array_merge($id, $description); /*ensures ID is first column in DB*/
			$description["created_by"] = !isset($description["created_by"]) ? 
				"INT" : $description["created_by"];
			$description["created_at"] = !isset($description["created_at"]) ? 
				"TEXT" : $description["created_at"];
			$description["updated_by"] = !isset($description["updated_by"]) ? 
				"INT" : $description["updated_by"];
			$description["updated_at"] = !isset($description["updated_at"]) ? 
				"TEXT" : $description["updated_at"];

			$sql = "CREATE TABLE IF NOT EXISTS `{$table}` (";
			
			$pair = [];
			foreach($description as $col=>$val){
				$pair[] = "{$col} {$val}";
			}

			$sql .= join(", ", $pair);
			$sql .= ")";		

			return $sql;		
		}

		/**
		* @param string $sql
		* @return array of stdObj of record 
		*/
		static function rows($sql="*"){
			$sql = $sql == "*" ? "SELECT * FROM `".self::$table."`" : $sql;
			$records = self::execute($sql);

			$r = [];
			while($a = $records->fetch(PDO::FETCH_ASSOC)){
				$r[] = (object)$a;
			}
			return $r;
		}

		/**
		* Alias for rows
		*/
		static function read($sql="*"){
			return self::rows($sql);
		}

		/**
		* @param string $arg1 may be column name or value for id column
		* @param string $arg2 value
		*/
		static function row($arg1=false, $arg2=false){
			return $arg2 == false ? 
				new ORMRecord($arg1, "id", self::$table) 	: 
				new ORMRecord($arg2, $arg1, self::$table);
		}

		/**
		* Alias for row
		*/
		static function record($arg1=false, $arg2=false){
			return self::row($arg1, $arg2);
		}

		/**
		* Adds new db record 
		* @param array 
		*/
		static function add($values=[]){

			$values["created_at"] = strftime("%Y-%m-%d %H:%M:%S", time());

			$cols = array_keys($values);
			$vals = array_values($values);
			foreach($cols as $i=>$col){
				$cols[$i] = "`{$col}`";
			}
			foreach($vals as $i=>$val){
				$val = self::prepare($val);
				$vals[$i] = "'{$val}'";
			}

			$sql = "INSERT INTO `".self::$table."` ( ";	
			$sql .= join(", ", $cols);
			$sql .= " ) VALUES ( ";
			$sql .= join(", ", $vals);
			$sql .= " )";
			self::execute($sql);
		}

		/**
		* Alias for add
		*/
		static function insert($values=[]){
			self::add($values);
		}

		/**
		* Truncates existing table(s)
		* @param array or string 
		*/
		static function truncate($tables=[]){
			if(is_array($table)){
				foreach($tables as $table){
					self::execute("TRUNCATE TABLE `".self::$table."`");		
				}
			} else {
				$table = (string)$tables;
				self::execute("TRUNCATE TABLE `".self::$table."`");
			}
			
		}

		/**
		* Updates table with cursor pointing
		* @param $col [integer | string] may be id or column name
		* @param $val [string | array] may column name or array of records to be set 
		* @param $vals array of records to be set 
		* @return boolean
		*/
		static function update($col=false, $val=false, $values=false){
			$args = func_get_args();

			switch (count($args)) {
				case 2:
					if(!is_array($args[0]) && is_array($args[1])){
						$args[1]["updated_at"] = self::updated_at();
						$sql = self::updateById($args[0], $args[1]);
					} else {
						return false;
					}
					break;
				case 3:
					if(is_string($args[0]) && !is_array($args[1]) && is_array($args[2])){
						$args[2]["updated_at"] = self::updated_at();
						$sql = self::updateByColumnAndValue($args[0], $args[1], $args[2]);
					} else {
						return false;
					}
					break;
				default:
					return false;
			}
			self::execute($sql);
			return true;
		}

		/**
		* @return string timestamp
		*/
		private function updated_at(){
			return strftime("%Y-%m-%d %H:%M:%S", time());
		}

		/**
		* Updates table with cursor by id
		*/
		private function updateById($id, $record){
			$pair = [];
			foreach($record as $c=>$v){
				$v = self::prepare($v);
				$pair[] = "`{$c}`='{$v}'";
			}

			$sql = "UPDATE `".self::$table."` SET ";
			$sql .= join(", ", $pair);
			$sql .= " WHERE `id`='{$id}'";

			return $sql;		
		}

		/**
		* Updates table rows with cursor by column name and value
		*/
		private function updateByColumnAndValue($col, $val, $record){
			$pair = [];
			foreach($record as $c=>$v){
				$v = self::prepare($v);
				$pair[] = "`{$c}`='{$v}'";
			}

			$sql = "UPDATE `".self::$table."` SET ";
			$sql .= join(", ", $pair);
			$sql .= " WHERE `{$col}`='{$val}'";

			return $sql;
		}

		/**
		* @param $col;
		* @param $val;
		*/
		static function remove($col=false, $val=false){
			$sql = $col != false && $val == false ? 
				"DELETE FROM `".self::$table."` WHERE `id`='{$col}'" : 
				"DELETE FROM `".self::$table."` WHERE `{$col}`='{$val}'";
			self::execute($sql);
		}

		/**
		* @return ORMRecord object matching first params
		*/
		static function firstRecord($col=false, $val=false){
			$sql = $val == false ? 
					"	SELECT id 
						FROM `".self::$table."`  
						ORDER BY id ASC LIMIT 1" 
						:
					"	SELECT id 
						FROM `".self::$table."` 
						WHERE `{$col}`='{$val}' 
						ORDER BY id ASC LIMIT 1";

			return self::singleRecord($sql);
		}

		/**
		* Alias for firstRecord
		*/
		static function first($col=false, $val=false){
			return self::firstRecord($col, $val);
		}

		/**
		* @return ORMRecord object last record matching params
		*/		
		static function lastRecord($col=false, $val=false){
			$sql = $val == false ? 
					"	SELECT id
						FROM `".self::$table."`  
						ORDER BY id DESC LIMIT 1" 
						:
					"	SELECT id
						FROM `".self::$table."` 
						WHERE `{$col}`='{$val}' 
						ORDER BY id DESC LIMIT 1";
			return self::singleRecord($sql);		
		}

		/**
		* Alias for lastRecord
		*/
		static function last($col=false, $val=false){
			return self::lastRecord($col, $val);
		}

		/**
		* 
		*/
		static function find($col, $val){
			$sql = "SELECT * FROM `".self::$table."` WHERE `{$col}` LIKE '%{$val}%' ";
			$records = self::rows($sql);
			return count($records) == 0 ? [] : $records;
		}

		/**
		* 
		*/
		static function findFirst($col, $val){
			$sql = "SELECT id FROM `".self::$table."` WHERE `{$col}` LIKE '%{$val}%' ORDER BY id ASC LIMIT 1";
			return self::singleRecord($sql);
		}

		/**
		* 
		*/
		static function findLast($col, $val){
			$sql = "SELECT id FROM `".self::$table."` WHERE `{$col}` LIKE '%{$val}%' ORDER BY id DESC LIMIT 1";
			return self::singleRecord($sql);		
		}

		/**
		* 
		*/		
		static function previous($id){
			$sql = "SELECT id FROM `".self::$table."` WHERE `id`<'{$id}' ORDER BY id ASC LIMIT 1";
			return self::singleRecord($sql);		
		}

		/**
		* 
		*/		
		static function next($id){
			$sql = "SELECT id FROM `".self::$table."` WHERE `id`>'{$id}' ORDER BY id ASC LIMIT 1";
			return self::singleRecord($sql);	
		}

		static function exists(){
			/**/
			$args = func_get_args();

			if(count($args) == 1 && !is_array($args[0])) 
				$sql = "SELECT COUNT(id) AS count FROM `".self::$table."` WHERE id='{$args[0]}'";

			if(count($args) == 2 && !is_array($args[1])) 
				$sql = "SELECT COUNT(id) AS count FROM `".self::$table."` WHERE `{$args[0]}`='{$args[1]}'";

			if(count($args) == 1 && is_array($args[0])){
				$sql = "SELECT COUNT(id) AS count FROM ".self::$table." WHERE ";
				$pairs = [];
				foreach($args[0] as $col=>$val){
					$pairs[] = "`{$col}`='{$val}'";
				}
				$pairs = join(" AND ", $pairs);
				$sql .= $pairs;
			} 
				
			if(isset($sql)){
				$count = self::execute($sql)->fetchObject()->count;
				$bool = $count == 0 ? false : true;

			}
			return !isset($sql) ? false : $bool;
		}
	}


	/**
	* ORMRecord returns a PDO query fetchObject for a single row
	*/
	class ORMRecord extends ZORM{

		public $record;

		function __construct($val=false, $col, $table){
			$sql = "SELECT * FROM `{$table}` WHERE `{$col}`='{$val}' ORDER BY id DESC LIMIT 1";
			$this->record = self::execute($sql)->fetchObject();
		}

		function __destruct(){
			return false;
		}

		function __get($attr){
			if(!property_exists($this, $attr) && @property_exists($this->record, $attr)){
				return $this->record->$attr;
			}
		}

		function commit(){
			$obj = (array)$this;
			$db = (array)$this->record;

			$ok = array_keys($obj);
			$dk = array_keys($db);

			$pair = [];
			foreach($obj as $k=>$v){
				if(in_array($k, $dk) && $db[$k] != $obj[$k]){
					$pair[$k] = $v;			
				}
			}

			$return = isset($this->record->id) ? self::update($this->record->id, $pair) : false;

		}

		function destroy(){
			/*remove record*/
			self::remove($this->record->id);
		}

		/**
		* Alias for destroy
		*/
		function delete(){
			self::destroy();
		}

	}

	/**
	* ORM connection abstracts PDO
	*/
	class ORMCxn {
		public $adapter;
		public $db;
		public $user;
		public $pass;
		public $host;
		public $port;

		public function __construct($adapter=false, $db=false, $user=false, $pass=false, $host=false, $port=false){
			$this->adapter = strtolower(trim($adapter));
			$this->host = $host;
			$this->user = $user;
			$this->pass = $pass;
			$this->db = $db;
			$this->port = $port;
			$this->pdo = self::$adapter($db, $user, $pass, $host, $port);
		}

		public function mysql($db, $user, $pass, $host, $port=3306){
			return new PDO("mysql:host={$this->host};port={$this->port};dbname={$this->db}", $this->user, $this->pass);
		}

		public function sqlite($db){
			$db = $db == "default" || $db == "app" || $db == "zedek" ? zroot."databases/app.db" : $db;
			return new PDO("sqlite:" . $db);
		}

		public function oracle($db, $user, $pass, $host, $port){}

		public function postgre($db, $user, $pass, $host, $port=5432){
			return $port == 5432 ? new PDO("pgsql:dbname={$this->db};host={$this->host};user={$this->user};password={$this->pass}") : new PDO("pgsql:dbname={$this->db};host={$this->host};port={$this->port};user={$this->user};password={$this->pass}");
		}

		public function mssql($db, $user, $pass, $host, $port=1433){
			$pdo = new PDO("dblib:host={$this->host}:{$this->port};dbname={$this->db}", $this->user, $this->pas);
			$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			return $pdo;
		}

		public function query($sql){
			return $this->pdo->query($sql);
		}
	}


	/**
	* Default config file
	*/
	class ORMConfig{

		public $file; /*config file*/
		public $conf; /*config file contents*/
		public $data; /*JSON */

		/**
		* @param string $conf the full path to the config file
		*/
		function __construct($file=false){	
			$this->file = $file == false ? "cxn.cnf" : $file;
			$this->conf = file_get_contents($this->file);
			$this->data = json_decode($this->conf);
			
		}

		/**
		* The getter setter method for config file
		* @param string $item
		* @param mixed $value
		* @return boolean only returns boolen false if arguments are not 1 or 2
		*/
		public function setting($item=false, $value=false){
			switch(count(func_get_args())){
				case 1:
					return self::get($item);
					break;
				case 2:
					self::set($item, $value);
					break;
				default:
					return false;
			}
		}

		/**
		* The getter method for config file
		* @param string $item
		* @return boolean only returns boolen false if the setting is not set
		*/	
		private function get($item){
			return isset($this->data->$item) ? $this->data->$item : false;
		}

		/**
		* The setter method for config file
		* @param string $item
		* @param mixed $value
		*/
		private function set($item, $value){
			$this->data->$item = $value;
			file_put_contents($this->file, json_encode($this->data, JSON_PRETTY_PRINT));
		}
	}
	/*class definition ends*/
	ZORM::config();
}

