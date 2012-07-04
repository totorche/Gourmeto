<?php
/**
 * Classe Miki_social_group
 * @package Miki
 */ 

/**
 * Inclut les fonctions d'importation et de traitement des images
 */ 
require_once("functions_pictures.php");

/**
 * Représentation d'un groupe de discussion.
 * 
 * Un groupe de discussion est composées de différents articles (Miki_social_group_article) écrits par les membres du site Internet.
 * 
 * Différentes règles (visibilité, lecture, écriture) peuvent être définies pour chaque groupe de discussion.
 * 
 * L'inscription au groupe peut être soumise à validation.
 * 
 * Un groupe de discussion est géré par un administrateur et peut également avoir des co-administrateur qui ont la possibilité de gérer, p.ex. les inscriptions au groupe.    
 *   
 * 
 * @see Miki_social_group_article
 *  
 * @package Miki  
 */
class Miki_social_group{

  /**
   * Id du groupe
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom du groupe
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Description du groupe
   *      
   * @var string
   * @access public   
   */
  public $description;
  
  /**
   * Catégorie du groupe
   *      
   * @var int
   * @access public   
   */
  public $category;
  
  /**
   * Image du groupe
   *      
   * @var string
   * @access public   
   */
  public $picture;
  
  /**
   * Langue du groupe
   *      
   * @var string
   * @access public   
   */
  public $language;
  
  /**
   * Administrateur du groupe
   *      
   * @var int
   * @access public   
   */
  public $administrator;
  
  /**
   * Rèble de visibilité du groupe
   *      
   * @var int
   * @access public   
   */
  public $view_rule;
  
  /**
   * Règle de lecture du groupe
   *      
   * @var int
   * @access public   
   */
  public $read_rule;
  
  /**
   * Règle d'écriture du groupe
   *      
   * @var int
   * @access public   
   */
  public $write_rule;
  
  /**
   * Si = 1, l'inscription au groupe est soumise à validation. Si = 0, pas de validation.
   *      
   * @var int
   * @access public   
   */
  public $inscription_approbation;
  
  /**
   * Date de création du groupe
   *      
   * @var string
   * @access public   
   */
  public $date_created;
  
  /**
   * Répertoire de stockage des images des groupes
   *    
   * @var string
   * @access private      
   */     
  private $picture_path = "pictures/logo_social_groups/";
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le groupe dont l'id a été donné
   * 
   * @param int $id Id du groupe à charger (optionnel)
   */
  function __construct($id = ""){
    // charge le groupe si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un groupe depuis un id
   *    
   * Si le groupe n'existe pas, une exception est levée.
   *    
   * @param int $id id du groupe à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_social_group WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le groupe demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id                       = $row['id'];
    $this->name                     = $row['name'];
    $this->description              = $row['description'];
    $this->category                 = $row['category'];
    $this->picture                  = $row['picture'];
    $this->language                 = $row['language'];
    $this->administrator            = $row['administrator'];
    $this->view_rule                = $row['view_rule'];
    $this->read_rule                = $row['read_rule'];
    $this->write_rule               = $row['write_rule'];
    $this->inscription_approbation  = $row['inscription_approbation'];
    $this->date_created             = $row['date_created'];
    return true;
  }
  
  /**
   * Sauvegarde le groupe dans la base de données.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){      
    // si un l'id du groupe existe, c'est que le groupe existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    $sql = sprintf("INSERT INTO miki_social_group (name, description, category, picture, language, administrator, view_rule, read_rule, write_rule, inscription_approbation, date_created) VALUES('%s', '%s', %d, '%s', %d, %d, %d, %d, %d, %d, NOW())",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->description),
      mysql_real_escape_string($this->category),
      mysql_real_escape_string($this->picture),
      mysql_real_escape_string($this->language),
      mysql_real_escape_string($this->administrator),
      mysql_real_escape_string($this->view_rule),
      mysql_real_escape_string($this->read_rule),
      mysql_real_escape_string($this->write_rule),
      mysql_real_escape_string($this->inscription_approbation));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion du groupe dans la base de données : ") ."<br />$sql<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la page
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour le groupe dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){      
    // si aucun id existe, c'est que le groupe n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
    
    $sql = sprintf("UPDATE miki_social_group SET name = '%s', description = '%s', category = '%s', picture = '%s', language = '%s', administrator = %d, view_rule = %d, read_rule = %d, write_rule = %d, inscription_approbation = %d WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->description),
      mysql_real_escape_string($this->category),
      mysql_real_escape_string($this->picture),
      mysql_real_escape_string($this->language),
      mysql_real_escape_string($this->administrator),
      mysql_real_escape_string($this->view_rule),
      mysql_real_escape_string($this->read_rule),
      mysql_real_escape_string($this->write_rule),
      mysql_real_escape_string($this->inscription_approbation),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du groupe dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime le groupe
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_social_group WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du groupe : ") ."<br />" .mysql_error());
    
    // supprime le logo  
    $this->delete_picture();
    
    return true;
  }
  
  /**
   * Supprime l'image du groupe
   * 
   * Supprime l'image du groupe ainsi que le fichier de cette image.
   *       
   * Si le fichier représentant l'image n'existe pas, une exception est levée
   * 
   * @return boolean
   */ 
  public function delete_picture(){
    if ($this->picture == "")
      return;
      
    // pour l'image
    $path = $this->picture_path .$this->picture;
    if (file_exists($path)){
      if (!unlink($path)){
        throw new Exception("Erreur pendant la suppression de l'image");
      }
    }
    
    $this->picture = "";
    $this->update();
  }
  
  /**
   * Recherche les co-administrateurs (membres du site - Miki_person) du groupe
   *
   * Si une erreur survient, une exception est levée.
   * 
   * @see Miki_person      
   *      
   * @return mixed Un tableau d'éléments Miki_person représentant les co-administrateurs du groupe
   */      
  public function get_coadministrators(){
    $return = array();
    $sql = sprintf("SELECT miki_person_id FROM miki_social_group_coadministrator WHERE miki_social_group_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la récupération des co-administrateurs : ") ."<br />" .mysql_error());
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_person($row[0]);
    }
    return $return;
  }
  
  /**
   * Ajoute un co-administrateur (membres du site - Miki_person) au groupe
   *
   * Si une erreur survient, une exception est levée.
   * 
   * @see Miki_person      
   *      
   * @return boolean
   */ 
  public function add_coadministrator($person_id){
    $sql = sprintf("INSERT INTO miki_social_group_coadministrator(miki_social_group_id, miki_person_id) values (%d, %d)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($person_id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'ajout du co-administrateur dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Inscription d'un membre (Miki_person) au groupe
   * 
   * Si l'inscription au groupe est soumise à validation (inscription_approbation), l'inscription du membre est mise en attente et l'administrateur est averti par e-mail.
   * Si une erreur survient, une exception est levée.
   *      
   * @param int $person_id Id du membre (Miki_person) à inscrire au groupe
   * 
   * @see Miki_person
   * @return boolean               
   */   
  public function subscribe($person_id){
    if ($this->inscription_approbation == 0)
      $state = 1;
    else
      $state = 0;
      
    $sql = sprintf("INSERT INTO miki_social_group_inscription (id_social_group, id_person, state) values (%d, %d, %d)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($person_id),
      mysql_real_escape_string($state));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur pendant l'inscription : ") ."<br />" .mysql_error());
    
    // si l'inscription doit être approuvée, on envoit un mail à l'adminisrateur
    if ($state == 0){
      $sitename = Miki_configuration::get('sitename');
      $site_url = Miki_configuration::get('site_url');
      $email_answer = Miki_configuration::get('email_answer');
      
      $admin = new Miki_person($this->administrator);
      $company_admin = new Miki_company($admin->company_id);
      $person = new Miki_person($person_id);
      $company_person = new Miki_company($person->company_id);
      
      // création du mail
      $mail = new miki_email('inscription_social_group', $admin->language);
    
      $mail->From     = $email_answer;
      $mail->FromName = $sitename;
      
      // prépare les variables nécessaires à la création de l'e-mail
      $vars_array['person_to'] = $admin;
      $vars_array['person_from'] = $person;
      $vars_array['company_from'] = $company_person;
      $vars_array['group'] = $this;
      $vars_array['sitename'] = $sitename;
      $vars_array['site_url'] = $site_url;
      
      // initialise le contenu de l'e-mail
      $mail->init($vars_array);
      
      $mail->AddAddress($admin->email1);

      if(!$mail->Send())
        throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
      $mail->ClearAddresses();
    }
    
    return true;
  }
  
  /**
   * Valide une inscription au groupe.
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @param int $person_id Id du membre (Miki_person) dont on doit valider l'inscription.
   * 
   * @return boolean            
   */   
  public function active_inscription($person_id){
    $sql = sprintf("UPDATE miki_social_group_inscription SET state = 1 WHERE id_social_group = %d AND  id_person = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($person_id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur pendant la validation de l'inscription : ") ."<br />" .mysql_error());
    
    $person = new Miki_person($person_id);
    $company = new Miki_company($person->company_id);
    
    $sitename = Miki_configuration::get('sitename');
    $site_url = Miki_configuration::get('site_url');
    $email_answer = Miki_configuration::get('email_answer');
    
    // création du mail
    $mail = new miki_email('validation_inscription_social_group', $person->language);
  
    $mail->From     = $email_answer;
    $mail->FromName = $sitename;
    
    // prépare les variables nécessaires à la création de l'e-mail
    $vars_array['person_to'] = $person;
    $vars_array['group'] = $this;
    $vars_array['sitename'] = $sitename;
    $vars_array['site_url'] = $site_url;
    
    // initialise le contenu de l'e-mail
    $mail->init($vars_array);

    $mail->AddAddress($person->email1);

    if(!$mail->Send())
      throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
    $mail->ClearAddresses();
    
    return true;
  }
  
  /**
   * Désinscription d'un membre (Miki_person) du groupe.
   * 
   * @param int $person_id Id du membre (Miki_person) a désinscrire.
   * @param string $remark   
   * 
   * @see Miki_person             
   */   
  public function unsubscribe($person_id){
    $sql = sprintf("DELETE FROM miki_social_group_inscription WHERE id_social_group = %d AND id_person = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($person_id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur pendant la désinscription : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Vérifie si un membre du site (Miki_person) est membre du groupe.
   * 
   * @param int $person_id Id du membre à tester.
   *      
   * @return boolean      
   */     
  public function is_subscribed($person_id){
    $sql = sprintf("SELECT state FROM miki_social_group_inscription WHERE id_social_group = %d AND id_person = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($person_id));
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    if (mysql_num_rows($result) == 0)
      return 0;
    if ($row[0] == 0)
      return 1;
    else
      return 2;
  }
  
  /**
   * Recherche le nombre d'inscrits au groupe
   * 
   * @param int $state si = 1, ne retourne que les personne dont l'inscription est effective. Si =0, ne retourne que les personne dont l'inscription n'est pas encore validée. Si = "", retourne tout le monde.
   * 
   * @return int            
   */   
  public function get_nb_participants($state = ""){
    $sql = sprintf("SELECT count(*) FROM miki_social_group_inscription WHERE id_social_group = %d",
      mysql_real_escape_string($this->id));
      
    if ($state !== "" && is_numeric($state))
      $sql .= " AND  state = $state";
      
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Recherche les membres inscrits au groupe
   * 
   * @param int $state si = 1, ne retourne que les personne dont l'inscription est effective. Si =0, ne retourne que les personne dont l'inscription n'est pas encore validée. Si = "", retourne tout le monde.
   * 
   * @return mixed Un tableau d'éléments de type Miki_person représentant les membres du groupe.            
   */
  public function get_participants($state = ""){
    $return = array();
    $sql = sprintf("SELECT id_person FROM miki_social_group_inscription WHERE id_social_group = %d",
      mysql_real_escape_string($this->id));
    
    if ($state !== "" && is_numeric($state))
      $sql .= " AND  state = $state";

    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_person($row[0]);
    }
    return $return;
  }
  
  /**
   * envoi des invitations pour le groupe aux personnes en paramètre.
   * 
   * @param Miki_person $from Membre qui envoit l'invitation.
   * @param mixed $to Un tableau d'éléments Miki_person représentant les membres à qui on veut envoyer l'invitation.
   */     
  public function send_invit($from, $to){
    if (!is_array($to))
      $to[] = $to;
    
    $sitename = Miki_configuration::get('sitename');
    $site_url = Miki_configuration::get('site_url');
    $email_answer = Miki_configuration::get('email_answer');
    
    foreach($to as $p){
      $company_from = new Miki_company($from->company_id);
      $company_to = new Miki_company($p->company_id);
      
      // création du mail
      $mail = new miki_email('validation_inscription_social_group', $p->language);
    
      $mail->From     = $email_answer;
      $mail->FromName = $sitename;
      
      // prépare les variables nécessaires à la création de l'e-mail
      $vars_array['person_to'] = $p;
      $vars_array['company_from'] = $company_from;
      $vars_array['group'] = $this;
      $vars_array['sitename'] = $sitename;
      $vars_array['site_url'] = $site_url;
      
      // initialise le contenu de l'e-mail
      $mail->init($vars_array);
      
      $mail->AddAddress($p->email1);

      if(!$mail->Send())
        throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
      $mail->ClearAddresses();
    }
  }
  
  /**
   * Ajoute une image au groupe
   * 
   * Ajoute une image au groupe et la redimensionne (maximum 100x100px)
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   * @param string $nom_destination Nom de destination de l'image finale
   * @return boolean
   */
  public function upload_picture($fichier, $nom_destination){  	
    
    // traite le nom de destination
    $nom_destination = decode($nom_destination);  
    $system = explode('.',mb_strtolower($fichier['name'], 'UTF-8'));
    $ext = $system[sizeof($system)-1];
    $nom_destination = $nom_destination ."." .$ext;  

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
  	createthumb($this->picture_path .$nom_destination, "./" .$nom_destination, 100, 100, true);
  	
  	$this->picture = $nom_destination;
  	return true;
  }
  
  /**
   * Récupert le dernier article (Miki_social_group_article) écrit dans le groupe
   * 
   * @return Miki_social_group_article      
   */      
  public function get_last_article(){
    $sql = sprintf("SELECT id FROM miki_social_group_article WHERE id_social_group = %d ORDER BY date DESC LIMIT 0,1",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return new Miki_social_group_article($row[0]);
  }
  
  /**
   * Récupert le dernier article (Miki_social_group_article) écrit dans un groupe de la catégorie donnée.
   * 
   * @param int $category Catégorie dans laquelle on veut récupérer le dernier article écrit.
   *      
   * @static   
   * @return Miki_social_group_article      
   */ 
  public static function get_last_article_by_category($category){
    $sql = sprintf("SELECT id 
                    FROM miki_social_group_article 
                    WHERE id_social_group IN (SELECT id FROM miki_social_group WHERE category = %d)
                    ORDER BY date DESC LIMIT 0,1",
      mysql_real_escape_string($category));
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return new Miki_social_group_article($row[0]);
  }
  
  /**
   * Recherche le nombre d'articles présents dans le groupe
   * 
   * @return int      
   */   
  public function get_nb_articles(){
    $sql = sprintf("SELECT count(*) FROM miki_social_group_article WHERE id_social_group = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Recherche le nombre d'articles total dans la catégorie donnée.
   * 
   * @param int $category Catégorie dans laquelle on veut rechercher le nombre d'articles.
   * 
   * @static   
   * @return int               
   */   
  public static function get_nb_articles_by_category($category){
    $sql = sprintf("SELECT COUNT(*) FROM miki_social_group_article WHERE id_social_group IN (SELECT id FROM miki_social_group WHERE category = %d)",
      mysql_real_escape_string($category));
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Recherche le nombre de groupes faisant partie de la catégorie donnée.
   * 
   * @param int $category Catégorie dans laquelle on veut rechercher le nombre de groupes.     
   * 
   * @static   
   * @return int       
   */   
  public static function get_nb_groups($category){
    $sql = sprintf("SELECT count(*) FROM miki_social_group WHERE category = %d",
      mysql_real_escape_string($category));
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Recherche tous les groupes dont le nom ou la catégorie correspondent au critère donné.
   * 
   * @param string $search Critère de recherche pour le nom ou la catégorie du groupe.
   * @param int $person_id Si != "", on ne récupert que les groupes auxquels la personne donnée est abbonnée.
   * 
   * @static    
   * @return mixed Un tableau d'éléments de type Miki_social_group représentant les groupes récupérés.     
   */   
  public static function search($search, $person_id = ""){
    $return = array();
    $search = mb_strtolower($search, 'UTF-8');
    
    if ($person_id != "" && is_numeric($person_id)){
      if ($search != ""){
        $sql = sprintf("SELECT miki_social_group.* FROM 
                        miki_social_group, miki_social_group_inscription WHERE 
                        miki_social_group_inscription.id_social_group = miki_social_group.id AND 
                        miki_social_group_inscription.id_person = %d AND 
                        (LOWER(name) LIKE '%%%s%%' OR 
                         LOWER(category) LIKE '%%%s%%')",
                mysql_real_escape_string($person_id),
                mysql_real_escape_string($search),
                mysql_real_escape_string($search));
      }
      else{
        $sql = sprintf("SELECT miki_social_group.* FROM 
                        miki_social_group, miki_social_group_inscription WHERE 
                        miki_social_group_inscription.id_social_group = miki_social_group.id AND 
                        miki_social_group_inscription.id_person = %d",
                mysql_real_escape_string($person_id));
      }
    }
    else{
      if ($search != ""){
        $sql = sprintf("SELECT * FROM miki_social_group WHERE LOWER(name) LIKE '%%%s%%' OR LOWER(category) LIKE '%%%s%%'",
                mysql_real_escape_string($search),
                mysql_real_escape_string($search));
      }
      else{
        $sql = "SELECT * FROM miki_social_group";
      }
    }
      
    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_social_group($row[0]);
    }
    return $return;
  }

  /**
   * Recherche les groupes
   * 
   * @param int $person_id Si != "", on ne récupert que les groupes auxquels la personne donnée est abbonnée.
   * @param int $category_id Si != "", on ne récupert que les groupes dans la catégorie spécifiée.   
   * 
   * @static    
   * @return mixed Un tableau d'éléments de type Miki_social_group représentant les groupes récupérés.     
   */ 
  public static function get_all_social_groups($person_id = "", $category_id = ""){
    $return = array();
    
    if ($person_id != "" && is_numeric($person_id)){
        $sql = sprintf("SELECT id 
                        FROM miki_social_group
                        WHERE administrator = %d or
                              id in (SELECT id_social_group FROM miki_social_group_inscription WHERE id_person = %d)",
        mysql_real_escape_string($person_id),
        mysql_real_escape_string($person_id));
    }
    elseif ($category_id != "" && is_numeric($category_id)){
      $sql = sprintf("SELECT id FROM miki_social_group WHERE category = %d",
        mysql_real_escape_string($category_id));
    }
    else
      $sql = "SELECT id FROM miki_social_group";
      
    $sql .= " ORDER BY id DESC";

    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $item = new Miki_social_group($row[0]);
      $return[] = $item;
    }
    return $return;
  }
}
?>