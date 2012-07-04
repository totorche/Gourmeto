<?php
  require_once ("include/headers.php");
  
  if (!test_right(51) && !test_right(52) && !test_right(53)){
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
  
  // vérifie si on utilise la gestion des stock ou pas
  $use_stock = Miki_configuration::get('miki_shop_gestion_stock');
  
  // teste que tous les champs obligatoires soient remplis
  if (!isset($_POST['name']) || sizeof($_POST['name']) == 0)
    $erreur .= "<br />- Nom";
  if (!isset($_POST['price']) || $_POST['price'] == "")
    $erreur .= "<br />- Prix";
  if (isset($_POST['use_stock']) && $_POST['use_stock'] == 1 && (!isset($_POST['quantity']) || $_POST['quantity'] == ""))
    $erreur .= "<br />- Quantité";
  
    
  if ($erreur != ""){
    $erreur = "Les champs suivants sont obligatoires : " .$erreur;
    
    $referer = "index.php?pid=282";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $erreur;
    
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }

  $option = new Miki_shop_article_option();
  $option->id_shop = $shop->id;
  $option->ref = $_POST['ref'];
  $option->price = $_POST['price'];
  $option->state = $_POST['state'];
  $option->use_stock = $_POST['use_stock'];
  $option->quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 0;
  
  foreach($_POST['name'] as $key => $content){
    $option->name[$key] = stripslashes($content);
  }
  
  foreach($_POST['description'] as $key => $content){
    $option->description[$key] = stripslashes($content);
  }
  
  // sauve l'article
  try{
    $option->save();
    
    // ajoute les images
    $nb_pictures = $_POST['nb_pictures'];
    $nb_pictures_uploaded = 0;
    
    for($x=1; $x<=$nb_pictures; $x++){
      if ($_FILES["picture$x"]['error'] != 4){
        $nb_pictures_uploaded++;
        $option->upload_picture($_FILES["picture$x"], reset($option->name), $x);
      }
    }
    
    // puis met à jours l'article avec les images
    $option->update();
    
  }catch(Exception $e){
    $referer = "index.php?pid=282";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("L'option a été ajoutée avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=281";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>