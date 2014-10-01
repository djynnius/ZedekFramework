<?php
/**
* @package Zedek Framework
* @subpackage ZModel zedek super model class
* @version 3
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/
namespace __zf__;
abstract class ZModel{	

	public $table;

	function __construct(){
		$this->orm = new ZORM;
		$this->uri = new ZURI;
	}

	/**
	* @return array multidimentional array of all the rows in the table
	*/
	final function fetchAll(){
		return $this->orm->table($this->table)->fetch();
	}

	/**
	* @param sting $q custom query
	* for managing custom queries
	*/
	final function fetch($q){
		return $this->orm->fetch($q);
	}

	/**
	* @param mixed $id value to check for
	* @param string $col strign name for where the value is to be checked
	* @return array of records that match search criteria
	*/
	final function find($id, $col="id"){
		$q = "SELECT * FROM `{$this->table}` WHERE `$col`='{$id}'";
		return $this->orm->fetch($q);
	}

	/**
	* @return object being the first record in the table
	*/
	final function findFirst(){
		$q = "SELECT * FROM `{$this->table}` WHERE `$col`='{$id}' ORDER BY id ASC LIMIT 1";		
		$record = $this->orm->fetch($q);
		$single = $record[0];
		return $this->orm->arrayToObject($single);		
	}

	/**
	* @return object being the last record in the table
	*/
	final function findLast(){
		$q = "SELECT * FROM `{$this->table}` WHERE `$col`='{$id}' ORDER BY id DESC LIMIT 1";		
		$record = $this->orm->fetch($q);
		$single = $record[0];
		return $this->orm->arrayToObject($single);
	}

	/**
	* @param object $object ZORMRow or fetchObject
	* @return array 
	*/
	final function toArray($object){
		return (array)$object->_row;
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
	function update($id, $array){
		$this->orm->table($this->table)->update($id, $array);
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
		return $this->orm->table($this->table)->exists($col, $val);
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