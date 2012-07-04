<?php
/**
 * Classe Miki_ad
 * @package Miki 
 */ 

/**
 * Inclut les fonctions d'importation et de traitement des images
 */ 
require_once("functions_pictures.php");

/**
 * Représentation d'une annonce
 * 
 * @package Miki  
 */ 
class Miki_ad{

  /**
   * Id de l'annonce
   *      
   * @var int
   * @access public   
   */    
  public $id;
  
  /**
   * Id de la personne ayant posté l'annonce (Miki_person)
   *      
   * @see Miki_person   
   * @var int
   * @access public   
   */    
  public $poster;
  
  /**
   * Type de l'annonce
   *      
   * @var string
   * @access public   
   */    
  public $ad_type;
  
  /**
   * Titre de l'annonce
   *      
   * @var string
   * @access public   
   */    
  public $title;
  
  /**
   * Description courte de l'annonce
   *      
   * @var string
   * @access public   
   */    
  public $short_description;
  
  /**
   * Description longue de l'annonce
   *      
   * @var string
   * @access public   
   */    
  public $long_description;
  
  /**
   * Région relative à l'annonce
   *      
   * @var string
   * @access public   
   */    
  public $region;
  
  /**
   * Localité relative à l'annonce
   *      
   * @var string
   * @access public   
   */    
  public $city;
  
  /**
   * Pays relatif à l'annonce
   *      
   * @var string
   * @access public   
   */    
  public $country;
  
  /**
   * Tags de l'annonce séparés par un espace, une virgule ou un point-virgul
   *
   * @var string
   * @access public   
   */    
  public $tags;
  
  /**
   * Type de réponse (p.ex. message privé, e-mail, sms, etc.)
   *      
   * @var int
   * @access public   
   */    
  public $send_type;
  
  /**
   * Adresse e-mail pour la réponse
   *      
   * @var int
   * @access public   
   */    
  public $email;
  
  /**
   * Tableau contenant les images liées à l'annonce
   *      
   * @var mixed
   * @access public   
   */      
  public $pictures;
  
  /**
   * Date à laquelle l'annonce a été postée (format yyyy-mm-dd hh:mm:ss)
   *      
   * @var string
   * @access public   
   */    
  public $date_posted;
  
  /**
   * Dossier dans lequel sont stockées les images (url relatif par rapport à la racine du site web)
   *      
   * @var int
   * @access private   
   */    
  private $picture_path = "pictures/ads/";
  
  
  
  
  
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge l'annonce dont l'id a été donné
   * 
   * @param int $id Id de l'annonce à charger (optionnel)
   */ 
  function __construct($id = ""){
    // charge l'annonce si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge une annonce depuis un id.
   *    
   * Si l'annonce n'existe pas, une exception est levée.
   *    
   * @param int $id id de l'annonce à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_ad WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("L'annonce demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id                = $row['id'];
    $this->poster            = $row['poster'];
    $this->ad_type           = $row['ad_type'];
    $this->short_description = $row['short_description'];
    $this->country           = $row['country'];
    $this->region            = $row['region'];
    $this->city              = $row['city'];
    $this->title             = $row['title'];
    $this->tags              = $row['tags'];
    $this->long_description  = $row['long_description'];
    $this->send_type         = $row['send_type'];
    $this->email             = $row['email'];
    $this->date_posted       = $row['date_posted'];
    
    $this->pictures          = explode("&&", $row['pictures']);
    
    // vérifie qu'il n'y ait pas d'image vide
    $tab_temp = array();
    foreach($this->pictures as $p){
      if ($p != "")
        $tab_temp[] = $p;
    }
    $this->pictures = $tab_temp;
    
    return true;
  }
  
  /**
   * Sauvegarde l'annonce dans la base de données.
   *    
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   *    
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */
  public function save(){      
    // si un l'id de l'annonce existe, c'est que l'annonce existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // concatène les images
    if (is_array($this->pictures))
      $pictures = implode("&&", $this->pictures);
    else
      $pictures = "";
      
    $sql = sprintf("INSERT INTO miki_ad (poster, ad_type, short_description, country, region, city, title, tags, long_description, send_type, email, pictures, date_posted) VALUES(%d, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, '%s', '%s', NOW())",
      mysql_real_escape_string($this->poster),
      mysql_real_escape_string($this->ad_type),
      mysql_real_escape_string($this->short_description),
      mysql_real_escape_string($this->country),
      mysql_real_escape_string($this->region),
      mysql_real_escape_string($this->city),
      mysql_real_escape_string($this->title),
      mysql_real_escape_string($this->tags),
      mysql_real_escape_string($this->long_description),
      mysql_real_escape_string($this->send_type),
      mysql_real_escape_string($this->email),
      mysql_real_escape_string($pictures));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion de l'annonce dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge l'annonce
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour l'annonce dans la base de données.
   *    
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si une erreur survient, une exception est levée   
   *    
   * @return boolean   
   */
  public function update(){      
    // si aucun id existe, c'est que l'événement n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // concatène les images
    if (is_array($this->pictures))
      $pictures = implode("&&", $this->pictures);
    else
      $pictures = "";
    
    $sql = sprintf("UPDATE miki_ad SET poster = %d, ad_type = '%s', title = '%s', short_description = '%s', country = '%s', region = '%s', city = '%s', title = '%s', tags = '%s', long_description = '%s', send_type = %d, email = '%s', pictures = '%s' WHERE id = %d",
      mysql_real_escape_string($this->poster),
      mysql_real_escape_string($this->ad_type),
      mysql_real_escape_string($this->title),
      mysql_real_escape_string($this->short_description),
      mysql_real_escape_string($this->country),
      mysql_real_escape_string($this->region),
      mysql_real_escape_string($this->city),
      mysql_real_escape_string($this->title),
      mysql_real_escape_string($this->tags),
      mysql_real_escape_string($this->long_description),
      mysql_real_escape_string($this->send_type),
      mysql_real_escape_string($this->email),
      mysql_real_escape_string($pictures),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour de l'annonce dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime l'annonce
   * 
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */     
  public function delete(){
    $sql = sprintf("DELETE FROM miki_ad WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de l'annonce : ") ."<br />" .mysql_error());
      
    try{
      // supprime toutes les images de l'annonce
      $this->delete_all_pictures();
    }
    catch(Exception $e){}
    
    return true;
  }
  
  /**
   * Ajoute une image à l'annonce
   * 
   * Ajoute une image à l'annonce, l'upload et créé une vignette
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   * @param string $nom_destination Nom de destination de l'image finale
   * @return boolean
   */     
  public function upload_picture($fichier, $nom_destination){
    
    // traite le nom de destination
    $nom_destination = decode($nom_destination);  
    $system = explode('.',strtolower($fichier['name']));
    $ext = $system[sizeof($system)-1];
    $nom_destination = $nom_destination ."." .$ext;  

    // le fichier doit être au format jpg ou png
  	if (!preg_match('/png/i',$ext) && !preg_match('/gif/i',$ext) && !preg_match('/jpg|jpeg/i',$ext)){
      throw new Exception(_("Le type de l'image n'est pas supporté. Les types supportés sont : JPEG, GIF et PNG : $nom_destination - " .$fichier['name']));
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
  	
  	$size = "";
  	$file = $fichier['tmp_name'];
  	if (!is_uploaded_file($file) || $fichier['size'] > 5 * 1024 * 1024)
  		throw new Exception(_("Veuillez uploader des images plus petites que 5Mb"));
  	if (!($size = @getimagesize($file)))
  		throw new Exception(_("Veuillez n'uploader que des images. Les autres fichiers ne sont pas supportés"));
  	if (!in_array($size[2], array(1, 2, 3) ) )
  		throw new Exception(_("Le type de l'image n'est pas supporté. Les types supportés sont : JPEG, GIF et PNG"));
  		
  	// pas d'erreur --> upload
  	if ((isset($fichier['tmp_name']))&&($fichier['error'] == UPLOAD_ERR_OK)) {
  		if (!move_uploaded_file($fichier['tmp_name'], $this->picture_path .$nom_destination))
        exit();
  	}
  	
  	// redimensionne l'image
  	createthumb($this->picture_path .$nom_destination, $this->picture_path ."thumb/" .$nom_destination, 200, 200, false);
  	
  	// ajoute l'image à l'annonce
  	$this->pictures[] = $nom_destination;
  	return true;
  }
  
  /**
   * Supprime une image liée à l'annonce
   * 
   * Supprime l'image passée en paramètre ($pic) de l'annonce. 
   * Si l'image n'est pas trouvée, aucune image n'est supprimée      
   * 
   * @param string $pic Le nom de l'image à supprimer
   * @return boolean         
   */   
  public function delete_picture($pic){
    $tab_temp = array();
    
    // parcourt toutes les images de l'annonce
    foreach($this->pictures as $p){
      // si c'est l'image à supprimer, on la supprime
      if ($p != "" && $p == $pic){
        // supprime l'image
        $path = $this->picture_path .$p;
        if (file_exists($path)){
          if (!unlink($path)){
            throw new Exception("Erreur pendant la suppression de l'image");
          }
        }
        
        // supprime la vignette
        $path = $this->picture_path ."thumb/" .$p;
        if (file_exists($path)){
          if (!unlink($path)){
            throw new Exception("Erreur pendant la suppression de la vignette");
          }
        }
      }
      // sinon on le garde
      else{
        $tab_temp[] = $p;
      }
    }
    $this->pictures = $tab_temp;
    $this->update();
    return true;
  }
  
  /**
   * Supprime toutes les images liées à l'article
   * 
   * @return boolean      
   */   
  public function delete_all_pictures(){
    foreach($this->pictures as $p){
      if ($p != ""){
        // supprime l'image
        $path = $this->picture_path .$p;
        if (file_exists($path)){
          if (!unlink($path)){
            throw new Exception("Erreur pendant la suppression de l'image");
          }
        }
        
        // supprime la vignette
        $path = $this->picture_path ."thumb/" .$p;
        if (file_exists($path)){
          if (!unlink($path)){
            throw new Exception("Erreur pendant la suppression de la vignette");
          }
        }
      }
    }
    $this->pictures = array();
    $this->update();
    return true;
  }
  
  /**
   * Recherche les annonces selon certains critères
   * 
   * @param string $type Le type de l'annonce
   * @param string $country Le pays relatif à l'annonce
   * @param string $region La région relative à l'annonce
   * @param string $region La localité relative à l'annonce
   * @param string $title Le titre de l'annonce
   * @param string $tags un tag représentant l'annonce                 
   * @static
   * @return mixed Un tableau d'éléments de type Miki_ad représentant toutes les annonces répondant aux critères donnés
   */   
  public static function search($type = "", $country = "", $region = "", $region = "", $title = "", $tags = ""){
    $return = array();
    $type             = mb_strtolower($type, "UTF-8");
    $country          = mb_strtolower($country, "UTF-8");
    $region           = mb_strtolower($region, "UTF-8");
    $city             = mb_strtolower($city, "UTF-8");
    $title            = mb_strtolower($title, "UTF-8");
    $tags             = mb_strtolower($tags, "UTF-8");
    
    $sql = "SELECT * FROM miki_ad";
    
    $search = "";
    if ($type != "")
      $search .= "LOWER(ad_type) LIKE '%$type%' AND  ";
    if ($country != "")
      $search .= "LOWER(country) LIKE '%$country%' AND  ";
    if ($region != "")
      $search .= "LOWER(region) LIKE '%$region%' AND  ";
    if ($city != "")
      $search .= "LOWER(city) LIKE '%$city%' AND  ";
    if ($title != "")
      $search .= "LOWER(title) LIKE '%$title%' AND  ";
    if ($tags != "")
      $search .= "LOWER(tags) LIKE '%$tags%' AND  ";
      
    if ($search != "")
      $search = " WHERE " .substr($search, 0, mb_strlen($search) - 5);
      
    $sql .= $search;
    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $item = new Miki_ad($row['id']);
      $return[] = $item;
    }
    return $return;
  }
  
  /**
   * Recherche les annonces selon un ou plusieurs mots-clés donnés
   * 
   * @param string $search Les mots-clés à rechercher
   * @static
   * @return mixed Un tableau d'éléments de type Miki_ad représentant toutes les annonces répondant aux critères donnés
   */  
  public static function search2($search = ""){
    $return = array();
    $search = mb_strtolower($search, "UTF-8");
    
    $sql = "SELECT * FROM miki_ad";
    
    if ($search != ""){
      $sql .= " WHERE LOWER(ad_type) LIKE '%$search%'";
      $sql .= " OR LOWER(country) LIKE '%$search%'";
      $sql .= " OR LOWER(region) LIKE '%$search%'";
      $sql .= " OR LOWER(city) LIKE '%$search%'";
      $sql .= " OR LOWER(title) LIKE '%$search%'";
      $sql .= " OR LOWER(tags) LIKE '%$search%'";
    }
    
    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $item = new Miki_ad($row['id']);
      $return[] = $item;
    }
    return $return;
  }

  /**
   * Recherche toutes les annonces
   * 
   * @static
   * @return mixed Un tableau d'éléments de type Miki_ad représentant toutes les annonces
   */
  public static function get_all_ads(){
    $return = array();
    $sql = "SELECT * FROM miki_ad ORDER BY id DESC";
    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $item = new Miki_ad($row['id']);
      $return[] = $item;
    }
    return $return;
  }
}
?>