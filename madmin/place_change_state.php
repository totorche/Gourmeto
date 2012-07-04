<?php 
  require_once("include/headers.php");
  
  if (!test_right(69))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_GET['id'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }
  
  if (!isset($_GET['state']) || !is_numeric($_GET['state'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }
  
  $state = $_GET['state'];
  
  $ids = explode(";",$_GET['id']);
  
  foreach($ids as $id){
    if (!is_numeric($id)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
  }
  
  // url du referer
  $referer = $_SESSION['url_back'];
  
  try{
    foreach($ids as $id){
      $place = new Miki_place($id);
      $place->state = $state;
      $place->update();
    }
  }catch(Exception $e){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = _("Une erreur est survenue lors la modification des bonnes adresses sélectionnées");
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("L'état des bonnes adresses sélectionnées a été modifié avec succès");
  // puis redirige vers la page précédente
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>