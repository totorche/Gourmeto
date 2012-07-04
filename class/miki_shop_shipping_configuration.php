<?php
/**
 * Classe Miki_shop_shipping_configuration
 * @package Miki
 */ 

/**
 * Configuration des frais de port entrés par un administrateur du site Internet dans le Miki (console d'administration)
 * 
 * @package Miki  
 */
class Miki_shop_shipping_configuration{

  /**
   * Id de la configuration
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Id du type de frais de port (Miki_shop_shipping) auquel cette configuration est liée
   *      
   * @var int
   * @see Miki_shop_shipping   
   * @access public   
   */
  public $id_shop_shipping;
  
  /**
   * Id du shop (Miki_shop)
   *      
   * @var int
   * @see Miki_shop   
   * @access public   
   */
  public $id_shop;
  
  /**
   * Id du transporteur (Miki_shop_transporter)
   *      
   * @var int
   * @see Miki_shop_transporter   
   * @access public   
   */
  public $id_shop_transporter;
  
  /**
   * Titre de la configuration
   *      
   * @var int
   * @access public   
   */
  public $title;
  
  /**
   * Description de la configuration
   *      
   * @var int
   * @access public   
   */
  public $description;
  
  /**
   * Valeur de la configuration
   *      
   * @var int
   * @access public   
   */
  public $value;
  
  /**
   * Pays lié à la configuration
   *      
   * @var int
   * @access public   
   */
  public $country;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge la configuration dont l'id a été donné
   * 
   * @param int $id Id de la configuration à charger
   */
  function __construct($id = ""){
    // charge la configuration si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge une configuration depuis un id
   *    
   * Si la configuration n'existe pas, une exception est levée.
   *    
   * @param int $id id de la configuration à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_shop_shipping_configuration WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La configuration demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->id_shop_shipping = $row['id_shop_shipping'];
    $this->id_shop = $row['id_shop'];
    $this->id_shop_transporter = $row['id_shop_transporter'];
    $this->title = $row['title'];
    $this->description = $row['description'];
    $this->value = $row['value'];
    $this->country = $row['country'];
    return true;
  }
  
  /**
   * Sauvegarde la configuration dans la base de données.
   *    
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   * 
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */
  public function save(){
    // si un l'id de la config existe, c'est que la config existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
    
    $sql = sprintf("INSERT INTO miki_shop_shipping_configuration (id_shop_shipping, id_shop, id_shop_transporter, title, description, value, country) VALUES(%d, %d, %d, '%s', '%s', '%s', '%s')",
      mysql_real_escape_string($this->id_shop_shipping),
      mysql_real_escape_string($this->id_shop),
      mysql_real_escape_string($this->id_shop_transporter),
      mysql_real_escape_string($this->title),
      mysql_real_escape_string($this->description),
      mysql_real_escape_string($this->value),
      mysql_real_escape_string($this->country));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion de la configuration dans la base de données : ") ."<br />$sql<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge le compte
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour la configuration dans la base de données.
   * 
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si une erreur survient, une exception est levée   
   *    
   * @return boolean   
   */
  public function update(){      
    // si aucun id existe, c'est que le compte n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
    
    $sql = sprintf("UPDATE miki_shop_shipping_configuration SET id_shop_shipping = %d, id_shop = %d, id_shop_transporter = %d, title = '%s', description = '%s', value = '%s', country = '%s' WHERE id = %d",
      mysql_real_escape_string($this->id_shop_shipping),
      mysql_real_escape_string($this->id_shop),
      mysql_real_escape_string($this->id_shop_transporter),
      mysql_real_escape_string($this->title),
      mysql_real_escape_string($this->description),
      mysql_real_escape_string($this->value),
      mysql_real_escape_string($this->country),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour de la configuration dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime la configuration
   * 
   * Si une erreur survient, une exception est levée
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_shop_shipping_configuration WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la configuration : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime toutes les configurations d'un shop donné
   * 
   * @param int $id_shop Id du shop dont on veut supprimer toutes les configurations.
   * @param int $id_shop_transporter Id du transporteur dont on veut supprimer les configurations. Si = "", on supprime les configurations de tous les transporteurs pour le shop donné
   *    
   * @static
   * @return boolean
   */                   
  public static function delete_from_shop($id_shop, $id_shop_transporter = ""){
    $sql = sprintf("DELETE FROM miki_shop_shipping_configuration WHERE id_shop = %d",
      mysql_real_escape_string($id_shop));
    
    // Si l'id d'un transporteur est donné, on ne supprime que les configurations du transporteur donné
    if ($id_shop_transporter != "" && is_numeric($id_shop_transporter)){
      $sql .= sprintf(" AND id_shop_transporter = %d",
        mysql_real_escape_string($id_shop_transporter));
    }
    
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression des configuration : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Recherche toutes les configurations d'un type de frais de port pour un shop donné.
   * 
   * @param int $id_shop_shipping Id du type de frais de port dont on veut récupérer les configurations-
   * @param int $id_shop Id du shop dont on veut récupérer les configurations.
   * @param int $id_shop_transporter Id du transporteur dont on veut récupérer les configurations.   
   * 
   * @return mixed Tableau d'éléments de type Miki_shop_shipping_configuration représentant les configurations récupérées.      
   */   
  public static function get_all_configurations($id_shop_shipping, $id_shop, $id_shop_transporter){
    $return = array();
    $sql = sprintf("SELECT * FROM miki_shop_shipping_configuration WHERE id_shop_shipping = %d AND id_shop = %d AND id_shop_transporter = %d",
      mysql_real_escape_string($id_shop_shipping),
      mysql_real_escape_string($id_shop),
      mysql_real_escape_string($id_shop_transporter));
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_shop_shipping_configuration($row['id']);
    }
    return $return;
  }
}  
?>