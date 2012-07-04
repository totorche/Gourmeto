<?php
/**
 * Classe Miki_redaction
 * @package Miki 
 */ 


/**
 * Inclut les fonctions d'importation et de traitement des images
 */ 
require_once("functions_pictures.php");


/**
 * Représentation d'un texte à rédiger pour un client (Miki_person)
 * 
 * @see Miki_user
 * @package Miki
 */ 
class Miki_redaction{

  /**
   * Id du texte
   * @var int
   * @access public
   */           
  public $id;
  
  /**
   * Id de la personne (Miki_person) ayant commandé le texte
   * @var int
   * @access public
   */
  public $id_person;
  
  /**
   * Etat du texte (0 = non-rédigé, 1 = rédigé)
   * @var int
   * @access public
   */
  public $state;
  
  /**
   * Titre du texte
   * @var string
   * @access public
   */
  public $title;
  
  /**
   * Tableau contenant les mots-clés du texte
   * 
   * Un texte peut contenir plusieurs mots-clés.
   * Ceux-ci sont regroupés ici dans un tableau.
   *         
   * @var mixed
   * @access public
   */
  public $keywords;
  
  /**
   * Nombre de mots devant être présents dans le texte
   *         
   * @var int
   * @access public
   */
  public $nb_words;
  
  /**
   * Tableau contenant les images liées au texte
   * 
   * Un texte peut contenir plusieurs images.
   * Celles-ci sont regroupées ici dans un tableau.
   *         
   * @var mixed
   * @access public
   */
  public $pictures;
  
  /**
   * Commentaires donnés par le client 
   * 
   * @var string
   * @access public
   */
  public $comment;
  
  /**
   * Fichier envoyé par le rédacteur
   * 
   * @var string
   * @access public
   */
  public $file_sent;
  
  /**
   * Date à laquelle le texte a été commandé
   * @var string
   * @access public
   */
  public $date_created;
  
  /**
   * Date à laquelle le texte a été envoyé au client
   * @var string
   * @access public
   */
  public $date_published;
  
  /**
   * Répertoire où sont situées les images du texte
   *      
   * @var string
   * @access private   
   */
  private $picture_path = "../pictures/redactions/";
  
  
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le texte dont l'id a été donné
   * 
   * @param int $id Id du texte à charger (optionnel)
   */         
  function __construct($id = ""){
    // charge le texte si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un texte depuis un id.
   *    
   * Si le texte n'existe pas, une exception est levée.
   *    
   * @param int $id id du texte à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_redaction WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le texte demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->id_person = $row['id_person'];
    $this->state = $row['state'];
    $this->title = $row['title'];
    $this->keywords = explode("&&", $row['keywords']);
    $this->nb_words = $row['nb_words'];
    $this->pictures = explode("&&", $row['pictures']);
    $this->comment = $row['comment'];
    $this->file_sent = $row['file_sent'];
    $this->date_created = $row['date_created'];
    $this->date_published = $row['date_published'];
    
    // traite les mots-clés
    $tab_temp = array();
    foreach($this->keywords as $f){
      if ($f != ""){
        $tab_temp[] = $f;
      }
    }
    $this->keywords = $tab_temp;
    
    // traite les images
    $tab_temp = array();
    foreach($this->pictures as $f){
      if ($f != ""){
        $tab_temp[] = $f;
      }
    }
    $this->pictures = $tab_temp;
    
    return true;
  }
  
  
  /**
   * Sauvegarde le texte dans la base de données.
   *    
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   *    
   * Si une erreur survient, une exception est levée      
   *    
   * @return boolean   
   */
  public function save(){
    // si un l'id du texte existe, c'est que le texte existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
    
    // traite les mots-clés et les images
    $keywords = implode("&&", $this->keywords);
    $pictures = implode("&&", $this->pictures);
      
    $sql = sprintf("INSERT INTO miki_redaction (id_person, state, title, keywords, nb_words, pictures, comment, file_sent, date_created, date_published) VALUES(%d, %d, '%s', '%s', %d, '%s', '%s', 'NULL', NOW(), 'NULL')",
      mysql_real_escape_string($this->id_person),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->title),
      mysql_real_escape_string($keywords),
      mysql_real_escape_string($this->nb_words),
      mysql_real_escape_string($pictures),
      mysql_real_escape_string($this->comment));
    $result = mysql_query($sql);

    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion du texte dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la page
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour le texte dans la base de données.
   *    
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si une erreur survient, une exception est levée   
   *    
   * @return boolean   
   */
  public function update(){
    // si aucun id existe, c'est que le texte n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // traite les mots-clés et les images
    $keywords = implode("&&", $this->keywords);
    $pictures = implode("&&", $this->pictures);
      
    $sql = sprintf("UPDATE miki_redaction SET id_person = %d, state = %d, title = '%s', keywords = '%s', nb_words = %d, pictures = '%s', comment = '%s', file_sent = '%s' WHERE id = %d",
      mysql_real_escape_string($this->id_person),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->title),
      mysql_real_escape_string($keywords),
      mysql_real_escape_string($this->nb_words),
      mysql_real_escape_string($pictures),
      mysql_real_escape_string($this->comment),
      mysql_real_escape_string($this->file_sent),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du texte dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime le texte
   * 
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */     
  public function delete(){
    $sql = sprintf("DELETE FROM miki_redaction WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du texte : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Ajoute une image au texte
   * 
   * Ajoute une image au texte, la redimensionne (maximum 1500x1500px) et créé une miniature (maximum 300x300px)
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   * @return boolean
   */
  public function upload_picture($fichier){  	
    
    // traite le nom de destination (correspond au titre de l'activité dans la langue principale)
    $nom_destination = decode($this->title);  
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
   * Envoi un e-mail à l'administrateur du site
   */   
  public function send(){
    $sitename = Miki_configuration::get('sitename');
    $site_url = Miki_configuration::get('site_url');
    $email_answer = Miki_configuration::get('email_answer');
    
    // récupert les sociétés émettrice et réceptrice
    $person_from = new Miki_person($this->id_person);

    // création du mail à destination de l'administrateur
    $mail = new miki_email('envoi_redaction', 'fr');
  
    $mail->From     = $email_answer;
    $mail->FromName = $sitename;
    
    // prépare les variables nécessaires à la création de l'e-mail
    $vars_array['person_from'] = $person_from;
    $vars_array['redaction'] = $this;
    $vars_array['sitename'] = $sitename;
    $vars_array['site_url'] = $site_url;
    
    // initialise le contenu de l'e-mail
    $mail->init($vars_array);

    $mail->AddAddress($email_answer);
    //$mail->AddAddress("herve@fbw-one.com");
    
    if(!$mail->Send())
      throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
    $mail->ClearAddresses();
    
    
    // création du mail à destination du client
    $mail = new miki_email('envoi_redaction_client', 'fr');
  
    $mail->From     = $email_answer;
    $mail->FromName = $sitename;
    
    // prépare les variables nécessaires à la création de l'e-mail
    $vars_array['person_to'] = $person_from;
    $vars_array['redaction'] = $this;
    $vars_array['sitename'] = $sitename;
    $vars_array['site_url'] = $site_url;
    
    // initialise le contenu de l'e-mail
    $mail->init($vars_array);

    $mail->AddAddress($person_from->email1);
    //$mail->AddAddress("herve@fbw-one.com");
    
    if(!$mail->Send())
      throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
    $mail->ClearAddresses();
  }
  
  /** 
   * Recherche tous les textes selon certains critères
   * 
   * @param mixed $search Critère de recherche qui sera appliqué sur le titre, le nom et le prénom du client   
   * @param int $id_person Id de la personne dont on veut récupérer les textes. Si vide, récupert tous les textes.
   * @param int $state Etat des textes (0 = non-rédigé, 1 = rédigé) que l'on veut récupérer. Si vide, récupert tous les textes.    
   *      
   * @static
   * @return mixed un tableau contenant tous les textes récupérés
   */            
  public static function search($search = "", $id_person = "", $state = "", $order = "", $order_type = "asc", $nb = "", $page = 1){
    $return = array();
    $sql = "SELECT mr.* FROM miki_redaction mr, miki_person mp WHERE mp.id = mr.id_person";
    
    // Applique les critères de recherche
    if ($search != ""){
      $search = mb_strtolower($search, 'UTF-8');
      $sql .= sprintf(" AND (LOWER(mr.title) LIKE '%%%s%%' OR 
                            LOWER(mp.firstname) LIKE '%%%s%%' OR
                            LOWER(mp.lastname) LIKE '%%%s%%')",
                mysql_real_escape_string($search),
                mysql_real_escape_string($search),
                mysql_real_escape_string($search));
    }
    
    if ($id_person != "" && is_numeric($id_person)){
      $sql .= sprintf(" AND id_person = %d", 
        mysql_real_escape_string($id_person));
    }
    
    if ($state != "" && is_numeric($state)){
      $sql .= sprintf(" AND state = %d", 
        mysql_real_escape_string($state));
    }
    
    if ($order == "title")
      $sql .= " ORDER BY LCASE(mr.title) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "person")
      $sql .= " ORDER BY LCASE(mp.lastname) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "nb_words")
      $sql .= " ORDER BY mr.nb_words " .$order_type;
    elseif ($order == "date_created")
      $sql .= " ORDER BY mr.date_created " .$order_type;
    elseif ($order == "date_published")
      $sql .= " ORDER BY mr.date_published " .$order_type;
    else
      $sql .= " ORDER BY mr.date_created " .$order_type;
    
    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }

    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_redaction($row['id']);
    }
    return $return;
  }
  
  /** 
   * Récupère tous les textes
   * 
   * @param int $id_person Id de la personne dont on veut récupérer les textes. Si vide, récupert tous les textes.
   * @param int $state Etat des textes (0 = non-rédigé, 1 = rédigé) que l'on veut récupérer. Si vide, récupert tous les textes.    
   *      
   * @static
   * @return mixed un tableau contenant tous les textes récupérés
   */            
  public static function get_all_redactions($id_person = "", $state = ""){
    $return = array();
    $sql = "SELECT * FROM miki_redaction WHERE 1";
    
    if ($id_person != "" && is_numeric($id_person)){
      $sql .= sprintf(" AND id_person = %d", 
        mysql_real_escape_string($id_person));
    }
    
    if ($state != "" && is_numeric($state)){
      $sql .= sprintf(" AND state = %d", 
        mysql_real_escape_string($state));
    }
    
    $sql .= "ORDER BY title ASC";

    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Aucun texte n'est présent dans la base de données"));
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_redaction($row['id']);
    }
    return $return;
  }
}
?>