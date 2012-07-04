<?php 
  require_once("include/headers.php");
  
  if (!test_right(51) && !test_right(52) && !test_right(53)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_GET['id']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  $ids = explode(";",$_GET['id']);
  
  foreach($ids as $id){
    if (!is_numeric($id))
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }
  
  try{
    foreach($ids as $id){
      $el = new Miki_shop_article_option_set($id); 
      $el->delete();
    }
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Les sets sélectionnés ont été supprimés avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=291";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    $referer = "index.php?pid=291";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>