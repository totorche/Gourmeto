<?php
/**
 * Classe Miki_global_content_content
 * @package Miki 
 */


/**
 * Représentation du contenu d'un bloc de contenu global (Miki_global_content)
 * 
 * Un bloc de contenu global possède un contenu par langue configurée
 *  
 * @see Miki_global_content   
 * 
 * @package Miki  
 */ 
class Miki_global_content_content{

  /**
   * Id du contenu
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Id du bloc de contenu global auquel fait partie le contenu
   *      
   * @var int
   * @access public   
   */
  public $global_content_id;
  
  /**
   * Id de la langue du contenu
   *      
   * @var int
   * @access public   
   */
  public $language_id;
  
  /**
   * Type de contenu (code ou file). Code veut dire que le contenu sera du code HTML. File veut dire que le contenu sera un nom de fichier qui contiendra le contenu réel.
   *      
   * @var string
   * @access public   
   */
  public $content_type = 'code';
  
  /**
   * Contenu
   *      
   * @var string
   * @access public   
   */
  public $content;
  
  /**
   * Date de la dernière modification du contenu
   *      
   * @var string
   * @access public   
   */
  public $date_modification;
  
  /**
   * Utilisateur ayant effectué la dernière modification du contenu
   *      
   * @var int
   * @access public   
   */
  public $user_modification;
  
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
   * Définit la langue du contenu (obligatoire avant de sauver)
   * 
   * @param string $language Code de la langue du contenu
   * @return boolean         
   */     
  public function setLanguage($language){
    // recherche si la langue existe
    $sql = sprintf("SELECT id FROM miki_language WHERE code = '%s'",
      mysql_real_escape_string($language));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0){
      throw new Exception(_("La langue spécifiée n'existe pas"));
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
   * Lève une exception si le contenu avec l'id donné n'a pas été trouvé      
   *    
   * @param int $id id du contenu à charger
   * @return boolean   
   */
  public function load($id){
    // recherche si la langue existe
    $sql = sprintf("SELECT * FROM miki_global_content_content WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le contenu demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->content_type = $row['content_type'];
    $this->content = $row['content'];
    $this->global_content_id = $row['global_content_id'];
    $this->language_id = $row['language_id'];
    $this->date_modification = $row['date_modification'];
    $this->user_modification = $row['user_modification'];
    return true;
  }
  
  /**
   * Sauvegarde le contenu dans la base de données.
   *    
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   * 
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->global_content_id))
      throw new Exception(_("Le contenu n'est lié à aucune contenu global"));
    if (!isset($this->language_id))
      throw new Exception(_("Aucune langue n'a été définie pour ce contenu"));
      
    // si l'id du contenu existe, c'est que le contenu existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
    
    $this->user_modification = $_SESSION['miki_admin_user_id'];
    
    // recherche si la langue existe
    $sql = sprintf("INSERT INTO miki_global_content_content (date_modification, user_modification, content_type, content, global_content_id, language_id) 
                    values (NOW(), %d, '%s', '%s', %d, %d)",
                    mysql_real_escape_string($this->user_modification),
                    mysql_real_escape_string($this->content_type),
                    mysql_real_escape_string($this->content),
                    mysql_real_escape_string($this->global_content_id),
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
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si une erreur survient, une exception est levée   
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->global_content_id))
      throw new Exception(_("Le contenu n'est lié à aucun bloc de contenu global"));
    if (!isset($this->language_id))
      throw new Exception(_("Aucune langue n'a été définie pour ce contenu"));
      
    // si aucun id existe, c'est que le contenu n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    $this->user_modification = $_SESSION['miki_admin_user_id'];
    
    // recherche si la langue existe
    $sql = sprintf("UPDATE miki_global_content_content SET date_modification = NOW(), user_modification = %d, content_type = '%s', content = '%s', global_content_id = %d, language_id = %d WHERE id = %d",
                    mysql_real_escape_string($this->user_modification),
                    mysql_real_escape_string($this->content_type),
                    mysql_real_escape_string($this->content),
                    mysql_real_escape_string($this->global_content_id),
                    mysql_real_escape_string($this->language_id),
                    mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du contenu dans la base de données :") ."<br />" .mysql_error());
      
    return true;
  }
  
  /**
   * Supprime de contenu 
   *    
   * @return boolean      
   */   
  public function delete(){
    $sql = sprintf("DELETE FROM miki_global_content_content WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du contenu : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Recherche le bloc de contenu global dont le contenu fait partie
   * 
   * @see Miki_global_content   
   * @return Miki_global_content Le bloc de contenu global dont le contenu fait partie
   */   
  public function get_global_content(){
    if (!isset($this->global_content_id))
      return false;
    
    return new Miki_global_content($this->global_content_id);
  }
  
  /** 
   * Recherche tous les contenu
   * 
   * Si aucun contenu n'est présent, une exception est levée
   *       
   * @static
   * 
   * @return mixed Un tableau d'éléments de type Miki_global_content_content représentant les contenus trouvés
   */ 
  public static function gat_all_global_content_contents(){
    $return = array();
    $sql = "SELECT * FROM miki_global_content_content";
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Aucun contenu n'est présent dans la base de données"));
  
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_global_content_content($row['id']);
    }
    return $return;
  }
}
?>