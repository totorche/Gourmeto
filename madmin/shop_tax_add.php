<?php
  require_once ("include/headers.php");
  
  if (!test_right(47)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['tax_name']) || empty($_POST['tax_name'])){
    $referer = "index.php?pid=271";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = "Les champs suivants sont obligatoires : " ._("Nom de la taxe");
    
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  $tax_name = $_POST['tax_name'];
  
  // ajoute la taxe
  try{
    Miki_shop::add_tax($tax_name, "all", 0);
  }
  catch(Exception $e){
    $referer = "index.php?pid=271";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = "Erreur : " .$e->getMessage();
    
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("La taxe a été ajoutée avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=271";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>