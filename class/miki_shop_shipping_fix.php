<?php
/**
 * Classe Miki_shop_shipping_fix
 * @package Miki
 */ 

/**
 * Classe de gestion des frais de port. Cette classe permet de gérer des frais de port à prix fixe.
 * 
 * Les frais de port sont gérés via le Miki (interface d'administration).
 * Cette classe étant la classe Miki_shop_shipping.
 *  
 * @see Miki_shop_shipping
 *  
 * @package Miki  
 */
class Miki_shop_shipping_fix extends Miki_shop_shipping{
  // retourne le prix du port
  public function get_shipping($limit, Miki_person $person){
    $shipping = "";
    
    // récupert les configurations requises
    foreach($this->configurations as $config){
      if ($config->title = "shipping_table" &&
         ($config->country == "all" || $config->country == $person->country)){
        $shipping = $config->value;
      }
      elseif ($config->title = "shipping_table" && $config->country == $person->country){
        $shipping = $config->value;
      }
    }
    
    return $shipping;
  }
}
?>