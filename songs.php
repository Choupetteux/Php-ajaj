<?php
require_once('myPDO.mySql.phpAuth.php');

try {
    if (isset($_GET['q'])) {
        if (isset($_GET['wait'])) {
            usleep(rand(0, 20) * 100000);
        }
        $req = myPDO::getInstance()->prepare(<<<SQL
            SELECT LPAD(number, 2, '0') num,
                   name,
                   CONCAT(LPAD(duration DIV 60, 2, "0"),
                          ":",
                          LPAD(duration % 60, 2, "0")) duration
            FROM track, song
            WHERE album = ?
            AND track.song = song.id
            ORDER BY number
SQL
);
        $req->execute(array($_GET['q']));
        $songs = $req->fetchAll();
        $songs = $songs ?: array();
        header('Content-type: application/json');
        echo json_encode($songs, JSON_PRETTY_PRINT);
    }
}
catch (Exception $e) {
}