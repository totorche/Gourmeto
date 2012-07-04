<?php 
  require_once("include/headers.php");
  
  if (!test_right(66))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_GET['id']) || !isset($_GET['state']) || !is_numeric($_GET['state'])){
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
      $document = new Miki_document($id);
      $document->state = $state;
      $document->update();
    }
  }catch(Exception $e){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = _("Une erreur est survenue lors la modification des documents sélectionnés");
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("L'état des documents sélectionnés a été modifié avec succès");
  // puis redirige vers la page précédente
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>