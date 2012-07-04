<?php
/**
 * Classe Miki_template
 * @package Miki
 */ 

/**
 * Représentation d'un gabarit d'une page du site Internet.
 * 
 * Une page (Miki_page) du site Internet est mise en page grâce à un gabarit. 
 * Un gabarit s'occupe de tout l'aspect visuel et peut être appliqué à autant de page que désiré.
 * 
 * Pour placer le contenu d'une page dans un gabarit, il suffit de place le code [miki_content] à l'endroit désiré. 
 * Le contenu de la page appelée sera ainsi automatiquement chargé et mis à la place de cette balise.
 * 
 * Un gabarit peut contenir une ou plusieurs sections de gabarit (Miki_template_part)
 * 
 * Une section de gabarit peut contenir un ou plusieurs blocs de contenu global (Miki_global_content)  
 * 
 * Lors de l'édition d'une page (Miki_page) dont le gabarit possède au moins une section de gabarit, on peut sélectionner un bloc de contenu global appartenant
 * à cette section. On peut également définir l'ordre dans lequel doivent apparaître les blocs de contenu global directement dans l'édition de la page.
 * 
 * Pour intégrer une section de gabarit dans une page, on place la balise [miki_part='code_de_la_section'] à l'endroit désiré dans le code de la page. 
 *   
 * @see Miki_template_part
 * @see Miki_global_content
 *  
 * @package Miki  
 */
class Miki_template{

  /**
   * Id du gabarit
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom du gabarit
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Type du contenu(code ou file). Code veut dire que le contenu sera du code HTML. File veut dire que le contenu sera un nom de fichier qui contiendra le contenu réel.
   *      
   * @var string
   * @access public   
   */
  public $content_type = 'code';
  
  /**
   * Contenu du gabarit
   *      
   * @var string
   * @access public   
   */
  public $content;
  
  /**
   * Etat du gabarit (0 = désactivé, 1 = activé, 2 = gabarit par défaut)
   *      
   * @var int
   * @access public   
   */
  public $state = 0;
  
  /**
   * Id de la feuille de style (Miki_stylesheet) liée au gabarit
   *      
   * @var int
   * @see Miki_stylesheet   
   * @access public   
   */
  public $stylesheet_id;
  
  /**
   * Date de création du gabarit
   *      
   * @var string
   * @access public   
   */
  public $date_creation;
  
  /**
   * Utilisateur (Miki_user) ayant créé du gabarit
   *      
   * @var int
   * @see Miki_user   
   * @access public   
   */
  public $user_creation;
  
  /**
   * Date de la dernière modification du gabarit
   *      
   * @var string
   * @access public   
   */
  public $date_modification;
  
  /**
   * Utilisateur (Miki_user) ayant effectué la dernière modification du gabarit
   *      
   * @var int
   * @see Miki_user   
   * @access public   
   */
  public $user_modification;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le gabarit dont l'id a été donné
   * 
   * @param int $id Id du gabarit à charger (optionnel)
   */
  function __construct($id = ""){
    // charge le gabarit si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un gabarit depuis un id
   *    
   * Si le gabarit n'existe pas, une exception est levée.
   *    
   * @param int $id id du gabarit à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_template WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le gabarit demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->content_type = $row['content_type'];
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
   * Charge un gabarit depuis son nom
   *    
   * Si le gabarit n'existe pas, une exception est levée.
   *    
   * @param string $name Le nom du gabarit à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load_by_name($name){
    $sql = sprintf("SELECT * FROM miki_template WHERE name = '%s'",
      mysql_real_escape_string($name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Aucun gabarit ne correspond au nom donné"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->content_type = $row['content_type'];
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
   * Sauvegarde le gabarit dans la base de données.
   * 
   * Le gabarit doit posséder un nom unique, sinon une exception est levée
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
    $sql = sprintf("SELECT id FROM miki_template WHERE name = '%s'",
      mysql_real_escape_string($this->name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Un gabarit du même nom existe déjà dans la base de données"));
      
    $this->user_creation = $_SESSION['miki_admin_user_id'];
    $this->user_modification = $_SESSION['miki_admin_user_id'];
    
    $sql = sprintf("INSERT INTO miki_template (name, content_type, content, state, stylesheet_id, date_creation, user_creation, date_modification, user_modification) VALUES('%s', '%s', '%s', %s, %s, NOW(), %d, NOW(), %d)",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->content_type),
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
    // recharge la page
    $this->load($this->id);
    return true;
  }
  
  /**
   * Met à jour le gabarit dans la base de données.
   * 
   * Le gabarit doit posséder un nom unique, sinon une exception est levée  
   *      
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
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
    $sql = sprintf("SELECT id FROM miki_template WHERE name = '%s' AND  id != %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Un gabarit du même nom existe déjà dans la base de données"));
          
    $this->user_modification = $_SESSION['miki_admin_user_id'];
    
    $sql = sprintf("UPDATE miki_template SET name = '%s', content_type = '%s', content = '%s', state = %d, stylesheet_id = %s, date_modification = NOW(), user_modification = %s WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->content_type),
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
   * Supprime le gabarit
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_template WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du gabarit : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Définit le gabarit comme étant le gabarit par défaut.
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean      
   */   
  public function set_default(){
    mysql_query("START TRANSACTION");
    $sql = "UPDATE miki_template SET state = 1 WHERE state = 2";
    $result = mysql_query($sql);
    if (!$result){
      mysql_query("ROLLBACK");
      throw new Exception(_("Erreur lorsque le gabarit a été défini par défaut : ") ."<br />" .mysql_error());
    }
    
    $this->state = 2;
    $sql = sprintf("UPDATE miki_template SET state = 2 WHERE id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      mysql_query("ROLLBACK");
      throw new Exception(_("Erreur lorsque le gabarit a été défini par défaut : ") ."<br />" .mysql_error());
    }
    mysql_query("COMMIT");
    return true;
  }
  
  /**
   * Change l'état du gabarit : si actif -> inactif et si inactif -> actif. 
   * 
   * Si c'est le gabarit par défaut, on ne fait rien.
   * 
   * Si une erreur survient, une exception est levée.
   * 
   * @param int $action si != "", le nouvel état du gabarit sera égal à $action            
   * 
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
    
    $sql = sprintf("UPDATE miki_template SET state = %d WHERE id = %d",
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur pendant la modification de l'état du gabarit : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Définit le gabarit comme étant le gabarit pour toutes les pages
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean      
   */     
  public function set_for_all_pages(){
    $sql = sprintf("UPDATE miki_page SET template_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour des pages : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Ajoute une section de gabarit au gabarit
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @param Miki_template_part $part Section de gabarit à ajouter
   * 
   * @see Miki_template_part
   * @return boolean   
   */   
  public function add_part(Miki_template_part $part){
    // Si la section y est déjà, ne fait rien (mot-clé "ignore")
    $sql = sprintf("insert ignore into miki_template_s_miki_template_part (template_id, template_part_id) VALUES(%d, %d)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($part->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur pendant l'ajout de la section au gabarit : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime toutes les sections de gabarit liées au gabarit
   * 
   * Si une erreur survient, une exception est levée.      
   */   
  public function remove_parts(){
    $sql = sprintf("DELETE FROM miki_template_s_miki_template_part WHERE template_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur pendant la suppression des sections liées au gabarit : ") ."<br />" .mysql_error());
  }
  
  /**
   * Recherche toutes les sections de gabarit liées au gabarit
   * 
   * @return mixed Un tableau d'éléments de type Miki_template_part représentant les sections de gabarit liées au gabarit      
   */   
  public function get_parts(){
    $return = array();
    $sql = sprintf("SELECT template_part_id FROM miki_template_s_miki_template_part WHERE template_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_row($result)){
      $part = new Miki_template_part($row['0']);
      $return[] = $part;
    }
    return $return;
  }
  
  /**
   * Recherche tous les gabarits
   * 
   * @param boolean $all Si true, tous les gabarits sont récupérés. Si false, on récupert uniquement les gabarits activés (state > 0).
   *      
   * @static
   * @return mixed Un tableau d'éléments de type Miki_template représentant gabarits récupérés         
   */   
  public static function get_all_templates($all = true){
    $return = array();
    $sql = "SELECT * FROM miki_template";
    
    if (!$all)
      $sql .= " WHERE state > 0";
      
    $sql .= " ORDER BY name asc";
    
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_template($row['id']);
    }
    return $return;
  }
}
?>