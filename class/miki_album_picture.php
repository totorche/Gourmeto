<?php
/**
 * Classe Miki_album_picture
 * @package Miki
 */ 

/**
 * Inclut les fonctions d'importation et de traitement des images
 */ 
require_once("functions_pictures.php");

/**
 * Représentation d'une photo d'un album photo
 * 
 * @package Miki  
 */ 
class Miki_album_picture{

  /**
   * Id de la photo
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Etat de la photo. 0 = non-publiée, 1 = publiée
   *      
   * @var int
   * @access public   
   */
  public $state;
  
  /**
   * Titre de la photo. Tableau dont la clé représente le code de la langue
   *      
   * @var string
   * @access public   
   */
  public $title;
  
  /**
   * Description de la photo. Tableau dont la clé représente le code de la langue
   *      
   * @var string
   * @access public   
   */
  public $description;
  
  /**
   * Lieu où a été prise la photo
   *      
   * @var string
   * @access public   
   */
  public $place;
  
  /**
   * Date de mise en ligne de la photo
   *      
   * @var string
   * @access public   
   */
  public $date_created;
  
  /**
   * Nom de fichier de la photo
   *      
   * @var string
   * @access public   
   */
  public $filename;
  
  /**
   * Répertoire où est situé la photo
   *      
   * @var string
   * @access public   
   */
  public $folder;
  
  /**
   * Id de l'album dans lequel a été placée la photo
   * 
   * @see Miki_album   
   * @var string
   * @access public   
   */
  public $id_album;
  
  /**
   * Position de la photo dans l'album
   *      
   * @var string
   * @access public   
   */
  public $position;
  
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge la photo dont l'id a été donné
   * 
   * @param int $id Id de la photo à charger (optionnel)
   */
  function __construct($id = ""){
    // charge l'album si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge une photo depuis un id
   *    
   * Si la photo n'existe pas, une exception est levée.
   *    
   * @param int $id id de la photo à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){                    
    $sql = sprintf("SELECT map.*, temp.title title, temp.description description, temp.language_code code
                    FROM miki_album_picture map
     
                    LEFT OUTER JOIN (SELECT mat1.title, mad1.description, mat1.language_code, mat1.id_album_picture
                    FROM miki_album_picture_title mat1,
                         miki_album_picture_description mad1
                    WHERE mat1.id_album_picture = mad1.id_album_picture
                      AND mat1.language_code = mad1.language_code) temp ON temp.id_album_picture = map.id
     
                    WHERE map.id = %d",  
                  
    mysql_real_escape_string($id));
      
    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql : $sql");
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La photo demandée n'existe pas : <br />$sql"));
    while ($row = mysql_fetch_array($result)){
      $this->id                               = $row['id'];
      $this->state                            = $row['state'];
      $this->title[$row['code']]              = stripslashes($row['title']);
      $this->description[$row['code']]        = stripslashes($row['description']);
      $this->place                            = $row['place'];
      $this->date_created                     = $row['date_created'];
      $this->filename                         = $row['filename'];
      $this->folder                           = $row['folder'];
      $this->id_album                         = $row['id_album'];
      $this->position                         = $row['position'];
    }
  
    return true;
  }
  
  /**
   * Sauvegarde la photo dans la base de données.
   *    
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   * 
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */
  public function save(){
    // si l'id de la photo existe, c'est que la photo existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // débute la transaction
    mysql_query("START TRANSACTION");
      
    $sql = sprintf("INSERT INTO miki_album_picture(state, place, date_created, filename, folder, id_album, position) VALUES(%d, '%s', NOW(), '%s', '%s', %d, %d)",
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->place),
      mysql_real_escape_string($this->filename),
      mysql_real_escape_string($this->folder),
      mysql_real_escape_string($this->id_album),
      mysql_real_escape_string($this->position));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'ajout de la photo dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    
    
    // ajoute le titre de la photo dans chaque langue
    if (is_array($this->title)){
      foreach($this->title as $key => $t){
        $sql = sprintf("INSERT INTO miki_album_picture_title (id_album_picture, language_code, title) VALUES(%d, '%s', '%s')",
          mysql_real_escape_string($this->id),
          mysql_real_escape_string($key),
          mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
        $result = mysql_query($sql);
        if (!$result){
          // annule les modifications
          mysql_query("ROLLBACK");

          throw new Exception(_("Erreur lors de l'ajout de la photo dans la base de données : ") ."<br />" .mysql_error());
        }
      }
    }
    
    
    // ajoute la description de la photo dans chaque langue
    if (is_array($this->description)){
      foreach($this->description as $key => $t){
        $sql = sprintf("INSERT INTO miki_album_picture_description (id_album_picture, language_code, description) VALUES(%d, '%s', '%s')",
          mysql_real_escape_string($this->id),
          mysql_real_escape_string($key),
          mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
        $result = mysql_query($sql);
        if (!$result){
          // annule les modifications
          mysql_query("ROLLBACK");

          throw new Exception(_("Erreur lors de l'ajout de la photo dans la base de données : ") ."<br />" .mysql_error());
        }
      }
    }
    
    // applique les modifications
    mysql_query("COMMIT");
    
    // recharge la page
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour la photo dans la base de données.
   * 
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si une erreur survient, une exception est levée   
   *    
   * @return boolean   
   */
  public function update(){
    // si aucun id existe, c'est que la photo n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // débute la transaction
    mysql_query("START TRANSACTION");
    
    $sql = sprintf("UPDATE miki_album_picture SET state = %d, place = '%s', filename = '%s', folder = '%s', id_album = %d, position = %d WHERE id = %d",
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->place),
      mysql_real_escape_string($this->filename),
      mysql_real_escape_string($this->folder),
      mysql_real_escape_string($this->id_album),
      mysql_real_escape_string($this->position),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de la photo dans la base de données : ") ."<br />" .mysql_error());
    }
      
    
    // supprime le titre de la photo dans chaque langue
    $sql = sprintf("DELETE FROM miki_album_picture_title WHERE id_album_picture = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de la photo dans la base de données : ") ."<br />" .mysql_error());
    }
    
    // ajoute le titre de la photo dans chaque langue
    if (is_array($this->title)){
      foreach($this->title as $key => $t){
        if ($key != ""){
          $sql = sprintf("INSERT INTO miki_album_picture_title (id_album_picture, language_code, title) VALUES(%d, '%s', '%s')",
            mysql_real_escape_string($this->id),
            mysql_real_escape_string($key),
            mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
          $result = mysql_query($sql);
          if (!$result){
            // annule les modifications
            mysql_query("ROLLBACK");
          
            throw new Exception(_("Erreur lors de la mise à jour de la photo dans la base de données : ") ."<br />" .mysql_error());
          }
        }
      }
    }
    
    
    // supprime la description de la photo dans chaque langue
    $sql = sprintf("DELETE FROM miki_album_picture_description WHERE id_album_picture = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de la photo dans la base de données : ") ."<br />" .mysql_error());
    }
    
    // ajoute la description de la photo dans chaque langue
    if (is_array($this->description)){
      foreach($this->description as $key => $t){
        if ($key != ""){
          $sql = sprintf("INSERT INTO miki_album_picture_description (id_album_picture, language_code, description) VALUES(%d, '%s', '%s')",
            mysql_real_escape_string($this->id),
            mysql_real_escape_string($key),
            mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
          $result = mysql_query($sql);
          if (!$result){
            // annule les modifications
            mysql_query("ROLLBACK");
          
            throw new Exception(_("Erreur lors de la mise à jour de la photo dans la base de données : ") ."<br />" .mysql_error());
          }
        }
      }
    }
    
    // applique les modifications
    mysql_query("COMMIT");
    
    return true;
  }
  
  /**
   * Supprime la photo
   * 
   * @return boolean
   */ 
  public function delete($delete_file = true){
    
    // supprime l'image du serveur si demandé
    if ($delete_file)
      $this->delete_picture();
    
    // puis de la base de données
    $sql = sprintf("DELETE FROM miki_album_picture WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la photo : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Vérifie si la photo existe déjà dans l'album
   * 
   * @return boolean true si la photo existe déjà dans l'album, false sinon  
   */   
  public function has_double(){
     // vérifie que l'image en cours d'upload n'existe pas déjà dans l'album
    $sql = sprintf("SELECT id FROM miki_album_picture WHERE id_album = %d AND filename = '%s'",
      mysql_real_escape_string($this->id_album),
      mysql_real_escape_string($this->filename));
    $result = mysql_query($sql);
  
    if (mysql_num_rows($result) > 1)
      return true;
    else      
      return false;
  }
  
   /**
   * Teste l'image à uploader, la redimensionne puis l'upload
   * 
   * Upload l'image, la redimensionne dans les grandeurs données et créé une vignette dans les grandeurs données
   * 
   * Si une erreur survient, une exception est levée   
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   * @param string $nom_destination Nom de destination de l'image finale
   * @param string $path Chemin où l'image doit être uploadée
   * @param int $picture_width Largeur maximale de la grande image
   * @param int $picture_height Hauteur maximale de la grande image
   * @param int $thumb_width Largeur maximale de la vignette
   * @param int $thumb_height Hauteur maximale de la vignette   
   * @return boolean
   */          
  public function upload_picture($fichier, $nom_destination, $path, $picture_width, $picture_height, $thumb_width, $thumb_height){
    $nom_destination = decode($nom_destination);

    if (substr($path, -1, 1) != '/')
      $path .= '/';
    
    // ajoute l'extension
    $system = explode('.',strtolower($fichier['name']));
    $ext = $system[sizeof($system)-1];
    $nom_destination_temp = $nom_destination ."_temp." .$ext;
    $nom_destination = $nom_destination ."." .$ext;
    $this->filename = $nom_destination;
    $this->update();

    // le fichier doit être au format jpg ou png
  	if (!preg_match('/png/i',$ext) && !preg_match('/jpg|jpeg/i',$ext)){
      throw new Exception(_("Le type de l'image n'est pas supporté. Les types supportés sont : JPEG et PNG : $nom_destination - " .$fichier['name']));
    }

    // teste s'il y a eu une erreur
  	if ($fichier['error']) {
  		switch ($fichier['error']){
  			case 1: // UPLOAD_ERR_INI_SIZE
  				throw new Exception(_("Le fichier dépasse la limite autorisée (5Mo)"));
  				break;
  			case 2: // UPLOAD_ERR_FORM_SIZE
  				throw new Exception(_("Le fichier dépasse la limite autorisée (5Mo)"));
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
  	// si le poid de l'image dépasse la taille définie par la constante "IMAGE_MAX_SIZE"
  	//else if ($fichier['size'] > IMAGE_MAX_SIZE)
  	//	throw new Exception(_("L'image choisie est trop volumineuse. La taille maximale est 5 Mo"));
  	
  	$size = "";
  	$file = $fichier['tmp_name'];
  	if (!is_uploaded_file($file) || $fichier['size'] > 5 * 1024 * 1024)
  		throw new Exception(_("Veuillez uploader des images plus petites que 5Mb"));
  	if (!($size = @getimagesize($file)))
  		throw new Exception(_("Veuillez n'uploader que des images. Les autres fichiers ne sont pas supportés"));
  	if (!in_array($size[2], array( 2, 3) ) )
  		throw new Exception(_("Le type de l'image n'est pas supporté. Les types supportés sont : JPEG et PNG"));
  	/*if (($size[0] < 25) || ($size[1] < 25))
  		throw new Exception(_("Veuillez uploader une image plus grande que 25px de côté."));*/

  	// pas d'erreur --> upload de l'image temporaire
  	if ((isset($fichier['tmp_name']))&&($fichier['error'] == UPLOAD_ERR_OK)) {
  		if (!move_uploaded_file($fichier['tmp_name'], $path .$nom_destination_temp))
        throw new Exception(_("Erreur pendant l'upload de l'image"));
  	}
  	
  	// si le répertoire "thumb n'existe pas, on le créé
  	if (!is_dir($path ."thumb/")){
      if (!mkdir($path ."thumb/"))
        throw new Exception(_("Erreur lors de la création du répertoire des vignettes' '"));
    }
  	
  	// redimensionne l'image
  	createthumb($path .$nom_destination_temp, $path .$nom_destination, $picture_width, $picture_height, false);
  	createthumb($path .$nom_destination_temp, $path ."thumb/" .$nom_destination, $thumb_width, $thumb_height, false);
  	
  	// puis supprime l'image temporaire
  	if (!unlink($path .$nom_destination_temp))
      throw new Exception("Erreur pendant la suppression de l'image");
  	
  	$this->title = $nom_destination;
  	return true;
  }
  
  /**
   * Supprime le fichier représentant l'image ainsi que sa miniature
   * 
   * Si une erreur survient, une exception est levée   
   *      
   * @return boolean
   */         
  public function delete_picture(){
    // pour l'image
    $path = URL_BASE .$this->folder;

    if (substr($path, -1, 1) != '/')
      $path .= '/';

    $path .= $this->filename;

    if (file_exists($path)){
      if (!unlink($path)){
        throw new Exception("Erreur pendant la suppression de l'image");
      }
    }
    
    // pour la miniature
    $path = URL_BASE .$this->folder;

    if (substr($path, -1, 1) != '/')
      $path .= '/';

    $path .= "thumb/" .$this->filename;
    
    if (file_exists($path)){
      if (!unlink($path)){
        throw new Exception("Erreur pendant la suppression de l'image");
      }
    }
    
    return true;
  }
  
  /** 
   * Récupert le nombre de commentaires de la photo
   * 
   * @return int
   */         
  public function get_nb_comments(){
    $sql = sprintf("SELECT count(*) FROM miki_album_picture_comment WHERE id_picture = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Récupert les commentaires de la photo
   *  
   * Récupert les commentaires de la photo et retourne un tableau de 2 dimensions
   * Voici la description des éléments de la 2ème dimension :   
   *   - index 'id' = id du commentaire
   *   - index 'id_person' = id de la personne
   *   - index 'comment' = commentaire
   *   - index 'date' = date du commentaire
   *  
   * @return mixed Un tableau contenant les commentaires de la photo
   */                                
  public function get_comments(){
    $sql = sprintf("SELECT id, id_person, comment, date FROM miki_album_picture_comment WHERE id_picture = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    
    $return = array();
    $x = 0;
    
    while($row = mysql_fetch_array($result)){
      $return[$x]['id'] = $row[0];
      $return[$x]['id_person'] = $row[1];
      $return[$x]['comment'] = $row[2];
      $return[$x]['date'] = $row[3];
      $x++;
    }
    
    return $return;
  }
  
  /**
   * Ajoute un commentaire à la photo
   *    
   * Si une erreur survient, une exception est levée
   *    
   * @param int $id_person Id de la personne (de type Miki_person) postant le commentaire
   * @param string $comment Commentaire
   * @return boolean          
   */  
  public function add_comment($id_person, $comment){
    $sql = sprintf("INSERT INTO miki_album_picture_comment(id_picture, id_person, comment, date) VALUES(%d, %d, '%s', NOW())",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($id_person),
      mysql_real_escape_string($comment));
    $result = mysql_query($sql);
    
    if (!$result){
      throw new Exception(_("Erreur lors de l'ajout du commentaire") ."<br />" .mysql_error());
    }
    
    return true;
  }
  
  /**
   * Supprime un commentaire de la photo
   * 
   * Si une erreur survient, une exception est levée      
   *  
   * @param int $id_comment Id du commentaire à supprimer
   * @return boolean
   */
  public function remove_comment($id_comment){
    $sql = sprintf("DELETE FROM miki_album_picture_comment WHERE id = %d",
      mysql_real_escape_string($id_comment));
    $result = mysql_query($sql);
    
    if (!$result){
      throw new Exception(_("Erreur lors de la suppression du commentaire") ."<br />" .mysql_error());
    }
    return true;
  }

  /**
   * Récupère toutes les photos trié par leur position
   * 
   * @param int $id_album Si définit, récupère uniquement les photos de l'album (Miki_album) dont l'id est donné et les trie selon leur position dans l'album
   * @static
   * @return mixed Un tableau d'éléments Miki_album_picture représentant les photos trouvées. False si aucune photo n'est trouvée.
   */     
  public static function get_all_pictures($id_album = ""){
    $return = array();
    $sql = "SELECT * FROM miki_album_picture";
    
    if ($id_album != "" && is_numeric($id_album)){
      $sql .= sprintf(" WHERE id_album = %d ORDER BY position ASC",
          mysql_real_escape_string($this->id_album));
    }
    else{
      $sql .= " ORDER BY id ASC";
    }
    
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      return false;
    
    while($row = mysql_fetch_array($result)){
      $item = new Miki_album_picture($row['id']);
      $return[] = $item;
    }
    return $return;
  }
}
?>