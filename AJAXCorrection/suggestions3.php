

<?php
require_once "webpage.class.php" ;

$p = new WebPage('Suggestions de noms en AJAX') ;

$p->appendCssUrl('/css/style.css') ;
$p->appendJsUrl('request.js') ;

$p->appendJs(<<<JAVASCRIPT
    // Fonction appelée au chargement complet de la page
    window.onload = function () {
        // Désactivation de l'envoi du formulaire
        document.forms['f'].onsubmit = function () { return false ; }

        // Placer le focus dans le champ de saisie
        document.forms['f'].elements['nom'].focus() ;

        var request = null ;
        // Fonction appelée lors d'une modification de la saisie
        document.forms['f'].elements['nom'].onkeyup = function() {
            var str = document.forms['f'].elements['nom'].value ;

            if (request != null) {
                request.cancel() ;
                wait(false) ;
            }

            // Chaîne vide ?
            if (str.length == 0) {
                // Oui => état initial, pas de suggestion
                document.getElementById("liste_ajax").innerHTML = "" ;
                return ;
            }
            // Création de la requête AJAX
            request = new Request(
                {
                    url        : "liste_artistes.php",
                    method     : 'get',
                    handleAs   : 'text',
                    parameters : { q : str, wait : true },
                    onSuccess  : function(res) {
                            document.getElementById("liste_ajax").innerHTML = res ;
                            wait(false) ;
                        },
                    onError    : function(status, message) {
                            window.alert('Error ' + status + ': ' + message) ;
                        }
                }) ;
            wait(true) ;
        }

        // Fonction d'affichage de l'attente
        function wait(etat) {
            document.getElementById('img_wait').style.visibility = etat ? 'visible' : 'hidden' ;
        }
    }
JAVASCRIPT
) ;

$p->appendContent(<<<HTML
    <!-- Formulaire -->
    <form name='f'>
        <p>Partie du nom de l'artiste&nbsp;:
        <!-- Champ de saisie -->
        <input type="text" id="nom"><img src='wait2.gif' id='img_wait' style='visibility: hidden'>
        <!-- SPAN pour afficher les suggestions -->
        <p>Suggestions: <span id="liste_ajax"></span></p>
    </form>
HTML
) ;

echo $p->toHTML() ;

