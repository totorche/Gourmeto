<?php
 
  require_once("include/headers.php");
  
  if (!test_right(15))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
    echo "<script type='text/javascript'>document.location='" .$_SERVER["HTTP_REFERER"] ."';</script>";
  }
  
  // nettoie l'url du referer
  $referer = $_SERVER["HTTP_REFERER"];
  
  try{
    $page = new Miki_page($_GET['id']);
    
    // détermine le gabarit par défaut
    $page->set_default();
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("La page a été définie par défaut avec succès");
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = _("Une erreur est survenue lorsque la page a été définie par défaut");
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }
?>