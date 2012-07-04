<?php
  require_once("include/headers.php");
  
  if (!test_right(13))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
    echo "<script type='text/javascript'>document.location='" .$_SERVER["HTTP_REFERER"] ."';</script>";
  }
  
  // nettoie l'url du referer
  $referer = $_SERVER["HTTP_REFERER"];
  
  try{
    $page = new Miki_template($_GET['id']);
    
    // détermine le gabarit par défaut
    $page->set_for_all_pages();
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Le gabarit a été défini pour toutes les pages avec succès");
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = _("Une erreur est survenue lorsque le gabarit a été défini pour toutes les pages : ");
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }
?>