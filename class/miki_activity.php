<?php
/**
 * Classe Miki_activity
 * @package Miki
 */ 

/**
 * Inclut les fonctions d'importation et de traitement des images
 */ 
require_once("functions_pictures.php");

/**
 * Représentation d'une activité disponible à un endroit donné.
 * 
 * @package Miki  
 */ 
class Miki_activity{

  /**
   * Id de l'activité
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Titre de l'activité. Tableau dont la clé représente le code de la langue
   *      
   * @var mixed
   * @access public   
   */
  public $title;
  
  /**
   * Description de l'activité. Tableau dont la clé représente le code de la langue
   *      
   * @var mixed
   * @access public   
   */
  public $description;
  
  /**
   * Ville où se situe l'activité
   *      
   * @var string
   * @access public   
   */
  public $city;
  
  /**
   * Région où se situe l'activité
   *      
   * @var string
   * @access public   
   */
  public $region;

  /**
   * Pays où se situe l'activité
   *      
   * @var string
   * @access public   
   */
  public $country;
  
  /**
   * URL de l'activité
   *   
   * @var string
   * @access public   
   */
  public $web;
  
  /**
   * Tableau contenant les photos de l'activité
   *       
   * @var mixed
   * @access public   
   */
  public $pictures;
  
  /**
   * Répertoire où sont situées les images de l'activité
   *      
   * @var string
   * @access private   
   */
  private $picture_path = "../pictures/activities/";
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge l'activité dont l'id a été donné
   * 
   * @param int $id Id de l'activité à charger (optionnel)
   */
  function __construct($id = ""){
    // charge l'activité si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge une activité depuis un id
   *    
   * Si l'activité n'existe pas, une exception est levée.
   *    
   * @param int $id id de l'activité à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT ma.*, temp.title title, temp.description description, temp.language_code code
                    FROM miki_activity ma
     
                    LEFT OUTER JOIN (SELECT mat1.title, mad1.description, mat1.language_code, mat1.id_activity
                    FROM miki_activity_title mat1,
                         miki_activity_description mad1
                    WHERE mat1.id_activity = mad1.id_activity
                      AND mat1.language_code = mad1.language_code) temp ON temp.id_activity = ma.id
     
                    WHERE ma.id = %d",
      mysql_real_escape_string($id));

    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql");
    
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("L'activité demandé n'existe pas"));
    while ($row = mysql_fetch_array($result)){
      $this->id                               = $row['id'];
      $this->title[$row['code']]              = stripslashes($row['title']);
      $this->description[$row['code']]        = stripslashes($row['description']);
      $this->city                             = stripslashes($row['city']);
      $this->region                           = stripslashes($row['region']);
      $this->country                          = stripslashes($row['country']);
      $this->web                              = stripslashes($row['web']);
      $this->pictures                         = explode("&&", $row['pictures']);
    }
    
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
   * Sauvegarde l'activité dans la base de données.
   *    
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   * 
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */
  public function save(){
    // si l'id de l'activité existe, c'est que l'activité existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // concatène les images
    if (is_array($this->pictures))
      $pictures = implode("&&", $this->pictures);
    else
      $pictures = "";
      
    // débute la transaction
    mysql_query("START TRANSACTION");

    $sql = sprintf("INSERT INTO miki_activity (city, region, country, web, pictures) VALUES('%s', '%s', '%s', '%s', '%s')",
      mysql_real_escape_string($this->city),
      mysql_real_escape_string($this->region),
      mysql_real_escape_string($this->country),
      mysql_real_escape_string($this->web),
      mysql_real_escape_string($pictures));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'ajout de l'activité dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    
    // ajoute le titre de l'activité dans chaque langue
    if (is_array($this->title)){
      foreach($this->title as $key => $t){
        $sql = sprintf("INSERT INTO miki_activity_title (id_activity, language_code, title) VALUES(%d, '%s', '%s')",
          mysql_real_escape_string($this->id),
          mysql_real_escape_string($key),
          mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
        $result = mysql_query($sql);
        
        if (!$result){
          // annule les modifications
          mysql_query("ROLLBACK");

          throw new Exception(_("Erreur lors de l'ajout de l'activité dans la base de données :") ."<br />" .mysql_error());
        }
      }
    }
    
    // ajoute la description de l'activité dans chaque langue
    if (is_array($this->description)){
      foreach($this->description as $key => $t){
        $sql = sprintf("INSERT INTO miki_activity_description (id_activity, language_code, description) VALUES(%d, '%s', '%s')",
          mysql_real_escape_string($this->id),
          mysql_real_escape_string($key),
          mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
        $result = mysql_query($sql);
        if (!$result){
          // annule les modifications
          mysql_query("ROLLBACK");

          throw new Exception(_("Erreur lors de l'ajout de l'activité dans la base de données :") ."<br />" .mysql_error());
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
   * Met à jour l'activité dans la base de données.
   * 
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si une erreur survient, une exception est levée   
   *    
   * @return boolean   
   */  
  public function update(){
    // si aucun id existe, c'est que l'activité n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // concatène les images
    if (is_array($this->pictures))
      $pictures = implode("&&", $this->pictures);
    else
      $pictures = "";
      
    // débute la transaction
    mysql_query("START TRANSACTION");
    
    $sql = sprintf("UPDATE miki_activity SET city = '%s', region = '%s', country = '%s', web = '%s', pictures = '%s' WHERE id = %d",
      mysql_real_escape_string($this->city),
      mysql_real_escape_string($this->region),
      mysql_real_escape_string($this->country),
      mysql_real_escape_string($this->web),
      mysql_real_escape_string($pictures),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de l'activité dans la base de données : ") ."<br />" .mysql_error());
    }
      
      
    // supprime le titre de l'activité dans chaque langue
    $sql = sprintf("DELETE FROM miki_activity_title WHERE id_activity = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de l'activité dans la base de données : ") ."<br />" .mysql_error());
    }
    
    // ajoute le titre de l'activité dans chaque langue
    foreach($this->title as $key => $t){
      $sql = sprintf("INSERT INTO miki_activity_title (id_activity, language_code, title) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
      $result = mysql_query($sql);
      if (!$result){
        // annule les modifications
        mysql_query("ROLLBACK");
      
        throw new Exception(_("Erreur lors de la mise à jour de l'activité dans la base de données : ") ."<br />" .mysql_error());
      }
    }
    
    
    // supprime la description de l'activité dans chaque langue
    $sql = sprintf("DELETE FROM miki_activity_description WHERE id_activity = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de l'activité dans la base de données : ") ."<br />" .mysql_error());
    }
    
    // ajoute la description de l'activité dans chaque langue
    foreach($this->description as $key => $t){
      $sql = sprintf("INSERT INTO miki_activity_description (id_activity, language_code, description) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
      $result = mysql_query($sql);
      if (!$result){
        // annule les modifications
        mysql_query("ROLLBACK");
      
        throw new Exception(_("Erreur lors de la mise à jour de l'activité dans la base de données : ") ."<br />" .mysql_error());
      }
    }
    
    // applique les modifications
    mysql_query("COMMIT");
    
    return true;
  }
  
  /**
   * Supprime l'activité
   * 
   * @return boolean
   */         
  public function delete(){
    // supprime l'activité de la base de données
    $sql = sprintf("DELETE FROM miki_activity WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de l'activité : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Retourne une partie de la description de l'activité
   * 
   * Si la description de l'activité est plus courte que la partie demandée, la description de l'activité est retournée en entier.      
   * 
   * @param string $lang Langue dans laquelle on veut récupérer la description de l'activité   
   * @param int $nb_char Nombre de caractères désirés
   * @param boolean $full_word Si true, le texte est coupé par mots entiers. Si false, les mots peuvent être coupé en plein milieu      
   * 
   * @return string
   */    
  public function get_description($lang, $nb_char, $full_word = true){
    if ($nb_char < mb_strlen($this->description[$lang])){
      if ($full_word === false)
        $stop = $nb_char;
      else{
        $stop = mb_strpos($this->description[$lang], ' ', $nb_char);
        if ($stop === false){
          $stop = mb_strlen($this->description[$lang]);
        }
      }
      return mb_substr($this->description[$lang], 0, $stop) ."...";
    }
    else
      return $this->description[$lang];
  }
  
  /**
   * Ajoute une image à l'activité
   * 
   * Ajoute une image à l'activité la redimensionne (maximum 1000x1000px) et créé une miniature (maximum 100x100px)
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   * @return boolean
   */
  public function upload_picture($fichier){  	
    
    // traite le nom de destination (correspond au titre de l'activité dans la langue principale)
    $nom_destination = decode($this->title[Miki_language::get_main_code()]);  
    $system = explode('.',mb_strtolower($fichier['name'], 'UTF-8'));
    $ext = $system[sizeof($system)-1];
    
    // donne un nom à l'image qui n'est pas encore utilisé
    $nom_temp = $nom_destination ."." .$ext;
    $x = 1;
    while(file_exists($this->picture_path .$nom_temp)){
      $nom_temp = $nom_destination ."$x.$ext";
      $x++;
    }
    $nom_destination = $nom_temp;

    // le fichier doit être au format jpg, gif ou png
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
  	if (!in_array($size[2], array(1, 2, 3) ) )
  		throw new Exception(_("Le type de l'image n'est pas supporté. Les types supportés sont : JPEG, GIF et PNG"));
  		
  	// pas d'erreur --> upload
  	if ((isset($fichier['tmp_name']))&&($fichier['error'] == UPLOAD_ERR_OK)) {
  		if (!move_uploaded_file($fichier['tmp_name'], $this->picture_path .$nom_destination))
        exit();
  	}
  	
  	// redimensionne l'image
  	createthumb($this->picture_path .$nom_destination, "./" .$nom_destination, 1000, 1000, true);
  	
    // puis créé une vignette
  	createthumb($this->picture_path .$nom_destination, $this->picture_path ."thumb/" .$nom_destination, 100, 100, false);
  	
  	$this->pictures[] = $nom_destination;
  	return true;
  }
  
  /**
   * Supprime la photo dont le nom est donné en paramètre
   * 
   * Supprime la photo dont le nom est donné en paramètre ainsi que le fichier représentant la photo
   * 
   * Si une erreur survient, une exception est levée            
   * 
   * @param string $name Le nom de la photo à supprimer
   * 
   * @return boolean            
   */   
  public function delete_picture($name){
    $pics_temp = array();
    foreach($this->pictures as $p){
      if ($p === $name){
        $path = $this->picture_path .$p;
        if (file_exists($path)){
          if (!unlink($path)){
            throw new Exception("Erreur pendant la suppression de la photo");
          }
        }
        
        // efface la miniature
        $path = $this->picture_path ."thumb/" .$p;
        if (file_exists($path)){
          if (!unlink($path)){
            throw new Exception("Erreur pendant la suppression de la photo");
          }
        }
      }
      else{
        $pics_temp[] = $p;
      }
    }
    $this->pictures = $pics_temp;
    $this->update();
    return true;
  }
  
  /**
   * Supprime toutes les photos
   * 
   * Supprime toutes les photos ainsi que le fichier représentant la photo
   * 
   * Si une erreur survient, une exception est levée            
   * 
   * @return boolean            
   */   
  public function delete_all_pictures(){
    foreach($this->pictures as $p){
      $path = $this->picture_path .$p;
      if (file_exists($path)){
        if (!unlink($path)){
          throw new Exception("Erreur pendant la suppression de la photo");
        }
      }
      
      // efface la miniature
      $path = $this->picture_path ."thumb/" .$p;
      if (file_exists($path)){
        if (!unlink($path)){
          throw new Exception("Erreur pendant la suppression de la photo");
        }
      }
    }
    $this->pictures = array();
    $this->update();
    return true;
  }
  
  /**
   * Recherche le nombre d'activités total
   * 
   * @static   
   * @return int      
   */     
  public static function get_nb_activities(){
    $sql = "SELECT COUNT(*) FROM miki_activity";
    
    $result = mysql_query($sql);
    
    $row = mysql_fetch_array($result);
    return $row[0];
  }

  /**
   * Recherche toutes les activités selon certains critères
   * 
   * @param string $search Critère de recherche qui sera appliqué sur le nom (dans toutes les langues), le pays et la ville
   * @param string $country Recherche seulement les activités du pays donné
   * @param string $city Recherche seulement les activités de la ville donnée
   * @param string $region Recherche seulement les activités de la région donnée   
   * @param string $order Par quel champ les activités trouvées seront triées (city, country). Si vide, on tri selon l'id.
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * @param int $nb nombre d'activités à retourner par page. Si = "" on retourne toutes les activités
   * @param int $page numéro de la page à retourner     
   * 
   * @static
   * @return mixed un tableau d'élément Miki_activity représentant tous les activités trouvées   
   */             
  public static function search($search = "", $country = "", $city = "", $region = "", $order = "", $order_type = "asc", $nb = "", $page = 1){
    $return = array();
    $sql = "SELECT DISTINCT(ma.id) 
            FROM miki_activity ma, miki_activity_title mat
            WHERE mat.id_activity = ma.id";
    
    // Applique les critères de recherche
    if ($search != ""){
      $search = mb_strtolower($search, 'UTF-8');
      $sql .= sprintf(" AND (LOWER(mat.title) LIKE '%%%s%%' OR 
                            LOWER(ma.country) LIKE '%%%s%%' OR
                            LOWER(ma.region) LIKE '%%%s%%' OR
                            LOWER(ma.city) LIKE '%%%s%%')",
                mysql_real_escape_string($search),
                mysql_real_escape_string($search),
                mysql_real_escape_string($search),
                mysql_real_escape_string($search));
    }
    
    // Recherche selon le pays
    if ($country != ""){
      $country = mb_strtolower($country, 'UTF-8');
      $sql .= sprintf(" AND LOWER(ma.country) LIKE '%%%s%%'",
                mysql_real_escape_string($country));
    }
    
    // Recherche selon la ville
    if ($city != ""){
      $city = mb_strtolower($city, 'UTF-8');
      $sql .= sprintf(" AND LOWER(ma.city) LIKE '%%%s%%'",
                mysql_real_escape_string($city));
    }
    
    // Recherche selon la région
    if ($region != ""){
      $region = mb_strtolower($region, 'UTF-8');
      $sql .= sprintf(" AND LOWER(ma.region) LIKE '%%%s%%'",
                mysql_real_escape_string($region));
    }
    
    if ($order == "city")
      $sql .= " ORDER BY LCASE(ma.city) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "region")
      $sql .= " ORDER BY LCASE(ma.region) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "country")
      $sql .= " ORDER BY LCASE(ma.country) COLLATE utf8_general_ci " .$order_type;
    else
      $sql .= " ORDER BY id " .$order_type;
      
    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }

    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_activity($row['id']);
    }
    return $return;
  }
  
  /**
   * Recherche toutes les activités
   * 
   * @param string $order Par quel champ les activités trouvées seront triées (city, country). Si vide, on tri selon leur id.
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * @param int $nb Nombre d'activités à retourner par page. Si = "" on retourne toutes les activités
   * @param int $page Numéro de la page à retourner     
   * 
   * @static
   * @return mixed un tableau d'élément Miki_activity représentant toutes les activités trouvées   
   */             
  public static function get_all_activities($order = "", $order_type = "asc", $nb = "", $page = 1){
    $return = array();
    $sql = "SELECT * FROM miki_activity";
    
    if ($order == "city")
      $sql .= " ORDER BY LCASE(city) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "region")
      $sql .= " ORDER BY LCASE(region) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "country")
      $sql .= " ORDER BY LCASE(country) COLLATE utf8_general_ci " .$order_type;
    else
      $sql .= " ORDER BY id " .$order_type;
      
    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }
    
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_activity($row['id']);
    }
    return $return;
  }
}
?>