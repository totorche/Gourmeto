<?php
  
  require_once("include/headers.php");
  require_once("scripts/set_links.php");
  
  /*****************************************************************
   *
   * Liste des actions possibles :
   * 
   *   1 : Ajoute un article à la commande
   *   2 : Ajoute x quantité d'un article à la commande
   *   3 : Supprime x quantité d'un article de la commande
   *   4 : Modifie la quantité d'un article dans la commande      
   *   5 : Supprime un article de la commande
   *   6 : Vide le panier                     
   *   
   *****************************************************************/


  // si on effectue une action (ajout, modification, suppression) sur un article, on récupert l'article
  if (isset($_GET['v']) && is_numeric($_GET['v'])){
    try{
      $article = new Miki_shop_article($_GET['v']);
    }catch(Exception $e){
      $article = false;    
    } 
  }
  else
    $article = false;
    
  // si l'id de l'article en liaison avec la commande est donné, on récupert ses valeurs ainsi que les options qui sont liées à l'article
  if (isset($_GET['w']) && is_numeric($_GET['w'])){
    try{
      $order_article = new Miki_order_article($_GET['w']);
      
      // recherche les options de l'article
      $options = $order_article->get_options();
      $options_tab = array();
      if ($article !== false && $article->type == 2){
        foreach($options as $option){
          $options_tab[] = $option->id;
        }
      }
      $options = implode("&&", $options_tab);
      
    }catch(Exception $e){
      $order_article = false;
      
    } 
  }
  else{
    $order_article = false;
    $options = "";
  }
  
  // si on effectue une action (ajout, modification, suppression) on récupert l'action désirée
  if (isset($_GET['a']) && is_numeric($_GET['a'])){
    $action = $_GET['a'];
  }
  else
    $action = false;
    
  // si on effectue une action (ajout, modification, suppression) on récupert le nombre d'article impliqués
  if (isset($_GET['n']) && is_numeric($_GET['n'])){
    $nombre = $_GET['n'];
  }
  else
    $nombre = false;
    
  // vérifie si c'est un deal ou pas
  $is_miki_deal = (isset($_GET['miki_deal']) && $_GET['miki_deal'] == 1);

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
    // sinon, si on effectue un ajout d'article, on en créé une
    if ($action == 1 && $article !== false && $nombre !== false){
      $order = new Miki_order();
      
      // on affecte le membre connecté à la commande
      if ($miki_person instanceof Miki_person)
        $order->id_person = $miki_person->id;
      else
        $order->id_person = 'NULL';
      
      $order->save();
      $_SESSION['miki_order'] = $order;
    }
    // sinon on affiche rien
    else
      $order = false;
  }

  if ($order){
    try{
      // si on doit effectuer une action
      if ($action == 1 && $nombre !== false && $order_article !== false && $article !== false){
        $order->add_article($article->id, $nombre, false, $order_article->attributes, $options, $is_miki_deal);
      }
      elseif ($action == 2 && $nombre !== false && $order_article !== false && $article !== false){
        $order->add_article($article->id, $nombre, false, $order_article->attributes, $options, $is_miki_deal);
      }
      elseif ($action == 3 && $nombre !== false && $order_article !== false && $article !== false){
        $nombre = $nombre * (-1);
        $order->add_article($article->id, $nombre, false, $order_article->attributes, $options, $is_miki_deal);
      }
      elseif ($action == 4 && $nombre !== false && $order_article !== false && $article !== false){
        $order->add_article($article->id, $nombre, true, $order_article->attributes, $options, $is_miki_deal);
      }
      elseif ($action == 5 && $order_article !== false){
        $order->remove_article($order_article->id);
      }
      elseif ($action == 6){
        $order->clear();
        $order->delete();
        unset($_SESSION['miki_order']);
      }
    }
    catch(Exception $e){
      send_result(false, $e->getMessage(), false);
    }
  }
  
  // redirection vers le panier
  send_result(true, _("Votre panier a été modifié avec succès"), false);
?>