<?php 
  require_once("include/headers.php");
  
  if (!test_right(47))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_GET['id']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  $ids = explode(";",$_GET['id']);
  
  foreach($ids as $id){
    if (!is_numeric($id))
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }
  
  try{
    $id_article = "";
    
    foreach($ids as $id){
      $code_promo = new Miki_shop_code_promo($id);
      $code_promo->delete();
    }
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Les code de promotion sélectionnés ont été supprimés avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=156";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    $referer = "index.php?pid=156";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>