<?php
/**
 * Classe Miki_shop_shipping
 * @package Miki
 */ 

/**
 * Classe abstraite permettant à une autre classe (en étendant celle-ci), de créer un type de frais de port.
 * 
 * Les frais de port sont gérés via le Miki (interface d'administration). Chaque type de frais de port est géré via une classe qui a l'obligation d'étendre celle-ci. 
 *  
 * @package Miki  
 */
abstract class Miki_shop_shipping{

  /**
   * Id du type de frais de port
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom du type de frais de port
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Configurations du type de frais de port (paramétrées dans les Miki).
   *      
   * @var mixed
   * @access public   
   */
  public $configurations;



  
  /**
   * Constructeur. Si les variables $id et $miki_shop_id sont renseignées, charge le type de frais de port dont l'id et le shop ont été donnés.
   * 
   * @param int $id Id du type de frais de port à charger
   * @param int $miki_shop_id Id du shop auquel est lié le type de frais de port à charger
   * @param int $miki_shop_transporter_id Id du transporteur auquel est lié le type de frais de port à charger      
   */
  function __construct($id = "", $miki_shop_id = "", $miki_shop_transporter_id = ""){
    // charge la configuration si l'id est fourni
    if ($id !== "" && $miki_shop_id !== "")
      $this->load($id, $miki_shop_id, $miki_shop_transporter_id);
  }
  
  /**
   * Charge un type de frais de port en fonction de son id et du shop auquel il est lié.
   *    
   * Si le type de frais de port n'existe pas, une exception est levée.
   *    
   * @param int $id id de la promotion à charger
   * @param int $miki_shop_id Id du shop auquel est lié le type de frais de port à charger
   * @param int $miki_shop_transporter_id Id du transporteur auquel est lié le type de frais de port à charger      
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id, $miki_shop_id, $miki_shop_transporter_id){
    $sql = sprintf("SELECT * FROM miki_shop_shipping WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La configuration demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    // charge toutes les configurations pour le module de livraison
    $this->configurations = Miki_shop_shipping_configuration::get_all_configurations($this->id, $miki_shop_id, $miki_shop_transporter_id);
    return true;
  }
  
  /**
   * Fonction abstraite retournant le prix de port d'un article donné pour la personne donnée
   * 
   * Cette fonction doit être définie dans les classe étendant cette classe-ci.
   *      
   * @param float $limit Prix, poid ou autre information par rapport à laquelle les frais de port sont calculés
   * @param Miki_person $person Personne passant la commande   
   */   
  abstract public function get_shipping($limit, Miki_person $person);
}  
?>