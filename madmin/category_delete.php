<?php 
  require_once("include/headers.php");
  
  if (!test_right(27))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_GET['id']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  $ids = explode(";",$_GET['id']);
  
  foreach($ids as $id){
    if (!is_numeric($id))
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }
  
  try{
    foreach($ids as $id){
      $cat = new Miki_category($id); 
      $cat->delete();
    }
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Les catégories sélectionnées ont été supprimées avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=81";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    $referer = "index.php?pid=81";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>