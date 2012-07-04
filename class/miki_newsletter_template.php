<?php
/**
 * Classe Miki_newsletter_template
 * @package Miki
 */ 

/**
 * Représentation d'un template de newsletter.
 * 
 * Une newsletter (Miki_newsletter) est formée d'un template dans lequel vient s'intégrer le contenu de la newsletter.
 * 
 * @see Miki_newsletter
 *  
 * @package Miki  
 */
class Miki_newsletter_template{

  /**
   * Id du template
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom du template
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Contenu du template
   *      
   * @var string
   * @access public   
   */
  public $content;
  
  /**
   * Etat du template. 0 = désactivé, 1 = activé
   *      
   * @var int
   * @access public   
   */
  public $state = 0;
  
  /**
   * Id de la feuille de style (Miki_stylesheet) du template
   *      
   * @var int
   * @see Miki_stylesheet   
   * @access public   
   */
  public $stylesheet_id;
  
  /**
   * Date de la création du template
   *      
   * @var string
   * @access public   
   */
  public $date_creation;
  
  /**
   * Id de l'utilisateur (Miki_user) ayant créé le template
   *      
   * @var int
   * @see Miki_user   
   * @access public   
   */
  public $user_creation;
  
  /**
   * Date de la dernière modification du template
   *      
   * @var string
   * @access public   
   */
  public $date_modification;
  
  /**
   * Id du dernier utilisateur (Miki_user) ayant modifié le template
   *      
   * @var int
   * @see Miki_user   
   * @access public   
   */
  public $user_modification;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le template dont l'id a été donné
   * 
   * @param int $id Id du template à charger (optionnel)
   */
  function __construct($id = ""){
    // charge le gabarit si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un template depuis un id
   *    
   * Si le template n'existe pas, une exception est levée.
   *    
   * @param int $id id du template à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_newsletter_template WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le gabarit demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->content = $row['content'];
    $this->state = $row['state'];
    $this->date_creation = $row['date_creation'];
    $this->user_creation = $row['user_creation'];
    $this->date_modification = $row['date_modification'];
    $this->user_modification = $row['user_modification'];
    $this->stylesheet_id = $row['stylesheet_id'];
    return true;
  }
  
  /**
   * Charge un template d'après son nom (name)
   *    
   * Si le template n'existe pas, une exception est levée.
   *    
   * @param string $name Nom du template à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load_by_name($name){
    $sql = sprintf("SELECT * FROM miki_newsletter_template WHERE name = '%s'",
      mysql_real_escape_string($name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Aucun gabarit ne correspond au nom donné"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->content = $row['content'];
    $this->state = $row['state'];
    $this->date_creation = $row['date_creation'];
    $this->user_creation = $row['user_creation'];
    $this->date_modification = $row['date_modification'];
    $this->user_modification = $row['user_modification'];
    $this->stylesheet_id = $row['stylesheet_id'];
    return true;
  }
  
  /**
   * Sauvegarde le template dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Le template doit posséder un nom unique (name). Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été donné au gabarit"));
      
    // si un l'id du gabarit existe, c'est que le gabarit existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // vérifie que le nom du gabarit n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_newsletter_template WHERE name = '%s'",
      mysql_real_escape_string($this->name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Un gabarit du même nom existe déjà dans la base de données"));
      
    $this->user_creation = $_SESSION['miki_admin_user_id'];
    $this->user_modification = $_SESSION['miki_admin_user_id'];
    
    $sql = sprintf("INSERT INTO miki_newsletter_template (name, content, state, stylesheet_id, date_creation, user_creation, date_modification, user_modification) VALUES('%s', '%s', %s, %s, NOW(), %d, NOW(), %d)",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->content),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->stylesheet_id),
      mysql_real_escape_string($this->user_creation),
      mysql_real_escape_string($this->user_modification));
      
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion du gabarit dans la base de données : ") ."<br />$sql<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge le template
    $this->load($this->id);
    return true;
  }
  
  /**
   * Met à jour le template dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Le template doit posséder un nom unique (name). Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été donné au gabarit"));
      
    // si aucun id existe, c'est que le gabarit n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // vérifie que le nom du gabarit n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_newsletter_template WHERE name = '%s' AND  id != %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Un gabarit du même nom existe déjà dans la base de données"));
          
    $this->user_modification = $_SESSION['miki_admin_user_id'];
    
    $sql = sprintf("UPDATE miki_newsletter_template SET name = '%s', content = '%s', state = %d, stylesheet_id = %s, date_modification = NOW(), user_modification = %s WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->content),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->stylesheet_id),
      mysql_real_escape_string($this->user_modification),
      mysql_real_escape_string($this->id));
        
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du gabarit dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime le template 
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_newsletter_template WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du gabarit : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Change l'état du gabarit.
   * 
   * Si aucun paramètre n'est fourni, l'état du groupe est changé ainsi : si actif -> inactif et si inactif -> actif.
   * 
   * Si une erreur survient, une exception est levée.      
   * 
   * @param int $action Nouvel état du groupe
   * @return boolean 
   */               
  public function change_state($action = ""){
    // si c'est le gabarit par défaut, on ne fait rien
    if ($this->state == 2)
      return false;
      
    // change l'état du gabarit
    if ($action != "")
      $this->state = $action;
    else
      $this->state = ($this->state + 1) % 2;
    
    $sql = sprintf("UPDATE miki_newsletter_template SET state = %d WHERE id = %d",
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur pendant la modification de l'état du gabarit : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Recherche tous les templates
   * 
   * @param boolean $all Si true, récupert tous les templates, y compris ceux inactifs. Si False, ne récupert que les templates actifs
   * 
   * @return mixed Un tableau d'éléments de type Miki_newsletter_template                  
   */
  public static function get_all_templates($all = true){
    $return = array();
    $sql = "SELECT * FROM miki_newsletter_template";
    
    if (!$all)
      $sql .= " WHERE state > 0";
      
    $sql .= " ORDER BY name asc";
    
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_newsletter_template($row['id']);
    }
    return $return;
  }
}
?>