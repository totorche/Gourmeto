<?php 
  require_once("include/headers.php");
  
  session_start();
  $_SESSION['lang'] = 'fr';
  
  if (!test_right(32))
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
  $referer = "index.php?pid=107";
  
  try{
    foreach($pid as $p){
      $order = new Miki_order($p); 
      if ($state == 2){
        $order->set_completed(false, true);
      }
      else{
        $order->state = $state;
        $order->date_payed = 'NULL';
        $order->update();
      }
    }
  }catch(Exception $e){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = _("Une erreur est survenue lors la modification des commandes sélectionnés : " .$e->getMessage());
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("L'état des commandes sélectionnés a été modifié avec succès");
  // puis redirige vers la page précédente
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>