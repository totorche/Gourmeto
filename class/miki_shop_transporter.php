<?php
/**
 * Classe miki_shop_transporter
 * @package Miki
 */ 

/**
 * Inclut les fonctions d'importation et de traitement des images
 */ 
require_once("functions_pictures.php");

/**
 * Représentation d'un transporteur configuré pour un shop.
 * 
 * @package Miki  
 */
class Miki_shop_transporter{

  /**
   * Id de l'article
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom du transporteur
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Etat du transporteur (0 = désactivé, 1 = activé)
   *      
   * @var int
   * @access public   
   */
  public $state;
  
  /**
   * logo du transporteur
   * 
   * @var string
   * @access public   
   */
  public $logo;
  
  /**
   * Délais de livraison proposés par le transpoteur (une phrase décrivant les délais)
   *      
   * @var string
   * @access public   
   */
  public $shipping_delay;
  
  /**
   * Si les taxes doivent être ajoutées au frais de port du transporteur
   *      
   * @var boolean
   * @access public   
   */
  public $tax;
  
  /**
   * Lien permettant de suivre le colis. Entrer '@' à la place du numéro du colis
   *      
   * @var string
   * @access public   
   */
  public $url_tracking;
  
  /**
   * Le chemin du logo du transporteur depuis la console d'administration (Miki).
   *      
   * @var string
   * @access private
   */       
  private $picture_path = "../pictures/shop_transporter/";
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le transporteur dont l'id a été donné
   * 
   * @param int $id Id du transporteur à charger (optionnel)
   */
  function __construct($id = ""){
    // charge l'article si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un transporteur depuis un id
   *    
   * Si le transporteur n'existe pas, une exception est levée.
   *    
   * @param int $id id du transporteur à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_shop_transporter WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le transporteur demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->state = $row['state'];
    $this->logo = $row['logo'];
    $this->shipping_delay = $row['shipping_delay'];
    $this->tax = $row['tax'] == 1 ? true : false;
    $this->url_tracking = $row['url_tracking'];
    
    return true;
  }
  
  /**
   * Sauvegarde le transporteur dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    // si l'id du transporteur existe, c'est que le transporteur existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
    
    $sql = sprintf("INSERT INTO miki_shop_transporter (name, state, logo, shipping_delay, tax, url_tracking) VALUES('%s', %d, '%s', '%s', %d, '%s')",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->logo),
      mysql_real_escape_string($this->shipping_delay),
      mysql_real_escape_string($this->tax),
      mysql_real_escape_string($this->url_tracking));
    $result = mysql_query($sql);
    if (!$result){
      throw new Exception(_("Erreur lors de l'insertion du transporteur dans la base de données : ") .mysql_error());
    }
    // récupert l'id
    $this->id = mysql_insert_id();

    // recharge le transporteur
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour le transporteur dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    // si aucun id existe, c'est que le transporteur n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    $sql = sprintf("UPDATE miki_shop_transporter SET name = '%s', state = %d, logo = '%s', shipping_delay = '%s', tax = %d, url_tracking = '%s' WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->logo),
      mysql_real_escape_string($this->shipping_delay),
      mysql_real_escape_string($this->tax),
      mysql_real_escape_string($this->url_tracking),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      throw new Exception(_("Erreur lors de la mise à jour du transporteur dans la base de données : ") ."<br />" .mysql_error());
    }
    
    return true;
  }
  
  /**
   * Supprime le transporteur
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_shop_transporter WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du transporteur : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Change l'état du transporteur
   * 
   * @param int $state Nouvel état du transporteur      
   */   
  public function change_state($state){
    $this->state = $state;
    $this->update();
  }
  
  /**
   * Supprime le logo du transporteur
   * 
   * Si une erreur survient, une exception est levée.      
   *
   * @return boolean
   */   
  public function delete_logo(){
    $path = $this->picture_path .$this->logo;
    if (file_exists($path)){
      if (!unlink($path)){
        throw new Exception("Erreur pendant la suppression de l'image");
      }
    }
    $this->logo = "";
    $this->update();
    return true;
  }
  
  /**
   * Récupert la méthode de frais de port choisie pour ce transporteur pour un shop donné
   * 
   * @param int $id_shop Id du shop pour lequel on veut récupérer la méthode de frais de port du transporteur
   * @return mixed La méthode de frais de port du transporteur pour le shop donné ou FALSE si aucune méthode trouvée 
   */
  public function get_shipping_method($id_shop){
    $sql = sprintf("SELECT shipping_method FROM miki_shop_transporter_s_miki_shop WHERE id_shop_transporter = %d AND id_shop = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($id_shop));
    $result = mysql_query($sql);
    
    if (mysql_num_rows($result) == 0)
      return false;
      
    $row = mysql_fetch_array($result);
    return $row['shipping_method'];
  }
  
  /**
   * Définit la méthode de frais de port pour ce transporteur pour un shop donné
   * 
   * @param int $id_shop Id du shop pour lequel on veut définir la méthode de frais de port du transporteur
   * @param int $shipping_method La méthode de frais de port à définir pour le transporteur   
   * @return mixed La méthode de frais de port du transporteur pour le shop donné ou FALSE si aucune méthode trouvée 
   */
  public function set_shipping_method($id_shop, $shipping_method){
    $sql = sprintf("INSERT INTO miki_shop_transporter_s_miki_shop (id_shop_transporter, id_shop, shipping_method) VALUES(%d, %d, %d)
                    ON DUPLICATE KEY UPDATE shipping_method = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($id_shop),
      mysql_real_escape_string($shipping_method),
      mysql_real_escape_string($shipping_method));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la modification du transporteur : ") .mysql_error());
    return true;
  }
  
  /**
   * Ajoute un logo au transporteur et le redimensionne à maximum 200x200px
   * 
   * Ajoute un logo au transporteur, l'upload et le redimensionne en une image de taille maximale de 200px de large ou de long
   * 
   * Si une erreur survient, une exception est levée.      
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   *    
   * @return boolean
   */    
  public function upload_picture($fichier, $nb){
    
    // si un logo existe déjà, on le supprime
  	if (!empty($this->logo) && $this->logo != "NULL")
  	  $this->delete_logo();
    
    // traite le nom de destination
    $nom_destination = decode($this->name);  	
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
  	if (!in_array($size[2], array(1, 2, 3) ) )
  		throw new Exception(_("Le type de l'image n'est pas supporté. Les types supportés sont : JPEG, GIF et PNG"));
  	/*if (($size[0] < 25) || ($size[1] < 25))
  		throw new Exception(_("Veuillez uploader une image plus grande que 25px de côté."));*/
  		
  	// pas d'erreur --> upload
  	if ((isset($fichier['tmp_name']))&&($fichier['error'] == UPLOAD_ERR_OK)) {
  		if (!move_uploaded_file($fichier['tmp_name'], $this->picture_path .$nom_destination))
        exit();
        //throw new Exception(_("Erreur pendant l'upload de l'image"));
  	}
  	
  	// redimensionne l'image
  	createthumb($this->picture_path .$nom_destination, $nom_destination, 200, 200, true);
  	
  	$this->logo = $nom_destination;
  	return true;
  }
  
  /**
   * Recherche des transporteurs
   * 
   * @param string $search Critères de recherche. Recherche dans le nom (name) de l'article
   * @param int $valid Si = true, récupère seulement les transporteurs activés (state = 1)
   * @param string $order Par quel champ les transporteurs trouvés seront triés (name, state, tax). Si vide, on tri selon leur nom (name).
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)               
   *
   * @static         
   * @return mixed Un tableau d'éléments de type miki_shop_transporter représentant les transporteurs trouvés 
   */      
  public static function search($search = "", $valid = true, $order = "", $order_type = "asc"){
    $return = array();
    
      $sql = "SELECT id
              FROM miki_shop_transporter 
              WHERE 1";
    
    // recherche dans le nom et la référence de l'article
    if ($search !== ""){
      $search = mb_strtolower($search, 'UTF-8');
                
      $sql .= sprintf(" AND LOWER(name) LIKE '%%%s%%'",
                mysql_real_escape_string($search));
    }
    
    if ($valid){
      $sql .= " AND state = 1";
    }
    
    if ($order == "name")
      $sql .= " ORDER BY LCASE(name) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "state")
      $sql .= " ORDER BY LCASE(state) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "tax")
      $sql .= " ORDER BY LCASE(tax) COLLATE utf8_general_ci " .$order_type;
    else
      $sql .= " ORDER BY LCASE(name) COLLATE utf8_general_ci " .$order_type;

    $result = mysql_query($sql) or die("Erreur : $sql");
    
    while($row = mysql_fetch_array($result)){
      $return[] = new miki_shop_transporter($row['id']);
    }
    return $return;
  }
  
  /**
   * Recherche le nombre de transporteurs total
   * 
   * @static
   * @return int Le nombre de transporteurs trouvés
   */   
  public static function get_nb_transporters(){
    $sql = "SELECT count(*) FROM miki_shop_transporter";
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * fonction statique récupérant tous les transporteurs
   * 
   * @static
   * @return mixed Un tableau d'éléments de type miki_shop_transporter représentant les transporteurs trouvés   
   */ 
  public static function get_all_transporters(){
    $return = array();
    $sql = "SELECT id FROM miki_shop_transporter";
    $result = mysql_query($sql) or die("Erreur sql : <br />$sql");

    while($row = mysql_fetch_array($result)){
      $return[] = new miki_shop_transporter($row[0]);
    }
    return $return;
  }
}
?>