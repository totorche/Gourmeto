<?php

// vérifie si on tourne sous Windows ou pas
if (strpos($_SERVER['SERVER_SOFTWARE'], 'Win32') !== false || 
    strpos($_SERVER['SERVER_SOFTWARE'], 'Win64') !== false)
  $is_windows = true;
else
  $is_windows = false;

if ($is_windows)
  ini_set("include_path", ini_get("include_path") .".;/usr/local/lib/php;./class;./include;./scripts;../class;../include;../scripts;./include/mail");
else
  ini_set("include_path", ini_get("include_path") .".:/usr/local/lib/php:./class:./include:./scripts:../class:../include:../scripts:./include/mail");



// définit le décalage horaire
date_default_timezone_set ("Europe/Zurich");

// auto-include des classes
function __autoload($class_name) {
  global $is_windows;
  
  $class_name = strtolower($class_name);
  
  if ($is_windows)
    $ps = explode(";", ini_get('include_path'));
  else
    $ps = explode(":", ini_get('include_path'));

  foreach($ps as $path){
    if(is_readable($path.'/'.$class_name .".php")){
      require_once $path.'/'.$class_name .".php";
      return true;
    }
  }
  if(is_readable($class_name .".php")){
    require_once $class_name .".php";
    return true;
  }
}

// inclusion du script de connexion à la base de données
require_once("config.php");

// détermine le chemin du fichier par rapport au site de base (pour l'url rewritting)
$url_base = Miki_configuration::get('site_url');
$url_base = mb_substr($url_base, 7);

$url_script = $_SERVER["SERVER_NAME"] .$_SERVER["REQUEST_URI"];

// ajoute ou enlève le "www." selon s'il est présent ou pas dans la configuration du site Internet
/*if (mb_substr($url_base, 0, 4) == 'www.' && mb_substr($url_script, 0, 4) != 'www.'){
  $url_script = 'www.' .$url_script;
}
elseif (mb_substr($url_base, 0, 4) != 'www.' && mb_substr($url_script, 0, 4) == 'www.'){
  $url_script = mb_substr($url_script, 4);
}*/

// enlève le premier sous-domaine de l'url
$url_script_parts = explode(".", $url_script);
if (sizeof($url_script_parts) == 3 && $url_script_parts[0] == 'www')
  $url_script = $url_script_parts[1] ."." .$url_script_parts[2];

$tab = explode("?", $url_script);
$url_script = $tab[0];

$url = str_replace($url_base, "", $url_script);

if (substr($url, 0, 1) == '/')
  $url = mb_substr($url, 1, mb_strlen($url)-1);

$tab_domain = explode("/",$url);

$url = "";
for($x=1; $x<sizeof($tab_domain); $x++){
  $url .= "../";
}

define("URL_BASE", $url);

// configure puis démarre la session
ini_set('session.use_trans_sid', false);
ini_set('session.use_cookies', true);
ini_set('url_rewriter.tags',''); 

$domain_session = Miki_configuration::get('site_url');
if (substr($domain_session, 0, 7) == 'http://')
  $domain_session = mb_substr($domain_session, 7);
elseif (substr($domain_session, 0, 8) == 'https://')
  $domain_session = mb_substr($domain_session, 8);
  
if (substr_count($domain_session, '.') == 1)
  $domain_session = '.' .$domain_session;

session_set_cookie_params(0, '/', $domain_session);
session_name('miki');

session_start();

$headers_lang_define = false;

// définit la langue par défaut
if (!isset($_SESSION['lang'])){
  $_SESSION['lang'] = Miki_language::get_main_code();
  $headers_lang_define = true;
}

if (!isset($_SESSION['url_back']) || $_SESSION['url_back'] == "")
  $_SESSION['url_back'] = Miki_configuration::get('site_url');

// inclus le différentes fonctions Miki
require_once("functions.php");

header('Content-type: text/html; charset=UTF-8');

// définit l'encodage interne
mb_internal_encoding("UTF-8");

// définition de certaine constantes
define("SITENAME", Miki_configuration::get("sitename"));
define("SITE_URL", Miki_configuration::get("site_url"));
define("EMAIL_ANSWER", Miki_configuration::get("email_answer"));

// récupert la langue préférée d'après le navigateur
function get_pref_language_array()
{
  $resultat = array();
  
  if (!isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]))
    return $resultat;
  
  $langs = explode(',',$_SERVER["HTTP_ACCEPT_LANGUAGE"]);
  $qcandidat = 0;
  $nblang = sizeof($langs);

  for ($i=0; $i<$nblang; $i++)
  {
    for ($j=0; $j<sizeof($langs); $j++) {
      $lang = trim($langs[$j]);
      
      if (!strstr($lang, ';') && $qcandidat != 1) {
        $candidat = $lang;
        $qcandidat = 1;
        $indicecandidat = $j;
      }
      else {
        $q = preg_replace('/.*;q=(.*)/', '\\1', $lang);

        if ($q > $qcandidat) {
          $candidat = preg_replace('/(.*);.*/', '\\1', $lang);
          $qcandidat = $q;
          $indicecandidat = $j;     
        }
      }
    }

    if (strpos($candidat, "-")){
      $candidat = explode("-", $candidat);
      $candidat = $candidat[0];
    }
    
    $resultat[$i] = $candidat;

    $qcandidat=0;
    unset($langs[$indicecandidat]);   
    $langs = array_values($langs);
  }
  return $resultat;
}


// gestionnaire d'exceptions
function exception_handler($exception) {
  // on lit les principales variables d'environnement
  // pour ecrire leur contenu dans le log
  global $HTTP_HOST, $HTTP_USER_AGENT, $REMOTE_ADDR, $REQUEST_URI;
  // on donne un nom au fichier d'erreur
  $errorLog = URL_BASE ."erreur.log";    
  // construction du contenu du fichier d'erreur
  $errorString = "Date: " . date("d-m-Y H:i:s") . "\n";
  $errorString .= "Type d'erreur: EXCEPTION\n";
  $errorString .= "Message d'erreur: " .$exception->getMessage() ."\n";
  $errorString .= "Fichier: " .$exception->getFile() ."(" .$exception->getLine() .")\n";
  $errorString .= "Host: $HTTP_HOST\n";
  $errorString .= "Client: $HTTP_USER_AGENT\n";
  $errorString .= "Client IP: $REMOTE_ADDR\n";
  $errorString .= "Request URI: $REQUEST_URI\n\n";
  // ecriture du log dans le fichier erreur.log
  $fp = fopen($errorLog, "a+");
  fwrite($fp, $errorString);
  fclose($fp);

  // display error message
  echo "<h4>Erreur (<small>" .$exception->getMessage() ."</small>)</h4>";
  echo "Nous sommes désolés, mais cette page ne peut être affichée à cause d'une erreur interne.
  <br />
  Cette erreur a été enregistrée et sera corrigée dès que possible.
  <br /><br/>
  <a href=# onClick='history.go(-1)'>Cliquer ici pour revenir au menu précédent.</a>";
}
set_exception_handler('exception_handler');

// fonction permettant de traiter les erreurs et de les archiver dans un fichier log
function error($type, $msg, $file, $line)
{
    // on lit les principales variables d'environnement
    // pour ecrire leur contenu dans le log
    global $HTTP_HOST, $HTTP_USER_AGENT, $REMOTE_ADDR, $REQUEST_URI;
    // on donne un nom au fichier d'erreur
    $errorLog = URL_BASE ."erreur.log";   
    // construction du contenu du fichier d'erreur
    $errorString = "Date: " . date("d-m-Y H:i:s") . "\n";
    $errorString .= "Type d'erreur: $type\n";
    $errorString .= "Message d'erreur: $msg\n";
    $errorString .= "Fichier: $file($line)\n";
    $errorString .= "Host: $HTTP_HOST\n";
    $errorString .= "Client: $HTTP_USER_AGENT\n";
    $errorString .= "Client IP: $REMOTE_ADDR\n";
    $errorString .= "Request URI: $REQUEST_URI\n\n";
    // ecriture du log dans le fichier erreur.log
    $fp = fopen($errorLog, "a+");
    fwrite($fp, $errorString);
    fclose($fp);
    
    // n'affiche que 
    if ($type == E_ERROR || $type == E_WARNING ){
      // display error message
      echo "<h4>Erreur (<small>$msg</small>)</h4>";
      echo "Nous sommes désolés, mais cette page ne peut être affichée à cause d'une erreur interne.
      <br />
      Cette erreur a été enregistrée et sera corrigée dès que possible.
      <br /><br/>
      <a href=# onClick='history.go(-1)'>Cliquer ici pour revenir au menu précédent.</a>";
    }
}

// pour le développement : renvoie toutes les erreurs 
error_reporting(E_ALL | E_STRICT);

// pour la production : définit le gestionnaire d'erreurs
//set_error_handler("error");

?>