<?php
/**
 * Classe Miki_album
 * @package Miki
 */ 

/**
 * Inclut les fonctions d'importation et de traitement des images
 */ 
require_once("functions_pictures.php");

/**
 * Représentation d'un album photo
 * 
 * @package Miki  
 */ 
class Miki_album{

  /**
   * Id de l'album photo
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom de l'album photo
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Etat de l'album photo (0 = désactivé, 1 = activé)
   *      
   * @var int
   * @access public   
   */
  public $state;
  
  /**
   * Titre de l'album photo. Tableau dont la clé représente le code de la langue
   *      
   * @var string
   * @access public   
   */
  public $title;
  
  /**
   * Description de l'album photo. Tableau dont la clé représente le code de la langue
   *      
   * @var string
   * @access public   
   */
  public $description;
  
  /**
   * Id de la photo de couverture de l'album. Aucune couverture par défaut.
   *      
   * @var int
   * @access public   
   */
  public $cover_picture = 'NULL';
  
  /**
   * Largeur maximale (en pixel) d'une photo grand format. 1000 par défaut.
   *      
   * @var int
   * @access public   
   */
  public $picture_width = 1000;
  
  /**
   * Hauteur maximale (en pixel) d'une photo grand format. 1000 par défaut.
   *      
   * @var int
   * @access public   
   */
  public $picture_height = 1000;
  
  /**
   * Largeur maximale (en pixel) d'une vignette. 100 par défaut.
   *      
   * @var int
   * @access public   
   */
  public $thumb_width = 100;
  
  /**
   * Hauteur maximale (en pixel) d'une vignette. 100 par défaut.
   *      
   * @var int
   * @access public   
   */
  public $thumb_height = 100;
  
  /**
   * Répertoire de l'album photo dans lequel sont stockées les photos de l'album
   *      
   * @var string
   * @access public   
   */
  public $folder;
  
  /**
   * Date de création de l'album photo (format yyyy-mm-dd hh:mm:ss)
   *      
   * @var string
   * @access public   
   */
  public $date_creation;
  
  /**
   * Id de l'utilisateur (Miki_user) ayant créé l'album photo
   *   
   * @see Miki_user     
   * @var string
   * @access public   
   */
  public $user_creation;
  
  /**
   * Tableau contenant les photos de l'album (éléments de type Miki_album_picture)
   *      
   * @see Miki_album_picture   
   * @var string
   * @access public   
   */
  public $pictures;
  
  
  
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge l'album dont l'id a été donné
   * 
   * @param int $id Id de l'album à charger (optionnel)
   */
  function __construct($id = ""){
    // charge l'album si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un album photo depuis un id
   *    
   * Si l'album n'existe pas, une exception est levée.
   *    
   * @param int $id id de l'album photo à charger
   * @param boolean $get_pictures Si true on charge les images de l'album, sinon on ne les charge pas
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id, $get_pictures = true){
    $sql = sprintf("SELECT ma.*, temp.title title, temp.description description, temp.language_code code
                    FROM miki_album ma
     
                    LEFT OUTER JOIN (SELECT mat1.title, mad1.description, mat1.language_code, mat1.id_album
                    FROM miki_album_title mat1,
                         miki_album_description mad1
                    WHERE mat1.id_album = mad1.id_album
                      AND mat1.language_code = mad1.language_code) temp ON temp.id_album = ma.id
     
                    WHERE ma.id = %d
                    ",
      mysql_real_escape_string($id));

    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql");
    
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("L'album demandé n'existe pas"));
    while ($row = mysql_fetch_array($result)){
      $this->id                               = $row['id'];
      $this->name                             = $row['name'];
      $this->state                            = $row['state'];
      $this->title[$row['code']]              = stripslashes($row['title']);
      $this->description[$row['code']]        = stripslashes($row['description']);
      $this->cover_picture                    = $row['cover_picture'] == "" ? 'NULL' : $row['cover_picture'];
      $this->picture_width                    = $row['picture_width'];
      $this->picture_height                   = $row['picture_height'];
      $this->thumb_width                      = $row['thumb_width'];
      $this->thumb_height                     = $row['thumb_height'];
      $this->folder                           = stripslashes($row['folder']);
      $this->date_creation                    = $row['date_creation'];
      $this->user_creation                    = $row['user_creation'];
    }
    
    // récupert les photos de l'album id demandé
    if ($get_pictures)
      $this->pictures = $this->get_pictures();
      
    return true;
  }
  
  /**
   * Charge un album photo d'après son nom
   *    
   * Si l'album n'existe pas, une exception est levée.
   *    
   * @param string $name nom de l'album photo à charger
   * @param boolean $get_pictures Si true on charge les images de l'album, sinon on ne les charge pas
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load_by_name($name, $get_pictures = true){
    $sql = sprintf("SELECT ma.*, temp.title title, temp.description description, temp.language_code code
                    FROM miki_album ma
     
                    LEFT OUTER JOIN (SELECT mat1.title, mad1.description, mat1.language_code, mat1.id_album
                    FROM miki_album_title mat1,
                         miki_album_description mad1
                    WHERE mat1.id_album = mad1.id_album
                      AND mat1.language_code = mad1.language_code) temp ON temp.id_album = ma.id
     
                    WHERE ma.name = '%s'
                    ",
      mysql_real_escape_string($name));

    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql");
    
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("L'album demandé n'existe pas"));
    while ($row = mysql_fetch_array($result)){
      $this->id                               = $row['id'];
      $this->name                             = $row['name'];
      $this->state                            = $row['state'];
      $this->title[$row['code']]              = stripslashes($row['title']);
      $this->description[$row['code']]        = stripslashes($row['description']);
      $this->cover_picture                    = $row['cover_picture'] == "" ? 'NULL' : $row['cover_picture'];
      $this->picture_width                    = $row['picture_width'];
      $this->picture_height                   = $row['picture_height'];
      $this->thumb_width                      = $row['thumb_width'];
      $this->thumb_height                     = $row['thumb_height'];
      $this->folder                           = stripslashes($row['folder']);
      $this->date_creation                    = $row['date_creation'];
      $this->user_creation                    = $row['user_creation'];
    }
    
    // récupert les photos de l'album id demandé
    if ($get_pictures)
      $this->pictures = $this->get_pictures();
      
    return true;
  }
  
  /**
   * Sauvegarde l'album photo dans la base de données.
   *    
   * Met à jour le répertoire de l'album photo d'après sont nom
   *
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   * 
   * Si un album photo du même nom existe déjà, une exception est levée      
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */
  public function save(){
    // si l'id de l'album existe, c'est que l'album existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // vérifie qu'aucun album du même nom n'existe déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_album WHERE name = '%s'",
      mysql_real_escape_string($this->name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Un album avec le même nom existe déjà dans la base de données"));  

    // redéfinit le répertoire contenant les photos de l'album
    $this->change_folder("pictures/albums/" .decode($this->name));

    // débute la transaction
    mysql_query("START TRANSACTION");

    $sql = sprintf("INSERT INTO miki_album(name, state, cover_picture, picture_width, picture_height, thumb_width, thumb_height, folder, date_creation, user_creation) VALUES('%s', %d, %s, %d, %d, %d, %d, '%s', NOW(), %d)",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->cover_picture),
      mysql_real_escape_string($this->picture_width),
      mysql_real_escape_string($this->picture_height),
      mysql_real_escape_string($this->thumb_width),
      mysql_real_escape_string($this->thumb_height),
      mysql_real_escape_string($this->folder),
      mysql_real_escape_string($this->user_creation));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'ajout de l'album dans la base de données : ") ."<br />$sql<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    
    // ajoute le titre de l'album dans chaque langue
    if (is_array($this->title)){
      foreach($this->title as $key => $t){
        $sql = sprintf("INSERT INTO miki_album_title (id_album, language_code, title) VALUES(%d, '%s', '%s')",
          mysql_real_escape_string($this->id),
          mysql_real_escape_string($key),
          mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
        $result = mysql_query($sql);
        
        if (!$result){
          // annule les modifications
          mysql_query("ROLLBACK");

          throw new Exception(_("Erreur lors de l'ajout de l'album dans la base de données : $sql") ."<br />" .mysql_error());
        }
      }
    }
    
    // ajoute la description de l'album dans chaque langue
    if (is_array($this->description)){
      foreach($this->description as $key => $t){
        $sql = sprintf("INSERT INTO miki_album_description (id_album, language_code, description) VALUES(%d, '%s', '%s')",
          mysql_real_escape_string($this->id),
          mysql_real_escape_string($key),
          mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
        $result = mysql_query($sql);
        if (!$result){
          // annule les modifications
          mysql_query("ROLLBACK");

          throw new Exception(_("Erreur lors de l'ajout de l'album dans la base de données : $sql") ."<br />" .mysql_error());
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
   * Met à jour l'album photo dans la base de données.
   * 
   * Met à jour le répertoire de l'album photo d'après sont nom      
   *    
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si un album photo du même nom existe déjà, une exception est levée   
   * Si une erreur survient, une exception est levée   
   *    
   * @return boolean   
   */  
  public function update(){
    // si aucun id existe, c'est que l'album n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // vérifie qu'aucun album du même nom n'existe déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_album WHERE name = '%s' AND id != %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Un album avec le même nom existe déjà dans la base de données"));
      
    // redéfinit le répertoire contenant les photos de l'album
    $this->change_folder("pictures/albums/" .decode($this->name));
      
    // débute la transaction
    mysql_query("START TRANSACTION");
    
    $sql = sprintf("UPDATE miki_album SET name = '%s', state = %d, cover_picture = %s, picture_width = %d, picture_height = %d, thumb_width = %d, thumb_height = %d, folder = '%s' WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->cover_picture),
      mysql_real_escape_string($this->picture_width),
      mysql_real_escape_string($this->picture_height),
      mysql_real_escape_string($this->thumb_width),
      mysql_real_escape_string($this->thumb_height),
      mysql_real_escape_string($this->folder),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de l'album dans la base de données : ") ."<br />$sql<br />" .mysql_error());
    }
      
      
    // supprime le titre de l'album dans chaque langue
    $sql = sprintf("DELETE FROM miki_album_title WHERE id_album = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de l'album dans la base de données : ") ."<br />$sql<br />" .mysql_error());
    }
    
    // ajoute le titre de l'album dans chaque langue
    foreach($this->title as $key => $t){
      $sql = sprintf("INSERT INTO miki_album_title (id_album, language_code, title) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
      $result = mysql_query($sql);
      if (!$result){
        // annule les modifications
        mysql_query("ROLLBACK");
      
        throw new Exception(_("Erreur lors de la mise à jour de l'album dans la base de données : ") ."<br />$sql<br />" .mysql_error());
      }
    }
    
    
    // supprime la description de l'album dans chaque langue
    $sql = sprintf("DELETE FROM miki_album_description WHERE id_album = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de l'album dans la base de données : ") ."<br />$sql<br />" .mysql_error());
    }
    
    // ajoute la description de l'album dans chaque langue
    foreach($this->description as $key => $t){
      $sql = sprintf("INSERT INTO miki_album_description (id_album, language_code, description) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
      $result = mysql_query($sql);
      if (!$result){
        // annule les modifications
        mysql_query("ROLLBACK");
      
        throw new Exception(_("Erreur lors de la mise à jour de l'album dans la base de données : ") ."<br />$sql<br />" .mysql_error());
      }
    }
    
    // applique les modifications
    mysql_query("COMMIT");
    
    return true;
  }
  
  /**
   * Modifie le répertoire dans lequel seront stockées les photos de l'album
   * 
   * Si on affecte pour la première fois un répertoire à l'album, on créé le répertoire
   * Si l'album possède déjà un répertoire, on renomme le répertoire
   * 
   * Si le création ou la modification du répertoire échoue, une exception est levée.      
   * 
   * Modifie le répertoire de chaque image (de type Miki_album_picture) en cascade.       
   * 
   * @see Miki_album_picture
   * @access private      
   * @param string $new_folder Répertoire à affecter à l'album photo
   * @return boolean   
   */   
  private function change_folder($new_folder){
    // si le répertoire ne change pas on ne fait rien
    if ($this->folder == $new_folder)
      return true;
    
    // si le répertoire existe déjà, on ne fait rien et on retourne FALSE
    if (is_dir(URL_BASE .$new_folder))
      return false;
    
    // si on affecte pour la première fois un répertoire à l'album
    if ($this->folder == ""){
      // on créé le répertoire
      if (!mkdir(URL_BASE .$new_folder))
        throw new Exception(_("Erreur lors de la création du répertoire '" .URL_BASE ."$new_folder'"));
    }
    // sinon si l'album possède déjà un répertoire 
    else{
      // on renomme le répertoire
      if (!rename(URL_BASE .$this->folder, URL_BASE .$new_folder))
        throw new Exception(_("Erreur lors de la modification du répertoire '" .URL_BASE ."$new_folder'"));
    }
    
    // si tout est OK on affecte le nouveau répertoire à l'album
    $this->folder = $new_folder;
    
    // ainsi qu'à toutes les photos de l'album    
    $pics = $this->get_pictures();
    foreach($pics as $pic){
      $pic->folder = $this->folder;
      $pic->update();
    }
    
    return true;
  }
  
  /**
   * Recherche toutes les photos de l'album 
   * 
   * @return mixed Un tableau d'éléments Miki_album_picture représentant toutes les photos de l'album
   */         
  public function get_pictures($nb = "", $page = 1){
    $return = array();
    $sql = sprintf("SELECT * FROM miki_album_picture WHERE id_album = %d ORDER BY position ASC",
      mysql_real_escape_string($this->id));
    
    // si on effecute une pagination
    if ($nb != "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= sprintf(" LIMIT %d, %d",
                mysql_real_escape_string($start),
                mysql_real_escape_string($nb));
    }
      
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $item = new Miki_album_picture($row['id']);
      $return[] = $item;
    }
    return $return;
  }
  
  /** 
   * Recherche le nombre de photos de l'album
   * 
   * @return int le nombre de photos de l'album
   */           
  public function get_nb_pictures(){
    $return = array();
    $sql = sprintf("SELECT count(*) FROM miki_album_picture WHERE id_album = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /** 
   * Supprime un répertoire et tout son contenu
   *
   * @access private
   * @param string $dir le répertoire à supprimer   
   * @return boolean   
   */       
  private function deleteDirectory($dir) {
    if (!file_exists($dir)) return true;
    
    if (!is_dir($dir) || is_link($dir))
      return unlink($dir);
      
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        if (!$this->deleteDirectory($dir . "/" . $item)) {
            chmod($dir . "/" . $item, 0777);
            if (!$this->deleteDirectory($dir . "/" . $item)) return false;
        };
    }
    return rmdir($dir);
  }
  
  /**
   * Supprime l'image dont l'id est donné en paramètre
   * 
   * Si la photo ne fait pas partie de l'album, une exception est levée
   * 
   * @param int $pic_id id de l'image à supprimer
   * @return boolean   
   */             
  public function delete_picture($pic_id){
    $pic = new Miki_album_picture($pic_id);
    
    // vérifie que la photo fasse bien partie de l'album en cours
    if ($pic->id_album != $this->id){
      throw new Exception("La photo ne fait pas partie de l'album");
    }
    
    // supprime l'image de la base de données
    $sql = sprintf("DELETE FROM miki_album_picture WHERE id_album = %d AND id = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($pic->id));
    $result = mysql_query($sql);    
    
    // puis du système de fichier
    try{
      $pic->delete_picture();
    }catch(Exception $e){
      return false;
    }
    
    return true;
  }
  
  /**
   * Supprime l'album photo
   * 
   * @return boolean
   */         
  public function delete(){
    // supprime le répertoire qui contenait les photos de l'album (supprime également son contenu)
    $path = URL_BASE .$this->folder;
    if ($this->folder != ""){
      $this->deleteDirectory($path);
    }

    // puis supprime l'album de la base de données
    $sql = sprintf("DELETE FROM miki_album WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de l'album : ") ."<br />" .mysql_error());
    return true;
  }
  
  // définit la photo de couverture de l'album  
  public function set_cover_picture($id_picture){
    if (!is_numeric($id_picture))
      return false;
      
    // vérifie que l'image fasse bien partie de cet album
    $sql = sprintf("SELECT COUNT(*) FROM miki_album_picture WHERE id_album = %d AND id = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($id_picture));
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    
    if ($row[0] > 0){
      $this->cover_picture = $id_picture;
      return $this->update();
    }
    else
      return false;
  }
  
  /**
   * Retourne l'url de l'album photo en fonction de son titre.
   * 
   * Cette fonction peut être utilisée pour afficher un lien vers l'album photos dans une page web dans le cadre du référencement.
   * Le lien vers la page réelle est gérer via l'Url Rewriting    
   * 
   * @param int $page_no Numéro de la page à affiche
   *      
   * @return string L'url de l'album photos en fonction de son titre   
   */   
  public function get_url_simple($page_no = 1){
    $url_base = URL_BASE;
    
    if (isset($_SESSION['lang']) && Miki_language::exist($_SESSION['lang'])) {
    	$lang = $_SESSION['lang'];
    }
    else{
      $lang = Miki_language::get_main_code();
    }
    
    $url = 'photos/' .decode($this->title[$lang]) .'-' .$this->id ."?p=$page_no";
    
    return $url;
  }

  /**
   * Recherche tous les albums photo
   * 
   * @param boolean $all si true, on prend tous les albums, si false on ne prend que les albums dans l'état '1' (publié)
   * 
   * @static
   * @return mixed un tableau d'élément Miki_album représentant tous les albums photos trouvés   
   */             
  public static function get_all_albums($all = true){
    $return = array();
    $sql = "SELECT * FROM miki_album";
    
    // ne prend que les albums dans l'état 'publiés'
    if (!$all){
      $sql .= " WHERE state = 1";
    }
    
    $sql .= " ORDER BY date_creation ASC";
    
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $item = new Miki_album($row['id']);
      $return[] = $item;
    }
    return $return;
  }
}
?>