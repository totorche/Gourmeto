<?php
  require_once ("include/headers.php");
  
  if (!test_right(76)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['id']) || !is_numeric($_POST['id'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  try{
    $element = new Miki_shop_transporter($_POST['id']);
  }
  catch(Exception $e){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // test que l'on aie bien un shop de configuré
  $shops = Miki_shop::get_all_shops();
  if (sizeof($shops) == 0){
    $shop = false;
  }
  else
    $shop = array_shift($shops);
    
  if (!$shop){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  $erreur = "";
  
  // teste que tous les champs obligatoires soient remplis
  if (!isset($_POST['name']) || $_POST['name'] == "")
    $erreur .= "<br />- Nom";
    
  if ($erreur != ""){
    $erreur = "Les champs suivants sont obligatoires : " .$erreur;
    
    $referer = "index.php?pid=252";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $erreur;
    
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }

  $element->name = stripslashes($_POST['name']);
  $element->state = $_POST['state'];
  $element->shipping_delay = $_POST['shipping_delay'];
  $element->tax = isset($_POST['tax']) ? $_POST['tax'] : 0;
  $element->url_tracking = $_POST['url_tracking'];
  
  // sauve l'élément
  try{
    // ajoute le logo
    if ($_FILES["logo"]['error'] != 4){
      $element->upload_picture($_FILES["logo"], $element->id, $x);
    }
    
    // puis met à jours l'article avec les images
    $element->update();
  }catch(Exception $e){
    $referer = "index.php?pid=252";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("L'article a été modifié avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=251";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>