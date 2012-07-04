<?php
/**
 * Classe Miki_newsletter_group
 * @package Miki
 */ 

/**
 * Représentation d'un groupe de membres à la newsletter (Miki_newsletter).
 * 
 * Les membres (Miki_newsletter_member) d'une newsletter appartiennent à un ou plusieurs groupes de membres. (gestion via le Miki, la console d'administrateur)  
 * Une newsletter peut être envoyée à un groupe d'utilisateurs ou à tous les groupes à la fois.   
 * 
 * @see Miki_newsletter
 * @see Miki_newsletter_member
 *  
 * @package Miki  
 */
class Miki_newsletter_group{
  
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
    $sql = sprintf("SELECT * FROM miki_newsletter_group WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le groupe demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
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
    if (!isset($this->name) || $this->name == "")
      throw new Exception(_("Le groupe doit avoir un nom"));
      
    // si l'id du groupe existe, c'est que le groupe existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // teste qu'un groupe avec le même nom n'existe pas déjà
    $sql = sprintf("SELECT * FROM miki_newsletter_group WHERE name = '%s'",
      mysql_real_escape_string($this->name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) != 0)
      throw new Exception(_("Un groupe avec le même nom existe déjà dans la base de données"));
      
    $sql = sprintf("INSERT INTO miki_newsletter_group (name) VALUES ('%s')",
      mysql_real_escape_string($this->name));
   
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion du groupe dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge le groupe
    $this->load($this->id);
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
    if (!isset($this->name) || $this->name == "")
      throw new Exception(_("Le groupe doit avoir un nom"));
      
    // si aucun id existe, c'est que le groupe n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // teste qu'un groupe avec le même nom n'existe pas déjà
    $sql = sprintf("SELECT * FROM miki_newsletter_group WHERE name = '%s' AND id != %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) != 0)
      throw new Exception(_("Un groupe avec le même nom existe déjà dans la base de données"));
    
    $sql = sprintf("UPDATE miki_newsletter_group SET name = '%s' WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id));
   
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du groupe dans la base de données : ") ."<br />" .mysql_error());
  }
  
  /**
   * Supprime le groupe 
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){    
    $sql = sprintf("DELETE FROM miki_newsletter_group WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du groupe : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Recherche les membres (Miki_newsletter_member) du groupe
   * 
   * @see Miki_newsletter_member       
   * @return mixed Un tableau d'éléments Miki_newsletter_member représentant les membres du groupe
   */
  public function get_members(){
    $sql = sprintf("SELECT id_newsletter_member
                    FROM miki_newsletter_group_s_newsletter_group
                    WHERE id_newsletter_group = %d",
      mysql_real_escape_string($this->id));
      
    
    $result = mysql_query($sql);
    $return = array();
    
    while($row = mysql_fetch_array($result)){
      $return[]['id'] = new Miki_newsletter_member($row[0]);
    }
    return $return;
  }
  
  /**
   * Détermine si le membre donné fait partie du groupe
   * 
   * @param int $member_id L'id du membre (Miki_newsletter_member) a testé
   * @see Miki_newsletter_member   
   * @return boolean
   */
  public function has_member($member_id){
    if (!is_numeric($member_id))
      return false;
    	
    $sql = sprintf("SELECT COUNT(*) 
                    FROM miki_newsletter_group_s_newsletter_group 
                    WHERE id_newsletter_group = %d AND id_newsletter_member = %d", 
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($member_id));
    
    $result = mysql_query($sql);
    
    $row = mysql_fetch_array($result);
    return $row[0] > 0;
  }
  
  /**
   * Recherche le nombre de membres (Miki_newsletter_member) du groupe
   * 
   * @see Miki_newsletter_member
   * @return int      
   */
  public function get_nb_members(){
    
    $sql = sprintf("SELECT COUNT(DISTINCT id_newsletter_member) 
                    FROM miki_newsletter_member_s_newsletter_group 
                    WHERE id_newsletter_group = %d", 
      mysql_real_escape_string($this->id));
    
    $result = mysql_query($sql);
    
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Récupert le nombre de groupes au total              
   * 
   * @static   
   * @return int     
   */
  public static function get_nb_groups(){
    $sql = "SELECT COUNT(id) FROM miki_newsletter_group"; 
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Recherche les groupes selon certains critères
   * 
   * @param string $search Critères de recherche. Recherche le critère dans le nom du groupe.
   * @param string $order Par quel champ les groupes trouvés seront triés (uniquement name). Si vide, on tri selon le nom (name).
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * @param int $limit Le nombre de résultats retournés
   * @param int $page Quelle page de résultat on retourne si $limit > 0
   * 
   * @static
   * @return mixed Un tableau d'éléments de type Miki_newsletter_group                  
   */
  public static function search($search, $order = "", $order_type = "asc", $limit = 0, $page = 1){
    
    $sql = "SELECT id
            FROM miki_newsletter_group
            WHERE LOWER(name) LIKE '%$search%'";
    
    // ordonne les résultats
    if ($order == "name")
      $sql .= " ORDER BY name " .$order_type;
    else
      $sql .= " ORDER BY name " .$order_type;
      
    if ($limit != 0){
      $start = $limit * ($page - 1);
      $sql .= " LIMIT $start, $limit";
    }
    
    $return = array();
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_newsletter_group($row['id']);
    } 
    return $return;
  }
  
  /**
   * Recherche tous les groupes
   * 
   * @param string $order Par quel champ les groupes trouvés seront triés (uniquement name). Si vide, on tri selon le nom (name).
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * 
   * @return mixed Un tableau d'éléments de type Miki_newsletter_group                  
   */
  public static function get_all_groups($order = "", $order_type = "asc"){
    
    $sql = "SELECT id FROM miki_newsletter_group";
    
    // ordonne les résultats
    if ($order == "name")
      $sql .= " ORDER BY name " .$order_type;
    else
      $sql .= " ORDER BY name " .$order_type;
      
    $return = array();
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_newsletter_group($row['id']);
    } 
    return $return;
  }
}
?>