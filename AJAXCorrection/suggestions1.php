

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

        // Fonction appelée lors d'une modification de la saisie
        document.forms['f'].elements['nom'].onkeyup = function() {
            var str = document.forms['f'].elements['nom'].value ;

            // Chaîne vide ?
            if (str.length == 0) {
                // Oui => état initial, pas de suggestion
                document.getElementById("liste_ajax").innerHTML = "" ;
                return ;
            }
            // Création de la requête AJAX
            new Request(
                {
                    url        : "liste_artistes.php",
                    method     : 'get',
                    handleAs   : 'text',
                    parameters : { q : str },
                    onSuccess  : function(res) {
                            document.getElementById("liste_ajax").innerHTML = res ;
                        },
                    onError    : function(status, message) {
                            window.alert('Error ' + status + ': ' + message) ;
                        }
                }) ;
        }
        // Placer le focus dans le champ de saisie
        document.forms['f'].elements['nom'].focus() ;
    }
JAVASCRIPT
) ;

$p->appendContent(<<<HTML
    <!-- Formulaire -->
    <form name='f'>
        <p>Partie du nom de l'artiste&nbsp;:
        <!-- Champ de saisie -->
        <input type="text" name="nom">
        <!-- SPAN pour afficher les suggestions -->
        <p>Suggestions: <span id="liste_ajax"></span></p>
    </form>
HTML
) ;

echo $p->toHTML() ;

