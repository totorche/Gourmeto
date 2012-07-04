<?php
  require_once ("include/headers.php");
  
  if (!test_right(52)){
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
  
  if (!isset($_POST['id_deal'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  try{
    $deal = new Miki_deal($_POST['id_deal']);
    $article = new Miki_shop_article($deal->id_article);
  }
  catch(Exception $e){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }
  
  $erreur = "";
  
  // teste que tous les champs obligatoires soient remplis
  if (!isset($_POST['date_debut']) || $_POST['date_debut'] == "")
    $erreur .= "<br />- Date de début";
  if (!isset($_POST['date_fin']) || $_POST['date_fin'] == "")
    $erreur .= "<br />- Date de fin";
  if (!isset($_POST['price']) || $_POST['price'] == "")
    $erreur .= "<br />- Prix";
    
  if ($erreur != ""){
    $erreur = "Les champs suivants sont obligatoires : " .$erreur;
    
    $referer = "index.php?pid=263&id=$promo->id";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $erreur;
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  $date_start = explode("/", $_POST['date_debut']);
  $date_start = $date_start[2] .'-' .$date_start[1] .'-' .$date_start[0] .' ' .$_POST['heure_debut'] .':' .$_POST['minute_debut'] .':00';
  $date_stop = explode("/", $_POST['date_fin']);
  $date_stop = $date_stop[2] .'-' .$date_stop[1] .'-' .$date_stop[0] .' ' .$_POST['heure_fin'] .':' .$_POST['minute_fin'] .':00';
  
  $deal->id_article = $article->id;
  $deal->date_start = $date_start;
  $deal->date_stop = $date_stop;
  $deal->price = $_POST['price'];
  $deal->quantity_start = $_POST['quantity_start'];
  $deal->quantity = $deal->quantity_start - $_POST['quantity_sold'];
  
  // vérifie la quantité
  if ($deal->quantity < 0)
    $deal->quantity = 0;
  
  // sauve l'article
  try{
    $deal->update();
  }catch(Exception $e){
    $referer = "index.php?pid=263&id=$article->id";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("Le deal a été modifié avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=261&id=$article->id";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>