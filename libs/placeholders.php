<?php
namespace __zf__;

class Placeholders {
	static function output(){}

	static public function role(){
		if(!isset($_SESSION['__z__']['user']['id']) || empty($_SESSION['__z__']['user']['id'])) return 'Guest';
		$orm = new ZORM;
		$table = $orm->table('user_roles');
		$row = $table->row($_SESSION['__z__']['user']['id'], 'user_id');
		$role = $orm->table('roles')->row($row->role_id)->role;
		return $role;
	}
}