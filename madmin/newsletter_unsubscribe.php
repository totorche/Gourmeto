<?php 
  require_once("include/headers.php");

  if (!isset($_GET['id']) || $_GET['id'] == ""){
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = "Des informations sont manquantes ou erronnées.";
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_GET['gid']) || $_GET['gid'] == ""){
    $group = "";
  }
  else{
    $group = $_GET['gid'];
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
      $member = new Miki_newsletter_member($id);
      
      if (is_numeric($group))
        $member->remove_from_group($group);
      else
        $member->delete();
    }
    
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = "L'annulation de l'abonnement aux newsletter a été effectué avec succès.";
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }
  catch(Exception $e){
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = "Une erreur est survenue lors de l'annulation de l'abonnement aux newsletter  : " .$e->getMessage();
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
?>