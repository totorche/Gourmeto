<?php
/**
 * Classe Miki_company
 * @package Miki
 */ 

/**
 * Inclut les fonctions d'importation et de traitement des images
 */ 
require_once("functions_pictures.php");

/**
 * Représentation d'une société
 * 
 * @package Miki  
 */ 
class Miki_company{

  /**
   * Id de la société
   *      
   * @var int
   * @access public   
   */ 
  public $id;
  
  /**
   * Nom de la société
   *      
   * @var string
   * @access public   
   */ 
  public $name;
  
  /**
   * Description de la société
   *      
   * @var string
   * @access public   
   */ 
  public $description;
  
  /**
   * Logo de la société
   *      
   * @var string
   * @access public   
   */ 
  public $logo;
  
  /**
   * Taille de la société (nombre d'employés)
   *      
   * @var int
   * @access public   
   */ 
  public $size;
  
  /**
   * Activités de la société
   *      
   * @var string
   * @access public   
   */ 
  public $activities;
  
  /**
   * Produits et services proposés par la société
   *      
   * @var string
   * @access public   
   */ 
  public $products_services;
  
  /**
   * Projets de la société 
   *      
   * @var string
   * @access public   
   */ 
  public $projects;
  
  /**
   * Adresse (Rue et n°) de la société 
   *      
   * @var string
   * @access public   
   */ 
  public $address;
  
  /**
   * Case postale de la localité de la société
   *      
   * @var string
   * @access public   
   */ 
  public $npa;
  
  /**
   * Localité de la société
   *      
   * @var string
   * @access public   
   */ 
  public $city;
  
  /**
   * Région (département, canton, etc.) de la société
   *      
   * @var string
   * @access public   
   */ 
  public $dept;
  
  /**
   * Pays de la société
   *      
   * @var string
   * @access public   
   */ 
  public $country;
  
  /**
   * Site web de la société
   *      
   * @var string
   * @access public   
   */ 
  public $web;
  
  /**
   * Heures d'ouverture de la société
   *      
   * @var string
   * @access public   
   */ 
  public $open_time;
  
  /**
   * Avantages offerts aux membre du site par la société
   *      
   * @var string
   * @access public   
   */ 
  public $advantage_member;
  
  /**
   * Emplacement des logos des sociétés (chemin relatif par rapport à la racine du site web) 
   * Valeur par défaut : "pictures/logo_companies/"
   *    
   * @var string
   * @access public   
   */ 
  public $picture_path = "pictures/logo_companies/";
  
  
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge la société dont l'id a été donné
   * 
   * @param int $id Id de la société à charger (optionnel)
   */
  function __construct($id = ""){
    // charge la société si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  
  /**
   * Charge une société depuis un id
   *    
   * Si la société n'existe pas, une exception est levée.
   *    
   * @param int $id id de la société à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_company WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La société demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id                 = $row['id'];
    $this->name               = $row['name'];
    $this->description        = $row['description'];
    $this->logo               = $row['logo'];
    $this->size               = $row['size'];
    $this->activities         = $row['activities'];
    $this->products_services  = $row['products_services'];
    $this->projects           = $row['projects'];
    $this->address            = $row['address'];
    $this->npa                = $row['npa'];
    $this->city               = $row['city'];
    $this->dept               = $row['dept'];
    $this->country            = $row['country'];
    $this->web                = $row['web'];
    $this->open_time          = $row['open_time'];
    $this->advantage_member   = $row['advantage_member'];
    return true;
  }
  
  /**
   * Charge une société d'après son nom
   *    
   * Si la société n'existe pas, une exception est levée.
   *    
   * @param string $name nom de la société à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load_from_name($name){
    $sql = sprintf("SELECT * FROM miki_company WHERE name = '%s'",
      mysql_real_escape_string($name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La société demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id                 = $row['id'];
    $this->name               = $row['name'];
    $this->description        = $row['description'];
    $this->logo               = $row['logo'];
    $this->size               = $row['size'];
    $this->activities         = $row['activities'];
    $this->products_services  = $row['products_services'];
    $this->projects           = $row['projects'];
    $this->address            = $row['address'];
    $this->npa                = $row['npa'];
    $this->city               = $row['city'];
    $this->dept               = $row['dept'];
    $this->country            = $row['country'];
    $this->web                = $row['web'];
    $this->open_time          = $row['open_time'];
    $this->advantage_member   = $row['advantage_member'];
    return true;
  }
  
  /**
   * Sauvegarde la société dans la base de données.
   *    
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   * 
   * Si une société du même nom existe déjà, une exception est levée      
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */
  public function save(){      
    // si un l'id de la société existe, c'est que la société existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
    
    // vérifie que le nom de l'entreprise n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_company WHERE name = '%s'",
      mysql_real_escape_string($this->name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Une société du même nom existe déjà dans la base de données"));
      
    $sql = sprintf("INSERT INTO miki_company (name, description, logo, size, activities, products_services, projects, address, npa, city, dept, country, web, open_time, advantage_member) VALUES('%s', '%s', '%s', %d, '%s', '%s', '%s', '%s', %d, '%s', '%s', '%s', '%s', '%s', '%s')",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->description),
      mysql_real_escape_string($this->logo),
      mysql_real_escape_string($this->size),
      mysql_real_escape_string($this->activities),
      mysql_real_escape_string($this->products_services),
      mysql_real_escape_string($this->projects),
      mysql_real_escape_string($this->address),
      mysql_real_escape_string($this->npa),
      mysql_real_escape_string($this->city),
      mysql_real_escape_string($this->dept),
      mysql_real_escape_string($this->country),
      mysql_real_escape_string($this->web),
      mysql_real_escape_string($this->open_time),
      mysql_real_escape_string($this->advantage_member));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion de la société dans la base de données : ") ."<br />$sql<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la société
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour la société dans la base de données.
   *    
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si une société du même nom existe déjà, une exception est levée   
   * Si une erreur survient, une exception est levée   
   *    
   * @return boolean   
   */
  public function update(){      
    // si aucun id existe, c'est que la société n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // vérifie que le nom de l'entreprise n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_company WHERE name = '%s' AND id != %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Une société du même nom existe déjà dans la base de données"));
    
    $sql = sprintf("UPDATE miki_company SET name = '%s', description = '%s', logo = '%s', size = %d, activities = '%s', products_services = '%s', projects = '%s', address = '%s', npa = %d, city = '%s', dept = '%s', country = '%s', web = '%s', open_time = '%s', advantage_member = '%s' WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->description),
      mysql_real_escape_string($this->logo),
      mysql_real_escape_string($this->size),
      mysql_real_escape_string($this->activities),
      mysql_real_escape_string($this->products_services),
      mysql_real_escape_string($this->projects),
      mysql_real_escape_string($this->address),
      mysql_real_escape_string($this->npa),
      mysql_real_escape_string($this->city),
      mysql_real_escape_string($this->dept),
      mysql_real_escape_string($this->country),
      mysql_real_escape_string($this->web),
      mysql_real_escape_string($this->open_time),
      mysql_real_escape_string($this->advantage_member),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour de la société dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime la société
   * 
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */ 
  public function delete(){
    $sql = sprintf("DELETE FROM miki_company WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la société : ") ."<br />" .mysql_error());
    
    $this->delete_logo();
    return true;
  }
  
  /**
   * Ajoute un logo à la société
   * 
   * Ajoute un logo à la société, l'upload et le redimensionne pour 
   * qu'il ait une taille de maximum 200px de large ou de long
   * 
   * Si une erreur survient, une exception est levée.      
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   * @param string $nom_destination Nom de destination de l'image finale
   * @return boolean
   */        
  public function upload_picture($fichier, $nom_destination){
    
    // traite le nom du fichier dans lequel sera enregistrée l'image
    $nom_destination = decode($nom_destination);  	
      
    // récupert l'extension puis l'ajoute au nom de destination
    $system = explode('.',strtolower($fichier['name']));
    $ext = $system[sizeof($system)-1];
    $nom_destination = $nom_destination ."." .$ext;  

    // le fichier doit être au format jpg ou png
  	if (!preg_match('/png/i',$ext) && !preg_match('/jpg|jpeg/i',$ext)){
      throw new Exception(_("Le type de l'image n'est pas supporté. Les types supportés sont : JPEG et PNG : $nom_destination - " .$fichier['name']));
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
  		if (!move_uploaded_file($fichier['tmp_name'], $this->picture_path .$nom_destination))
        exit();
  	}
  	
  	// redimensionne l'image et remplace l'image de base par la miniature
  	createthumb($this->picture_path .$nom_destination, "./" .$nom_destination, 200, 200, true);
  	
  	$this->logo = $nom_destination;
  	return true;
  }
  
  /**
   * Supprime le logo de la société
   * 
   * Supprime le logo de la société ainsi que le fichier de ce logo.
   *       
   * Si le fichier représentant le logo n'existe pas, une exception est levée
   * 
   * @return boolean
   */               
  public function delete_logo(){
    if ($this->logo == "")
      return true;
      
    $path = $this->picture_path .$this->logo;
    if (file_exists($path)){
      if (!unlink($path)){
        throw new Exception("Erreur pendant la suppression du logo");
      }
    }
    return true;
  }
  
  /**
   * Recherche les personnes faisant partie de la société
   *      
   * Recherche les personnes faisant partie de la société et les stock dans un tableau. Chaque personne est de type Miki_person
   * 
   * @see Miki_person   
   * @return mixed Un tableau contenant toutes les personnes de la société (1 personne étant de type Miki_person)
   */                 
  public function get_persons(){
    $return = array();
    $sql = sprintf("SELECT id FROM miki_person WHERE company_id = %d ORDER BY id ASC",
      mysql_real_escape_string($this->id));
    
    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $item = new Miki_person($row['id']);
      $return[] = $item;
    }
    return $return;
  }

  /**
   * Recherche toutes les sociétés
   * 
   * @param string $order Par quelle champs les sociétés trouvées seront triées (id ou name)
   * @param string $order_type Type de tri (ascendant "asc" ou descendant "desc")       
   * @static
   * @return mixed un tableau d'élément Miki_company représentant toutes les sociétés trouvées
   */            
  public static function get_all_companies($order = "", $order_type = "asc"){
    $return = array();
    
    // vérifie le type de tri
    if ($order_type != "asc" && $order_type != "desc"){
      $order_type = "asc";
    }
    
    $sql = "SELECT * FROM miki_company";
    
    // ordonne les catégories
    if ($order == "id")
      $sql .= " ORDER BY id " .$order_type;
    elseif ($order == "name")
      $sql .= " ORDER BY name " .$order_type;
    else
      $sql .= " ORDER BY name " .$order_type;

    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $item = new Miki_company($row['id']);
      $return[] = $item;
    }
    return $return;
  }
}
?>