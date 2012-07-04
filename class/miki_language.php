<?php
/**
 * Classe Miki_language
 * @package Miki
 */ 


/**
 * Représentation d'un language
 * 
 * @package Miki  
 */ 
class Miki_language{

  /**
   * Id du language
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Code du language
   *      
   * @var string
   * @access public   
   */
  public $code;
  
  /**
   * Nom du language
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Image du language
   *      
   * @var string
   * @access public   
   */
  public $picture;
  
  /**
   * 0 si le language n'est pas le language principale. 1 si le language est le language principale
   *      
   * @var int
   * @access public   
   */
  public $mainLanguage;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le language dont l'id a été donné
   * 
   * @param int $id Id du language à charger (optionnel)
   */
  function __construct($id = ""){
    // charge le language si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge une langue depuis un id
   *    
   * Si le language n'existe pas, une exception est levée.
   *    
   * @param int $id id du language à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_language WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("le language demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->code = $row['code'];
    $this->name = $row['name'];
    $this->picture = $row['picture'];
    $this->mainLanguage = $row['mainLanguage'];
    return true;
  }
  
  /**
   * Charge une langue d'après son code
   * 
   * Le code d'une langue correspond au code ISO 639-1 ({@link http://fr.wikipedia.org/wiki/Liste_des_codes_ISO_639-1})      
   *    
   * Si le language n'existe pas, une exception est levée.
   *    
   * @param int $id id du language à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load_from_code($code){
    $sql = sprintf("SELECT * FROM miki_language WHERE code = '%s'",
      mysql_real_escape_string($code));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Aucune langue ne correspond au code donné"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->code = $row['code'];
    $this->name = $row['name'];
    $this->picture = $row['picture'];
    $this->mainLanguage = $row['mainLanguage'];
    return true;
  }
  
  /**
   * Sauvegarde le language dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * le language doit posséder un code unique. Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->code))
      throw new Exception(_("Aucun code n'est associé au language"));
      
    // si un l'id de la page existe, c'est que la page existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // vérifie que le code du language n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_language WHERE code = '%s'",
      mysql_real_escape_string(strtolower($this->code)));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Un language avec le même code existe déjà dans la base de données"));
      
    $sql = sprintf("INSERT INTO miki_language (code, name, picture, mainLanguage) VALUES('%s', '%s', '%s', %d)",
      mysql_real_escape_string(strtolower($this->code)),
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->picture),
      mysql_real_escape_string($this->mainLanguage));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion du language dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la page
    $this->load($this->id);
    return true;
  }
  
  /**
   * Met à jour le language dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Le language doit posséder un nom unique (name). Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->code))
      throw new Exception(_("Aucun code n'est associé au language"));
      
    // si aucun id existe, c'est que le language n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // vérifie que le code du language n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_language WHERE code = '%s' AND  id != %d",
      mysql_real_escape_string(strtolower($this->code)),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Un language avec le même code existe déjà dans la base de données"));
      
    $sql = sprintf("UPDATE miki_language SET code = '%s', name = '%s', picture = '%s', mainLanguage = %d WHERE id = %d",
      mysql_real_escape_string(strtolower($this->code)),
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->picture),
      mysql_real_escape_string($this->mainLanguage),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du language dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime le language 
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */ 
  public function delete(){
    $sql = sprintf("DELETE FROM miki_language WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du language : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Ajoute une image au language
   * 
   * Ajoute une image au language, l'upload et la redimensionne pour qu'elle ait une taille de maximum 30px de large ou de long.
   * 
   * Si une erreur survient, une exception est levée.      
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   * @param string $chemin_destination Chemin de destination de l'image finale
   * @param string $nom_destination Nom de destination de l'image finale
   * @return boolean
   */        
  public function upload_picture($fichier, $chemin_destination, $nom_destination){ 
    
    // Inclut les fonctions d'importation et de traitement des images
    require_once("functions_pictures.php"); 	
    
    // traite le nom de destination
    $nom_destination = decode($nom_destination);
    $system = explode('.',strtolower($fichier['name']));
    $ext = $system[sizeof($system)-1];
    $nom_destination = $nom_destination ."." .$ext;  

    // le fichier doit être au format jpg, png ou gif
  	if (!preg_match('/png/i',$ext) && !preg_match('/gif/i',$ext) && !preg_match('/jpg|jpeg/i',$ext)){
      throw new Exception(_("Le type de l'image n'est pas supporté. Les types supportés sont : JPEG, GIF et PNG : $nom_destination - " .$fichier['name']));
    }
    
    // teste s'il y a eu une erreur
  	if ($fichier['error']) {
  		switch ($fichier['error']){
  			case 1: // UPLOAD_ERR_INI_SIZE
  				throw new Exception(_("Le fichier dépasse la limite autorisée (1Mo)"));
  				break;
  			case 2: // UPLOAD_ERR_FORM_SIZE
  				throw new Exception(_("Le fichier dépasse la limite autorisée (1Mo)"));
  				break;
  			case 3: // UPLOAD_ERR_PARTIAL
  				throw new Exception(_("L'envoi du fichier a été interrompu pendant le transfert"));
  				break;
  			case 4: // UPLOAD_ERR_NO_FILE
  				throw new Exception(_("Aucun fichier n'a été indiqué"));
  				break;
  			case 6: // UPLOAD_ERR_NO_TMP_DIR
  			  throw new Exception(_("Aucun dossier temporaire n'a été configuré. Veuillez contacter l'administrateur du site Internet."));
  			  break;
  			case 7: // UPLOAD_ERR_CANT_WRITE
  			  throw new Exception(_("Erreur d'écriture sur le disque"));
  			  break;
  			case 8: // UPLOAD_ERR_EXTENSION
  			  throw new Exception(_("L'extension du fichier n'est pas supportée"));
  			  break;
  		}
  	}

  	$size = "";
  	$file = $fichier['tmp_name'];
  	if (!is_uploaded_file($file) || $fichier['size'] > 5 * 1024 * 1024)
  		throw new Exception(_("Veuillez uploader des images plus petites que 5Mb"));
  	if (!($size = @getimagesize($file)))
  		throw new Exception(_("Veuillez n'uploader que des images. Les autres fichiers ne sont pas supportés"));
  	if (!in_array($size[2], array( 2, 3) ) )
  		throw new Exception(_("Le type de l'image n'est pas supporté. Les types supportés sont : JPEG et PNG"));
  		
  	// pas d'erreur --> upload
  	if ((isset($fichier['tmp_name']))&&($fichier['error'] == UPLOAD_ERR_OK)) {
  		if (!move_uploaded_file($fichier['tmp_name'], $chemin_destination .$nom_destination))
        exit();
  	}
  	
  	// redimensionne l'image
  	createthumb($chemin_destination .$nom_destination, "./" .$nom_destination, 30, 30);
  	
  	$this->picture = $nom_destination;
  	return true;
  }
  
  /**
   * Vérifie si le language courant est le language principal
   * 
   * @return boolean
   */         
  public function is_main(){
    return $this->mainLanguage == 1;
  }
  
  /**
   * Récupert le language principal
   * 
   * @static
   * @return Miki_language Le language principal
   */            
  public static function get_main(){
    $sql = "SELECT * FROM miki_language WHERE mainLanguage = 1";
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return new Miki_language($row['id']);
  }
  
  /**
   * Récupert le code du language principal
   * 
   * @static
   * @return string le code du language principal        
   */     
  public static function get_main_code(){
    $sql = "SELECT code FROM miki_language WHERE mainLanguage = 1";
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row['code'];
  }
  
  /**
   * Teste si le language passé en paramètre existe
   * 
   * @param string $lang Le code de la langue dont on veut vérifier l'existence
   * @static   
   * @return boolean
   */            
  public static function exist($lang){
    
    $sql = sprintf("SELECT count(*) FROM miki_language WHERE code = '%s'",
      mysql_real_escape_string($lang));    
    $result = mysql_query($sql);
    $nb = mysql_result($result,0);
    return $nb > 0;
  }
  
  /**
   * Récupère le nombre de languages configurés
   * 
   * @static
   * @return int 
   */           
  public static function get_nb_languages(){
    $return = array();
    $sql = "SELECT count(*) FROM miki_language";
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Récupère tous les languages configurés
   * 
   * @static
   * @return mixed Un tableau d'éléments de type Miki_language
   */            
  public static function get_all_languages(){
    $return = array();
    $sql = "SELECT * FROM miki_language ORDER BY mainLanguage DESC";
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_language($row['id']);
    }
    return $return;
  }
}
?>