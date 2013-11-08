<?php

namespace __zf__;
use ZORM as ORM, \PDOException as PDOException;

class CMS extends ZModel{

	function __call($method, $args){
		if(!method_exists($this, $method)){
			$uri = new URIMaper;
			$orm = new ORM;
			$id = $orm
			->table('content')
			->row($uri->method, 'short_title')
			->id;
			if(empty($id)){
				parent::__call($method, $args);
			} else {
				$this->renderContent($id);
			}
		}
	}

	function addContent($content, $image, $imgFolder, $table=false, $orm){
		$timestamp = time();
		$table = $table == false ? $orm->table("content") : $orm->table($table);
		if(strlen($image['name']) > 3){
			$imo = new Image;
			$ext = $imo->getExtension($image['name']);
			$imageDestination = $imgFolder.$_SESSION['__z__']['user']['id'].'_'.$timestamp.'.'.$ext;
			$imo->resize($image['tmp_name'], $imageDestination, 320, 360);
		} else {
			$imageDestination = '';
		}
		if(isset($content['submit'])) unset($content['submit']);
		$content['category'] = $content['category'] == 'custom' ? $content['custom'] : $content['category'];
		if(isset($content['custom'])) unset($content['custom']);
		$content['author'] = $_SESSION['__z__']['user']['id'];
		$content['created_on'] = $timestamp;
		$content['image'] = $imageDestination;
		try{
			if($table->add($content)){
				return true;
			} else {
				throw new PDOException("something went wrong.");
			}
		}catch(PDOException $e){
			return false;
		}
	}

	function getContent($id){}

	function getAllContent(){}

	function updateContent($content, $image, $imgFolder, $table=false){
		$timestamp = time();
		$orm = new ORM;
		$table = $table == false ? 
			$orm->table("content") : 
			$orm->table($table);
		if(strlen($image['name']) > 3){
			$imo = new Image;
			$ext = $imo->getExtension($image['name']);
			$imageDestination = $imgFolder.$_SESSION['__z__']['user']['id'].'_'.$timestamp.'.'.$ext;
			$imo->resize($image['tmp_name'], $imageDestination, 280, 360);
		} else {
			$imageDestination = '';
		}
		$id = $content['id'];
		unset($content['submit']);
		unset($content['id']);
		$content['category'] = $content['category'] == 'custom' ? $content['custom'] : $content['category'];
		if(isset($content['custom'])) unset($content['custom']);
		$content['editor'] = $_SESSION['__z__']['user']['id'];
		$content['updated_on'] = $timestamp;
		$content['image'] = $imageDestination;
		try{
			if($table->update($content, $id)){
				return true;
			} else {
				throw new PDOException("something went wrong.");
			}
		}catch(PDOException $e){
			return false;
		}		
	}

	function removeContent(){}

	function _dbInit(){
		$contentDesc = array(
			'id' => "integer primary key",
			'title' => "varchar", 
			'content' => "text", 
			'author' => "int", 
			'created_on' => "varchar", 
			'editor' => "int", 
			'updated_on' => "varchar", 
			'short_title' => "varchar", 
			'category' => "varchar",
			'image' => "varchar",
		);
 		$orm = new ORM;
		$orm->table("content", $contentDesc);
	}

	function add(){
		if(isset($_POST['submit'])){
			$this->addContent($_POST, $_FILES['image']);
			header('Location: ' . $_SERVER['HTTP_REFERER']);
		} else {
			print $this->template('add')->render();
		}
	}

	function renderContent($imgFolder, $view, $temp = array(), $id=false){
		$id = $id=false ? $this->id() : $id;
		$orm = new ORM;
		$content = $orm->table('content')->row($id);
		$author = $orm->table('users')->row($content->author)->username;
		if(strlen($content->image) < 3){
			$image = $imgFolder.'generic.jpg';
		} else {
			$image = end(explode("/", $content->image));
			$image = $imgFolder.$image;
		}
		print $this->template($view, $temp)->render();
	}

	function edit($view, $temp=array()){
		if(isset($_POST['submit'])){
			$this->updateContent($_POST, $_FILES['image']);
			header('Location: ' . $_SERVER['HTTP_REFERER']);
		} else {
			print $this->template($view, $temp)->render();	
		}	
	}
}