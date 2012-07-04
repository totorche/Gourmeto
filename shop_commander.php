<?php
  /**
   * La gestion des stocks est laissée à la classe "Miki_order"
   */
   
  require_once ("include/headers.php");
    
  if (!isset($_POST['aid']) || !is_numeric($_POST['aid'])){
    header('location: ' .$_SESSION['url_back']);
    exit();
  }
  
  // vérifie si on utilise Ajax ou non
  $ajax = (isset($_POST['ajax']) && $_POST['ajax'] == 1);
  
  // vérifie si c'est un deal ou pas
  $is_miki_deal = (isset($_POST['miki_deal']) && $_POST['miki_deal'] == 1);
  
  try{
    $aid = $_POST['aid'];
    $article = new Miki_shop_article($aid);
    $nombre = $_POST['quantity'];
    
    // vérifie les informations
    if (!is_numeric($nombre)){
      // erreur
      send_result(false, _("Veuillez spécifier la quantité désirée"), $ajax);
    }
    
    // affecte les attributs de l'article
    $attributes_list = $article->get_attributes();
    $attributs = "";
    foreach($attributes_list as $key => $a){
      $attributs .= "$key={$_POST[$a['code']]}&&";
    }
    $attributs = substr($attributs, 0, strlen($attributs) - 2);
    
    // récupert la personne connectée si disponible
    $miki_person = is_connected();
    
    // si une commande est déjà en cours, on la récupert
    if (isset($_SESSION['miki_order']) && $_SESSION['miki_order'] instanceof Miki_order){
      $order = $_SESSION['miki_order'];
      
      // si personne n'est encore affecté à la commande, on affecte le membre connecté à la commande
      if (!is_numeric($order->id_person)){
        if ($miki_person instanceof Miki_person){
          $order->id_person = $miki_person->id;
          $order->update();
        }
      }
    }
    else{
      // sinon, on en créé une
      $order = new Miki_order();
      
      // on affecte le membre connecté à la commande
      if ($miki_person instanceof Miki_person)
        $order->id_person = $miki_person->id;
      else
        $order->id_person = 'NULL';
      
      // si c'est un deal, on définit la commande comme étant de type "deal" (type = 2)
      if ($is_miki_deal)
        $order->type = 2;
        
      $order->save();
      $_SESSION['miki_order'] = $order;
    }
    
    // si c'est un article configurable et que des options ont été demandées, 
    // on récupert ces options pour ensuite vérifier si l'article existe déjà dans le panier avec les options données
    $options = array();
    if ($article->type == 2 && isset($_POST['article_options'])){
      if (is_array($_POST['article_options'])){
        foreach($_POST['article_options'] as $option_id){
          $options[] = $option_id;
        }
      }
    }
    $options = implode("&&", $options);

    // ajoute l'article à la commande
    $id_order_article = $order->add_article($article->id, $nombre, false, $attributs, $options, $is_miki_deal);
    
    // si c'est un article configurable et que des options ont été demandées, on ajoute ces options à l'article dans la commande 
    if ($article->type == 2 && isset($_POST['article_options'])){
      $order_article = new Miki_order_article($id_order_article);
      if (is_array($_POST['article_options'])){
        foreach($_POST['article_options'] as $option_id){
          $order_article->add_option($option_id);
        }
      }
    }
    
    // OK
    //send_result(true, _("L'article a été ajouté à votre panier."), $ajax);
    send_result(true, "", $ajax);
  }
  catch(Exception $e){
    // erreur
    send_result(false, $e->getMessage(), $ajax);
  }

?>