<?php
  require_once ("include/headers.php");
  
  if (!test_right(51)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // si le type d'article n'a pas été donné, on retourne à la 1ère étape
  if (!isset($_POST['article_type']) || !is_numeric($_POST['article_type'])){
    $referer = "index.php?pid=1441";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  // si on est en train d'ajouter un produit configurable et que les sets d'options n'ont pas été transmis, on retourne à la 1ère étape
  if ($_POST['article_type'] == 2 && !isset($_POST['article_option_sets'])){
    $referer = "index.php?pid=1441";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
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
  if (!isset($_POST['price']) || $_POST['price'] == "")
    $erreur .= "<br />- Prix";
  if ($use_stock && (!isset($_POST['quantity']) || $_POST['quantity'] == ""))
    $erreur .= "<br />- Quantité";
  
    
  if ($erreur != ""){
    $erreur = "Les champs suivants sont obligatoires : " .$erreur;
    
    $referer = "index.php?pid=1442&article_type=" .$_REQUEST['article_type'];
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $erreur;
    
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }

  $article = new Miki_shop_article();
  $article->id_shop = $shop->id;
  $article->id_category = $_POST['category'];
  $article->type = $_POST['article_type'];
  $article->ref = $_POST['ref'];
  $article->weight = $_POST['weight'];
  $article->price = $_POST['price'];
  $article->state = $_POST['state'];
  $article->quantity = $_POST['quantity'];
  
  foreach($_POST['name'] as $key => $content){
    $article->name[$key] = stripslashes($content);
  }
  
  foreach($_POST['description'] as $key => $content){
    $article->description[$key] = stripslashes($content);
  }
  
  // sauve l'article
  try{
    $article->save();
    
    // si on est en train d'ajouter un produit configurable on ajoute les options
    if ($article->type == 2 && isset($_POST['article_option'])){
      // parcourt les sets d'options
      $x = 0;
      foreach($_POST['article_option'] as $set_id => $sets){
        // ajoute le set à l'article
        $article->add_set($set_id);
        
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
    $referer = "index.php?pid=1442&article_type=" .$_REQUEST['article_type'];
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("L'article a été ajouté avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=143";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>