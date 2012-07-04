<?php
/**
 * Classe Miki_global_content
 * @package Miki
 */ 

/**
 * Représentation du bloc de contenu global
 * 
 * Un bloc de contenu global peut faire partie d'une ou de plusieurs section(s) de gabarit (Miki_template_part).
 *  
 * Une section de gabarit peut faire partie d'un ou de plusieurs gabarit(s) (Miki_template).
 *  
 * Lors de l'édition d'une page (Miki_page) dont le gabarit possède au moins une section de gabarit, on peut sélectionner un bloc de contenu global appartenant
 * à cette section. On peut également définir l'ordre dans lequel doivent apparaître les blocs de contenu global directement dans l'édition de la page.
 * 
 * Pour intégrer une section de gabarit dans une page, on place la balise [miki_part='code_de_la_section'] à l'endroit désiré dans le code de la page.   
 * 
 * @see Miki_template_part   
 * @see Miki_template
 * @see Miki_page 
 * 
 * @package Miki  
 */ 
class Miki_global_content{
  
  /**
   * Id du bloc de contenu global
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom du bloc de contenu global
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Date de création du bloc de contenu global
   *      
   * @var string
   * @access public   
   */
  public $date_creation;
  
  /**
   * Id de l'utilisateur ayant créé le bloc de contenu global
   *      
   * @var int
   * @access public   
   */
  public $user_creation;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le bloc de contenu global dont l'id a été donné
   * 
   * @param int $id Id du bloc de contenu global à charger (optionnel)
   */
  function __construct($id = ""){
    // charge le bloc de contenu global si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un bloc de contenu global depuis un id
   *    
   * Si le bloc de contenu global n'existe pas, une exception est levée.
   * 
   * Lève une exception si le bloc de contenu global avec l'id donné n'a pas été trouvé    
   *   
   * @param int $id id du bloc de contenu global à charger
   * @return boolean   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_global_content WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le bloc de contenu global demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->user_creation = $row['user_creation'];
    $this->date_creation = $row['date_creation'];
    return true;
  }
  
  /**
   * Charge un bloc de contenu global d'après son nom
   *    
   * Si le bloc de contenu global n'existe pas, une exception est levée.
   * 
   * Lève une exception si le bloc de contenu global avec l'id donné n'a pas été trouvé   
   *    
   * @param string $name Nom du bloc de contenu global à charger
   * @return boolean True si le chargement s'est déroulé correctement   
   */
  public function load_from_name($name){
    $sql = sprintf("SELECT * FROM miki_global_content WHERE name = '%s'",
      mysql_real_escape_string($name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le bloc de contenu global demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->user_creation = $row['user_creation'];
    $this->date_creation = $row['date_creation'];
    return true;
  }
  
  /**
   * Sauvegarde le bloc de contenu global dans la base de données.
   *    
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   * 
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */
  public function save(){      
    // si un l'id du bloc de contenu global existe, c'est que le bloc de contenu global existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // vérifie que le nom du bloc de contenu global n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_global_content WHERE name = '%s'",
      mysql_real_escape_string($this->name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Une bloc de contenu global du même nom existe déjà dans la base de données"));
    
    $this->user_creation = $_SESSION['miki_admin_user_id'];
    
    $sql = sprintf("INSERT INTO miki_global_content (name, user_creation, date_creation) VALUES('%s', %d, NOW())",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->user_creation));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion du bloc de contenu global dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge le bloc de contenu global
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour le bloc de contenu global dans la base de données.
   * 
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si une erreur survient, une exception est levée   
   *    
   * @return boolean   
   */
  public function update(){
    // si aucun id existe, c'est que le bloc de contenu global n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
    
    $sql = sprintf("UPDATE miki_global_content SET name = '%s' WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du bloc de contenu global dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime le bloc de contenu global 
   * 
   * Le bloc est supprimé des parties dont il fait partie via les clés étrangères.
   * Si une erreur survient, une exception est levée   
   * 
   * @return boolean      
   */   
  public function delete(){
    $sql = sprintf("DELETE FROM miki_global_content WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du bloc de contenu global : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Récupert la position du bloc de contenu dans la page donnée
   * 
   * Si le bloc de contenu global n'est pas disponible dans la page donnée, une exception est levée 
   *      
   * @param int $page_id Id de la page dans laquelle on veut récupérer la position du bloc de contenu global
   * @return int La position du bloc de contenu dans la page donnée  
   */   
  public function get_position($page_id){
    $sql = sprintf("SELECT position FROM miki_global_content_s_miki_page WHERE page_id = %d AND global_content_id = %d",
      mysql_real_escape_string($page_id),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Ce bloc de contenu global n'est pas disponible pour la page demandée"));
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Modifie la position du bloc de contenu global dans la page donnée
   *  
   * Si le bloc de contenu global n'est pas disponible dans la page donnée, une exception est levée 
   *      
   * @param int $page_id Id de la page dans laquelle on veut modifier la position du bloc de contenu global
   * @param int $position La nouvelle position du bloc de contenu global dans la page donnée
   */   
  public function set_position($page_id, $position){
    $sql = sprintf("UPDATE miki_global_content_s_miki_page SET position = %d WHERE page_id = %d AND global_content_id = %d",
      mysql_real_escape_string($position),
      mysql_real_escape_string($page_id),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Ce bloc de contenu global n'est pas disponible pour la page demandée"));
    return true;
  }
  
  /**
   * Recherche tous les contenus (Miki_global_content_content) du bloc de contenu global
   * 
   * Si aucun contenu n'est trouvé, une exception est levée.
   *      
   * @param string $lang Si spécifié, on ne récupert que les contenus de la langue donnée.
   * 
   * @see Miki_global_content_content
   *      
   * @return mixed Un tableau d'éléments de type Miki_global_content_content représentant les différents contenus du bloc de contenu global. L'indice du tableau représente la langue du contenu.
   */   
  public function get_contents($lang = ""){
    // récupert les langues des contenus présents dans le bloc de contenu global
    $return = array();
    $sql = "SELECT distinct miki_global_content_content.language_id, miki_language.code 
            FROM miki_global_content_content, miki_language 
            WHERE miki_global_content_content.global_content_id = %d AND miki_language.id = miki_global_content_content.language_id";
    
    // si la langue a été spécifiée, on ne récupert que le contenu dans cette langue
    if ($lang != ""){
      $language = new Miki_language();
      $language->load_from_code($lang);
      $sql .= " AND miki_global_content_content.language_id = $language->id";
    }
    
    $sql = sprintf($sql,
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Aucun contenu ne correspond au bloc de contenu global en cours"));
      
    // parcourt les langues trouvées
    while($row = mysql_fetch_array($result)){
      // recherche le contenu le plus récent dans la langue en cours
      $language_id = $row[0];
      $language_code = $row[1];
      $sql = sprintf("SELECT id FROM miki_global_content_content 
                      WHERE language_id = %d 
                      AND global_content_id = %d
                      AND date_modification = (SELECT max(date_modification) FROM miki_global_content_content WHERE global_content_id = %d AND language_id = %d)",
        mysql_real_escape_string($language_id),
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($language_id));
      $result2 = mysql_query($sql);
      $row2 = mysql_fetch_array($result2);
      $content = new Miki_global_content_content($row2[0]);
      // et le stock dans le tableau
      $return[strtolower($language_code)] = $content;
    }
    
    // s'il n'y a pas de résultat, on retourne "false""
    if (sizeof($return) == 0)
      $return = false;
    
    return $return;
  }
  
  /**
   * Recherche toutes les section de gabarit (Miki_template_part) dont le bloc de contenu global fait partie
   * 
   * Si le bloc de contenu global ne fait partie d'aucune section de gabarit une exception est levée
   * 
   * @see Miki_template_part      
   * 
   * @return mixed Un tableau d'éléments de type Miki_template_part représentant les sections de gabarit dont le bloc de contenu global fait partie
   */   
  public function get_parts(){
    $return = array();
    $sql = sprintf("SELECT template_part_id FROM miki_template_part_s_global_content WHERE global_content_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le bloc de contenu global n'est affecté à aucune partie"));
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_template_part($row['id']);
    }
    return $return;
  }

  /** 
   * Recherche tous les blocs de contenu global
   * 
   * Si aucun bloc de contenu global n'est présent, une exception est levée
   *       
   * @static
   * 
   * @return mixed Un tableau d'éléments de type Miki_global_content représentant les blocs de contenu global trouvés
   */   
  public static function get_all_global_contents(){
    $return = array();
    $sql = "SELECT * FROM miki_global_content ORDER BY name ASC";
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Aucun bloc de contenu global n'est présent dans la base de données"));
    
    while($row = mysql_fetch_array($result)){
      $global_content = new Miki_global_content($row['id']);
      $return[] = $global_content;
    }
    return $return;
  }
}
?>