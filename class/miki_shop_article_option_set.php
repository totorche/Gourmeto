<?php
/**
 * Classe Miki_shop_article_option_set
 * @package Miki
 */ 

/**
 * Représentation d'un set d'options pour les articles du shop
 *
 * Un set d'option représente un set de 0 à plusieurs objets de type Miki_shop_article_option
 * 
 * @see Miki_shop_article_option    
 * 
 * @package Miki  
 */ 
class Miki_shop_article_option_set{
  
  /**
   * Id du set
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom du set
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * L'id du shop auquel est lié le set
   *      
   * @var int
   * @access public   
   */
  public $id_shop;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le set dont l'id a été donné
   * 
   * @param int $id Id du set à charger (optionnel)
   */
  function __construct($id = ""){
    // charge le set si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un set depuis un id
   *    
   * Si le set n'existe pas, une exception est levée.
   *    
   * @param int $id id du set à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_shop_article_option_set WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le set demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->id_shop = $row['id_shop'];
    return true;
  }
  
  /**
   * Sauvegarde le set dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Le set doit posséder un nom unique (name) au sein d'un même shop. Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été attribué au set"));
      
    // si l'id du set existe, c'est que le set existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
    
    // vérifie que le nom du set n'existe pas déjà dans la base de données pour le même shop
    $sql = sprintf("SELECT id FROM miki_shop_article_option_set WHERE name = '%s' AND id_shop = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id_shop));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Un set du même nom existe déjà"));
    
    // sauve le set
    $sql = sprintf("INSERT INTO miki_shop_article_option_set (name, id_shop) VALUES('%s', %d)",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id_shop));
    $result = mysql_query($sql);
    if (!$result){
      mysql_query("ROLLBACK");
      throw new Exception(_("Erreur lors de l'insertion du set"));
    }
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la page
    $this->load($this->id);  
    return true;
  }
  
  /**
   * Met à jour le set dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Le set doit posséder un nom unique (name) au sein d'un même shop. Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été attribué au set"));
      
    // si aucun id existe, c'est que le set n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // vérifie que le nom du set n'existe pas déjà dans la base de données pour le même shop
    $sql = sprintf("SELECT id FROM miki_shop_article_option_set WHERE name = '%s' AND id_shop = %d AND id != %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id_shop),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Un set du même nom existe déjà"));
    
    // met à jour le set
    $sql = sprintf("UPDATE miki_shop_article_option_set SET name = '%s', id_shop = %d WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id_shop),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du set"));
    
    return true;
  }
  
  /**
   * Supprime le set 
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_shop_article_option_set WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du set"));
    return true;
  }
  
  /**
   * Ajoute une option (Miki_shop_article_option) dans le set
   * 
   * Si une erreur survient, une exception est levée.  
   * 
   * @param int $id_option Id de l'option à ajouter au set   
   * @see Miki_shop_article_option
   * @return boolean
   */      
  public function add_option($id_option){
    // si le set n'existe pas encore dans la base de données, on essaye de l'y insérer
    if (!isset($this->id)){
      try{
        $this->save();
      }catch(Exception $e){
        throw new Exception(_("Le set n'a pas pu être ajouté"));
      }
    }
    // insert l'option dans le set. Si il y est déjà, ne fait rien (mot-clé "ignore")
    $sql = sprintf("INSERT ignore INTO miki_shop_article_option_s_miki_shop_article_option_set (miki_shop_article_option_id, miki_shop_article_option_set_id) VALUES(%d, %d)",
      mysql_real_escape_string($id_option),
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant l'ajout de l'option au set"));
    
    return true;
  }
  
  /**
   * Supprime une option (Miki_shop_article_option) du set
   * 
   * Si une erreur survient, une exception est levée.  
   * 
   * @param int $id_option Id de l'option à supprimer du set   
   * @see Miki_shop_article_option
   * @return boolean
   */ 
  public function remove_option($id_option){
    $sql = sprintf("DELETE FROM miki_shop_article_option_s_miki_shop_article_option_set WHERE miki_shop_article_option_id = %d AND miki_shop_article_option_set_id = %d",
      mysql_real_escape_string($id_option),
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de l'option du set"));
      
    return true;
  }
  
  /**
   * Supprime toutes les options (Miki_shop_article_option) du set
   * 
   * Si une erreur survient, une exception est levée.  
   * 
   * @see Miki_shop_article_option
   * @return boolean
   */ 
  public function remove_all_options(){
    $sql = sprintf("DELETE FROM miki_shop_article_option_s_miki_shop_article_option_set WHERE miki_shop_article_option_set_id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression des options du set"));
      
    return true;
  }
  
  /**
   * Détermine si l'option dont l'id est donné est dans le set en cours
   * 
   * @param int $id_option L'option à tester
   * @return boolean
   */
  public function has_option($id_option){
    $sql = sprintf("SELECT count(*) 
                    FROM miki_shop_article_option_s_miki_shop_article_option_set 
                    WHERE miki_shop_article_option_set_id = %d AND miki_shop_article_option_id = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($id_option));
    $result = mysql_query($sql);
    $row = mysql_fetch_row($result);
    return $row[0] > 0;
  }
  
  /**
   * Recherche toutes les options faisant partie du set
   * 
   * @see Miki_shop_article_option   
   * @return mixed Un tableau d'éléments Miki_shop_article_option représentant les options faisant partie du set
   */          
  public function get_options(){
    $return = array();
    $sql = sprintf("SELECT miki_shop_article_option_id 
                    FROM miki_shop_article_option_s_miki_shop_article_option_set 
                    WHERE miki_shop_article_option_set_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_row($result)){
      $return[] = new Miki_shop_article_option($row[0]);
    }
    return $return;
  }
  
  /**
   * Recherche le nombre d'options faisant partie du set
   * 
   * @see Miki_shop_article_option   
   * @return int Le nombre d'options faisant partie du set
   */          
  public function get_nb_options(){
    $return = array();
    $sql = sprintf("SELECT COUNT(*)
                    FROM miki_shop_article_option_s_miki_shop_article_option_set 
                    WHERE miki_shop_article_option_set_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    $row = mysql_fetch_row($result);
    return $row[0];
  }
  
  /**
   * Récupére tous les sets
   *    
   * @param boolean $all Si true, retourne tous les sets, y compris ceux qui ne sont pas activés. Si false, retourne uniquement les sets activés
   * 
   * @static
   *    
   * @return mixed Un tableau d'éléments de type Miki_shop_article_option_set
   */ 
  public static function get_all_sets($id_shop = ""){
    $return = array();
    $sql = "SELECT * FROM miki_shop_article_option_set";
    
    if ($id_shop != "")
      $sql .= sprintf(" WHERE id_shop = %d", mysql_real_escape_string($id_shop));
    
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_shop_article_option_set($row['id']);
    }
    return $return;
  }
}
?>