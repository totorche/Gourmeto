<?php
  require_once ("include/headers.php");
  
  if (!test_right(47)){
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
  
  if (!isset($_POST['id_code_promo'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  try{
    $code_promo = new Miki_shop_code_promo($_POST['id_code_promo']);
  }
  catch(Exception $e){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  $erreur = "";
  
  // teste que tous les champs obligatoires soient remplis
  if (!isset($_POST['code']) || $_POST['code'] == "")
    $erreur .= "<br />- Code";
  if (!isset($_POST['type']) || $_POST['type'] == "")
    $erreur .= "<br />- Type de rabais";
  if (!isset($_POST['discount']) || $_POST['discount'] == "")
    $erreur .= "<br />- Rabais";
  if (!isset($_POST['date_start']) || $_POST['date_start'] == "")
    $erreur .= "<br />- Date de début";
  if (!isset($_POST['date_stop']) || $_POST['date_stop'] == "")
    $erreur .= "<br />- Date de fin";
    
  if ($erreur != ""){
    $erreur = "Les champs suivants sont obligatoires : " .$erreur;
    
    $referer = "index.php?pid=158&id=$code_promo->id";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $erreur;
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  $date_start = explode("/", $_POST['date_start']);
  $date_start = $date_start[2] .'-' .$date_start[1] .'-' .$date_start[0];
  $date_stop = explode("/", $_POST['date_stop']);
  $date_stop = $date_stop[2] .'-' .$date_stop[1] .'-' .$date_stop[0];
  
  $code_promo->code = $_POST['code'];
  $code_promo->type = $_POST['type'];
  $code_promo->discount = $_POST['discount'];
  $code_promo->date_start = $date_start;
  $code_promo->date_stop = $date_stop;
  
  // sauve l'article
  try{
    $code_promo->update();
  }catch(Exception $e){
    $referer = "index.php?pid=158&id=$code_promo->id";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("Le code de promotion a été modifié avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=156";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>