<?php
// Singleton de connexion à une base de données
require_once 'myPDO.class.php' ;
// Paramètre de connexion
myPDO::setConfiguration('mysql:host=mysql;dbname=cutron01_cdobj;charset=utf8', 'web', 'web');

?>
