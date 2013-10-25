<?php

namespace __zf__;

class Article extends ZModel{
	const itemsadmin = "itemsadmin";
	const catadmin = "catadmin";
	const index = "index";

	function _dbInit(){
		$articlesDesc = array(
			'id'=>"integer primary key", 
			'title'=>"text", 
			'owner'=>"int", 
			'cat'=>"int", 
			'article'=>"text", 
			'tags'=>"text", 
			'created_on'=>"varchar", 
			'updated_on'=>"varchar", 
			'updated_by'=>"int", 
		);
		$catsDesc = array(
			'id'=>"integer primary key", 
			'category'=>"varchar", 
			'description'=>"text", 
		);
		$articlesAttachementsDesc = array(
			'id'=>"integer primary key", 
			'article_id'=>"int", 
			'attachment'=>"varchar", 
			'type'=>"varchar", 
		);
		$articlesCommentsDesc = array(
			'id'=>"integer primary key", 
			'article_id'=>"int", 
			'created_on'=>"timestamp", 
			'commentor_name'=>"varchar", 
			'commentor_id'=>"int", 
			'commentor_email'=>"varchar", 
			'commentor_mobile'=>"varchar", 
			'comment'=>"text", 
		);
		$orm = new ZORM;
		$orm->table("articles", $articlesDesc);
		$orm->table("article_categories", $catsDesc);
		$orm->table("article_attachments", $articlesAttachementsDesc);
		$orm->table("article_comments", $articlesCommentsDesc);
	}

	function admin($placeholders=array(), $views=false, $orm, $uri){
		$views=array(self::itemsadmin, self::catadmin, self::index);
		$categories = $orm->table("articles_categories")->fetch();
		$temp = array(
			'categories'=>$categories, 
		);
		$temp = array_merge($temp, $placeholders);
		switch($uri->arguments){
			case "articles":
				echo $this->template($views[0])->render();
				break;
			case "categories":
				echo $this->template($views[1], $temp)->render();
				break;
			default:
				echo $this->template($temp, $views[2])->render();
		}		
	}

	function newArticle($destination="directory path", $uploads=array(), $orm){
		$article = $orm->table("articles");
		unset($_POST['submit']);
		$posts = $_POST;
		$posts['owner'] = $_SESSION['__z__']['user']['id'];
		$posts['created_on'] = time();
		$article->add($posts);
		$q = "SELECT id  
				FROM `articles` WHERE owner='".$_SESSION['__z__']['user']['id']."' ORDER BY id DESC LIMIT 1 ";
		$r = $orm->fetch($q);
		$this->uploadFile($uploads, $r, $destination);
		header("Location: ". $_SERVER['HTTP_REFERER']);
	}

	private function uploadFile($uploads, $r, $destination){
		if(isset($uploads['name']) && !empty($uploads['name'])){
			$attachment = array();
			$attachment['article_id'] = $r[0]['id'];
			$attachment['attachment'] = $uploads['name'];
			$ext = $uploads['name'];
			$ext = explode(".", $ext);
			$ext = end($ext);
			$attachment['type'] = $ext;
			try{
				if(move_uploaded_file($uploads['tmp_name'], $destination.$attachment['attachment'])){
					$articleAttachments = $orm->table("article_attachments");
					$articleAttachments->add($attachment);
				} else {
					throw new Exception("Attachment Failed");
				}
			}catch(Exception $e){
				//
			}			
		}		
	}	

	function edit(){}

	function delete($orm){
		$article = $orm->table("articles")->row($this->currentIndex());
		if($article->owner == $_SESSION['__z__']['user']['id']){
			$article->remove();
			$orm->table("article_attachments")->row($this->currentIndex(), "article_id")->remove();			
		}
		header("Location: " . $_SERVER['HTTP_REFERER']);
	}

	function newCat($orm){
		unset($_POST['submit']);
		$orm->table("articles_categories")->add($_POST);
		header("Location: ". $_SERVER['HTTP_REFERER']);
	}

	function editCat(){}

	function deleteCat(){}

	function read($imgFolder, $view, $temp = array(), $users="users table", $orm){
		$article = $orm->table("articles")->row($this->currentIndex());
		$author = $orm->table($users)->row($article->owner);
		$authorPlus = $orm->table("extended_users")->row($article->owner, "user_id");
		$authorTitle = $orm->table("titles")->row($authorPlus->title)->short_title;
		$category = $orm->table("article_categories")->row($article->cat)->category;
		$attachment = $orm->table("article_attachments")->row($article->id, "article_id");
		$file = $this->getAttachedFiles($attachment);
		echo $this->template($temp, $view)->render();
	}

	private function getAttachedFiles($attachment){
		$type = $attachment->type;
		switch($type){
			case "doc": case "docx": $mime = "word"; break;
			case "ppt": case "pptx": $mime = "powerpoint"; break;
			case "xls": case "xlsx": $mime = "excel"; break;
			case "pdf": $mime = "pdf"; break;
			default: $mime = "unknown";
		}
		$puts = strlen($attachment->attachment) <= 3 ? 
				"" : 
				"
				<h2>Files</h2>
				<a href='/public/uploads/{$attachment->attachment}'>
				<img src='/public/images/{$mime}.png' />
				</a>
				"
		;
		return $puts;
	}

	function publications(){
		$uri = new URIMaper;
		echo $uri->arguments;
	}

}

?>