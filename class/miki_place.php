<?php
/**
 * Classe Miki_place
 * @package Miki
 */ 

/**
 * Inclut les fonctions d'importation et de traitement des images
 */ 
require_once("functions_pictures.php");

/**
 * Représentation d'une bonne adresse
 * 
 * @package Miki  
 */ 
class Miki_place{

  /**
   * Id de la bonne adresse
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Etat de la bonne adresse (0 = non-publié, 1 = publié)
   * 
   * @var int
   * @access public         
   */     
   public $state;
  
  /**
   * Titre de la bonne adresse. Tableau dont la clé représente le code de la langue
   *      
   * @var mixed
   * @access public   
   */
  public $title;
  
  /**
   * Description de la bonne adresse. Tableau dont la clé représente le code de la langue
   *      
   * @var mixed
   * @access public   
   */
  public $description;
  
  /**
   * Adresse où se situe la bonne adresse
   *      
   * @var string
   * @access public   
   */
  public $address;
  
  /**
   * Code postal où se situe la bonne adresse
   *      
   * @var int
   * @access public   
   */
  public $npa;
  
  /**
   * Ville où se situe la bonne adresse
   *      
   * @var string
   * @access public   
   */
  public $city;

  /**
   * Pays où se situe la bonne adresse
   *      
   * @var string
   * @access public   
   */
  public $country;
  
  /**
   * Numéro de téléphone de la bonne adresse
   *      
   * @var string
   * @access public   
   */
  public $tel;
  
  /**
   * Numéro de fax de la bonne adresse
   *      
   * @var string
   * @access public   
   */
  public $fax;
  
  /**
   * Adresse e-mail de la bonne adresse
   *      
   * @var string
   * @access public   
   */
  public $email;
  
  /**
   * URL de la bonne adresse
   *   
   * @var string
   * @access public   
   */
  public $web;
  
  /**
   * Tableau contenant les photos de la bonne adresse
   *       
   * @var mixed
   * @access public   
   */
  public $pictures;
  
  /**
   * Catégorie de la bonne adresse
   *       
   * @var string
   * @access public   
   */
  public $category;
  
  /**
   * Date de création de la bonne adresse (format yyyy-mm-dd hh:mm:ss)
   *      
   * @var string
   * @access public   
   */
  public $date_created;
  
  /**
   * Répertoire où sont situées les images de la bonne adresse
   *      
   * @var string
   * @access public   
   */
  public $picture_path = "pictures/places/";
  
  /**
   * Position de la bonne adresse
   *      
   * @var int
   * @access public   
   */
  public $position;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge la bonne adresse dont l'id a été donné
   * 
   * @param int $id Id de la bonne adresse à charger (optionnel)
   */
  function __construct($id = ""){
    // charge la bonne adresse si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge une bonne adresse depuis un id
   *    
   * Si la bonne adresse n'existe pas, une exception est levée.
   *    
   * @param int $id id de la bonne adresse à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT mp.*, temp.title title, temp.description description, temp.language_code code
                    FROM miki_place mp
     
                    LEFT OUTER JOIN (SELECT mpt1.title, mpd1.description, mpt1.language_code, mpt1.id_place
                    FROM miki_place_title mpt1,
                         miki_place_description mpd1
                    WHERE mpt1.id_place = mpd1.id_place
                      AND mpt1.language_code = mpd1.language_code) temp ON temp.id_place = mp.id
     
                    WHERE mp.id = %d",
      mysql_real_escape_string($id));

    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql : <br /><br />$sql<br /><br />");
    
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La bonne adresse demandée n'existe pas"));
    while ($row = mysql_fetch_array($result)){
      $this->id                               = $row['id'];
      $this->state                            = $row['state'];
      $this->title[$row['code']]              = stripslashes($row['title']);
      $this->description[$row['code']]        = stripslashes($row['description']);
      $this->address                          = stripslashes($row['address']);
      $this->npa                              = $row['npa'];
      $this->city                             = stripslashes($row['city']);
      $this->country                          = stripslashes($row['country']);
      $this->tel                              = stripslashes($row['tel']);
      $this->fax                              = stripslashes($row['fax']);
      $this->email                            = stripslashes($row['email']);
      $this->web                              = stripslashes($row['web']);
      $this->category                         = stripslashes($row['category']);
      $this->date_created                     = $row['date_created'];
      $this->pictures                         = explode("&&", $row['pictures']);
      $this->position                         = $row['position'];
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
   * Sauvegarde la bonne adresse dans la base de données.
   *    
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   * 
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */
  public function save(){
    // si l'id de la bonne adresse existe, c'est que la bonne adresse existe déjà dans la bdd. 
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

    $sql = sprintf("INSERT INTO miki_place
    (state, address, npa, city, country, tel, fax, email, web, pictures, category, date_created, position) VALUES(%d, '%s', %d, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', NOW(), %d)",
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->address),
      mysql_real_escape_string($this->npa),
      mysql_real_escape_string($this->city),
      mysql_real_escape_string($this->country),
      mysql_real_escape_string($this->tel),
      mysql_real_escape_string($this->fax),
      mysql_real_escape_string($this->email),
      mysql_real_escape_string($this->web),
      mysql_real_escape_string($pictures),
      mysql_real_escape_string($this->category),
      mysql_real_escape_string($this->position));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'ajout de la bonne adresse dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    
    // ajoute le titre de la bonne adresse dans chaque langue
    if (is_array($this->title)){
      foreach($this->title as $key => $t){
        $sql = sprintf("INSERT INTO miki_place_title (id_place, language_code, title) VALUES(%d, '%s', '%s')",
          mysql_real_escape_string($this->id),
          mysql_real_escape_string($key),
          mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
        $result = mysql_query($sql);
        
        if (!$result){
          // annule les modifications
          mysql_query("ROLLBACK");

          throw new Exception(_("Erreur lors de l'ajout de la bonne adresse dans la base de données :") ."<br />" .mysql_error());
        }
      }
    }
    
    // ajoute la description de la bonne adresse dans chaque langue
    if (is_array($this->description)){
      foreach($this->description as $key => $t){
        $sql = sprintf("INSERT INTO miki_place_description (id_place, language_code, description) VALUES(%d, '%s', '%s')",
          mysql_real_escape_string($this->id),
          mysql_real_escape_string($key),
          mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
        $result = mysql_query($sql);
        if (!$result){
          // annule les modifications
          mysql_query("ROLLBACK");

          throw new Exception(_("Erreur lors de l'ajout de la bonne adresse dans la base de données :") ."<br />" .mysql_error());
        }
      }
    }
    
    // applique les modifications
    mysql_query("COMMIT");
    
    // recharge la bonne adresse
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour la bonne adresse dans la base de données.
   * 
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si une erreur survient, une exception est levée   
   *    
   * @return boolean   
   */  
  public function update(){
    // si aucun id existe, c'est que la bonne adresse n'existe pas encore dans la bdd. 
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
    
    $sql = sprintf("UPDATE miki_place SET state = %d, address = '%s', npa = %d, city = '%s', country = '%s', tel = '%s', fax = '%s', email = '%s', web = '%s', pictures = '%s', category = '%s', position = %d WHERE id = %d",
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->address),
      mysql_real_escape_string($this->npa),
      mysql_real_escape_string($this->city),
      mysql_real_escape_string($this->country),
      mysql_real_escape_string($this->tel),
      mysql_real_escape_string($this->fax),
      mysql_real_escape_string($this->email),
      mysql_real_escape_string($this->web),
      mysql_real_escape_string($pictures),
      mysql_real_escape_string($this->category),
      mysql_real_escape_string($this->position),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de la bonne adresse dans la base de données : ") ."<br />" .mysql_error());
    }
      
      
    // supprime le titre de la bonne adresse dans chaque langue
    $sql = sprintf("DELETE FROM miki_place_title WHERE id_place = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de la bonne adresse dans la base de données : ") ."<br />" .mysql_error());
    }
    
    // ajoute le titre de la bonne adresse dans chaque langue
    foreach($this->title as $key => $t){
      $sql = sprintf("INSERT INTO miki_place_title (id_place, language_code, title) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
      $result = mysql_query($sql);
      if (!$result){
        // annule les modifications
        mysql_query("ROLLBACK");
      
        throw new Exception(_("Erreur lors de la mise à jour de la bonne adresse dans la base de données : ") ."<br />" .mysql_error());
      }
    }
    
    
    // supprime la description de la bonne adresse dans chaque langue
    $sql = sprintf("DELETE FROM miki_place_description WHERE id_place = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de la bonne adresse dans la base de données : ") ."<br />" .mysql_error());
    }
    
    // ajoute la description de la bonne adresse dans chaque langue
    foreach($this->description as $key => $t){
      $sql = sprintf("INSERT INTO miki_place_description (id_place, language_code, description) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
      $result = mysql_query($sql);
      if (!$result){
        // annule les modifications
        mysql_query("ROLLBACK");
      
        throw new Exception(_("Erreur lors de la mise à jour de la bonne adresse dans la base de données : ") ."<br />" .mysql_error());
      }
    }
    
    // applique les modifications
    mysql_query("COMMIT");
    
    return true;
  }
  
  /**
   * Supprime la bonne adresse
   * 
   * Supprime également les images de la bonne adresse.      
   * 
   * Si une erreur survient, une exception est levée       
   * 
   * @return boolean
   */         
  public function delete(){
    // Supprime toutes les images de la bonne adresse
    $this->delete_all_pictures();
    
    // supprime la bonne adresse de la base de données
    $sql = sprintf("DELETE FROM miki_place WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la bonne adresse : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Retourne une partie de la description de la bonne adresse
   * 
   * Si la description de la bonne adresse est plus courte que la partie demandée, la description de la bonne adresse est retournée en entier.      
   * 
   * @param string $lang Langue dans laquelle on veut récupérer la description de la bonne adresse   
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
   * Change l'état de la bonne adresse. Si elle était publié, on la dépublie et inversément.
   * 
   * Si une erreur survient, une exception est levée
   * 
   * @param int $state Si = "", l'état est simplement inversé. Sinon, l'état affecté sera l'état correspondant à $state              
   * 
   * @return boolean
   */         
  public function change_state($state = ""){
    if (is_numeric($state))
      $this->state = $state;
    else
      $this->state = ($this->state + 1) % 2;
      
    // puis supprime la bonne adresse de la base de données
    $sql = sprintf("UPDATE miki_place SET state = %d WHERE id = %d",
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant le changement de l'état de la bonne adresse : ") ."<br />" .mysql_error());
    return true;
  }
  

  /**
   * Ajoute une image à la bonne adresse
   * 
   * Ajoute une image à la bonne adresse la redimensionne (maximum 1000x1000px) et créé une miniature (maximum 100x100px)
   * 
   * Si une erreur survient, une exception est levée       
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   * @return boolean
   */
  public function upload_picture($fichier){
    
    // traite le nom de destination (correspond au titre de la bonne adresse dans la langue principale)
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
    
    // récupert le nom de destination pour l'image noir-blanc
    $tab_temp = explode(".", $nom_destination);
    $nom_destination_nb = basename($nom_destination, "." .$tab_temp[sizeof($tab_temp) - 1]) ."-nb.$ext";  
    

    // le fichier doit être au format jpg, gif ou png
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
  	createthumb($this->picture_path .$nom_destination, $this->picture_path .$nom_destination, 1000, 1000, false);
  	
  	// créé les images noir et blanc
  	//createthumb($this->picture_path .$nom_destination, $this->picture_path .$nom_destination_nb, 1000, 1000, false, true);
  	//createthumb($this->picture_path .$nom_destination, $this->picture_path ."thumb/" .$nom_destination_nb, 100, 100, false, true);
  	
    // puis créé une vignette couleur
  	createthumb($this->picture_path .$nom_destination, $this->picture_path ."thumb/" .$nom_destination, 200, 200, false);
  	
  	// puis une vignette noir/blanc
  	//createthumb($this->picture_path .$nom_destination, $this->picture_path ."thumb_n_b/" .$nom_destination, 200, 200, false, true);
  	
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
        
        // efface la miniature noir/blanc
        /*$path = $this->picture_path ."thumb_n_b/" .$p;
        if (file_exists($path)){
          if (!unlink($path)){
            throw new Exception("Erreur pendant la suppression de la photo");
          }
        }*/
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
   * Recherche l'URL de la bonne adresse en fonction de son titre
   * 
   * @param string $lang Le code du language dans lequel on veut récupérer l'URL
   * 
   * @return string            
   */   
  public function get_url_simple($lang = ""){
    $url = "";
    
    if ($lang == "")
      $lang = Miki_language::get_main_code();
    
    $url = 'places/' .decode($this->title[$lang]) .'-' .$this->id;
    
    return $url;
  }
  
  /**
   * Recherche le nombre de bonnes adresses total
   * 
   * @static   
   * @return int      
   */     
  public static function get_nb_places(){
    $sql = "SELECT COUNT(*) FROM miki_place";
    
    $result = mysql_query($sql);
    
    $row = mysql_fetch_array($result);
    return $row[0];
  }

  /**
   * Recherche toutes les bonnes adresses selon certains critères
   * 
   * @param string $search Critère de recherche qui sera appliqué sur le nom (dans toutes les langues), le pays, la ville et la catégorie
   * @param string $country Recherche seulement les bonnes adresses du pays donné
   * @param string $city Recherche seulement les bonnes adresses de la ville donnée
   * @param string $category Recherche seulement les bonnes adresses de la catégorie donnée      
   * @param boolean $all si true, on prend toutes les bonnes adresses, si false on ne prend que les bonnes adresses publiées (state = 1)
   * @param string $order Par quel champ les bonnes adresses trouvées seront triées (npa, city, country, category, date_created, state). Si vide, on tri selon la date de création (date_created).
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * @param int $nb nombre de bonnes adresses à retourner par page. Si = "" on retourne toutes les bonnes adresses
   * @param int $page numéro de la page à retourner
   * @param int $total Cette variable sera modifiée avec le nombre total d’enregistrements qui seraient retournés par la requête sans la clause LIMIT   
   * 
   * @static
   * @return mixed un tableau d'élément Miki_place représentant toutes les bonnes adresses trouvées   
   */             
  public static function search($search = "", $country = "", $city = "", $category = "", $all = true, $order = "", $order_type = "asc", $nb = "", $page = 1, &$total){
    $return = array();
    $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT(mp.id) 
            FROM miki_place mp, miki_place_title mpt
            WHERE mpt.id_place = mp.id";
    
    // Applique les critères de recherche
    if ($search != ""){
      $search = mb_strtolower($search, 'UTF-8');
      $sql .= sprintf(" AND (LOWER(mpt.title) LIKE '%%%s%%' OR 
                            LOWER(mp.country) LIKE '%%%s%%' OR
                            LOWER(mp.city) LIKE '%%%s%%' OR
                            LOWER(mp.category) LIKE '%%%s%%')",
                mysql_real_escape_string($search),
                mysql_real_escape_string($search),
                mysql_real_escape_string($search),
                mysql_real_escape_string($search));
    }
    
    // Recherche selon le pays
    if ($country != ""){
      $country = mb_strtolower($country, 'UTF-8');
      $sql .= sprintf(" AND LOWER(mp.country) LIKE '%%%s%%'",
                mysql_real_escape_string($country));
    }
    
    // Recherche selon la ville
    if ($city != ""){
      $city = mb_strtolower($city, 'UTF-8');
      $sql .= sprintf(" AND LOWER(mp.city) LIKE '%%%s%%'",
                mysql_real_escape_string($city));
    }
    
    // Recherche selon la catégorie
    if ($category != ""){
      $category = mb_strtolower($category, 'UTF-8');
      $sql .= sprintf(" AND LOWER(mp.category) LIKE '%%%s%%'",
                mysql_real_escape_string($category));
    }
    
    // ne prend que les bonnes adresses dans l'état 'publiées'
    if (!$all){
      $sql .= " AND state = 1";
    }
    
    if ($order == "npa")
      $sql .= " ORDER BY mp.npa " .$order_type;
    elseif ($order == "city")
      $sql .= " ORDER BY mp.city " .$order_type;
    elseif ($order == "country")
      $sql .= " ORDER BY mp.country " .$order_type;
    elseif ($order == "category")
      $sql .= " ORDER BY mp.category " .$order_type;
    elseif ($order == "date_created")
      $sql .= " ORDER BY mp.date_created " .$order_type;
    elseif ($order == "state")
      $sql .= " ORDER BY mp.state " .$order_type;
    elseif ($order == "position")
      $sql .= " ORDER BY mp.position " .$order_type;
    else
      $sql .= " ORDER BY mp.position " .$order_type;
      
    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }

    $result = mysql_query($sql);

    // calcul le nombre de résultats total, sans la clause LIMIT
    $result2 = mysql_query("SELECT FOUND_ROWS()");
    $row2 = mysql_fetch_array($result2);
    $total = $row2[0];

    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_place($row['id']);
    }
    
    return $return;
  }
  
  /**
   * Recherche toutes les bonnes adresses
   * 
   * @param boolean $all si true, on prend toutes les bonnes adresses, si false on ne prend que les bonnes adresses publiées (state = 1)
   * @param string $order Par quel champ les bonnes adresses trouvées seront triées (npa, city, country, category, date_created). Si vide, on tri selon la date de création (date_created).
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * @param int $nb Nombre de bonnes adresses à retourner par page. Si = "" on retourne toutes les bonnes adresses
   * @param int $page Numéro de la page à retourner
   * @param int $total Cette variable sera modifiée avec le nombre total d’enregistrements qui seraient retournés par la requête sans la clause LIMIT   
   * 
   * @static
   * @return mixed un tableau d'élément Miki_place représentant toutes les bonnes adresses trouvées   
   */             
  public static function get_all_places($all = true, $order = "", $order_type = "asc", $nb = "", $page = 1, &$total){
    $return = array();
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM miki_place";
    
    // ne prend que les bonnes adresses dans l'état 'publiés'
    if (!$all){
      $sql .= " WHERE state = 1";
    }
    
    if ($order == "npa")
      $sql .= " ORDER BY npa " .$order_type;
    elseif ($order == "city")
      $sql .= " ORDER BY city " .$order_type;
    elseif ($order == "country")
      $sql .= " ORDER BY country " .$order_type;
    elseif ($order == "category")
      $sql .= " ORDER BY category " .$order_type;
    elseif ($order == "date_created")
      $sql .= " ORDER BY date_created " .$order_type;
    elseif ($order == "position")
      $sql .= " ORDER BY position " .$order_type;
    else
      $sql .= " ORDER BY position " .$order_type;
    
    // puis recalcule avec le clause LIMIT
    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }
    
    $result = mysql_query($sql);
    
    // calcul le nombre de résultats total, sans la clause LIMIT
    $result2 = mysql_query("SELECT FOUND_ROWS()");
    $row2 = mysql_fetch_array($result2);
    $total = $row2[0];
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_place($row['id']);
    }
    
    return $return;
  }
}
?>