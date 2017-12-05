<?php

require_once('myPDO.mySql.phpAuth.php') ;

try{
	if(isset($_GET ['q'])){
		if(isset($_GET['wait'])){
			usleep(rand(0,20) * 100000);
		}

		$request = myPDO::getInstance()->prepare(<<<SQL
			SELECT id, CONCAT(year, " - ", name) txt
			FROM album
			WHERE artist = ?
			ORDER BY year
SQL
);
		$request->execute(array($_GET['q']));
		$albums = $request->fetchAll();
		$albums = $albums ?: array();
		header('Content-type: application/json');
		echo json_encode($albums, JSON_PRETTY_PRINT);
	}
}
catch(Exception $e){
}

?>