<?php
	error_reporting(E_ALL | E_STRICT);
	date_default_timezone_set('Europe/Paris');
	spl_autoload_register(function($_class)
	{
		$fichier= $_class.'.class.php';
		if (file_exists($fichier)) 
			require_once ($fichier);
		if (file_exists('../'. $fichier)) 
			require_once('../'. $fichier);
	}
//    include $class_name . '.class.php';
);
?>