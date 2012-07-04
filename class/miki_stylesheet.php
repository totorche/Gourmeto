<?php
/**
 * Classe Miki_stylesheet
 * @package Miki
 */ 

/**
 * Représentation d'une feuille de style CSS.
 * 
 * Une feuille de style peut être liée à un template (Miki_template) ainsi qu'à un menu (Miki_menu)  
 * 
 * @see Miki_template
 * @see Miki_menu 
 *  
 * @package Miki  
 */
class Miki_stylesheet{

  /**
   * Id de la feuille de style
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom de la feuille de style
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Contenu de la feuille de style
   *      
   * @var string
   * @access public   
   */
  public $content;
  
  /**
   * Etat de la feuille de style (0 = désactivée, 1 = activée)
   *      
   * @var int
   * @access public   
   */
  public $state = 0;
  
  /**
   * Type de média de la feuille de style (pas utilisé)
   *      
   * @var int
   * @access public   
   */
  public $media_type = 0;
  
  /**
   * Date de création de la feuille de style
   *      
   * @var string
   * @access public   
   */
  public $date_creation;
  
  /**
   * Utilisateur (Miki_user) ayant créé de la feuille de style
   *     
   * @var int
   * @see Miki_user   
   * @access public   
   */
  public $user_creation;
  
  /**
   * Date de la dernière modification de la feuille de style
   *      
   * @var string
   * @access public   
   */
  public $date_modification;
  
  /**
   * Utilisateur (Miki_user) ayant effectué la dernière modification de la feuille de style
   *      
   * @var int
   * @see Miki_user     
   * @access public   
   */
  public $user_modification;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge la feuille de style dont l'id a été donné
   * 
   * @param int $id Id de la feuille de style à charger (optionnel)
   */
  function __construct($id = ""){
    // charge la feuille de style si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge une feuille de style depuis un id
   *    
   * Si la feuille de style n'existe pas, une exception est levée.
   *    
   * @param int $id id de la feuille de style à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_stylesheet WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La feuille de style demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->content = $row['content'];
    $this->state = $row['state'];
    $this->media_type = $row['media_type'];
    $this->date_creation = $row['date_creation'];
    $this->user_creation = $row['user_creation'];
    $this->date_modification = $row['date_modification'];
    $this->user_modification = $row['user_modification'];
    return true;
  }
  
  /**
   * Sauvegarde la feuille de style dans la base de données.
   * 
   * La feuille de style doit posséder un nom unique, sinon une exception est levée
   *      
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été donné à la feuille de style"));
      
    // si un l'id de la feuille de style existe, c'est que la feuille de style existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // vérifie que le nom de la feuille de style n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_stylesheet WHERE name = '%s'",
      mysql_real_escape_string($this->name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Une feuille de style du même nom existe déjà dans la base de données"));
      
    $this->user_creation = $_SESSION['miki_admin_user_id'];
    $this->user_modification = $_SESSION['miki_admin_user_id'];
      
    $sql = sprintf("INSERT INTO miki_stylesheet (name, content, state, media_type, date_creation, user_creation, date_modification, user_modification) VALUES('%s', '%s', %d, %d, NOW(), %d, NOW(), %d)",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->content),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->media_type),
      mysql_real_escape_string($this->user_creation),
      mysql_real_escape_string($this->user_modification));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion de la feuille de style dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la page
    $this->load($this->id);
    return true;
  }
  
  /**
   * Met à jour la feuille de style dans la base de données.
   * 
   * La feuille de style doit posséder un nom unique, sinon une exception est levée  
   *      
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été donné à la feuille de style"));
      
    // si aucun id existe, c'est que la feuille de style n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // vérifie que le nom de la feuille de style n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_stylesheet WHERE name = '%s' AND  id != %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Une feuille de style du même nom existe déjà dans la base de données"));
    
    $this->user_modification = $_SESSION['miki_admin_user_id'];
    
    $sql = sprintf("UPDATE miki_stylesheet SET name = '%s', content = '%s', state = %d, media_type = %d, date_modification = NOW(), user_modification = %s WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->content),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->media_type),
      mysql_real_escape_string($this->user_modification),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour de la feuille de style dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime la feuille de style
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){    
    $sql = sprintf("DELETE FROM miki_stylesheet WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la feuille de style : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Recherche toutes les feuilles de style
   *
   * @static
   * @return mixed Un tableau d'éléments de type Miki_stylesheet représentant les feuilles de style récupérées.         
   */
  public static function get_all_stylesheets(){
    $return = array();
    $sql = "SELECT * FROM miki_stylesheet ORDER BY name asc";
    $result = mysql_query($sql);
      
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_stylesheet($row['id']);
    }
    return $return;
  }
}
?>