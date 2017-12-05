

<?php
require_once('myPDO.mysql.cd.include.php') ;

try {
    // Une question ?
    if (isset($_GET['q'])) {
        // On demande à PHP de se reposer quelques secondes pour introduire une latence
        if (isset($_GET['wait'])) {
            usleep(rand(0, 20) * 100000) ;
        }
        // Préparation de la requête
        // Recherche de tous les artistes possédant au moins un album d'un genre
        $req = myPDO::getInstance()->prepare(<<<SQL
            SELECT id, name txt
            FROM artist
            WHERE id IN (SELECT artist
                         FROM album
                         WHERE genre = ?)
            ORDER BY name
SQL
            ) ;
        // Exécution de la requête
        $req->execute(array($_GET['q'])) ;
        $artists = $req->fetchAll() ;
        $artists = $artists ?: array() ;
        // En-tete HTTP pour signifier au navigateur client que la réponse est du JSON
        header('Content-type: application/json') ;
        echo json_encode($artists, JSON_PRETTY_PRINT /* Uniquement pour la présentation dans le sujet */) ;
    }
}
catch (Exception $e) {
}

