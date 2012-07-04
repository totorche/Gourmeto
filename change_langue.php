<?php

include_once("include/headers.php");

if (!isset($_GET['l']))
  exit(_("Aucune langue spécifiée !"));
  
if (!isset($_GET['p']) || !is_numeric($_GET['p']))
  exit(_("Aucune page spécifiée !"));
  
try{
  $page = new Miki_page($_GET['p']);
  $url = $page->get_url_simple($_GET['l']);
  
  if (!$url)
    exit(_("La page demandée n'existe pas dans la langue donnée !"));
}
catch(Exception $e){
  exit(_("La page demandée n'existe pas dans la langue donnée !"));
}

// récupert les valeur passées en GET
$gets = $_GET;
$get_req = "";
foreach($gets as $key=>$val){
  if ($key != "p" && $key != "pn" && $key != "l")
    $get_req .= "$key=$val&";
}

if ($get_req != "" && strpos($url, '?') === false){
  $url .= "?$get_req";
}
elseif ($get_req != ""){
  $url .= "&$get_req";
}
  
$_SESSION['lang'] = $_GET['l'];
miki_redirect($url);

?>