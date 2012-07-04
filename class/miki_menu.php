<?php
/**
 * Classe Miki_menu
 * @package Miki
 */ 

/**
 * Représentation d'un menu
 * 
 * Un menu est un élément qui peut être intégré à un template (Miki_template)
 * On intègre un menu en ajoutant le code suivant à l'endroit désiré dans le template : [miki_menu='nom_du_menu']  
 * 
 * @see Miki_template 
 * @package Miki  
 */ 
class miki_menu{

  /**
   * Id du menu
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom du menu
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Contenu du menu
   * 
   * Le contenu du menu est le code HTML ou PHP qui permettra la création du menu.
   * Il est possible d'intégrer une page en tant que contenu en mettant le code PHP suivant : <code> require_once("chemin_relatif_du_menu/mon_menu.php"); </code>
   *      
   * @var string
   * @access public   
   */
  public $content;
  
  /**
   * Id de la feuille de style CSS liée au menu
   *      
   * @var int
   * @access public   
   */
  public $stylesheet_id;
  
  /**
   * Date de la création du menu
   *      
   * @var string
   * @access public   
   */
  public $date_creation;
  
  /**
   * Id de l'utilisateur ayant créé le menu
   *      
   * @var int
   * @access public   
   */
  public $user_creation;
  
  /**
   * Date de la dernière modification
   *      
   * @var string
   * @access public   
   */
  public $date_modification;
  
  /**
   * Id du dernier utilisateur ayant modifié le menu
   *      
   * @var int
   * @access public   
   */
  public $user_modification;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le menu dont l'id a été donné
   * 
   * @param int $id Id du menu à charger (optionnel)
   */
  function __construct($id = ""){
    // charge le menu si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un menu depuis un id
   *    
   * Si le menu n'existe pas, une exception est levée.
   *    
   * @param int $id id du menu à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_menu WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le menu demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->content = $row['content'];
    $this->date_creation = $row['date_creation'];
    $this->user_creation = $row['user_creation'];
    $this->date_modification = $row['date_modification'];
    $this->user_modification = $row['user_modification'];
    $this->stylesheet_id = $row['stylesheet_id'];
    return true;
  }
  
  /**
   * Charge un menu d'après son nom
   *    
   * Si le menu n'existe pas, une exception est levée.
   *    
   * @param string $name Nom du menu à charger
   * @return boolean True si le chargement s'est déroulé correctement   
   */
  public function load_from_name($name){
    $sql = sprintf("SELECT * FROM miki_menu WHERE name = '%s'",
      mysql_real_escape_string($name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Aucun menu ne correspond au nom donné"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->content = $row['content'];
    $this->date_creation = $row['date_creation'];
    $this->user_creation = $row['user_creation'];
    $this->date_modification = $row['date_modification'];
    $this->user_modification = $row['user_modification'];
    $this->stylesheet_id = $row['stylesheet_id'];
    return true;
  }
  
  /**
   * Sauvegarde le menu dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Le menu doit posséder un nom unique (name). Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été donné au menu"));
      
    // si un l'id du menu existe, c'est que le menu existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // vérifie que le nom du menu n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_menu WHERE name = '%s'",
      mysql_real_escape_string($this->name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Un menu du même nom existe déjà dans la base de données"));
      
    $this->user_creation = $_SESSION['miki_admin_user_id'];
    $this->user_modification = $_SESSION['miki_admin_user_id'];
    
    $sql = sprintf("INSERT INTO miki_menu (name, content, stylesheet_id, date_creation, user_creation, date_modification, user_modification) VALUES('%s', '%s', %s, NOW(), %d, NOW(), %d)",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->content),
      mysql_real_escape_string($this->stylesheet_id),
      mysql_real_escape_string($this->user_creation),
      mysql_real_escape_string($this->user_modification));
      
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion du menu dans la base de données : ") ."<br />$sql<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge le menu
    $this->load($this->id);
    return true;
  }
  
  /**
   * Met à jour le menu dans la base de données.
   * 
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   * 
   * Le menu doit posséder un nom unique (name). Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été donné au menu"));
      
    // si aucun id existe, c'est que le menu n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // vérifie que le nom du menu n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_menu WHERE name = '%s' AND  id != %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Un menu du même nom existe déjà dans la base de données"));
          
    $this->user_modification = $_SESSION['miki_admin_user_id'];
    
    $sql = sprintf("UPDATE miki_menu SET name = '%s', content = '%s', stylesheet_id = %s, date_modification = NOW(), user_modification = %s WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->content),
      mysql_real_escape_string($this->stylesheet_id),
      mysql_real_escape_string($this->user_modification),
      mysql_real_escape_string($this->id));
        
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du menu dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime le menu 
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_menu WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du menu : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Récupére tous les menus
   *    
   * @static
   *    
   * @return mixed Un tableau d'éléments de type Miki_menu
   */
  public static function get_all_menus(){
    $return = array();
    $sql = "SELECT * FROM miki_menu";
    
    $sql .= " ORDER BY name asc";
    
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $return[] = new miki_menu($row['id']);
    }
    return $return;
  }
}
?>