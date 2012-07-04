<?php
/**
 * Classe Miki_shop_shipping_none
 * @package Miki
 */ 

/**
 * Classe de gestion des frais de port. Cette classe permet de gérer des frais de port nuls (frais de port = 0).
 * 
 * Les frais de port sont gérés via le Miki (interface d'administration).
 * Cette classe étant la classe Miki_shop_shipping.
 *  
 * @see Miki_shop_shipping
 *  
 * @package Miki  
 */
class Miki_shop_shipping_none extends Miki_shop_shipping{
  // retourne le prix du port (0 CHF)
  public function get_shipping($limit, Miki_person $person){
    return 0;
  }
}
?>