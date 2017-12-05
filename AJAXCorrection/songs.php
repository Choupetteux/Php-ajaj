

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
        // Recherche de tous les morceaux d'un album
        $req = myPDO::getInstance()->prepare(<<<SQL
            SELECT -- Numéro du morceau avec des zéros en tête
                   LPAD(number, 2, '0') num,
                   name,
                   -- Durée du morceau au format mm:ss
                   CONCAT(LPAD(duration DIV 60, 2, "0"),
                          ":",
                          LPAD(duration % 60, 2, "0")) duration
            FROM track, song
            WHERE album = ?
              AND track.song = song.id
            ORDER BY number
SQL
            ) ;
        // Exécution de la requête
        $req->execute(array($_GET['q'])) ;
        $songs = $req->fetchAll() ;
        $songs = $songs ?: array() ;
        // En-tete HTTP pour signifier au navigateur client que la réponse est du JSON
        header('Content-type: application/json') ;
        echo json_encode($songs, JSON_PRETTY_PRINT /* Uniquement pour la présentation dans le sujet */) ;
    }
}
catch (Exception $e) {
}

