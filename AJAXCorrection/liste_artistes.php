

<?php
// Un petit 404 pour tester les erreurs au niveau du client AJAX
// header("HTTP/1.0 404 Not Found") ;
// die() ;
require_once('myPDO.mysql.cdobj.include.php') ;


try {
    // Le tableau des suggestions
    $suggestion = array() ;
    // Une question ?
    if (isset($_GET['q'])) {
        // On demande à PHP de se reposer quelques secondes pour introduire une latence
        if (isset($_GET['wait'])) {
            usleep(rand(0, 20) * 100000) ;
        }
        $question = $_GET['q'] ;
        // Preparation de la requete
        $req = myPDO::getInstance()->prepare(<<<SQL
            SELECT name
            FROM artist
            WHERE name LIKE ?
            ORDER BY name
SQL
            ) ;
        // Execution de la requete
        $req->execute(array("%{$question}%")) ;
        // Parcours du resultat
        foreach ($req->fetchAll() as $artiste) {
            $suggestion[] = $artiste['name'] ;
        }

        // Des suggestions ?
        if (empty($suggestion)) {
            // Bof
            echo 'Rien à vous proposer...' ;
        }
        else {
            // Affichage de la question et des suggestions
            array_walk($suggestion,
                       function(&$v, $k) use ($question) {
                           $v = mb_ereg_replace($question, '<em>\\0</em>', $v, 'i') ;
                       }) ;
            header('Content-type: text/plain; charset=utf-8') ;
            echo "'{$question}' =&gt; ".implode(', ', $suggestion) ;
        }
    }
}
catch (Exception $e) {
    // En cas d'erreur, on envoie le message d'erreur
    header('HTTP/1.1 400 Bad Request', true, 400) ;
    echo $e->getMessage() ;
}

