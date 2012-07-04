<?php
/**
 * Classe Miki_action
 * @package Miki 
 */ 

/**
 * Représentation d'une action qu'un administrateur du Miki peut effectuer
 * 
 * Cette classe est utilisée dans pour la gestion des droits des utilisateurs et groupes d'utilisateurs.
 * 
 * @see Miki_user
 * @see Miki_group   
 * @package Miki
 */ 
class Miki_action{

  /**
   * Id de l'action
   * @var int
   * @access public
   */           
  public $id;
  
  /**
   * Nom de l'action
   * @var string
   * @access public
   */
  public $name;
  
  /**
   * Titre du groupe auquel appartient l'action
   * 
   * Plusieurs actions peuvent être regroupées dans un même groupe, ceci afin de 
   * faciliter la gestion des permissions dans le Miki où les actions sont justement 
   * affiché et regroupées par titre
   *         
   * @var string
   * @access public
   */
  public $title;
  
  
  
  
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge l'action dont l'id a été donné
   * 
   * @param int $id Id de l'action à charger (optionnel)
   */         
  function __construct($id = ""){
    // charge l'action si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge une action depuis un id.
   *    
   * Si l'action n'existe pas, une exception est levée.
   *    
   * @param int $id id de l'action à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_action WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("L'action demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->title = $row['title'];
    return true;
  }
  
  /**
   * Charge une action d'après son nom.
   *    
   * Si l'action n'existe pas, une exception est levée.
   *    
   * @param int $name nom de l'action à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load_from_name($name){
    $sql = sprintf("SELECT * FROM miki_action WHERE name = '%s'",
      mysql_real_escape_string($name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Aucune action ne correspond au nom donné"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->title = $row['title'];
    return true;
  }
  
  /**
   * Sauvegarde l'action dans la base de données.
   *    
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   *    
   * Si le nom de l'action est vide, une exception est levée
   * Si le nom de l'action existe déjà, une exception est levée
   * Si une erreur survient, une exception est levée      
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été attribué à l'action"));
      
    // si un l'id de l'action existe, c'est que l'action existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // vérifie que le nom de l'action n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_action WHERE name = '%s'",
      mysql_real_escape_string($this->name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Le nom attribué à l'action est déjà enregistré"));
      
    $sql = sprintf("INSERT INTO miki_action (name, title) VALUES('%s', '%s')",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->title));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion de l'action dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la page
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour l'action dans la base de données.
   *    
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si le nom de l'action est vide, une exception est levée
   * Si le nom de l'action existe déjà, une exception est levée
   * Si une erreur survient, une exception est levée   
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été attribué à l'action"));
      
    // si aucun id existe, c'est que l'action n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // vérifie que le nom de l'action n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_action WHERE name = '%s' AND  id != %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Une action du même nom existe déjà enregistré"));
    
    $sql = sprintf("UPDATE miki_action SET name = '%s', title = '%s' WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->title),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour de l'action dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime l'action
   * 
   * Suppression en cascade via les clé étrangères des liens avec les utilisateurs et groupes      
   * 
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */     
  public function delete(){
    $sql = sprintf("DELETE FROM miki_action WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de l'action : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Retourne un tableau contenant tous les utilisateurs ayant la permission d'effectuer cette action
   * 
   * @see Miki_user   
   * @return mixed Un tableau contenant les id de tous les utilisateurs (Miki_user) ayant la permission d'effectuer cette action
   */      
  public function get_users(){
    $return = array();
    $sql = sprintf("SELECT * FROM miki_user_s_action WHERE action_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_row($result)){
      $user = new Miki_user($row['user_id']);
      $return[] = $user;
    }
    return $return;
  }
  
  /**
   * Retourne un tableau contenant tous les groupes ayant la permission d'effectuer cette action
   * 
   * @see Miki_group 
   * @return mixed Un tableau contenant les id de tous les groupes d'utilisateurs (Miki_group) ayant la permission d'effectuer cette action
   */         
  public function get_groups(){
    $return = array();
    $sql = sprintf("SELECT * FROM miki_group_s_action WHERE action_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_row($result)){
      $group = new Miki_group($row['group_id']);
      $return[] = $group;
    }
    return $return;
  }

  /** 
   * Récupère toutes les actions
   * 
   * @static
   * @return mixed un tableau contenant toutes les actions récupérés
   */            
  public static function get_all_actions(){
    $return = array();
    $sql = "SELECT * FROM miki_action ORDER BY title ASC, name ASC";
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Aucune action n'est présente dans la base de données"));
    
    while($row = mysql_fetch_array($result)){
      $action = new Miki_action($row['id']);
      $return[] = $action;
    }
    return $return;
  }
}
?>