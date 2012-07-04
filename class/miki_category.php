<?php
/**
 * Classe Miki_category
 * @package Miki
 */ 

/**
 * Représentation d'une catégorie de page.
 * 
 * Une catégorie est utilisée pour l'affichage l'url d'une page. 
 *  
 * Une page ayant un alias est concernée par l'url rewritting. L'url de la page correspondra alors à son alias + l'extension ".php"
 * Une catégorie sert à ajouter un répertoire. P.ex. une page dont l'alias est "mapage" et faisant partie de la catégorie "macategorie"
 * sera accesible via l'url suivante : http://www.monsite.com/macategorie/mapage.php   
 * 
 * @package Miki  
 */ 
class Miki_category{
  
  /**
   * Id de la catégorie
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom de la catégorie
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Position de la catégorie
   *      
   * @var int
   * @access public   
   */
  public $position;
  
  /**
   * Id de la catégorie parent (Miki_category)
   *      
   * @var int
   * @access public 
   */
  public $parent_id;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge la catégorie dont l'id a été donné
   * 
   * @param int $id Id de la catégorie à charger (optionnel)
   */
  function __construct($id = ""){
    // charge la catégorie si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Recherche la position de la catégorie en fonction de son parent si elle en a un ou en fonction des autres catégories
   * 
   * @access private      
   */      
  private function get_position(){
    if ($this->parent_id == 'NULL'){
      $sql = "SELECT max(position) FROM miki_category WHERE parent_id is null";
      $result = mysql_query($sql);
      $pos = mysql_result($result,0) + 1;
      if ($pos == "")
        $pos = 1;
    }
    else{
      $sql = "SELECT position, (SELECT count(*) FROM miki_category WHERE parent_id = $this->parent_id) FROM miki_category WHERE id = $this->parent_id";
      $result = mysql_query($sql);
      $row = mysql_fetch_array($result);
      $pos_parent = $row[0];
      $nb_children = $row[1];
      $pos = $pos_parent ."." .($nb_children + 1);
    }
    $this->position = $pos;
  }
  
  /**
   * Charge une catégorie depuis un id.
   *    
   * Si la catégorie n'existe pas, une exception est levée.
   *    
   * @param int $id id de la catégorie à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_category WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La catégorie demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->parent_id = $row['parent_id'];
    return true;
  }
  
  /**
   * Charge une catégorie d'après son nom.
   *    
   * Si la catégorie n'existe pas, une exception est levée.
   *    
   * @param string $name Nom de la catégorie à charger
   * @return boolean True si le chargement s'est déroulé correctement   
   */
  public function load_from_name($name){
    $sql = sprintf("SELECT * FROM miki_category WHERE name = '%s'",
      mysql_real_escape_string($name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Aucune catégorie ne correspond au nom donné"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->parent_id = $row['parent_id'];
    return true;
  }
  
  /**
   * Sauvegarde la catégorie dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   *
   * Si la catégorie ne possède pas de nom, une exception est levée
   * Si une autre catégorie possède le même nom, une exception est levée
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été attribué à la catégorie"));
      
    // si un l'id de la catégorie existe, c'est que la catégorie existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // vérifie que le nom de la catégorie n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_category WHERE name = '%s'",
      mysql_real_escape_string($this->name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Une catégorie du même nom existe déjà dans la base de données"));
    
    $this->get_position();
    
    $sql = sprintf("INSERT INTO miki_category (name, position, parent_id) VALUES('%s', '%s', %s)",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->position),
      mysql_real_escape_string($this->parent_id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion de la catégorie dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la page
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour la catégorie dans la base de données.
   * 
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si la catégorie ne possède pas de nom, une exception est levée
   * Si une autre catégorie possède le même nom, une exception est levée
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été attribué à la catégorie"));
      
    // si aucun id existe, c'est que la catégorie n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // vérifie que le nom de la catégorie n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_category WHERE name = '%s' AND  id != %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Une catégorie du même nom existe déjà dans la base de données"));
    
    $this->get_position();
    
    $sql = sprintf("UPDATE miki_category SET name = '%s', position = '%s', parent_id = %s WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->position),
      mysql_real_escape_string($this->parent_id),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour de la catégorie dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime la catégorie
   * 
   * @return boolean
   */
  public function delete(){
    // test qu'aucune page ne fasse partie de cette catégorie
    $sql = sprintf("SELECT count(*) FROM miki_page_content WHERE category_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la recherche des pages faisant partie de la catégorie donnée : ") ."<br />" .mysql_error());
    if (mysql_result($result,0) > 0)
      throw new Exception(_("Des pages existantes font partie de cette catégorie") ."<br />" .mysql_error());
    
    $sql = sprintf("DELETE FROM miki_category WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la catégorie : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Recherche toutes les pages faisant partie de la catégorie.
   *
   * @return mixed Un tableau d'élément de type Miki_page correspondant aux pages faisant partie de la catégorie
   */         
  public function get_pages(){
    $return = array();
    $sql = sprintf("SELECT DISTINCT(page_id) FROM miki_page_content WHERE category_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Aucune page ne fait partie de cette catégorie"));
    
    while($row = mysql_fetch_array($result)){
      $page = new Miki_page($row[0]);
      $return[] = $page;
    }
    return $return;
  }
  
  /**
   * Teste si la catégorie possède des catégorie enfants
   *
   * @return boolean
   */         
  public function has_children(){
    $sql = sprintf("SELECT count(*) FROM miki_category WHERE parent_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    $nb = mysql_result($result,0);
    return $nb > 0;
  }

  // fonction statique récupérant toutes les catégories
  
  /**
   * Récupère toutes les catégories
   * 
   * @param string $order Par quel champ les catégories trouvées seront triées (id ou name)
   * @param string $order_type Type de tri (ascendant "asc" ou descendant "desc")      
   * @static
   * @return mixed Un tableau d'éléments Miki_category représentant les catégories trouvées.
   */
  public static function get_all_categories($order = "", $type = "asc"){
    $return = array();
    $sql = "SELECT * FROM miki_category";
    
    // ordonne les catégories
    if ($order == "position")
      $sql .= " ORDER BY position " .$type;
    else
      $sql .= " ORDER BY id " .$type;
    
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $category = new Miki_category($row['id']);
      $return[] = $category;
    }
    return $return;
  }
}
?>