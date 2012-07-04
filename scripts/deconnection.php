<?php
  require_once("../include/headers.php");
  
  // détruit la variable de session concernant le login
  unset($_SESSION['miki_user_id']);
  
  // supprime le cookie s'il existe
  if (isset($_COOKIE['miki_user_id'])){
    setcookie("miki_user_id", "", time()-86400, "/");
  }
  
  // si une page de retour est donnée, on la récupert.
  if (isset($_REQUEST['goto'])){
    $goto = $_REQUEST['goto'];
  }
  // sinon on redirigera vers la page d'accueil
  else{
    $goto = Miki_configuration::get('site_url');
  }
  
  // redirige vers la page demandée
  miki_redirect($goto);
?>
