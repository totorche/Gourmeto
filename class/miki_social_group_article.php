<?php
/**
 * Classe Miki_social_group_article
 * @package Miki
 */ 

/**
 * Inclut les fonctions d'importation et de traitement des images
 */ 
require_once("functions_pictures.php");

/**
 * Représentation d'article écrit dans un groupe de discussion (Miki_social_group).
 * 
 * Plusieurs fichiers peuvent être attachés à un article.    
 * 
 * @see Miki_social_group
 *  
 * @package Miki  
 */
class Miki_social_group_article{

  /**
   * Id de l'article
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Id du groupe dans lequel a été posté l'article.
   *      
   * @var int
   * @access public   
   */
  public $id_social_group;
  
  /**
   * Id du membre (Miki_person) ayant posté l'article.
   *      
   * @var int
   * @see Miki_person   
   * @access public   
   */
  public $poster;
  
  /**
   * Titre de l'article
   *      
   * @var string
   * @access public   
   */
  public $title;
  
  /**
   * Texte de l'article
   *      
   * @var string
   * @access public   
   */
  public $text;
  
  /**
   * Date à laquelle l'article a été posté.
   *      
   * @var string
   * @access public   
   */
  public $date;
  
  /**
   * Id de l'article
   *      
   * @var int
   * @access public   
   */
  public $follow;
  
  /**
   * Fichiers liés à l'article (Array)
   *      
   * @var mixed
   * @access public   
   */
  public $files;
  
  /**
   * Répertoire dans lequel sont stockés les fichiers liés à l'article.
   *      
   * @var string
   * @access private   
   */
  private $file_path = "pictures/social_groups_articles/";
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge l'article dont l'id a été donné
   * 
   * @param int $id Id de l'article à charger (optionnel)
   */
  function __construct($id = ""){
    // charge l'article si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un article depuis un id
   *    
   * Si l'article n'existe pas, une exception est levée.
   *    
   * @param int $id id de l'article à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_social_group_article WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("L'article demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id               = $row['id'];
    $this->id_social_group  = $row['id_social_group'];
    $this->poster           = $row['poster'];
    $this->title            = $row['title'];
    $this->text             = $row['text'];
    $this->date             = $row['date'];
    $this->follow           = $row['follow'];
    $this->files            = explode("&&", $row['files']);
    
    // vérifie qu'il n'y ait pas de fichier vide
    $tab_temp = array();
    foreach($this->files as $f){
      if ($f != "")
        $tab_temp[] = $f;
    }
    $this->files = $tab_temp;
    
    return true;
  }
  
  /**
   * Sauvegarde l'article dans la base de données.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){      
    // si un l'id du groupe existe, c'est que l'article existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
    
    // concatène les fichiers
    if (is_array($this->files))
      $files = implode("&&", $this->files);
    else
      $files = "";
      
    $sql = sprintf("INSERT INTO miki_social_group_article (id_social_group, poster, title, text, date, follow, files) VALUES(%d, %d, '%s', '%s', NOW(), %d, '%s')",
      mysql_real_escape_string($this->id_social_group),
      mysql_real_escape_string($this->poster),
      mysql_real_escape_string($this->title),
      mysql_real_escape_string($this->text),
      mysql_real_escape_string($this->follow),
      mysql_real_escape_string($files));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion de l'article dans la base de données : <br />$sql<br />") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la page
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour l'article dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){      
    // si aucun id existe, c'est que l'article n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // concatène les fichiers
    if (is_array($this->files))
      $files = implode("&&", $this->files);
    else
      $files = "";
    
    $sql = sprintf("UPDATE miki_social_group_article SET id_social_group = %d, poster = %d, title = '%s', text = '%s', follow = %d, files = '%s' WHERE id = %d",
      mysql_real_escape_string($this->id_social_group),
      mysql_real_escape_string($this->poster),
      mysql_real_escape_string($this->title),
      mysql_real_escape_string($this->text),
      mysql_real_escape_string($this->follow),
      mysql_real_escape_string($files),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour de l'article dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime l'article
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_social_group_article WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de l'article : ") ."<br />" .mysql_error());
      
    // supprime les fichiers liés à l'article
    $this->delete_all_files();
    
    return true;
  }
  
  /**
   * Recherche le texte de l'article avec un nombre de caractères limités
   * 
   * Si le texte est plus court que la partie demandée, le texte est retourné en entier.      
   *      
   * @param int $nb_char Nombre de caractères désirés
   * @param boolean $full_word Si true, le texte est coupé par mots entiers. Si false, les mots peuvent être coupé en plein milieu
   * 
   * @return string         
   */    
  public function get_text($nb_char, $full_word = true){
    if ($nb_char < mb_strlen($this->text)){
      if ($full_word === false)
        $stop = $nb_char;
      else{
        $stop = mb_strpos($this->text, ' ', $nb_char);
        if ($stop === false){
          $stop = mb_strlen($this->text);
        }
      }
      return mb_substr($this->text, 0, $stop);
    }
    else
      return $this->text;
  }
  
  /**
   * Recherche le nombre de commentaires de l'article
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
   * Récupert les commentaires de l'article
   *  
   * @return mixed Un tableau de 2 dimensions avec dans la 2è dimension : 'id' (id du commentaire), 'id_person' (id du posteur), 'comment' (commentaire), 'date' (date du commentaire)      
   */                                
  public function get_comments(){
    $sql = sprintf("SELECT id, id_person, comment, date FROM miki_social_group_article_comment WHERE id_article = %d",
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
   * Ajoute un commentaire à l'article
   *  
   * @param int $id_person Id du membre (Miki_person) ayant posté le commentaire
   * @param string $comment Commentaire    
   *
   * @return boolean   
   */  
  public function add_comment($id_person, $comment){
    $sql = sprintf("INSERT INTO miki_social_group_article_comment(id_article, id_person, comment, date) VALUES(%d, %d, '%s', NOW())",
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
   * Supprime un commentaire de l'article
   *  
   * @param int $id_comment Id du commentaire à supprimer
   *
   * @return boolean   
   */
  public function remove_comment($id_comment){
    $sql = sprintf("DELETE FROM miki_social_group_article_comment WHERE id = %d",
      mysql_real_escape_string($id_comment));
    $result = mysql_query($sql);
    
    if (!$result){
      throw new Exception(_("Erreur lors de la suppression du commentaire") ."<br />" .mysql_error());
    }
    
    return true;
  }
  
  /**
   * Détermine si l'article a des fichiers joints
   * 
   * @return boolean
   */        
  public function has_files(){
    return (is_array($this->files) && sizeof($this->files) > 0);
  }
  
  /**
   * Ajoute un fichier à l'article
   * 
   * Ajoute un fichier à l'article. S'il s'agit d'une image, on créé une vignette (maximum 100x100px).
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   *    
   * @return boolean
   */    
  public function upload_file($fichier){
    $system = explode('.',mb_strtolower($fichier['name'], 'UTF-8'));
    // récupert le nom du fichier ainsi que son extension
    $nom_destination = decode(implode("_", array_slice($system, 0, sizeof($system)-1)));
    $ext = $system[sizeof($system)-1];
    $nom_destination = $nom_destination ."-" .$this->id ."." .$ext;  

    // le fichier doit être au format jpg ou png
  	/*if (!preg_match('/png/i',$ext) && !preg_match('/gif/i',$ext) && !preg_match('/jpg|jpeg/i',$ext)){
      throw new Exception(_("Le type de l'image n'est pas supporté. Les types supportés sont : JPEG, GIF et PNG : $nom_destination - " .$fichier['name']));
    }*/
    
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

  	if (!is_uploaded_file($fichier['tmp_name']) || $fichier['size'] > 5 * 1024 * 1024)
  		throw new Exception(_("Veuillez uploader des images plus petites que 5Mb") ." - $nom_destination - " .print_r($fichier) ." - " .$fichier['size']);
  		
  	// pas d'erreur --> upload
  	if ((isset($fichier['tmp_name']))&&($fichier['error'] == UPLOAD_ERR_OK)) {
  		if (!move_uploaded_file($fichier['tmp_name'], $this->file_path .$nom_destination))
        exit();
  	}
  	
  	// s'il s'agisse d'une image, on créé une vignette
    if (preg_match('/jpg|jpeg/i',$system[sizeof($system)-1]) ||
        preg_match('/png/i',$system[sizeof($system)-1]) ||
        preg_match('/gif/i',$system[sizeof($system)-1])){
      
      createthumb($this->file_path .$nom_destination, $this->file_path ."thumb/" .$nom_destination, 100, 100, false);
    }
  	
  	// puis ajoute le logo
  	$this->files[] = $nom_destination;
  	return true;
  }
  
  /**
   * Supprime un fichier lié à l'article
   * 
   * @param string $file nom du fichier à supprimer 
   * 
   * @return boolean        
   */   
  public function delete_file($file){
    $tab_temp = array();
    
    // parcourt tous les fichiers joints à l'article
    foreach($this->files as $f){
      // si c'est le fichier à supprimer, on le supprime
      if ($f != "" && $f == $file){
        $path = $this->file_path .$f;
        if (file_exists($path)){
          if (!unlink($path)){
            throw new Exception("Erreur pendant la suppression du fichier");
          }
        }
      }
      // sinon on le garde
      else{
        $tab_temp[] = $f;
      }
    }
    $this->files = $tab_temp;
    $this->update();
    return true;
  }
  
  /**
   * Supprime tous les fichiers liés à l'article
   * 
   * @return boolean      
   */   
  public function delete_all_files(){
    foreach($this->files as $f){
      if ($f != ""){
        $path = $this->file_path .$f;
        if (file_exists($path)){
          if (!unlink($path)){
            throw new Exception("Erreur pendant la suppression du fichier");
          }
        }
      }
    }
    $this->files = array();
    $this->update();
    return true;
  }
  
  /**
   * Détermine si la personne passée en paramètre a la permission de lire l'article 
   * d'après la configuration du groupe auquel appartient l'article
   * 
   * @param int $id_person id de la personne dont les droits de lecture doivent être vérifiés
   *    
   * @return boolean         
   */     
  public function can_read($id_person){
    $group = new Miki_social_group($this->id_social_group);
    if ($group->read_rule == 1 || $group->is_subscribed($id_person))
      return true;
    else
      return false;
  }
  
  /**
   * Recherche des articles selon différents critères
   * 
   * @param string $search Critères de recherche à rechercher dans le titre de l'article ainsi que dans le prénom et le nom du posteur.
   * @param int $social_group_id Si != "", on ne recherche que les articles du groupe donné
   * @param int $person_id Si != "", on ne recherhce que les articles écrits par la personne donnée
   *      
   * @static       
   * @return mixed Un tableau d'éléments de type Miki_social_group_article représentant les articles récupérés.
   */  
  public static function search($search = "", $social_group_id = "", $person_id = ""){
    $return = array();
    
    if ($search != ""){
      $search = mb_strtolower($search, 'UTF-8');
      
      $sql = "SELECT miki_social_group_article.id FROM 
              miki_social_group_article, miki_person WHERE 
              miki_person.id = miki_social_group_article.poster AND  
              (LOWER(miki_social_group_article.title) LIKE '%$search%' or
               LOWER(miki_person.firstname) LIKE '%$search%' OR 
               LOWER(miki_person.lastname) LIKE '%$search%')";
               
      if ($social_group_id != "" && is_numeric($social_group_id))
        $sql .= " AND miki_social_group_article.id_social_group = $social_group_id";

      if ($person_id != "" && is_numeric($person_id))
        $sql .= " AND miki_social_group_article.poster = $person_id";

      $sql .= " ORDER BY date desc";
    }
    elseif ($social_group_id != "" && is_numeric($social_group_id)){
      $sql = "SELECT id FROM miki_social_group_article WHERE id_social_group = $social_group_id";
      
      if ($person_id != "" && is_numeric($person_id))
        $sql .= " AND  poster = $person_id";
        
      $sql .= " ORDER BY date desc";
    }
    elseif ($person_id != "" && is_numeric($person_id)){
      $sql = "SELECT id FROM miki_social_group_article WHERE poster = $person_id";
    }
    else
      $sql = "SELECT id FROM miki_social_group_article ORDER BY date asc"; 

    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_social_group_article($row[0]);
    }
    return $return;
  }


  /**
   * Recherche tous les articles
   *
   * @param int $person_id Si != "", on ne recherhce que les articles écrits par la personne donnée
   * @param int $social_group_id Si != "", on ne recherche que les articles du groupe donné
   *   
   * @static
   * @return mixed Un tableau d'éléments de type Miki_social_group_article représentant les articles récupérés.         
   */
  public static function get_all_articles($person_id = "", $social_group_id = ""){
    $return = array();
    
    $sql = "SELECT id FROM miki_social_group_article WHERE true";
    
    if ($person_id != "" && is_numeric($person_id)){
      $sql .= sprintf(" AND poster = %d",
        mysql_real_escape_string($person_id));
    }
    
    if ($social_group_id != "" && is_numeric($social_group_id)){
      $sql .= sprintf(" AND id_social_group = %d",
        mysql_real_escape_string($social_group_id));
    }
    
    $sql .= " ORDER BY date DESC";
    
    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_social_group_article($row[0]);
    }
    return $return;
  }
}
?>