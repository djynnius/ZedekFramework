<?php

class CControler extends ZControler implements ZIControler{
	function __construct(){
		$temp = self::template();
		//var_dump($temp->template);
		//$temp->render();
	}

	function next(){
		Z::import("model");
		$model = new ZModel();
		$q = "SELECT * FROM avalanche";
		$q = $model->dbo->query($q);
		while($r = $q->fetch(PDO::FETCH_ASSOC)){
			$a[] = $r;
		}
		$template['pips'] = "Avalanche";
		$template['avalanche'] = $a;
		self::template($template)->render();
	}
}

?>