<?php
/**
* @package Zedek Framework
* @subpackage ZModel zedek super model class
* @version 4
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/
namespace __zf__;
abstract class ZModel{	

	public $table;

	function __construct(){
		$this->uri = new ZURI;
	}

	/**
	*@param $record array multidmensional array
	*@return array array multidmensional array UTF encoded 
	*/
	function UTF8EncodeRecords($records){
		foreach($records as $i=>$record){
			foreach($record as $j=>$cell){
				$records[$i][$j] = utf8_encode($cell);
			}
		}
		return $records;
	}	

	/**
	* @param array $tmp a global variable
	* @return bool
	*/
	function appendToTemplate(&$tmp){
		if(!isset($tmp)) $tmp = array();
		$vars = get_object_vars($this);
		foreach($vars as $k=>$v){
			if($k == "orm" || $k == "uri") continue;
			$tmp[$k]=$v;
		}
		return true;
	}	

	/**
	* @param array $array or fetchObject
	* @return object ZORMRow 
	*/
	final function toObject($array){
		return (object)$array;
	}

	/**
	* @param int $id the current id usually in urimaper->id
	* @return object ZORMRow of the previous row that exists or return false if not exists
	*/
	function findPrev($id){
		$q = "	SELECT id 
				FROM {$this->table} 
				ORDER BY id ASC
		";
		$ids = $this->orm->fetch($q);
		
		foreach($ids as $k=>$current){
			if($current["id"] == $id){
				if(isset($ids[$k-1])){
					return $this->findOne($ids[$k-1]["id"]);
				}
			}
		}
		return false;
	}

	/**
	* @param int $id the current id usually in urimaper->id
	* @return object ZORMRow of the next row that exists or return false if not exists
	*/
	function findNext($id){
		$q = "	SELECT id 
				FROM {$this->table} 
				ORDER BY id ASC
		";
		$ids = $this->orm->fetch($q);
		
		foreach($ids as $k=>$current){
			if($current["id"] == $id){
				if(isset($ids[$k+1])){
					return $this->findOne($ids[$k+1]["id"]);
				}
			}
		}
		return false;
	}

	/**
	* @param mixed $id usually the integer from id column
	* @param string $col column name
	* @return object ZORMRow 
	*/
	final function findOne($id, $col="id"){
		return $this->orm->table($this->table)->row($id, $col);
	}

	/**
	*@param array $array array for input of single row of records
	*/
	final function add($array){
		$this->orm->table($this->table)->add($array);
		//print $this->table;
	}

	/**
	* alias for add method
	*/
	function create($array){
		$this->add($array);
	}

	/**
	* updates table row
	* @param int $id row id
	* @param array $array data for row update
	*/
	function update($id, $array, $col="id"){
		$this->orm->table($this->table)->update($id, $array, $col);
	}

	/**
	* @param int $id
	*/
	function remove($id){
		$this->orm->table($this->table)->row($id)->remove();
	}

	function delete($id){
		$this->remove($id);
	}

	/**
	* @param mixed $val the id but may be some other type
	* @param string $column the column name which by default is id
	* @return boolean true id the value exists
	*/
	final function exists($val, $col="id"){
		return $this->orm->table($this->table)->exists($val, $col);
	}

	/**
	* @param mixed $val the first value
	* @param string $column the first column name
	* @param mixed $val the second value which may be an integer primary key
	* @param string $column the column name which by default is id
	* @return boolean true id the pair exists
	*/
	final function pairExists($val, $col, $val2, $col2="id"){
		return $this->orm->table($this->table)->m2mExists($val, $col, $val2, $col2);
	}

	/**
	*@return object ZORMTable
	*/
	final function getTableObject(){
		return $this->orm->table($this->table);
	}

	/**
	*@param int $id row id
	*@return object ZORMRow 
	*/
	final function getRowObject($id, $col="id"){
		return $this->getTableObject()->row($id, $col);
	}

	/**
	* @return int number of rows in this table
	*/
	final function rowCount(){
		$q = "SELECT COUNT(*) AS `rcount` FROM {$this->table}";
		$count = $this->orm->dbo->query($q)->fetchObject()->rcount;
		return $count;
	}

	final public function asObject($array){
		if(count($array) == 1 && gettype($array[0]) == "array"){
			return (object)$array[0];
		} else {
			return (object)$array;
		}
	}
}