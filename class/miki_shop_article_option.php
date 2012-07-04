<?php
/**
 * Classe Miki_shop_article_option
 * @package Miki
 */ 

/**
 * Inclut les fonctions d'envoi d'e-mail
 */ 
require_once("class.phpmailer.php");

/**
 * Inclut les fonctions d'importation et de traitement des images
 */ 
require_once("functions_pictures.php");

/**
 * Représentation d'une option d'un article d'un shop.
 * 
 * Les options sont regroupées en groupes d'options.
 * Un article peut être lié à un groupe d'options.
 * Dès lors qu'un article est relié à un groupe d'options, l'utilisateur, lors du choix de l'article sur le site Internet, pourra choisir parmis ces différentes options.
 * Une option peut posséder un stock définit.
 * Elle peut également modifier le prix final de l'article.   
 * 
 * @see Miki_shop
 *  
 * @package Miki  
 */
class Miki_shop_article_option{

  /**
   * Id de l'option
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Référence de l'option
   *      
   * @var string
   * @access public   
   */
  public $ref;
  
  /**
   * Id du shop auquel appartient l'option
   *      
   * @var int
   * @access public   
   */
  public $id_shop;
  
  /**
   * Prix de l'option qui sera ajouté au prix de l'article
   *      
   * @var float
   * @access public   
   */
  public $price;
  
  /**
   * Etat de l'option (0 = suspendu, 1 = disponible)
   *      
   * @var int
   * @access public   
   */
  public $state;
  
  /**
   * Si on gère le stock pour l'option
   *      
   * @var boolean
   * @access public   
   */
  public $use_stock;
  
  /**
   * Quantité disponible
   *      
   * @var int
   * @access public   
   */
  public $quantity;
  
  /**
   * Nom de l'option. 
   * 
   * Un tableau comportant le nom dans les différentes langues. L'indice du tableau correspond au code de la langue (Code ISO 639-1 ({@link http://fr.wikipedia.org/wiki/Liste_des_codes_ISO_639-1})).
   *      
   * @var mixed
   * @access public   
   */
  public $name;
  
  /**
   * Description de l'option.
   * 
   * Un tableau comportant la description dans les différentes langues. L'indice du tableau correspond au code de la langue (Code ISO 639-1 ({@link http://fr.wikipedia.org/wiki/Liste_des_codes_ISO_639-1})).
   *      
   * @var mixed
   * @access public   
   */
  public $description;
  
  /**
   * Images de l'option.
   * 
   * Cette variable est un tableau comportant la différentes images de l'option.
   *      
   * @var mixed
   * @access public   
   */
  public $pictures;
  
  /**
   * Le chemin des images de l'option depuis la console d'administration (Miki).
   *      
   * @var string
   * @access private
   */       
  private $picture_path = "../pictures/shop_articles_options/";
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge l'option dont l'id a été donné
   * 
   * @param int $id Id de l'option à charger (optionnel)
   */
  function __construct($id = ""){
    // charge l'option si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge une option depuis un id
   *    
   * Si l'option n'existe pas, une exception est levée.
   *    
   * @param int $id id de l'option à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_shop_article_option WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("L'article demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->ref = $row['ref'];
    $this->id_shop = $row['id_shop'];
    $this->price = $row['price'];
    $this->state = $row['state'];
    $this->use_stock = $row['use_stock'] == 1;
    $this->quantity = $row['quantity'];
    $this->pictures = $row['pictures'] != "" ? explode("&&", $row['pictures']) : array();
    
    // recherche les noms de l'option
    $sql = sprintf("SELECT * FROM miki_shop_article_option_name WHERE id_option = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $this->name[$row['language']] = $row['name'];
    }
    
    // recherche les descriptions de l'option
    $sql = sprintf("SELECT * FROM miki_shop_article_option_description WHERE id_option = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $this->description[$row['language']] = $row['description'];
    }
    
    return true;
  }
  
  /**
   * Charge une option depuis sa référence
   *    
   * Si l'option n'existe pas, une exception est levée.
   *    
   * @param int $ref Référence de l'option à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load_by_ref($ref){
    $sql = sprintf("SELECT * FROM miki_shop_article_option WHERE ref = '%s'",
      mysql_real_escape_string($ref));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("L'article demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->ref = $row['ref'];
    $this->id_shop = $row['id_shop'];
    $this->price = $row['price'];
    $this->state = $row['state'];
    $this->use_stock = $row['use_stock'] == 1;
    $this->quantity = $row['quantity'];
    $this->pictures = $row['pictures'] != "" ? explode("&&", $row['pictures']) : array();
    
    // recherche les noms de l'option
    $sql = sprintf("SELECT * FROM miki_shop_article_option_name WHERE id_option = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $this->name[$row['language']] = $row['name'];
    }
    
    // recherche les descriptions de l'option
    $sql = sprintf("SELECT * FROM miki_shop_article_option_description WHERE id_option = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $this->description[$row['language']] = $row['description'];
    }
    
    return true;
  }
  
  /**
   * Sauvegarde l'option dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->id_shop))
      throw new Exception(_("L'option n'est attribuée à aucun shop"));
      
    // si un l'id de l'option existe, c'est que l'option existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // concatène les images
    $tab_temp = array();
    if (is_array($this->pictures)){
      foreach($this->pictures as $lang => $pic){
        if (!empty($pic))
          $tab_temp[] = $pic;
      }
      $pictures = implode("&&", $tab_temp);
    }
    else
      $pictures = "";
    
    // débute la transaction
    mysql_query("START TRANSACTION");
    
    // Si la référence de l'option est spécifiée, vérifie qu'aucune option avec la même référence n'existe déjà
    if ($this->ref != ""){
      $sql = sprintf("SELECT id FROM miki_shop_article_option WHERE ref = '%s'",
        mysql_real_escape_string($this->ref));
      $result = mysql_query($sql);
      if (mysql_num_rows($result) > 0){
        // annule les modifications dans la bdd
        mysql_query("ROLLBACK"); 
        throw new Exception(_("Un article possédant la même référence existe déjà dans la base de données"));
      }
    }
      
    $sql = sprintf("INSERT INTO miki_shop_article_option (ref, id_shop, price, state, use_stock, quantity, pictures, date_created) 
                    VALUES('%s', %d, '%F', %d, %d, %d, '%s', NOW())",
      mysql_real_escape_string($this->ref),
      mysql_real_escape_string($this->id_shop),
      mysql_real_escape_string($this->price),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->use_stock),
      mysql_real_escape_string($this->quantity),
      mysql_real_escape_string($pictures));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications dans la bdd
      mysql_query("ROLLBACK"); 
      throw new Exception(_("Erreur lors de l'insertion de l'option dans la base de données : ") .mysql_error());
    }
    // récupert l'id
    $this->id = mysql_insert_id();

    // insère les noms de l'option dans la base de données
    foreach($this->name as $key=>$name){
      $sql = sprintf("INSERT INTO miki_shop_article_option_name (id_option, language, name) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string($name));
      $result = mysql_query($sql);
      if (!$result){
        // annule les modifications dans la bdd
        mysql_query("ROLLBACK"); 
        throw new Exception(_("Erreur lors de l'insertion du nom de l'option dans la base de données : ") ."<br />" .mysql_error());
      }
    }
    
    // insère les descriptions de l'option dans la base de données
    foreach($this->description as $key=>$description){        
      $sql = sprintf("INSERT INTO miki_shop_article_option_description (id_option, language, description) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string($description));
      $result = mysql_query($sql);
      if (!$result){
        // annule les modifications dans la bdd
        mysql_query("ROLLBACK"); 
        throw new Exception(_("Erreur lors de l'insertion de la description de l'option dans la base de données : ") ."<br />" .mysql_error());
      }
    }
    
    // termine la transaction
    mysql_query("COMMIT");
    
    // recharge l'option
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour l'option dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->id_shop))
      throw new Exception(_("L'option n'est attribué à aucun shop"));
      
    // si aucun id existe, c'est que l'option n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // concatène les images
    $tab_temp = array();
    if (is_array($this->pictures)){
      foreach($this->pictures as $lang => $pic){
        if (!empty($pic))
          $tab_temp[] = $pic;
      }
      $pictures = implode("&&", $tab_temp);
    }
    else
      $pictures = "";
      
    // débute la transaction
    mysql_query("START TRANSACTION");
    
    $sql = sprintf("UPDATE miki_shop_article_option SET ref = '%s', id_shop = %d, price = '%F', state = %d, use_stock = %d, quantity = %d, pictures = '%s' WHERE id = %d",
      mysql_real_escape_string($this->ref),
      mysql_real_escape_string($this->id_shop),
      mysql_real_escape_string($this->price),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->use_stock),
      mysql_real_escape_string($this->quantity),
      mysql_real_escape_string($pictures),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      throw new Exception(_("Erreur lors de la mise à jour de l'option dans la base de données : ") ."<br />" .mysql_error());
      // annule les modifications dans la bdd
      mysql_query("ROLLBACK"); 
    }
    
    // insère les noms de l'option dans la base de données
    foreach($this->name as $key=>$name){
      $sql = sprintf("INSERT INTO miki_shop_article_option_name (id_option, language, name) VALUES (%d, '%s', '%s')
                      ON DUPLICATE KEY UPDATE name = '%s'",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string($name),
        mysql_real_escape_string($name));
      $result = mysql_query($sql);
      if (!$result){
        throw new Exception(_("Erreur lors de la mise à jour du nom de l'option dans la base de données : ") ."<br />" .mysql_error());
        // annule les modifications dans la bdd
        mysql_query("ROLLBACK"); 
      }
    }
    
    // insère les descriptions de l'option dans la base de données
    foreach($this->description as $key=>$description){
      $sql = sprintf("INSERT INTO miki_shop_article_option_description (id_option, language, description) VALUES (%d, '%s', '%s')
                      ON DUPLICATE KEY UPDATE description = '%s'",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string($description),
        mysql_real_escape_string($description));
      $result = mysql_query($sql);
      if (!$result){
        throw new Exception(_("Erreur lors de la mise à jour de la description de l'option dans la base de données : ") .mysql_error());
        // annule les modifications dans la bdd
        mysql_query("ROLLBACK"); 
      }
    }
    
    // termine la transaction
    mysql_query("COMMIT");
    
    return true;
  }
  
  /**
   * Supprime l'option
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    // Supprime toutes les images de l'option
    $this->delete_all_pictures();
    
    $sql = sprintf("DELETE FROM miki_shop_article_option WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de l'option : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Recherche le nom de l'option dans la langue dont le code est passée en paramètre
   * 
   * Les codes de langues sont au format ISO 639-1 ({@link http://fr.wikipedia.org/wiki/Liste_des_codes_ISO_639-1})
   * 
   * Si une erreur survient, une exception est levée.      
   * 
   * @param string $lang Le code de la langue dans laquelle on doit récupérer le nom de l'option
   * @return string Le nom de l'option dans la langue demandée               
   */   
  public function get_name($lang){
    $sql = sprintf("SELECT name FROM miki_shop_article_option_name WHERE id_option = %d AND language = '%s'",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($lang));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      return "";
    $row = mysql_fetch_array($result);
    return stripslashes($row[0]);
  }
  
  /**
   * Recherche la description de l'option dans la langue dont le code est passée en paramètre
   * 
   * Les codes de langues sont au format ISO 639-1 ({@link http://fr.wikipedia.org/wiki/Liste_des_codes_ISO_639-1})
   * 
   * Si une erreur survient, une exception est levée.      
   * 
   * @param string $lang Le code de la langue dans laquelle on doit récupérer la description de l'option
   * @param string $nb_char Si != "", on tronque la description après x caractères ou x = $nb_char
   * @param boolean $full_word Si true, on tronque par mot entier. Si false, on tronque par caractère         
   * @return string Le nom de l'option dans la langue demandée               
   */
  public function get_description($lang, $nb_char = "", $full_word = true){
    $sql = sprintf("SELECT description FROM miki_shop_article_option_description WHERE id_option = %d AND  language = '%s'",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($lang));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      return "";
    $row = mysql_fetch_array($result);
    $description = stripslashes($row[0]);
    
    // si on prend toute la description on la retourne en entier
    if ($nb_char == "")
      return $description;
    
    // sinon on la coupe de la façon demandée
    if ($nb_char < mb_strlen($description)){
      if ($full_word === false)
        $stop = $nb_char;
      else{
        $stop = mb_strpos($description, ' ', $nb_char);
        if ($stop === false){
          $stop = mb_strlen($description);
        }
      }
      return mb_substr($description, 0, $stop);
    }
    else
      return $description;
  }
  
  /**
   * Change l'état de l'option
   * 
   * @param int $state Nouvel état de l'option
   */   
  public function change_state($state){
    $this->state = $state;
    $this->update();
  }
  
  /**
   * Supprime une photo de l'option
   * 
   * Si une erreur survient, une exception est levée.      
   *
   * @param string $name Nom de la photo à supprimer   
   * 
   * @return boolean      
   */   
  public function delete_picture($name){
    $tab_temp = array();
    
    foreach($this->pictures as $p){
      if ($p === $name && !is_dir($p) && $p != "no-picture.gif"){
        $path = $this->picture_path .$p;
        if (file_exists($path)){
          if (!unlink($path)){
            throw new Exception("Erreur pendant la suppression de la photo");
          }
        }
        $path = $this->picture_path .'thumb/' .$p;
        if (file_exists($path)){
          if (!unlink($path)){
            throw new Exception("Erreur pendant la suppression de la miniature de la photo");
          }
        }
      }
      elseif($p != "no-picture.gif" || $p != $name){
        $tab_temp[] = $p;
      }
    }
    $this->pictures = $tab_temp;
    $this->update();
    return true;
  }
  
  /**
   * Supprime toutes les photos de l'option
   * 
   * Si une erreur survient, une exception est levée.      
   *
   * @return boolean      
   */   
  public function delete_all_pictures(){
    $tab_temp = array();
    
    foreach($this->pictures as $p){
      if (!is_dir($p) && $p != "no-picture.gif"){
        $path = $this->picture_path .$p;
        if (file_exists($path)){
          if (!unlink($path)){
            throw new Exception("Erreur pendant la suppression de la photo");
          }
        }
        $path = $this->picture_path .'thumb/' .$p;
        if (file_exists($path)){
          if (!unlink($path)){
            throw new Exception("Erreur pendant la suppression de la miniature de la photo");
          }
        }
      }
    }
    $this->pictures = array();
    $this->update();
    return true;
  }
   
  /**
   * Ajoute une image à l'option
   * 
   * Ajoute une image à l'option, l'upload et le créé une miniature de taille maximale de 200px de large ou de long
   * 
   * Si une erreur survient, une exception est levée.      
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   * @param string $nom_destination Nom de destination de l'image finale
   *    
   * @return boolean
   */    
  public function upload_picture($fichier, $nom_destination){
    
    // traite le nom de destination
    $nom_destination = decode($nom_destination);  
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
  		
  	// pas d'erreur --> upload
  	if ((isset($fichier['tmp_name']))&&($fichier['error'] == UPLOAD_ERR_OK)) {
  		if (!move_uploaded_file($fichier['tmp_name'], $this->picture_path .$nom_destination))
        exit();
  	}
  	
  	// redimensionne l'image
  	createthumb($this->picture_path .$nom_destination, $this->picture_path ."thumb/" .$nom_destination, 200, 200, false);

  	$this->pictures[] = $nom_destination;
  	return true;
  }
  
  /**
   * Ajoute une option à un set d'options (Miki_shop_article_option_set)
   * 
   * Si une erreur survient, une exception est levée.  
   * 
   * @param int $id_option_set Id du set auquel on veut ajouter l'option   
   * @see Miki_shop_article_option_set
   * @return boolean
   */      
  public function add_to_set($id_option_set){
    // si le set n'existe pas encore dans la base de données, on essaye de l'y insérer
    if (!isset($this->id)){
      try{
        $this->save();
      }catch(Exception $e){
        throw new Exception(_("L'option n'a pas pu être ajoutée"));
      }
    }
    // insert l'utilisateur dans la set. Si il y est déjà, ne fait rien (mot-clé "ignore")
    $sql = sprintf("INSERT ignore INTO miki_shop_article_option_s_miki_shop_article_option_set (miki_shop_article_option_id, miki_shop_article_option_set_id) VALUES(%d, %d)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($id_option_set));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant l'ajout de l'option au set"));
    
    return true;
  }
  
  /**
   * Supprime une option d'un set d'options (Miki_shop_article_option_set)
   * 
   * Si une erreur survient, une exception est levée.  
   * 
   * @param int $id_option_set Id du set duquel on veut supprimer l'option   
   * @see Miki_shop_article_option_set
   * @return boolean
   */ 
  public function remove_from_set(){
    $sql = sprintf("DELETE FROM miki_shop_article_option_s_miki_shop_article_option_set WHERE miki_shop_article_option_id = %d AND miki_shop_article_option_set_id = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($id_option_set));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de l'option du set"));
      
    return true;
  }
  
  /**
   * Calcule la quantité disponible pour l'option. 
   * 
   * @param Miki_order $order Décompte la quantité de cette option utilisée dans la commande passée en paramètre
   * @param Miki_order_article $id_article L'id d'un article à ne pas prendre en compte dans le calcul de la quantité disponible 
   * @return mixed TRUE si pas de gestion du stock. La quantité restante si gestion du stock        
   */
  public function get_quantity_available($order = "", $id_article_not_to_use = ""){
    // vérifie si on utilise la gestion des stock sur le site
    $use_stock = Miki_configuration::get('miki_shop_gestion_stock');
    
    // si on ne gère pas le stock sur le site ou pour cette option, on retourne TRUE
    if (!$use_stock || !$this->use_stock)
      return true;
      
    // récupert la quantité disponible pour l'option en cours
    $quantity = $this->quantity;
    
    // si une commande a été donnée, on décompte la quantité de l'option en cours déjà utilisée dans la commande
    if ($order instanceof Miki_order){
      // récupert tous les articles de la commande
      $articles = $order->get_all_articles();
      
      // parcourt ces articles
      foreach($articles as $article){
        if ($article->id != $id_article_not_to_use && $article->has_option($this->id))
         $quantity -= $article->nb;
      }
    }
    
    return $quantity;
  }
  
  /**
   * Recherche des options
   * 
   * @param int $id_shop Si != "", récupère seulement celles du shop dont l'id est spécifié
   * @param string $search Critères de recherche. Recherche dans le nom (name) et la référence (ref) de l'option
   * @param string $lang Si != "", recherche uniquement dans la langue dont le code est donné      
   * @param int $valid Si = true, récupère seulement les options disponibles (state = 1)
   * @param int $in_stock Si = true, récupère seulement les options en stock ou qui n'utilisent pas le stock
   * @param string $order Par quel champ les options trouvées seront triées (ref, name, state, price, quantity). Si vide, on tri selon leur nom.
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)               
   *
   * @static         
   * @return mixed Un tableau d'éléments de type Miki_shop_article_option représentant les options trouvées 
   */      
  public static function search($id_shop = "", $search = "", $lang = "", $valid = true, $in_stock = true, $order = "", $order_type = "asc"){
    $return = array();
    
    $sql = "SELECT DISTINCT ma.id
            FROM miki_shop_article_option ma, 
                 miki_shop_article_option_name man
            WHERE man.id_option = ma.id";
    
    if ($id_shop !== "" && is_numeric($id_shop))
      $sql .= sprintf(" AND ma.id_shop = %d",
                mysql_real_escape_string($id_shop));
    
    // recherche dans le nom et la référence de l'option
    if ($search !== ""){
      $search = mb_strtolower($search, 'UTF-8');
                
      $sql .= sprintf(" AND (LOWER(man.name) LIKE '%%%s%%' OR 
                             LOWER(ma.ref) LIKE '%%%s%%')",
                mysql_real_escape_string($search),
                mysql_real_escape_string($search));
    }
    
    // spécifie la langue dans laquelle on recherche les articles
    if ($lang !== ""){
      $sql .= sprintf(" AND man.language = '%s'",
                mysql_real_escape_string($lang));
    }
    else{
      $main_language = Miki_language::get_main_code();
      $sql .= sprintf(" AND man.language = '%s'",
                mysql_real_escape_string($main_language));
    }
    
    if ($valid){
      $sql .= " AND ma.state = 1";
    }
    
    if ($in_stock){
      $sql .= " AND (ma.use_stock = 0 OR ma.quantity > 0)";
    }
    
    if ($order == "ref")
      $sql .= sprintf(" ORDER BY LCASE(ma.ref) COLLATE utf8_general_ci %s",  mysql_real_escape_string($order_type));
    elseif ($order == "name")
      $sql .= sprintf(" ORDER BY LCASE(man.name) COLLATE utf8_general_ci %s",  mysql_real_escape_string($order_type));
    elseif ($order == "state")
      $sql .= sprintf(" ORDER BY LCASE(ma.state) %s",  mysql_real_escape_string($order_type));
    elseif ($order == "price")
      $sql .= sprintf(" ORDER BY LCASE(ma.price) %s",  mysql_real_escape_string($order_type));
    elseif ($order == "quantity")
      $sql .= sprintf(" ORDER BY LCASE(ma.quantity) %s",  mysql_real_escape_string($order_type));
    else
      $sql .= sprintf(" ORDER BY LCASE(man.name) COLLATE utf8_general_ci %s",  mysql_real_escape_string($order_type));

    $result = mysql_query($sql) or die("Erreur : $sql");
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_shop_article_option($row['id']);
    }
    return $return;
  }
  
  /**
   * Recherche le nombre d'options total
   * 
   * @param int $id_shop Si != "", recherche seulement les options dans le shop dont l'id est spécifié
   *      
   * @static
   * @return int   
   */   
  public static function get_nb_options($id_shop = ""){
    $sql = "SELECT count(*) FROM miki_shop_article_option ma WHERE 1";
    
    if ($id_shop !== "" && is_numeric($id_shop))
      $sql .= sprintf(" AND id_shop = %d", mysql_real_escape_string($id_shop));
    
    $result = mysql_query($sql);
    
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * fonction statique récupérant toutes les options (seulement celles du shop spécifié si $id_shop != "")
   * 
   * @param int $id_shop Si != "", recherche seulement les options dans le shop dont l'id est spécifié
   *      
   * @static
   * @return mixed Un tableau d'éléments de type Miki_shop_article_option représentant les options trouvées   
   */ 
  public static function get_all_options($id_shop = ""){
    $return = array();
    $sql = "SELECT id FROM miki_shop_article_option";
    
    if ($id_shop !== "" && is_numeric($id_shop))
      $sql .= sprintf(" WHERE id_shop = %d", mysql_real_escape_string($id_shop));
    
    $result = mysql_query($sql) or die("Erreur sql : <br />$sql");
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_shop_article_option($row[0]);
    }
    return $return;
  }
}
?>