<?php
/**
 * Affiche le prix d'un article donné.
 * 
 * Les paramètres suivants sont obligatoire (en GET ou POST) : 
 *   - aid : l'id de l'article dont on veut afficher le prix
 *   
 * Les paramètres suivants sont optionnels (en GET ou POST) :
 *  - article_options : les options de l'articles (id des options dans un tableau) choisis pour l'article
 *  
 * La gestion des stocks est laissée à la classe "Miki_order"  
 */ 
  
require_once ("include/headers.php");

if (!isset($_REQUEST['aid']) || !is_numeric($_REQUEST['aid'])){
  exit();
}

try{
  $aid = $_REQUEST['aid'];
  $article = new Miki_shop_article($aid);
  
  // si il y a une promotion
  $promo = $article->get_promotion();
  if ($promo)
    $price = $promo;
  else
    $price = $article->price;
  
  // vérifie si l'article est un article configurable et que des options ont été données
  if ($article->type == 2 && isset($_REQUEST['article_options'])){
    $options = $_REQUEST['article_options'];
    // on récupert les options
    foreach($options as $option_id){
      $option = new Miki_shop_article_option($option_id);
      $price += $option->price;
    }
  }
  
  $return['price'] = number_format($price,2,'.',"'");

  // si le pays du visiteur est donné, on va calculer les taxes
  if (isset($_REQUEST['country'])){
    $country = $_REQUEST['country'];
    // récupert les taxes
    $taxes = Miki_shop::get_taxes($country);
    if (!is_array($taxes))
      $taxes = array();
    
    // calcul et prépare l'affichage des taxes
    $taxes_total = 0;
    $taxes_print = "";
    foreach($taxes as $tax_name=>$tax){
      if ($tax[$country] > 0){
        $tax_ammount =  $tax[$country] / 100 * $price;
        $taxes_total += $tax_ammount;
        $taxes_print .= $tax_name ." (" .$tax[$country] ."%) : " .number_format($tax_ammount,2,'.',"'") ." CHF<br />";
      }
    }
    $price += $taxes_total;
    
    $return['price'] = number_format($price,2,'.',"'");
    $return['taxes'] = $taxes_print;
  }
  
  // affiche le prix et les taxes au format JSON
  echo json_encode($return);
}
catch(Exception $e){
  exit();
}
?>