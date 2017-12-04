<?php

require_once('myPDO.mySql.phpAuth.php') ;

try{
	if(isset($_GET ['q'])){
		if(isset($_GET['wait'])){
			usleep(rand(0,20) * 100000);
		}

		$request = myPDO::getInstance()->prepare(<<<SQL
			SELECT id, name txt
			FROM artist 
			WHERE id in (SELECT artist
						 FROM album
						 WHERE genre = ?)
			ORDER BY name
SQL
);
		$request->execute(array($_GET['q']));
		$artists = $request->fetchAll();
		$artists = $artists ?: array();

		header('Content-type: application/json');
		echo json_encode($artists, JSON_PRETTY_PRINT);
	}
}
catch(Exception $e){
}

?>