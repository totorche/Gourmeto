<?php
  require_once ("include/headers.php");
  
  if (!test_right(52)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['id']) || !is_numeric($_POST['id'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  try{
    $article = new Miki_shop_article($_POST['id']);
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
  
  // vérifie si on utilise la gestion des stock ou pas
  $use_stock = Miki_configuration::get('miki_shop_gestion_stock');
  
  // teste que tous les champs obligatoires soient remplis
  if (!isset($_POST['name']) || $_POST['name'] == "")
    $erreur .= "<br />- Nom";
  /*if (!isset($_POST['price']) || $_POST['price'] == "")
    $erreur .= "<br />- Prix";*/
  if ($use_stock && (!isset($_POST['quantity']) || $_POST['quantity'] == ""))
    $erreur .= "<br />- Quantité";
    
  if ($erreur != ""){
    $erreur = "Les champs suivants sont obligatoires : " .$erreur;
    
    $referer = "index.php?pid=1452&id=$article->id";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $erreur;
    
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  $article->id_shop = $shop->id;
  $article->id_category = $_POST['category'];
  $article->ref = $_POST['ref'];
  $article->weight = $_POST['weight'];
  $article->state = $_POST['state'];
  $article->quantity = $_POST['quantity'];
  
  if (isset($_POST['price']))
    $article->price = $_POST['price'];
  
  foreach($_POST['name'] as $key => $content){
    $article->name[$key] = stripslashes($content);
  }
  
  foreach($_POST['description'] as $key => $content){
    $article->description[$key] = stripslashes($content);
  }
  
  // sauve l'article
  try{
    
    // ajoute les option d'article à l'article en cours
    if ($article->type == 2 && isset($_POST['article_option'])){
      // supprime toutes les options
      $article->remove_all_options();
      
      // parcourt les sets d'options
      $x = 0;
      foreach($_POST['article_option'] as $set_id => $sets){
        // pour le positionnement des options
        $positions = array();
        // si les positions sont données
        if (isset($_POST['options_pos'][$x])){
          // on les récupert
          $pos_temp = explode("|", $_POST['options_pos'][$x]);
          $y = 1;
          foreach($pos_temp as $pos){
            if ($pos != ""){
              $positions[$pos] = $y;
              $y++;
            }
          }
        }
        
        // parcourt les options du set
        foreach($sets as $option_id){
          // récupert la position de l'option
          $pos = isset($positions[$option_id]) ? $positions[$option_id] : 0;
          // puis ajoute l'option à l'article en cours
          $article->add_option($option_id, $pos);
        }
        $x++;
      }
    }
    
    // ajoute les images
    $nb_pictures = $_POST['nb_pictures'];
    $nb_pictures_uploaded = 0;
    
    for($x=1; $x<=$nb_pictures; $x++){
      if ($_FILES["picture$x"]['error'] != 4){
      
        if (isset($article->pictures[$x-1]) && $article->pictures[$x-1] != ""){
          $article->delete_picture($article->pictures[$x-1]);
        }
      
        $nb_pictures_uploaded++;
        $article->upload_picture($_FILES["picture$x"], reset($article->name), $x);
      }
    }
  
    // supprime tous les attributs de l'article
    $article->delete_all_attributes();
    
    // ajoute les attributs sélectionnés à l'article    
    if (isset($_POST['attributes'])){
      // récupert les attributs sélectionnés
      $attributes = $_POST['attributes'];
      
      // ajoute les attributs sélectionnés à l'article
      foreach($attributes as $attribute){
        $article->add_attribute($attribute);
      }
    }
    
    // puis met à jours l'article avec les images
    $article->update();
    
    // et envoie l'article sur le blog si demandé
    if (Miki_configuration::get('publish_shop_article') == 1){
      $article->send_to_blog();
    }
      
  }catch(Exception $e){
    $referer = "index.php?pid=1452&id=$article->id";
    
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
  $referer = "index.php?pid=143";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>