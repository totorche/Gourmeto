<?php
/**
 * Classe Miki_shop_shipping_weight_price
 * @package Miki
 */ 

/**
 * Classe de gestion des frais de port. Cette classe permet de gérer des frais de port en fonction d'un poid ou d'un prix donné.
 * 
 * Les frais de port sont gérés via le Miki (interface d'administration).
 * Cette classe étant la classe Miki_shop_shipping.
 *  
 * @see Miki_shop_shipping
 *  
 * @package Miki  
 */
class Miki_shop_shipping_weight_price extends Miki_shop_shipping{
  // retourne le prix du port
  public function get_shipping($limit, Miki_person $person){
    $table = "";

    // récupert les configurations requises
    foreach($this->configurations as $config){
      if ($config->title == "shipping_table" && $table == "" &&
         ($config->country == "all" || $config->country == $person->country)){
        $table = $config->value;
      }
      elseif ($config->title == "shipping_table" && $config->country == $person->country){
        $table = $config->value;
      }
    }
    // prépare la table
    $table = explode(",",$table);
    $temp = array();
    foreach($table as $t){
      $temp1 = explode(":",$t);
      $temp[$temp1[0]] = $temp1[1];
    }
    $table = $temp;
    
    // récupert le prix de la livraison
    $shipping = 0;
    
    $shipping = current($table);
    while (current($table) && key($table) < $limit){
      if (next($table) !== false){
        $shipping = current($table);
      }
    }
    return $shipping;
  }
}
?>