<?php
/**
 * Classe Miki_page_content
 * @package Miki
 */ 

/**
 * Représentation du contenu d'une page du CMS (Miki_page)
 * 
 * Une page possède un contenu par langue définie (Miki_language). Chaque contenu est donc lié à une page et à une langue.
 * 
 * Chaque contenu possède des balise (Title, Description, Keywords, et autres) personnalisés. Cela veut dire que chaque page dans chaque langue peut avoir des balises différentes.
 * Les métas seront intégrés à la page web automatiquement et en respectant les règles du W3C.
 * 
 * Un contenu peut également posséder un alias. Cet alias est utilisé pour l'URL Rewritting. 
 * Si un contenu possède l'alias "mapage", la page concernée sera accessible via l'URL http://monsite.com/mapage.php
 * 
 * Un contenu peut aussi avoir une catégorie. Les catégories sont également utilisée pour l'URL Rewritting. 
 * Si un contenu fait partie de la catégorie "macategorie", la page concernée sera accessible via l'URL http://monsite.com/macategorie/mapage.php
 *
 *  
 * @see Miki_page
 * @see Miki_language 
 *  
 * @package Miki  
 */
class Miki_page_content{

  /**
   * Id du contenu
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Id de la page liée au contenu
   *      
   * @var int
   * @access public   
   */
  public $page_id;
  
  /**
   * Id de la langue du contenu
   *      
   * @var int
   * @access public   
   */
  public $language_id;
  
  /**
   * Date de la dernière modification du contenu
   *      
   * @var int
   * @access public   
   */
  public $date_modification;
  
  /**
   * Id de l'utilisateur (Miki_user) qui a modifié en dernier le contenu
   *      
   * @var int
   * @access public   
   */
  public $user_modification;
  
  /**
   * Balise Title du contenu
   *      
   * @var int
   * @access public   
   */
  public $title;
  
  /**
   * Balise Description du contenu
   *      
   * @var int
   * @access public   
   */
  public $description;
  
  /**
   * Balise Keywords du contenu
   *      
   * @var int
   * @access public   
   */
  public $keywords;
  
  /**
   * Autres Métas du contenu
   *      
   * @var int
   * @access public   
   */
  public $metas;
  
  /**
   * Alias du contenu. L'alias correspond à l'URL de la page utilisant ce contenu. La page sera donc accessible via monAlias.php
   *      
   * @var int
   * @access public   
   */
  public $alias;
  
  /**
   * Texte du menu pour ce contenu
   *      
   * @var int
   * @access public   
   */
  public $menu_text;
  
  /**
   * Id de la catégorie contenu
   *      
   * @var int
   * @access public   
   */
  public $category_id;
  
  /**
   * Type du contenu(code, file ou url). Code veut dire que le contenu sera du code HTML. File veut dire que le contenu sera un nom de fichier qui contiendra le contenu réel. Url est un url sur lequel le visiteur sera redirigé
   *      
   * @var string
   * @access public   
   */
  public $content_type = 'code';
  
  /**
   * Contenu du contenu
   *      
   * @var int
   * @access public   
   */
  public $content;
  
  /**
   * Balise Noembed. Si remplie, le contenu de cette balise sera affiché dans la page concernée.
   *      
   * @var int
   * @access public   
   */
  public $noembed;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le contenu dont l'id a été donné
   * 
   * @param int $id Id du contenu à charger (optionnel)
   */
  function __construct($id = ""){
    // charge le contenu si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Définit la langue du contenu (obligatoire avant de sauver le contenu).
   *  
   * @return boolean      
   */              
  public function setLanguage($language){
    // recherche si la langue existe
    $sql = sprintf("SELECT id FROM miki_language WHERE code = '%s'",
      mysql_real_escape_string($language));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0){
      throw new Exception(_("La langue spécifiée n'existe pas"));
      return false;
    }
    $row = mysql_fetch_array($result);
    $this->language_id = $row[0];
    return true;
  }
  
  /**
   * Charge un contenu depuis un id
   *    
   * Si le contenu n'existe pas, une exception est levée.
   *    
   * @param int $id id du contenu à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    // recherche si le contenu existe
    $sql = sprintf("SELECT * FROM miki_page_content WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le contenu demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->date_modification = $row['date_modification'];
    $this->user_modification = $row['user_modification'];
    $this->title = $row['title'];
    $this->description = $row['description'];
    $this->keywords = $row['keywords'];
    $this->metas = $row['metas'];
    $this->alias = $row['alias'];
    $this->menu_text = $row['menu_text'];
    $this->category_id = $row['category_id'] != '' ? $row['category_id'] : 'NULL';
    $this->content_type = $row['content_type'];
    $this->content = $row['content'];
    $this->noembed = $row['noembed'];
    $this->page_id = $row['page_id'];
    $this->language_id = $row['language_id'];
    return true;
  }
  
  /**
   * Sauvegarde le contenu dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Le contenu doit être lié à une page et à une langue. Le cas contraire une exception est levée.   
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->page_id))
      throw new Exception(_("Le contenu n'est lié à aucune page"));
    if (!isset($this->language_id))
      throw new Exception(_("Aucune langue n'a été définie pour ce contenu"));
      
    // si l'id du contenu existe, c'est que le contenu existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
    
    $this->user_modification = $_SESSION['miki_admin_user_id'];
    
    // recherche si la langue existe
    $sql = sprintf("INSERT INTO miki_page_content (date_modification, user_modification, title, description, keywords, metas, alias, menu_text, category_id, content_type, content, noembed, page_id, language_id) 
                    values (NOW(), %d, '%s', '%s', '%s', '%s', '%s', '%s', %s, '%s', '%s', '%s', %d, %d)",
                    mysql_real_escape_string($this->user_modification),
                    mysql_real_escape_string($this->title),
                    mysql_real_escape_string($this->description),
                    mysql_real_escape_string($this->keywords),
                    mysql_real_escape_string($this->metas),
                    mysql_real_escape_string($this->alias),
                    mysql_real_escape_string($this->menu_text),
                    mysql_real_escape_string($this->category_id),
                    mysql_real_escape_string($this->content_type),
                    mysql_real_escape_string($this->content),
                    mysql_real_escape_string($this->noembed),
                    mysql_real_escape_string($this->page_id),
                    mysql_real_escape_string($this->language_id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion du contenu dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la page
    $this->load($this->id);
    return true;
  }
  
  /**
   * Met à jour le contenu dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Le contenu doit être lié à une page et à une langue. Le cas contraire une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->page_id))
      throw new Exception(_("Le contenu n'est lié à aucune page"));
    if (!isset($this->language_id))
      throw new Exception(_("Aucune langue n'a été définie pour ce contenu"));
      
    // si aucun id existe, c'est que le contenu n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    $this->user_modification = $_SESSION['miki_admin_user_id'];
    
    // recherche si la langue existe
    $sql = sprintf("UPDATE miki_page_content SET date_modification = NOW(), user_modification = %d, title = '%s', description = '%s', keywords = '%s', metas = '%s', alias = '%s', menu_text = '%s', category_id = %s, content_type = '%s', content = '%s', noembed = '%s', page_id = %d, language_id = %d WHERE id = %d",
                    mysql_real_escape_string($this->user_modification),
                    mysql_real_escape_string($this->title),
                    mysql_real_escape_string($this->description),
                    mysql_real_escape_string($this->keywords),
                    mysql_real_escape_string($this->metas),
                    mysql_real_escape_string($this->alias),
                    mysql_real_escape_string($this->menu_text),
                    mysql_real_escape_string($this->category_id),
                    mysql_real_escape_string($this->content_type),
                    mysql_real_escape_string($this->content),
                    mysql_real_escape_string($this->noembed),
                    mysql_real_escape_string($this->page_id),
                    mysql_real_escape_string($this->language_id),
                    mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    //throw new Exception($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du contenu dans la base de données :") ."<br />$sql<br />" .mysql_error());
      
    return true;
  }
  
  /**
   * Supprime le contenu
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_page_content WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du contenu : ") ."<br />" .mysql_error());
  }
  
  /**
   * Retourne la page dont le contenu fait partie
   * 
   * @return Miki_page      
   */   
  public function get_page(){
    if (!isset($this->page_id))
      return false;
    
    $page = new Miki_page($this->page_id);
    return $page;
  }
  
  /**
   * Recherche tous les contenus
   *
   * @return mixed Un tableau d'éléments de type Miki_page_content                  
   */
  public static function gat_all_page_contents(){
    $return = array();
    $sql = "SELECT * FROM miki_page_content";
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Aucun contenu n'est présent dans la base de données"));
  
    while($row = mysql_fetch_array($result)){
      $page_content = new Miki_page_content($row['id']);
      $return[] = $page_content;
    }
    return $return;
  }
}
?>