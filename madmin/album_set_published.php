<?php 
  require_once("include/headers.php");
  
  if (!test_right(41))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_GET['id']) || !isset($_GET['state']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  $ids = explode(";",$_GET['id']);
  $state = $_GET['state'];
  
  foreach($ids as $id){
    if (!is_numeric($id))
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }
  
  try{
    foreach($ids as $id){
      $album = new Miki_album($id);
      $album->state = $state;
      $album->update();
    }
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Les albums sélectionnés ont été modifiés avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=131";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    $referer = "index.php?pid=131";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>