<?php 
  require_once("include/headers.php");
  
  if (!test_right(76))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_GET['id'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }
  
  if (!isset($_GET['state'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }
  
  $state = $_GET['state'];
  $pid = explode(";",$_GET['id']);
  
  foreach($pid as $p){
    if (!is_numeric($p))
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }
  
  // url du referer
  $referer = $_SESSION['url_back'];
  
  try{
    foreach($pid as $p){
      $element = new Miki_shop_transporter($p);
      $element->state = $state;
      $element->update();
    }
  }catch(Exception $e){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = _("Une erreur est survenue lors la modification des transporteurs sélectionnés");
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("L'état des transporteurs sélectionnés a été modifié avec succès");
  // puis redirige vers la page précédente
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>