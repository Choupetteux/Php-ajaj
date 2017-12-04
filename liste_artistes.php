<?php
/**require_once'myPDO.class.php';
require_once('myPDO.mySql.phpAuth.php');

$PDO = myPdo::getInstance()->prepare(
                "SELECT name
                FROM artist
                WHERE name LIKE '%' ? '%'
                ORDER BY name ASC"
);
       //     $PDO->setFetchMode(PDO::FETCH_CLASS,__CLASS__);)
            $PDO->execute(array($_GET['q']));
            $artists = $PDO->fetchAll();

$html = '';
foreach($artists as $i=>$artist){
	$html .= implode(", ",$artist ) . ', ';
}

echo $html;


//Celui du prof qui ne fonctionne pas

<?php

require_once'myPDO.class.php';
require_once('myPDO.mySql.phpAuth.php');


try{
	$suggestion= array();
	if (isset($_GET['q'])) {
		if (isset($_GET['wait'])) {
			usleep(rand(0,20)*100000);
		}	
		$question=$_GET['q'];
		$req=myPDO::getInstance()->prepare(<<<SQL
                SELECT name
                FROM artist
                WHERE name LIKE  ? 
                ORDER BY name
SQL
);
       //     $PDO->setFetchMode(PDO::FETCH_CLASS,__CLASS__);)
         $req->execute(array("%{$question}%"));
         foreach ($req->fetchall() as $artiste) {
         	$suggestion[]=$artiste['name'];
         }

         if (empty($suggestion)){
         	echo "rien à vous proposer...";
         }
         else{
         	array_walk($suggestion,
         		function(&$v,$k) use ($question){
         		$v=mb_ereg_replace($question, '<em>\\0</em>',$v,'i');
         	});
         	header('Content-type: text/plain;charset=utf-8');
         	echo "'{$question}'=&gt;".implode(',',$suggestion);
         }

         }
}
catch (Exception $e){
	header('HTTP/1.1 400 Bad Request',true,400);
	echo $e->getMessage();
}
**/
require_once'myPDO.mySql.phpAuth.php' ;

try {

    $suggestion = array() ;

    if (isset($_GET['q'])) {

        if (isset($_GET['wait'])) {
            usleep(rand(0, 20) * 100000) ;
        }
        $question = $_GET['q'] ;

        $req = myPDO::getInstance()->prepare(<<<SQL
            SELECT name
            FROM artist
            WHERE name LIKE ?
            ORDER BY name
SQL
);

        $req->execute(array("%{$question}%")) ;
        foreach ($req->fetchAll() as $artiste) {
            $suggestion[] = $artiste['name'] ;
        }

        
        if (empty($suggestion)) {

            echo 'pas de correspondance...' ;
        }
        else {

            array_walk($suggestion,
                       function(&$v, $k) use ($question) {
                           $v = mb_ereg_replace($question, '<em>\\0</em>', $v, 'i') ;
                       }) ;
           // header('Content-type: text/plain; charset=utf-8') ;
            echo "'{$question}' =&gt; ".implode(', ', $suggestion) ;
        }
        
    }
}
catch (Exception $e) {
    header('HTTP/1.1 400 Bad Request', true, 400) ;
    echo $e->getMessage() ;
}
