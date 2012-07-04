<?php
/**
 * Classe Miki_newsletter_member
 * @package Miki
 */ 

/**
 * Représentation d'un membre de la newsletter (Miki_newsletter).
 * 
 * Les membres d'une newsletter appartiennent à un ou plusieurs groupes de membres (Miki_newsletter_group). (gestion via le Miki, la console d'administrateur)  
 * Une newsletter peut être envoyée à un groupe d'utilisateurs ou à tous les groupes à la fois.
 * 
 * @see Miki_newsletter
 * @see Miki_newsletter_group
 *  
 * @package Miki  
 */
class Miki_newsletter_member{

  /**
   * Id du membre
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Prénom du membre
   *      
   * @var string
   * @access public   
   */
  public $firstname;
  
  /**
   * Nom du membre
   *      
   * @var string
   * @access public   
   */
  public $lastname;
  
  /**
   * Adresse e-mail du membre
   *      
   * @var string
   * @access public   
   */
  public $email;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le membre dont l'id a été donné
   * 
   * @param int $id Id du membre à charger (optionnel)
   */
  function __construct($id = ""){
    // charge le membre si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un membre depuis un id
   *    
   * Si le membre n'existe pas, une exception est levée.
   *    
   * @param int $id id du membre à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_newsletter_member WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le membre demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->firstname = $row['firstname'];
    $this->lastname = $row['lastname'];
    $this->email = $row['email'];
    return true;
  }
  
  /**
   * Charge un membre depuis son adresse e-mail
   *    
   * Si le membre n'existe pas, une exception est levée.
   *    
   * @param int $email Adresse e-mail du membre à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load_from_email($email){
    $sql = sprintf("SELECT * FROM miki_newsletter_member WHERE email = '%s'",
      mysql_real_escape_string($email));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Aucun membre ne correspond à l'adresse e-mail donnée"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->firstname = $row['firstname'];
    $this->lastname = $row['lastname'];
    $this->email = $row['email'];
    return true;
  }
  
  /**
   * Sauvegarde le membre dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Le membre doit posséder une adresse e-mail unique (email). Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->email))
      throw new Exception(_("Le membre doit posséder une adresse e-mail"));
      
    // si l'id du membre existe, c'est que le membre existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // teste qu'un membre avec la même adresse e-mail n'existe pas déjà
    $sql = sprintf("SELECT * FROM miki_newsletter_member WHERE email = '%s'",
      mysql_real_escape_string($this->email));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) != 0)
      throw new Exception(_("Un membre avec la même adresse e-mail existe déjà dans la base de données"));
      
    $sql = sprintf("INSERT INTO miki_newsletter_member (firstname, lastname, email) VALUES ('%s', '%s', '%s')",
      mysql_real_escape_string($this->firstname),
      mysql_real_escape_string($this->lastname),
      mysql_real_escape_string($this->email));
   
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion du membre dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge le membre
    $this->load($this->id);
  }
  
  /**
   * Met à jour le membre dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Le membre doit posséder une adresse e-mail unique (email). Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->email))
      throw new Exception(_("Le membre doit posséder une adresse e-mail"));
      
    // si aucun id existe, c'est que le membre n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // teste qu'un membre avec la même adresse e-mail n'existe pas déjà
    $sql = sprintf("SELECT * FROM miki_newsletter_member WHERE email = '%s' AND id != %d",
      mysql_real_escape_string($this->email),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) != 0)
      throw new Exception(_("Un membre avec la même adresse e-mail existe déjà dans la base de données"));
    
    $sql = sprintf("UPDATE miki_newsletter_member SET firstname = '%s', lastname = '%s', email = '%s' WHERE id = %d",
      mysql_real_escape_string($this->firstname),
      mysql_real_escape_string($this->lastname),
      mysql_real_escape_string($this->email),
      mysql_real_escape_string($this->id));
   
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du membre dans la base de données : ") ."<br />" .mysql_error());
  }
  
  /**
   * Supprime le membre 
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){    
    $sql = sprintf("DELETE FROM miki_newsletter_member WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du membre : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Ajoute le membre à un groupe (Miki_newsletter_group)
   * 
   * Si le groupe donné n'existe pas, une Exception est levée.
   * Si une erreur survient, une exception est levée.   
   *      
   * @param int $group_id Id du groupe dans lequel le membre doit être ajouté
   * 
   * @see Miki_newsletter_group      
   * @return boolean
   */
  public function add_to_group($group_id){
    // teste que le groupe existe déjà
    $sql = sprintf("SELECT * FROM miki_newsletter_group WHERE id = %d",
      mysql_real_escape_string($group_id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le groupe donné n'existe pas."));
      
    // si le membre est déjà dans le groupe demandé, on ne fait rien
    if ($this->is_in_group($group_id))
      return true;
  
    $sql = sprintf("INSERT INTO miki_newsletter_member_s_newsletter_group (id_newsletter_member, id_newsletter_group) VALUES (%d, %d)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($group_id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'ajout du membre au groupe donné : <br /><br />$sql<br /><br />") ."<br />" .mysql_error());
      
    return true;
  }
  
  /**
   * Supprime le membre d'un groupe (Miki_newsletter_group)
   * 
   * Si le groupe donné n'existe pas, une Exception est levée.
   * Si une erreur survient, une exception est levée.       
   * 
   * @param int $group_id Id du groupe duquel le membre doit être supprimé
   * 
   * @see Miki_newsletter_group      
   * @return boolean
   */      
  public function remove_from_group($group_id){
    // teste que le groupe existe déjà
    $sql = sprintf("SELECT * FROM miki_newsletter_group WHERE id = %d",
      mysql_real_escape_string($group_id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le groupe donné n'existe pas."));
      
    $sql = sprintf("DELETE FROM miki_newsletter_member_s_newsletter_group 
                    WHERE id_newsletter_member = %d 
                      AND id_newsletter_group = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($group_id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la suppression du membre du groupe donné : ") ."<br />" .mysql_error());
    
    return true;
  }
  
  /**
   * Supprime le membre de tous les groupes (Miki_newsletter_group)
   * 
   * Si une erreur survient, une exception est levée.     
   * 
   * @see Miki_newsletter_group      
   * @return boolean      
   */
  public function remove_from_all(){
    $sql = sprintf("DELETE FROM miki_newsletter_member_s_newsletter_group 
                    WHERE id_newsletter_member = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la suppression du membre : ") ."<br />" .mysql_error());
    
    return true;
  }
  
  /**
   * Recherche tous les groupes (Miki_newsletter_group) auxquels le membre est abonné
   * 
   * @see Miki_newsletter_group
   * @return mixed Un tableau d'élément Miki_newsletter_group représentant les groupes auxquels le membre est abonné   
   */
  public function get_groups(){
    $sql = sprintf("SELECT m1.id id
                    FROM miki_newsletter_group m1,
                         miki_newsletter_member_s_newsletter_group m2
                    WHERE m2.id_newsletter_member = %d AND
                          m2.id_newsletter_group = m1.id",
      mysql_real_escape_string($this->id));
      
    $return = array();
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_newsletter_group($row['id']);
    }
    return $return;
  }
  
  /**
   * Détermine si le membre fait partie du groupe donné
   * 
   * @param int $group_id Le groupe à tester      
   * @return boolean
   */
  public function is_in_group($group_id){
    if (!is_numeric($group_id))
      return false;
    	
    $sql = sprintf("SELECT count(*) 
                    FROM miki_newsletter_member_s_newsletter_group 
                    WHERE id_newsletter_member = %d AND id_newsletter_group = %d", 
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($group_id));
    
    $result = mysql_query($sql);
    
    $row = mysql_fetch_array($result);
    return $row[0] > 0;
  }
  
  /**
   * Vérifie si un membre existant possède déjà l'adresse e-mail donnée
   *    
   * @param string $email l'adresse e-mail à vérifier
   * 
   * @static
   * @return boolean
   */      
  public static function email_exists($email){
    $sql = sprintf("SELECT * FROM miki_newsletter_member WHERE email = '%s'",
      mysql_real_escape_string($email));
    $result = mysql_query($sql);
    return mysql_num_rows($result) > 0;
  }
  
  /**
   * Retourne le nombre total de membres à la newsletter
   * 
   * @static
   * @return int                       
   */
  public static function get_nb_members(){
    $sql = "SELECT COUNT(id) FROM miki_newsletter_member";
    $result = mysql_query($sql);
    
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Recherche les membres selon certains critères
   * 
   * @param string $search Critères de recherche. Recherche le critère dans le prénom, le nom et l'adresse e-mail du membre.
   * @param int $group_id Si définit, on recherche uniquement les membres du groupe possédant l'id donné.   
   * @param string $order Par quel champ les membres trouvés seront triés (firstname, lastname, email). Si vide, on tri selon le prénom (firstname).
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * @param int $limit Le nombre de résultats retournés
   * @param int $page Quelle page de résultat on retourne si $limit > 0
   * 
   * @static
   * @return mixed Un tableau d'éléments de type Miki_newsletter_member
   */
  public static function search($search, $group_id = "", $order = "", $order_type = "asc", $limit = 0, $page = 1){
    
    if (is_numeric($group_id)){
      $sql = sprintf("SELECT m1.id
                      FROM miki_newsletter_member m1,
                           miki_newsletter_member_s_newsletter_group m2
                      WHERE m1.id = m2.id_newsletter_member 
                        AND (LOWER(m1.firstname) LIKE '%%%s%%'
                             OR LOWER(m1.lastname) LIKE '%%%s%%'
                             OR LOWER(m1.email) LIKE '%%%s%%')
                        AND m2.id_newsletter_group = %d",
                mysql_real_escape_string($search),
                mysql_real_escape_string($search),
                mysql_real_escape_string($search),
                mysql_real_escape_string($group_id));
    }
    else{
       $sql = sprintf("SELECT m1.id id 
                       FROM miki_newsletter_member m1
                       WHERE (LOWER(m1.firstname) LIKE '%%%s%%'
                              OR LOWER(m1.lastname) LIKE '%%%s%%'
                              OR LOWER(m1.email) LIKE '%%%s%%')",
                mysql_real_escape_string($search),
                mysql_real_escape_string($search),
                mysql_real_escape_string($search));
    }
    
    // ordonne les résultats
    if ($order == "firstname")
      $sql .= " ORDER BY m1.firstname " .$order_type;
    elseif ($order == "lastname")
      $sql .= " ORDER BY m1.lastname " .$order_type;
    elseif ($order == "email")
      $sql .= " ORDER BY m1.email " .$order_type;
    else
      $sql .= " ORDER BY m1.firstname " .$order_type;
      
    if ($limit != 0){
      $start = $limit * ($page - 1);
      $sql .= " LIMIT $start, $limit";
    }
    
    $return = array();
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_newsletter_member($row['id']);
    } 
    return $return;
  }
  
  /**
   * Recherche tous les membres
   * 
   * @param int $group_id Si définit, on recherche uniquement les membres du groupe possédant l'id donné.   
   * @param string $order Par quel champ les membres trouvés seront triés (firstname, lastname, email). Si vide, on tri selon le prénom (firstname).
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * 
   * @return mixed Un tableau d'éléments de type Miki_newsletter_group                  
   */
  public static function get_all_members($group_id = "", $order = "", $order_type = "asc"){
    
    if (is_numeric($group_id)){
      $sql = sprintf("SELECT m1.id id
                      FROM miki_newsletter_member m1,
                           miki_newsletter_member_s_newsletter_group m2
                      WHERE m1.id = m2.id_newsletter_member
                      AND m2.id_newsletter_group = %d",
                mysql_real_escape_string($group_id));
    }
    else{
       $sql = "SELECT m1.id id FROM miki_newsletter_member m1";
    }
    
    // ordonne les résultats
    if ($order == "firstname")
      $sql .= " ORDER BY m1.firstname " .$order_type;
    elseif ($order == "lastname")
      $sql .= " ORDER BY m1.lastname " .$order_type;
    elseif ($order == "email")
      $sql .= " ORDER BY m1.email " .$order_type;
    else
      $sql .= " ORDER BY m1.firstname " .$order_type;
      
    $return = array();
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_newsletter_member($row['id']);
    } 
    return $return;
  }
}
?>