<?php
/**
 * Classe Miki_group
 * @package Miki
 */ 

/**
 * Représentation d'un groupe d'utilisateur du Miki (console d'administration du site web)
 *
 * Chaque groupe d'utilisateurs possède un certains nombre de droits à effectuer des actions (Miki_action).
 * 
 * @see Miki_action    
 * 
 * @package Miki  
 */ 
class Miki_group{
  
  /**
   * Id du groupe
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom du groupe
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Etat du groupe. 0 : désactivé, 1 : activé
   *      
   * @var int
   * @access public   
   */
  public $state = 0;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le groupe dont l'id a été donné
   * 
   * @param int $id Id du groupe à charger (optionnel)
   */
  function __construct($id = ""){
    // charge le groupe si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un groupe depuis un id
   *    
   * Si le groupe n'existe pas, une exception est levée.
   *    
   * @param int $id id du groupe à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_group WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le groupe demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->state = $row['state'];
    return true;
  }
  
  /**
   * Sauvegarde le groupe dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Le groupe doit posséder un nom unique (name). Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été attribué au groupe"));
      
    // si un l'id du groupe existe, c'est que le groupe existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
    
    // vérifie que le nom du groupe n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_group WHERE name = '%s'",
      mysql_real_escape_string($this->name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Un groupe du même nom existe déjà dans la base de données"));
    
    // sauve le groupe
    $sql = sprintf("INSERT INTO miki_group (name, state) VALUES('%s', %d)",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->state));
    $result = mysql_query($sql);
    if (!$result){
      mysql_query("ROLLBACK");
      throw new Exception(_("Erreur lors de l'insertion du group dans la base de données : ") ."<br />" .mysql_error());
    }
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la page
    $this->load($this->id);  
    return true;
  }
  
  /**
   * Met à jour le groupe dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Le groupe doit posséder un nom unique (name). Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été attribué au groupe"));
      
    // si aucun id existe, c'est que le groupe n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // vérifie que le nom du groupe n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_group WHERE name = '%s' AND  id != %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Le nom attribué à l'utilisateur est déjà enregistré"));
    
    // met à jour le groupe
    $sql = sprintf("UPDATE miki_group SET name = '%s', state = %d WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du groupe dans la base de données : ") ."<br />" .mysql_error());
    
    return true;
  }
  
  /**
   * Supprime le groupe 
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_group WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du groupe : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Change l'état du groupe
   * 
   * Si aucun paramètre n'est fourni, l'état du groupe est changé ainsi : si actif -> inactif et si inactif -> actif.
   * 
   * Si une erreur survient, une exception est levée.      
   * 
   * @param int $action Nouvel état du groupe
   * @return boolean   
   */   
  public function change_state($action = ""){     
    if ($action != "")
      $this->state = $action;
    else
      $this->state = ($this->state + 1) % 2;
    
    $sql = sprintf("UPDATE miki_group SET state = %d WHERE id = %d",
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      throw new Exception(_("Erreur pendant la modification de l'état du groupe : ") ."<br />" .mysql_error());
    }
    return true;
  }
  
  /**
   * Ajoute un utilisateur (Miki_user) dans le groupe
   * 
   * Si une erreur survient, une exception est levée.  
   * 
   * @param Miki_user $user Utilisateur à ajouter au groupe   
   * @see Miki_user
   * @return boolean
   */      
  public function add_user(Miki_user $user){
    // si le groupe n'existe pas encore dans la base de données, on essaye de l'y insérer
    if (!isset($this->id)){
      try{
        $this->save();
      }catch(Exception $e){
        throw new Exception(_("Le groupe n'a pas pu être ajouté à la base de données."));
      }
    }
    // insert l'utilisateur dans la groupe. Si il y est déjà, ne fait rien (mot-clé "ignore")
    $sql = sprintf("INSERT ignore INTO miki_user_s_group (group_id, user_id) VALUES(%d, %d)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($user->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant l'ajout de l'utilisateur au groupe : ") ."<br />" .mysql_error());
    
    return true;
  }
  
  /**
   * Supprime un utilisateur (Miki_user) du le groupe
   * 
   * Si une erreur survient, une exception est levée.  
   * 
   * @param Miki_user $user Utilisateur à supprimer du groupe   
   * @see Miki_user
   * @return boolean
   */ 
  public function remove_user(Miki_user $user){
    $sql = sprintf("DELETE FROM miki_user_s_group WHERE group_id = %d AND  user_id = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($user->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de l'utilisateur du groupe : ") ."<br />" .mysql_error());
      
    return true;
  }
  
  /**
   * Ajoute une permission au groupe
   * 
   * Si une erreur survient, une exception est levée.
   * 
   * @param Miki_action $action Action pour laquelle une permission doit être accordée au groupe
   * @see Miki_action   
   * @return boolean            
   */   
  public function add_action(Miki_action $action){
    // si le groupe n'existe pas encore dans la base de données, on essaye de l'y insérer
    if (!isset($this->id)){
      try{
        $this->save();
      }catch(Exception $e){
        throw new Exception(_("Le groupe n'a pas pu être ajouté à la base de données."));
      }
    }
    // ajoute la permission au groupe. Si elle y est déjà, ne fait rien (mot-clé "ignore")
    $sql = sprintf("insert ignore into miki_group_s_action (group_id, action_id) VALUES(%d, %d)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($action->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant l'ajout de la permission au groupe : ") ."<br />" .mysql_error());
    
    return true;
  }
  
  /** 
   * Supprime toutes les permissions du groupe
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean      
   */
  public function remove_actions(){
    $sql = sprintf("DELETE FROM miki_group_s_action WHERE group_id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression des permissions du groupe : ") ."<br />" .mysql_error());
      
    return true;
  }
  
  /**
   * Recherche tous les utilisateurs faisant partie du groupe
   * 
   * @see Miki_user   
   * @return mixed Un tableau d'éléments Miki_user représentant les utilisateurs faisant partie du groupe
   */          
  public function get_users(){
    $return = array();
    $sql = sprintf("SELECT * FROM miki_user_s_group WHERE group_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_row($result)){
      $user = new Miki_user($row['user_id']);
      $return[] = $user;
    }
    return $return;
  }
  
  /**
   * Recherche toutes les persmissions du groupe
   * 
   * @see Miki_action
   * @return mixed Un tableau d'éléments Miki_action représentant les persmissions du groupe
   */  
  public function get_actions(){
    $return = array();
    $sql = sprintf("SELECT action_id FROM miki_group_s_action WHERE group_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_row($result)){
      $return[] = new Miki_action($row[0]);
    }
    return $return;
  }
  
  /**
   * Détermine si le groupe a la permission d'effectuer l'action passée en paramètre
   * 
   * @param int $action_id Id de l'action (Miki_action) à tester
   * @see Miki_action      
   * @return boolean true si le groupe a la permission d'effectuer l'action passée en paramètre, false sinon
   */   
  public function can($action_id){
    $return = array();
    $sql = sprintf("SELECT * FROM miki_group_s_action WHERE action_id = %d AND  group_id = %d",
      mysql_real_escape_string($action_id),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    return mysql_num_rows($result) > 0;
  }

  /**
   * Récupére tous les groupes
   *    
   * @param boolean $all Si true, retourne tous les groupes, y compris ceux qui ne sont pas activés. Si false, retourne uniquement les groupes activés
   * 
   * @static
   *    
   * @return mixed Un tableau d'éléments de type Miki_group
   */ 
  public static function get_all_groups($all = true){
    $return = array();
    $sql = "SELECT * FROM miki_group";
    
    if (!$all)
      $sql .= " WHERE state > 0";
    
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_group($row['id']);
    }
    return $return;
  }
}
?>