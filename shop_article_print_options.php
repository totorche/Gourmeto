<?php

/**
 * Affiche les options d'un set donné pour l'article en cours.
 * 
 * Les paramètres suivants sont obligatoire (en GET ou POST) : 
 *   - aid : l'id de l'article dont on veut afficher les options
 *   - sid : l'id des sets dont on veut afficher les options (id séparés par un underline '_')
 */ 
  
// si on appelle ce script via Ajax
if (file_exists("include/headers.php")){
  require_once("include/headers.php");
}

if (!isset($_REQUEST['aid']) || !is_numeric($_REQUEST['aid']) ||
    !isset($_REQUEST['sid'])){
  exit();
}

try{
  // pour les textes des sets d'option (aide utilisateur)
  $sets_text[0] = _("1 | Choisissez votre type de carrure");
  $sets_text[1] = _("2 | Choisissez le cadran");
  $sets_text[2] = _("3 | Terminez votre montre avec le bracelet");
  
  // pour stocker toutes les options utilisées par les articles du panier afin de pouvoir calculer le stock restant
  $options_in_order = array();
  
  // si une commande (panier) est déjà en cours, on la récupert
  if (isset($_SESSION['miki_order']) && $_SESSION['miki_order'] instanceof Miki_order){
    $order = $_SESSION['miki_order'];
    
    // récupert tous les articles du panier en cours
    $articles = $order->get_all_articles();
    
    // parcourt ces articles
    foreach($articles as $article){
      // récupert les options de l'article en cours
      $options = $article->get_options();
      
      // parcourt ces options
      foreach($options as $option){
        // si l'option a déjà été prise en compte, on incrémente son compteur
        if (isset($options_in_order[$option->id])){
          $options_in_order[$option->id] += $article->nb;
        }
        // sinon on l'ajoute
        else{
          $options_in_order[$option->id] = $article->nb;
        }
      }
    }
  }
  
  $article = new Miki_shop_article($_REQUEST['aid']);
  
  $sets_id = explode("_", $_REQUEST['sid']);
  
  // vérifie si on utilise la gestion des stock sur le site
  $use_stock = Miki_configuration::get('miki_shop_gestion_stock');
  
  $x = 0;
  // parcourt chaque set à afficher
  foreach($sets_id as $sid){
    // récupert le set
    $set = new Miki_shop_article_option_set($sid);
    
    // récupert les options du set définies pour l'article en cours
    $options = $article->get_options($set->id, false);
    
    // si il y a des options dans le set, on l'affiche
    if (sizeof($options) > 0){
      echo "<h4>" .$sets_text[$x] ."</h4>
            <div class='content_option_set' id='content_option_set_$set->id' rel='$set->id'>
              <div class='slider_option_set'>
                <ul class='options_set' id='option_set_$set->id'>";
                
                // parcourt ces options
                foreach($options as $option){
                  
                  //echo "<li>";
                  
                  // affiche l'image de l'option si disponible
                  if(sizeof($option->pictures) > 0){
                    // si pas de gestion des stocks sur le site ou que l'option n'utilise pas la gestion des stocks
                    // ou que la quantité restante de l'option moins la quantité déjà utilisée de cette option dans la commande (panier) en cours est supérieur à 0 on affiche l'option normalement
                    if ($use_stock && $option->use_stock &&
                        ($option->quantity <= 0 || (isset($options_in_order[$option->id]) && $option->quantity - $options_in_order[$option->id] <= 0))){
                      
                      //echo "<li><div class='out_of_stock' title='" ._("Indisponible") ."'></div><img src='pictures/shop_articles_options/thumb/" .current($option->pictures) ."' style='margin: 0 5px; margin: 0 auto;' class='option_set_picture' rel='$option->id' /></li>";
                    }
                    else{
                      echo "<li><img src='pictures/shop_articles_options/thumb/" .current($option->pictures) ."' style='margin: 0 5px; margin: 0 auto;' class='option_set_picture in_stock' rel='$option->id' /></li>";
                    }
                  }
                  
                  //echo "</li>";
                }
        echo "  </ul>
            </div>
          </div>";
    }
    
    $x++;
  }
}
catch(Exception $e){
  exit();
}
?>