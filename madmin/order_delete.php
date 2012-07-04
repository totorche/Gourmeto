<?php 
  require_once("include/headers.php");
  
  if (!test_right(50)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_GET['id'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  $ids = explode(";",$_GET['id']);
  
  foreach($ids as $id){
    if (!is_numeric($id)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
  }
  
  try{
    foreach($ids as $id){
      $order = new Miki_order($id);
      $order->delete();
    }
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Les commandes sélectionnées ont été supprimées avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=147";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    $referer = "index.php?pid=147";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>