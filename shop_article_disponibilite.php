<?php

/**
 * Définit puis affiche les quantités disponibles pour un article donné.
 * 
 * Les paramètres suivants sont obligatoire (en GET ou POST) : 
 *   - aid : l'id de l'article à contrôler
 *   - qty_max : la quantité maximum à afficher
 *   
 * Les paramètres suivants sont optionnels (en GET ou POST) :
 *  - article_options : les options de l'articles (id des options dans un tableau) choisis pour l'article  
 */ 

// si on appelle ce script via Ajax
if (file_exists("include/headers.php")){
  require_once("include/headers.php");
}

// vérifie que les paramètres obligatoires aient bien été renseignés
if(!isset($_REQUEST['aid']) || !is_numeric($_REQUEST['aid']) || 
   !isset($_REQUEST['qty_max']) || !is_numeric($_REQUEST['qty_max'])){
  echo "Erreur !";
  exit();
}

function print_article_quantity(){
  try{
    // récupert l'article dont l'id a été donné
    $article = new Miki_shop_article($_REQUEST['aid']);
    
    // vérifie si c'est un deal ou pas
    $is_miki_deal = (isset($_REQUEST['miki_deal']) && $_REQUEST['miki_deal'] == 1);
    
    // ainsi que la quantité maximum à afficher 
    $quantity_max_config = $_REQUEST['qty_max'];
  
    // vérifie si on utilise la gestion des stock ou pas
    $use_stock = Miki_configuration::get('miki_shop_gestion_stock');
    
    // si on utilise la gestion des stocks ou que c'est un deal
    if ($use_stock || $is_miki_deal){
      $qty_actual = 0;
      
      // si c'est un deal
      if ($is_miki_deal){
        $deal = current(Miki_deal::get_all_deals($article->id, true));
        $quantity_max = $deal->quantity;
      }
      // si c'est un article normal
      else
        $quantity_max = $article->quantity;
      
      // si l'article est un article configurable et que des options ont été données
      $options_list = "";
      if ($article->type == 2 && isset($_REQUEST['article_options'])){
        $options = $_REQUEST['article_options'];
        
        // récupert la liste des options sous forme de chaîne de caractères
        $options_list = implode("&&", $options);
        
        // on vérifie la quantité disponible pour les options données
        foreach($options as $option_id){
          $option = new Miki_shop_article_option($option_id);
          if ($option->use_stock && $option->quantity < $quantity_max){
            $quantity_max = $option->quantity;
          }
        }
      }
      
      // si une commande est en cours
      if (isset($_SESSION['miki_order']) && $_SESSION['miki_order'] instanceof Miki_order){
        try{
          $order = $_SESSION['miki_order'];
          
          // vérifie la quantité de l'article en cours déjà dans la commande
          $qty_actual = Miki_order_article::get_nb_articles_by_order($article->id, $order->id, $options_list, $is_miki_deal);
        }
        catch(Exception $e){}
      }
      
      // détecte la quantité maximum disponible pour l'article en cours
      $quantity_max -= $qty_actual;
    }
    
    // affiche la quantité maximale configuré si plus petite que la quantité disponible
    if ($quantity_max > $quantity_max_config)
      $quantity_max = $quantity_max_config;
    
    // si on utilise la gestion des stocks et qu'il n'y a plus de stock, on affiche pas le bouton
    if ($use_stock && $quantity_max <= 0){
      echo "<p class='article_unavailable'>$quantity_max" ._("Indisponible") ."</p>";
    }
    else{
      echo "<label for='article_quantity'>Quantité :</label> 
            <select name='quantity' id='article_quantity'>";
              for ($x = 1; $x <= $quantity_max; $x++){
                echo "<option value='$x'>$x</option>\r\n";
              }
      echo "</select>
            <div class='buttons'>
              <input type='submit' value='" ._("Ajouter au panier") ."' class='button_big2' />
            </div>";
    }
  }
  catch(Exception $e){
    echo "Erreur !";
    exit();
  }
}

print_article_quantity();

?>