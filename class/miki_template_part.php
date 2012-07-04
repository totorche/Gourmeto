<?php
/**
 * Classe Miki_template_part
 * @package Miki
 */ 

/**
 * Représentation d'une section de gabarit.
 * 
 * Un gabarit (Miki_template) peut contenir une ou plusieurs sections de gabarit
 * 
 * Une section de gabarit peut contenir un ou plusieurs blocs de contenu global (Miki_global_content)  
 * 
 * Lors de l'édition d'une page (Miki_page) dont le gabarit possède au moins une section de gabarit, on peut sélectionner un bloc de contenu global appartenant
 * à cette section. On peut également définir l'ordre dans lequel doivent apparaître les blocs de contenu global directement dans l'édition de la page.
 * 
 * Pour intégrer une section de gabarit dans une page, on place la balise [miki_part='code_de_la_section'] à l'endroit désiré dans le code de la page. 
 *   
 * @see Miki_template
 * @see Miki_global_content
 *  
 * @package Miki  
 */
class Miki_template_part{

  /**
   * Id de la section de gabarit
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom de la section de gabarit
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Code de la section de gabarit. Ce code permet d'insérer la section dans une page.
   *      
   * @var string
   * @access public   
   */
  public $code;
  
  /**
   * Contenu de la section de gabarit
   *      
   * @var string
   * @access public   
   */
  public $content;

  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge la section de gabarit dont l'id a été donné
   * 
   * @param int $id Id de la section de gabarit à charger (optionnel)
   */
  function __construct($id = ""){
    // charge la section si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge une section de gabarit depuis un id
   *    
   * Si la section n'existe pas, une exception est levée.
   *    
   * @param int $id id de la section à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_template_part WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La section demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->code = $row['code'];
    $this->content = $row['content'];
    return true;
  }
  
  /**
   * Sauvegarde la section de gabarit dans la base de données.
   * 
   * La section doit posséder un nom unique, sinon une exception est levée
   *      
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){      
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été attribué à la section"));
      
    // si un l'id de la section existe, c'est que la section existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // vérifie que le code de la section n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_template_part WHERE name = '%s'",
      mysql_real_escape_string($this->name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Une section possédant le même nom existe déjà dans la base de données"));
      
    $sql = sprintf("INSERT INTO miki_template_part (name, code, content) VALUES('%s', '%s', '%s')",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->code),
      mysql_real_escape_string($this->content));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion de la section dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la page
    $this->load($this->id);   
    return true;
  }
  
  /**
   * Met à jour la section de gabarit dans la base de données.
   * 
   * La section doit posséder un nom unique, sinon une exception est levée  
   *      
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){      
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été attribué à la section"));
      
    // si aucun id existe, c'est que la section n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // vérifie que le code de la section n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_template_part WHERE code = '%s' AND  id != %d",
      mysql_real_escape_string($this->code),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Une section possédant le même code existe déjà dans la base de données"));
    
    $sql = sprintf("UPDATE miki_template_part SET name = '%s', code = '%s', content = '%s' WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->code),
      mysql_real_escape_string($this->content),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour de la section dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime la section de gabarit
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_template_part WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la section : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Ajoute un bloc de contenu global (Miki_global_content) à la section
   * 
   * Si une erreur survient, une exception est levée.      
   * 
   * @param Miki_global_content $global_content Bloc de contenu global à ajouter à la section.
   * 
   * @see Miki_global_content   
   * @return boolean            
   */ 
  public function add_global_content(Miki_global_content $global_content){
    // si la section n'existe pas encore dans la base de données, on essaye de l'y insérer
    if (!isset($this->id)){
      try{
        $this->save();
      }catch(Exception $e){
        throw new Exception(_("La section n'a pas pu être ajoutée à la base de données."));
      }
    }
    // ajoute le bloc de contenu global à la section. Si il y est déjà, ne fait rien (mot-clé "ignore")
    $sql = sprintf("insert ignore into miki_template_part_s_global_content (template_part_id, global_content_id) VALUES(%d, %d)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($global_content->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant l'ajout du bloc de contenu global à la section : ") ."<br />" .mysql_error());
    
    return true;
  }
  
  /**
   * Supprime tous les blocs de contenu globaux (Miki_global_content) de la section de gabarit
   * 
   * @see Miki_global_content   
   * @return boolean      
   */   
  public function remove_global_contents(){
    $sql = sprintf("DELETE FROM miki_template_part_s_global_content WHERE template_part_id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression des blocs de contenu globaaux de la section : ") ."<br />" .mysql_error());
      
    return true;
  }
  
  /**
   * Recherche tous les blocs de contenu globaux (Miki_global_content) faisant partie de la section de gabarit
   * 
   * @see Miki_global_content
   * @return mixed Un tableau d'éléments de type Miki_global_content représentant les blocs de contenu globaux récupérés.         
   */   
  public function get_global_contents(){
    $return = array();
    $sql = sprintf("SELECT global_content_id FROM miki_template_part_s_global_content WHERE template_part_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      try{
        $gc = new Miki_global_content($row[0]);
        $return[] = $gc;
      }
      catch(Exception $e){}
    }
    return $return;
  }

  /**
   * Recherche toutes les sections de gabarit
   * 
   * @static
   * @return mixed Un tableau d'éléments de type Miki_template_part représentant les sections de gabarit récupérées.
   */            
  public static function get_all_parts(){
    $return = array();
    $sql = "SELECT * FROM miki_template_part";
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Aucune section n'est présente dans la base de données"));
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_template_part($row['id']);
    }
    return $return;
  }
}
?>