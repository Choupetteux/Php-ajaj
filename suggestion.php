<?php
/**
require_once 'myPDO.class.php';
require_once 'myPDO.mySql.phpAuth.php';
require_once "webpage.class.php" ;

$p = new WebPage('Suggestions de noms en AJAX') ;

$p->appendJsUrl('request.js') ;

$p->appendJs(<<<JAVASCRIPT
		// Fonction appelée au chargement complet de la page
		window.onload = function () {
   			// Désactivation de l'envoi du formulaire
   		 	document.forms['artiste'].onsubmit = function () { return false ; }

    		// Fonction appelée lors d'une modification de la saisie
   			document.forms['artiste'].elements['firstname'].onkeyup = function() {
   				console.log(document.forms['artiste'].elements['firstname'].value);
   				new Request(
            	{
                url        : "{$_SERVER['PHP_SELF']}",
                method     : 'post',
                handleAs   : 'text',
                parameters : { q : "document.forms['artiste'].elements['firstname'].value" },
                onSuccess  : function(res) {
                        document.getElementById('test_results').appendChild(res) ;
                    },
                onError    : function(status, message) {
                        window.alert('Error ' + status + ': ' + message) ;
                    }
            }) ;
   		 	}
		}

JAVASCRIPT
);

$p->appendContent("
	<form name='artiste' method='GET'>
  		Partie du nom de l'artiste : <input name='firstname' type='text'>
	</form>

	<span id='sugg'>Suggestions : </span><br/><br/>");

echo $p->toHTML() ;



<?php
require_once 'myPDO.class.php';
require_once 'myPDO.mySql.phpAuth.php';
require_once "webpage.class.php" ;

$p = new WebPage('Suggestions de noms en AJAX') ;
$p->appendCssUrl('/css/style.css')
$p->appendJsUrl('request.js');
$p->appendJs(<<<JAVASCRIPT
window.onload=function(){
  document.form['f'].elements['nom'].onkeyup=function(){
  var str= document.forms['f'].elements['nom'].value;
      if(str.length==0){
  document.getElementById("liste_ajax").innerHTML="";
  return;
      }
      new AjaxRequest(
      {
        url:"liste_artistes.php",
        method:"get",
        handleAs:'text',
        parameters: {q:str},
        onSuccess
    }
**/
require_once 'webpage.class.php' ;

$p = new WebPage('Suggestions de noms en AJAX') ;

$p->appendCssUrl('/css/style.css') ;
$p->appendJsUrl('request.js') ;

$p->appendJs(<<<JAVASCRIPT

    window.onload = function () {

        document.forms['f'].onsubmit = function () { return false ; }


        document.forms['f'].elements['nom'].onkeyup = function() {
            var str = document.forms['f'].elements['nom'].value ;


            if (str.length == 0) {

                document.getElementById("liste_ajax").innerHTML = "" ;
                return ;
            }

            new AjaxRequest(
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
        
        document.forms['f'].elements['nom'].focus() ;
    }
    
JAVASCRIPT
) ;

$p->appendContent(<<<HTML
    <form name='f'>
        <p>Partie du nom de l'artiste&nbsp;:
        <input type="text" name="nom">
        <p>Suggestions: <span id="liste_ajax"></span></p>
    </form>
HTML
) ;

echo $p->toHTML() ;