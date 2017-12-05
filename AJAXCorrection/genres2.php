

<?php

require_once 'webpage.class.php' ;
require_once('myPDO.mysql.cd.include.php') ;

$p = new WebPage('Listes déroulantes en AJAX') ;

$p->appendCss(<<<CSS
    select {
        width : 15em ;
    }
CSS
) ;
$p->appendJsUrl('request.js') ;
$p->appendJs(<<<JAVASCRIPT
    window.onload = function () {
        // Vider les options (sauf la premiere) du select passé en paramètre
        function viderSelect(select) {
            select.selectedIndex = 0 ;
            for (var i=select.options.length-1; i>0; i--) {
                select.options.remove(i) ;
            }
        }

        // Ajouter une option en fin d'un select
        function ajouterOption(select, txt, id) {
            select.options.add(new Option(txt, id)) ;
        }

        // Vider un noeud DOM de tous ses fils
        function viderNoeud(noeud) {
            while (noeud.hasChildNodes()) {
                noeud.removeChild(noeud.lastChild) ;
            }
        }

        // Fonction d'affichage de l'attente
        function wait(etat) {
            document.getElementById('img_wait').style.visibility = etat ? 'visible' : 'hidden' ;
        }

        var request = null ;
        // Effectuer une requête auprès du serveur (url?q=str) et associer la fonction de traitement (stateChange)
        function charge(url    /** URL de la requête */,
                        str    /** La question */,
                        select /** Select à remplir */,
                        after  /** Fonction à lancer au résultat */) {
            if (request != null) request.cancel() ;
            wait(true) ;
            request = new Request(
                                    {
                                        url : url,
                                        // Méthode de la requête
                                        method   : "get",
                                        // Type de résultat attendu
                                        handleAs : 'json',
                                        // Paramètres de la requête
                                        parameters   : {
                                            // La question
                                            q   : str,
                                            // Demande au serveur de faire une pause
                                            wait : true,
                                            // Ajouter une partie aléatoire à la requête pour éviter la mise en cache
                                            sid : Math.random()
                                        },
                                        // Associer la fonction de traitement
                                        onSuccess    : function (json) {
                                            viderSelect(select) ;
                                            for (var i in json) {
                                                var element = json[i] ;
                                                ajouterOption(select, element.txt, element.id) ;
                                            }
                                            if (after) after() ;
                                            wait(false) ;
                                        },
                                        onError      : function () {
                                            wait(false) ;
                                        },
                                        // Effectuer la requête en mode ASYNCHRONE
                                        asynchronous : true }) ;
        }

        // Les divers éléments utiles dans les fonctions
        var formu = document.forms['choix'] ;
        var genre = formu.elements['genre'] ;
        var artist = formu.elements['artist'] ;
        var album = formu.elements['album'] ;
        var songs = document.getElementById('songs_panel') ;

        // En cas de changement de valeur
        genre.onchange = function () {
            charge("artists.php",
                   genre.options[genre.selectedIndex].value,
                   artist,
                   function () {
                       viderSelect(album) ;
                       viderNoeud(songs) ;
                   }
            ) ;
        }

        // En cas de changement de valeur
        artist.onchange = function () {
            charge("albums.php",
                   artist.options[artist.selectedIndex].value,
                   album,
                   function () {
                       viderNoeud(songs) ;
                   }
            ) ;
        }

        // En cas de changement de valeur
        album.onchange = function () {
            if (request != null) request.cancel() ;
            wait(true) ;
            new Request({
                url : "songs.php",
                method : "get",
                handleAs : "json",
                parameters : {
                    q : album.options[album.selectedIndex].value,
                    // Demande au serveur de faire une pause
                    wait : true,
                },
                onSuccess : function (json) {
                    viderNoeud(songs) ;
                    var h1 = document.createElement('h1') ;
                    h1.appendChild(document.createTextNode(artist.options[artist.selectedIndex].text
                                                           + ' - '
                                                           + album.options[album.selectedIndex].text)) ;
                    songs.appendChild(h1) ;
                    for (var i in json) {
                        var element = json[i] ;
                        var div = document.createElement('div') ;
                        div.appendChild(document.createTextNode(element.num
                                                                + ' - '
                                                                + element.name
                                                                + ' - '
                                                                + element.duration)) ;
                        songs.appendChild(div) ;
                    }
                    wait(false) ;
                },
                onError : function () {
                    wait(false) ;
                }
            }) ;
        }
    }
JAVASCRIPT
) ;

$p->appendContent(<<<HTML
    <form name='choix' action=''>
        <select name='genre' size='5'>
            <option value=''>Style...</option>\n
HTML
) ;

// Rechercher les genres
$res = myPDO::getInstance()->query(<<<SQL
    SELECT id, name
    FROM genre
    ORDER BY name
SQL
    ) ;

// Liste déroulante des genres
foreach ($res->fetchAll() as $genre) {
    $p->appendContent(<<<HTML
            <option value="{$genre['id']}">{$genre['name']}</option>\n
HTML
) ;
}
$p->appendContent(<<<HTML
        </select>
        <!-- Liste déroulante des artistes -->
        <select name='artist' size='5'>
            <option value=''>Artiste...</option>
        </select>
        <!-- Liste déroulante des albums -->
        <select name='album' size='5'>
            <option value=''>Album...</option>
        </select>
    </form>
    <img src='wait2.gif' id='img_wait' style='visibility: hidden'>
    <!-- Zone d'affichage pour les morceaux -->
    <div id='songs_panel'></div>
HTML
) ;

echo $p->toHTML() ;

