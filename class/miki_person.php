<?php
/**
 * Classe Miki_person
 * @package Miki
 */
 
/**
 * Inclut les fonctions d'importation et de traitement des images
 */ 
require_once("functions_pictures.php"); 

/**
 * Représentation d'un membre du site Internet.
 * 
 * Un membre est liée à un compte utilisateur (Miki_account).
 * Un membre peut également être lié à une société, entreprise ou autre (Miki_company).
 * 
 * Un membre peut posséder une photo de profil ainsi qu'une collection de photos liées à lui.
 * La photo de profil liée au profil est stockées dans le champ "picture".
 * Les autres photos liées au membre sont ajoutée via la fonction "add_picture" et récupérées via "get_pictures".
 * 
 * @see Miki_account
 * @see Miki_company 
 *  
 * @package Miki  
 */
class Miki_person{
  
  /**
   * Id du membre
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Genre du membre (Monsieur, Madame, Mademoiselle, etc.)
   *      
   * @var string
   * @access public   
   */
  public $type;
  
  /**
   * Prénom du membre
   *      
   * @var string
   * @access public   
   */
  public $firstname;
  
  /**
   * Nom de famille du membre
   *      
   * @var string
   * @access public   
   */
  public $lastname;
  
  /**
   * Adresse du membre
   *      
   * @var string
   * @access public   
   */
  public $address;
  
  /**
   * Numéro postal du membre
   *      
   * @var int
   * @access public   
   */
  public $npa;
  
  /**
   * Localité du membre
   *      
   * @var string
   * @access public   
   */
  public $city;
  
  /**
   * Département (ou région) du membre
   *      
   * @var string
   * @access public   
   */
  public $dept;
  
  /**
   * Pays du membre
   *      
   * @var string
   * @access public   
   */
  public $country;
  
  /**
   * Numéro de téléphone 1 du membre
   *      
   * @var string
   * @access public   
   */
  public $tel1;
  
  /**
   * Numéro de téléphone 2 du membre
   *      
   * @var string
   * @access public   
   */
  public $tel2;
  
  /**
   * Numéro de fax du membre
   *      
   * @var string
   * @access public   
   */
  public $fax;
  
  /**
   * Adresse e-mail 1 du membre
   *      
   * @var string
   * @access public   
   */
  public $email1;
  
  /**
   * Adresse e-mail 2 du membre
   *      
   * @var string
   * @access public   
   */
  public $email2;
  
  /**
   * Status du membre
   *      
   * @var string
   * @access public   
   */
  public $status;
  
  /**
   * Nombre d'enfants du membre
   *      
   * @var int
   * @access public   
   */
  public $nb_children;
  
  /**
   * Date d'anniversaire du membre
   *      
   * @var string
   * @access public   
   */
  public $birthday;
  
  /**
   * Code de la langue parlée par le membre du membre. Code ISO 639-1 ({@link http://fr.wikipedia.org/wiki/Liste_des_codes_ISO_639-1})
   *      
   * @var string
   * @access public   
   */
  public $language;
  
  /**
   * Travail du membre
   *      
   * @var int
   * @access public   
   */
  public $job;
  
  /**
   * Id de la société, entreprise ou autre auquel le membre appartient.
   *      
   * @var int
   * @access public   
   */
  public $company_id;
  
  /**
   * Photo de profil du membre
   *      
   * @var int
   * @access public   
   */
  public $picture;
  
  /**
   * Autres informations (toutes regroupées)
   *      
   * @var mixed
   * @access public   
   */
  public $others;
  
  /**
   * Numéro du membre
   *      
   * @var string
   * @access public   
   */
  public $number;
  
  /**
   * Id du membre qui a sponsorisé ce membre
   *      
   * @var int
   * @access public   
   */
  public $sponsor = 'NULL';
  
  /**
   * Site web du membre
   *      
   * @var string
   * @access public   
   */
  public $web;
  
  /**
   * Répertoire où est située la photo du membre
   *      
   * @var string
   * @access private   
   */
  private $picture_path = "pictures/persons/";
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le membre dont l'id a été donné
   * 
   * @param int $id Id du membre à charger (optionnel)
   */
  function __construct($id = ""){
    if (!file_exists($this->picture_path)){
      $this->picture_path = '../' .$this->picture_path;
      if (!file_exists($this->picture_path)){
        $this->picture_path = '';
      }
    }
  
    // charge la personne si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un membre depuis un id
   *    
   * Si le membre n'existe pas, une exception est levée.
   *    
   * @param int $id id du membre à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_person WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La personne demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id           = $row['id'];
    $this->type         = $row['type'];
    $this->firstname    = $row['firstname'];
    $this->lastname     = $row['lastname'];
    $this->address      = $row['address'];
    $this->npa          = $row['npa'];
    $this->city         = $row['city'];
    $this->dept         = $row['dept'];
    $this->country      = $row['country'];
    $this->tel1         = $row['tel1'];
    $this->tel2         = $row['tel2'];
    $this->fax          = $row['fax'];
    $this->email1       = $row['email1'];
    $this->email2       = $row['email2'];
    $this->status       = $row['status'];
    $this->nb_children  = $row['nb_children'];
    $this->birthday     = $row['birthday'];
    $this->language     = $row['language'];
    $this->job          = $row['job'];
    $this->company_id   = $row['company_id'];
    $this->picture      = $row['picture'];
    $this->number       = $row['number'];
    $this->sponsor      = $row['sponsor'];
    $this->web          = $row['web'];
    
    // récupert les autres valeurs
    $this->others = array();
    $others = explode("&&", $row['others']);
    foreach($others as $o){
      if ($o != "" && strpos($o, "==") !== false){
        $temp = explode("==", $o);
        $this->others[$temp[0]] = $temp[1]; 
      }
    }
    
    return true;
  }
  
  /**
   * Charge un membre depuis son adresse e-mail (champ "email1")
   *    
   * Si le membre n'existe pas, une exception est levée.
   *    
   * @param string $email Adresse e-mail du membre à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load_from_email($email){
    $sql = sprintf("SELECT * FROM miki_person WHERE email1 = '%s'",
      mysql_real_escape_string($email));
    $result = mysql_query($sql);
    
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La personne demandée n'existe pas"));
    elseif (mysql_num_rows($result) > 1)
      throw new Exception(_("Il existe plusieurs personnes possédant la même adresse e-mail"));
    
    $row = mysql_fetch_array($result);
    $this->id           = $row['id'];
    $this->type         = $row['type'];
    $this->firstname    = $row['firstname'];
    $this->lastname     = $row['lastname'];
    $this->address      = $row['address'];
    $this->npa          = $row['npa'];
    $this->city         = $row['city'];
    $this->dept         = $row['dept'];
    $this->country      = $row['country'];
    $this->tel1         = $row['tel1'];
    $this->tel2         = $row['tel2'];
    $this->fax          = $row['fax'];
    $this->email1       = $row['email1'];
    $this->email2       = $row['email2'];
    $this->status       = $row['status'];
    $this->nb_children  = $row['nb_children'];
    $this->birthday     = $row['birthday'];
    $this->language     = $row['language'];
    $this->job          = $row['job'];
    $this->company_id   = $row['company_id'];
    $this->picture      = $row['picture'];
    $this->number       = $row['number'];
    $this->sponsor      = $row['sponsor'];
    $this->web          = $row['web'];
    
    // récupert les autres valeurs
    $this->others = array();
    $others = explode("&&", $row['others']);
    foreach($others as $o){
      if ($o != "" && strpos($o, "==") !== false){
        $temp = explode("==", $o);
        $this->others[$temp[0]] = $temp[1]; 
      }
    }
    
    return true;
  }
  
  /**
   * Sauvegarde le membre dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Le membre doit posséder une adresse e-mail (champ "email1") unique. Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){      
    // si un l'id de la personne existe, c'est que la personne existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // concatène les autres données
    $others = "";
    if (is_array($this->others)){
      foreach($this->others as $key => $value)
      $others .= "$key==$value&&"; 
    }
    else
      $others = "";
      
    if ($this->company_id == "")
      $this->company_id = "NULL";
      
    // vérifie que l'adresse e-mail1 n'existe pas déjà dans la base de données
    /*$sql = sprintf("SELECT id FROM miki_person WHERE email1 = '%s'",
      mysql_real_escape_string($this->email1));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Une personne avec la même adresse e-mail existe déjà dans la base de données"));*/ 
      
    $sql = sprintf("INSERT INTO miki_person (type, firstname, lastname, address, npa, city, dept, country, tel1, tel2, fax, email1, email2, status, nb_children, birthday, language, job, company_id, picture, others, number, sponsor, web) 
                    VALUES('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, '%s', '%s', '%s', %s, '%s', '%s', '%s', %s, '%s')",
      mysql_real_escape_string($this->type),
      mysql_real_escape_string($this->firstname),
      mysql_real_escape_string($this->lastname),
      mysql_real_escape_string($this->address),
      mysql_real_escape_string($this->npa),
      mysql_real_escape_string($this->city),
      mysql_real_escape_string($this->dept),
      mysql_real_escape_string($this->country),
      mysql_real_escape_string($this->tel1),
      mysql_real_escape_string($this->tel2),
      mysql_real_escape_string($this->fax),
      mysql_real_escape_string($this->email1),
      mysql_real_escape_string($this->email2),
      mysql_real_escape_string($this->status),
      mysql_real_escape_string($this->nb_children),
      mysql_real_escape_string($this->birthday),
      mysql_real_escape_string($this->language),
      mysql_real_escape_string($this->job),
      mysql_real_escape_string($this->company_id),
      mysql_real_escape_string($this->picture),
      mysql_real_escape_string($others),
      mysql_real_escape_string($this->number),
      mysql_real_escape_string($this->sponsor),
      mysql_real_escape_string($this->web));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion de la personne dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la page
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour le membre dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Le membre doit posséder une adresse e-mail (champ "email1") unique. Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){      
    // si aucun id existe, c'est que la personne n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // concatène les autres données
    $others = "";
    if (is_array($this->others)){
      foreach($this->others as $key => $value)
      $others .= "$key==$value&&"; 
    }
    else
      $others = "";
      
    if ($this->company_id == "")
      $this->company_id = "NULL";
    
    if ($this->sponsor == "")
      $this->sponsor = "NULL";
      
    // vérifie que l'adresse e-mail1 n'existe pas déjà dans la base de données
    /*$sql = sprintf("SELECT id FROM miki_person WHERE email1 = '%s' AND id != %d",
      mysql_real_escape_string($this->email1),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Une personne avec la même adresse e-mail existe déjà dans la base de données"));*/
      
    $sql = sprintf("UPDATE miki_person SET type = '%s', firstname = '%s', lastname = '%s', address = '%s', npa = '%s', city = '%s', dept = '%s', country = '%s', tel1 = '%s', tel2 = '%s', fax = '%s', email1 = '%s', email2 = '%s', status = '%s', nb_children = %d, birthday = '%s', language = '%s', job = '%s', company_id = %s, picture = '%s', others = '%s', number = '%s', sponsor = %s, web = '%s' WHERE id = %d",
      mysql_real_escape_string($this->type),
      mysql_real_escape_string($this->firstname),
      mysql_real_escape_string($this->lastname),
      mysql_real_escape_string($this->address),
      mysql_real_escape_string($this->npa),
      mysql_real_escape_string($this->city),
      mysql_real_escape_string($this->dept),
      mysql_real_escape_string($this->country),
      mysql_real_escape_string($this->tel1),
      mysql_real_escape_string($this->tel2),
      mysql_real_escape_string($this->fax),
      mysql_real_escape_string($this->email1),
      mysql_real_escape_string($this->email2),
      mysql_real_escape_string($this->status),
      mysql_real_escape_string($this->nb_children),
      mysql_real_escape_string($this->birthday),
      mysql_real_escape_string($this->language),
      mysql_real_escape_string($this->job),
      mysql_real_escape_string($this->company_id),
      mysql_real_escape_string($this->picture),
      mysql_real_escape_string($others),
      mysql_real_escape_string($this->number),
      mysql_real_escape_string($this->sponsor),
      mysql_real_escape_string($this->web),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour de la personne dans la base de données : <br /><br />$sql<br /><br />") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Ajoute une photo de profil au membre
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   * 
   * @return boolean      
   */ 
  public function add_picture_profil($fichier){
    try{
      $picture_name = $this->upload_picture($fichier);
      
      // si le membre possédait déjà une photo de profil on la supprime
      if ($this->picture != ""){
        $this->delete_picture($this->picture);
      }
      
      $this->picture = $picture_name;
      $this->update();
      return true;
    }
    catch(Exception $e){
      return false;
    }
  }
  
  /**
   * Ajoute une image liée au membre
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   * @param string $title Titre de l'image
   * 
   * @return boolean      
   */   
  public function add_picture($fichier, $title){
    try{
      $picture_name = $this->upload_picture($fichier);
      
      $sql = sprintf("INSERT INTO miki_person_picture (id_person, picture, title, date) values (%d, '%s', '%s', NOW())",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($picture_name),
        mysql_real_escape_string($title));
      if (!mysql_query($sql))
        throw new Exception(_("Erreur pendant l'ajout de la photo : ") ."<br />" .mysql_error());
      return true;
    }
    catch(Exception $e){
      return false;
    }
  }
  
  /**
   * Supprime une image liée au membre
   * 
   * @param int $id Id de l'image à supprimer
   * 
   * @return boolean      
   */   
  public function remove_picture($id){
    $sql = sprintf("DELETE FROM miki_person_picture WHERE id = %d",
      mysql_real_escape_string($id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la photo : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Récupert les images liées au membre
   * 
   * @return mixed Un tableau contenant les informations des images récupérées : id, picture (nom du fichier de l'image), title (titre de l'image), date (date d'ajout), poster (id du membre auquel l'image est liée)      
   */   
  public function get_pictures(){
    $sql = "SELECT id, picture, title, date FROM miki_person_picture WHERE id_person = $this->id";
    $result = mysql_query($sql);
    
    $return = array();
    
    while($row = mysql_fetch_array($result)){
      $item['id'] = $row[0];
      $item['picture'] = $row[1];
      $item['title'] = $row[2];
      $item['date'] = $row[3];
      $item['poster'] = $this->id;
      $return[] = $item;
    }
    return $return;
  }
  
  /**
   * Supprime le membre
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    // supprime toutes les photos liées au membre
    $this->delete_all_pictures();
    
    $sql = sprintf("DELETE FROM miki_person WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la personne : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Demande à un autre membre pour l'ajouter dans son groupe "d'amis" ou de contacts
   * 
   * Enregistre la demande "d'ami" et envoie un mail au membre indiqué.
   *      
   * @param int $person_id Membre à qui on fait la demande
   * @param string $message Message personnalisé qui apparaîtra dans le mail adressé au membre indiquée           
   * 
   * @return boolean   
   */   
  public function ask_for_friend($person_id, $message){
    $sql = sprintf("INSERT INTO miki_friends (person_id1, person_id2, state) values (%d, %d, 0)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($person_id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur pendant votre demande d'ajout d'un ami : ") ."<br />" .mysql_error());
    
    // récupert les sociétés émettrice et réceptrice
    $person_from = $this;
    $company_from = new Miki_company($person_from->company_id);
    $person_to = new Miki_person($person_id);
    $company_to = new Miki_company($person_to->company_id);
    
    $sitename = Miki_configuration::get('sitename');
    $email_answer = Miki_configuration::get('email_answer');
    
    // création du mail
    $mail = new miki_email('demande_contact', $person_to->language);
  
    $mail->From     = $email_answer;
    $mail->FromName = $sitename;
    
    // prépare les variables nécessaires à la création de l'e-mail
    $vars_array['person_to'] = $person_to;
    $vars_array['person_from'] = $person_from;
    $vars_array['company_from'] = $company_from;
    $vars_array['message'] = $message;
    $vars_array['sitename'] = $sitename;
    
    // initialise le contenu de l'e-mail
    $mail->init($vars_array);
    
    $mail->AddAddress($person_to->email1);
    if(!$mail->Send())
      throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
    $mail->ClearAddresses();
    return true;
  }
  
  /**
   * Accepte une demande d'ajout comme ami
   * 
   * @param int $person_id Id du membre dont on accepte la demande
   * 
   * @return boolean      
   */   
  public function confirm_friend($person_id){
    $sql = sprintf("UPDATE miki_friends SET state = 1, date = NOW() WHERE  
                    state = 0 AND (person_id1 = $person_id AND  person_id2 = $this->id) OR (person_id1 = $this->id AND  person_id2 = $person_id)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($person_id));
    $result = mysql_query($sql);
    
    if (!$result)
      throw new Exception(_("Erreur pendant votre ajout d'un contact : ") ."<br />" .mysql_error());
    
    $sitename = Miki_configuration::get('sitename');
    $email_answer = Miki_configuration::get('email_answer');
    
    // récupert les sociétés émettrice et réceptrice
    $person_from = $this;
    $company_from = new Miki_company($person_from->company_id);
    $person_to = new Miki_person($person_id);
    $company_to = new Miki_company($person_to->company_id);
    
    // création du mail
    $mail = new miki_email('demande_contact_confirme', $person_to->language);
  
    $mail->From     = $email_answer;
    $mail->FromName = $sitename;
    
    // prépare les variables nécessaires à la création de l'e-mail
    $vars_array['person_to'] = $person_to;
    $vars_array['person_from'] = $person_from;
    $vars_array['company_from'] = $company_from;
    $vars_array['sitename'] = $sitename;
    
    // initialise le contenu de l'e-mail
    $mail->init($vars_array);
    
    $mail->AddAddress($person_to->email1);
    if(!$mail->Send())
      throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
    $mail->ClearAddresses();
    
    return true;
  }
  
  /**
   * Refuse une demande d'ajout comme ami
   * 
   * @param int $person_id Id du membre dont on refuse la demande
   * 
   * @return boolean      
   */
  public function refuse_friend($person_id){
    $sql = sprintf("DELETE FROM miki_friends WHERE (person_id1 = $person_id AND  person_id2 = $this->id) OR (person_id1 = $this->id AND  person_id2 = $person_id)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($person_id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur pendant votre refus de contact : ") ."<br />" .mysql_error());
    
    return true;
  }
  
  /**
   * Recherche tous les "amis" du membre
   *  
   * @param boolean $valide Si true, on retourne les contact actifs. Si false, on retourne les demandes de contact pas encore validées
   * 
   * @return mixed Un tableau d'éléments de type Miki_person représentant les "amis" trouvés
   */      
  public function get_friends($valid = true){
    if ($valid)
      $state = 1;
    else
      $state = 0;
      
    $return = array();
    $sql = sprintf("SELECT * FROM miki_friends WHERE state = %d AND (person_id2 = %d)",
      mysql_real_escape_string($state),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      if ($row['person_id1'] == $this->id)
        $return[] = new Miki_person($row['person_id2']);
      else
        $return[] = new Miki_person($row['person_id1']);
    }
    return $return;
  }
  
  /**
   * Vérifie si le membre est "ami" avec un autre membre
   *  
   * @param int $person_id Id du membre dont on veut savoir si le membre en cours est "ami" avec
   * @param int $state Si = 1, on ne prend que les contacts confirmés, si = 0, on les prend tous
   * 
   * @return boolean      
   */   
  public function is_friend($person_id, $state = 0){
    if ($state == 0){
      $sql = sprintf("SELECT * FROM miki_friends WHERE person_id1 = %d AND  person_id2 = %d",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($person_id));
      $result = mysql_query($sql);
      if (mysql_num_rows($result) > 0)
        return true;
      else{
        $sql = sprintf("SELECT * FROM miki_friends WHERE person_id1 = %d AND  person_id2 = %d",
        mysql_real_escape_string($person_id),
        mysql_real_escape_string($this->id));
        $result = mysql_query($sql);
        if (mysql_num_rows($result) > 0)
          return true;
      }
    }
    else{
      $sql = sprintf("SELECT * FROM miki_friends WHERE state = 1 AND  person_id1 = %d AND  person_id2 = %d",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($person_id));
      $result = mysql_query($sql);
      if (mysql_num_rows($result) > 0)
        return true;
      else{
        $sql = sprintf("SELECT * FROM miki_friends WHERE state = 1 AND  person_id1 = %d AND  person_id2 = %d",
        mysql_real_escape_string($person_id),
        mysql_real_escape_string($this->id));
        $result = mysql_query($sql);
        if (mysql_num_rows($result) > 0)
          return true;
      }
    }
    
    return false;
  }
  
  /**
   * Envoie l'inscription du membre par e-mail
   * 
   * @param string $password Le mot de passe du membre. On doit fournir ce paramètre car le mot de passe stocké dans la base de données est crypté et ne peut pas être décrypté.
   * @param boolean $to_confirm Si l'inscription doit être confirmée par un administrateur. Dans ce cas l'administrateur du site en est informé par e-mail.         
   */   
  public function send_inscription($password, $to_confirm = false){
    $account = new Miki_account();
    $account->load_from_person($this->id);
    
    $sitename = Miki_configuration::get('sitename');
    $site_url = Miki_configuration::get('site_url');
    $email_answer = Miki_configuration::get('email_answer');
    
    // création du mail
    $mail = new miki_email('envoi_inscription', $this->language);
  
    $mail->From     = $email_answer;
    $mail->FromName = $sitename;
    
    // prépare les variables nécessaires à la création de l'e-mail
    $vars_array['person_to'] = $this;
    $vars_array['account_to'] = $account;
    $vars_array['password'] = $password;
    $vars_array['sitename'] = $sitename;
    $vars_array['site_url'] = $site_url;
    
    // initialise le contenu de l'e-mail
    $mail->init($vars_array);
    
    $mail->AddAddress($this->email1);

    if(!$mail->Send())
      throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
    $mail->ClearAddresses();
    
    // si on doit confirmer l'inscription
    if ($to_confirm){
      // création du mail
      $mail = new miki_email('inscription_a_confirmer', 'fr');
    
      $mail->From     = $email_answer;
      $mail->FromName = $sitename;
      
      // prépare les variables nécessaires à la création de l'e-mail
      $vars_array['person_from'] = $this;
      $vars_array['sitename'] = $sitename;
      $vars_array['site_url'] = $site_url;
      
      // initialise le contenu de l'e-mail
      $mail->init($vars_array);
      
      $mail->AddAddress($email_answer);
        
      if(!$mail->Send())
        throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
      $mail->ClearAddresses();
    }
  }
  
  /**
   * Recherche le nombre de contacts ou "amis" du membre
   * 
   * @return int
   */
  public function get_nb_contacts(){
    $sql = sprintf("SELECT count(*) FROM miki_friends WHERE state = 1 AND (person_id1 = %d OR person_id2 = %d)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Recherche les x derniers contacts ou "amis" du membre
   * 
   * @param int $nb Le nombre de contacts à rechercher
   * 
   * @return mixed Un tableau à 2 dimensions. La deuxième dimension est composée d'un indice 'contact' correspondant au contact (Miki_person) et d'un indice 'date' correspondant à la date à laquelle ils sont devenus "amis"             
   */   
  public function get_last_contacts($nb = 5){
    $return = array();
    $sql = sprintf("SELECT * FROM miki_friends WHERE state = 1 AND (person_id1 = %d OR person_id2 = %d) ORDER BY date desc LIMIT 0, %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($nb));
    $result = mysql_query($sql);
    $x = 0;
    while($row = mysql_fetch_array($result)){
      if ($row['person_id1'] == $this->id){
        $return[$x]['contact'] = new Miki_person($row['person_id2']);
        $return[$x]['date'] = $row['date'];
      }
      else{
        $return[$x]['contact'] = new Miki_person($row['person_id1']);
        $return[$x]['date'] = $row['date'];
      }
      $x++;
    }
    return $return;
  }
  
  /**
   * Invite une personne à venir visiter le site
   * 
   * @param string $firstname Prénom de la personne à inviter
   * @param string $lastname Nom de famille de la personne à inviter
   * @param string $email Adresse e-mail de la personne à inviter
   * @param string $message Message personnalisé à envoyer à la personne à inviter
   */     
  public function invite($firstname, $lastname, $email, $message){
    $company = new Miki_company($this->company_id);
    
    $sitename = Miki_configuration::get('sitename');
    $site_url = Miki_configuration::get('site_url');
    $email_answer = Miki_configuration::get('email_answer');
    
    // création du mail
    $mail = new miki_email('invitation_site', $this->language);
  
    $mail->From     = $email_answer;
    $mail->FromName = $sitename;
    
    // prépare les variables nécessaires à la création de l'e-mail
    $vars_array['person_from'] = $this;
    $vars_array['firstname'] = $firstname;
    $vars_array['lastname'] = $lastname;
    $vars_array['message'] = $message;
    $vars_array['sitename'] = $sitename;
    $vars_array['site_url'] = $site_url;
    
    // initialise le contenu de l'e-mail
    $mail->init($vars_array);
    
    $mail->AddAddress($email);

    if(!$mail->Send())
      throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
    $mail->ClearAddresses();
  }
  
  /**
   * Ajoute une photo au membre
   * 
   * Ajoute une photo au membre, l'upload, la redimensionne pour qu'elle ait une taille de maximum 1000px de large ou de long.
   * Créé également une miniature de maximum 100px de large ou de long.
   * 
   * Si une erreur survient, une exception est levée.      
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   *       
   * @return boolean
   */     
  public function upload_picture($fichier){
    
    // traite le nom de destination (correspond au titre de l'événements dans la langue principale)
    $nom_destination = decode("$this->firstname-$this->lastname"); 
    $nom_destination .= uniqid();
    $system = explode('.',mb_strtolower($fichier['name'], 'UTF-8'));
    $ext = $system[sizeof($system)-1];
    
    // ajoute l'extension
    $nom_destination .= ".$ext";

    // le fichier doit être au format jpg, png ou gif
  	if (!preg_match('/png/i',$ext) && !preg_match('/gif/i',$ext) && !preg_match('/jpg|jpeg/i',$ext)){
      throw new Exception(_("Le type de l'image n'est pas supporté. Les types supportés sont : JPEG, GIF et PNG : $nom_destination - " .$fichier['name']));
    }
    
    // teste s'il y a eu une erreur
  	if ($fichier['error']) {
  		switch ($fichier['error']){
  			case 1: // UPLOAD_ERR_INI_SIZE
  				throw new Exception(_("Votre image dépasse la limite autorisée (10Mo)"));
  				break;
  			case 2: // UPLOAD_ERR_FORM_SIZE
  				throw new Exception(_("Votre image dépasse la limite autorisée (10Mo)"));
  				break;
  			case 3: // UPLOAD_ERR_PARTIAL
  				throw new Exception(_("L'envoi de votre image a été interrompu pendant le transfert"));
  				break;
  			case 4: // UPLOAD_ERR_NO_FILE
  				throw new Exception(_("Aucune image n'a été indiquée"));
  				break;
  			case 6: // UPLOAD_ERR_NO_TMP_DIR
  			  throw new Exception(_("Aucun dossier temporaire n'a été configuré. Veuillez contacter l'administrateur du site Internet."));
  			  break;
  			case 7: // UPLOAD_ERR_CANT_WRITE
  			  throw new Exception(_("Erreur d'écriture sur le disque"));
  			  break;
  			case 8: // UPLOAD_ERR_EXTENSION
  			  throw new Exception(_("L'extension de votre image n'est pas supportée"));
  			  break;
  		}
  	}
  	
  	$size = "";
  	$file = $fichier['tmp_name'];
  	if (!is_uploaded_file($file) || $fichier['size'] > 10 * 1024 * 1024)
  		throw new Exception(_("Veuillez uploader des images plus petites que 10Mb"));
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
  	
  	// retourne le nom de l'image uploadée
    return $nom_destination;
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
    $picture = "";
    
    if ($this->picture === $name){
      $picture = $this->picture;
      $this->picture = "";
    }
    else{
      $pictures = $this->get_pictures();
      foreach($pictures as $p){
        if ($p['picture'] === $name){
          $picture = $p['picture'];
          $this->remove_picture($p['id']);
        }
      }
    }
    
    if ($picture != ""){
      $path = $this->picture_path .$picture;
      if (file_exists($path)){
        if (!unlink($path)){
          throw new Exception("Erreur pendant la suppression de la photo");
        }
      }
      
      // efface la miniature
      $path = $this->picture_path ."thumb/" .$picture;
      if (file_exists($path)){
        if (!unlink($path)){
          throw new Exception("Erreur pendant la suppression de la photo");
        }
      }
    }
    
    $this->update();
    return true;
  }
  
  /**
   * Supprime toutes les photos liées au membre
   * 
   * Supprime toutes les photos liées au membre ainsi que les fichiers représentant les photos
   * 
   * Si une erreur survient, une exception est levée            
   * 
   * @return boolean            
   */   
  public function delete_all_pictures(){
    $picture = "";

    // supprime la photo de profil
    if ($this->picture != ""){
      // efface la grande image
      $path = $this->picture_path .$this->picture;
      if (file_exists($path)){
        if (!unlink($path)){
          throw new Exception("Erreur pendant la suppression de la photo");
        }
      }
      
      // efface la miniature
      $path = $this->picture_path ."thumb/" .$this->picture;
      if (file_exists($path)){
        if (!unlink($path)){
          throw new Exception("Erreur pendant la suppression de la photo");
        }
      }
      $this->picture = "";
    }
    
    // puis supprime les autres images liées au membre
    $pictures = $this->get_pictures();
    foreach($pictures as $p){
      if (isset($p['name']) && $p['name'] != ""){
        // efface la grande image
        $path = $this->picture_path .$p['name'];
        if (file_exists($path)){
          if (!unlink($path)){
            throw new Exception("Erreur pendant la suppression de la photo");
          }
        }
        
        // efface la miniature
        $path = $this->picture_path ."thumb/" .$p['name'];
        if (file_exists($path)){
          if (!unlink($path)){
            throw new Exception("Erreur pendant la suppression de la photo");
          }
        }
        $this->pictures = array();
      }
    }
    
    $this->update();
    return true;
  }
  
  /**
   * Détermine si le numéro de membre donné existe déjà
   * 
   * @param string $number Numéro de membre à tester
   * 
   * @static
   * 
   * @return boolean True si le numéro existe déjà, False sinon                    
   */     
  public static function number_exists($number){
    $sql = sprintf("SELECT * FROM miki_person WHERE number = '%s'",
      mysql_real_escape_string($number));
    $result = mysql_query($sql);
    
    return mysql_num_rows($result) > 0;
  }
  
  /**
   * Retourne le nombre de crédits utilisés ou restant pour le membre
   * 
   * @param boolean $used Si True : le nombre de tickets utilisés par le membre, si False : le nombre de tickets restant au membre
   * 
   * @return int Le nombre de tickets trouvés      
   */   
  public function get_nb_credits($used = false){
    if ($used){
      $sql = sprintf("select nb_credits_used from miki_person_credit where id_person = %d",
        mysql_real_escape_string($this->id));
    }
    else{
      $sql = sprintf("select nb_credits_remaining from miki_person_credit where id_person = %d",
        mysql_real_escape_string($this->id));
    }
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Ajoute des crédits au membre
   * 
   * @param int $nb_credits Le nombre de crédits à ajouter au membre
   * 
   * @return boolean            
   */   
  public function add_credits($nb_credits){
    $sql = sprintf("update miki_person_credit set nb_credits_remaining = nb_credits_remaining + %d where id_person = %d",
        mysql_real_escape_string($nb_credits),
        mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    return true;
  }
  
  /**
   * Retire (utilise) des crédits au membre
   * 
   * @param int $nb_credits Le nombre de crédits à retirer au membre
   * 
   * @return boolean            
   */ 
  public function remove_credits($nb_credits = 1){
    $sql = sprintf("update miki_person_credit set nb_credits_remaining = nb_credits_remaining - %d, nb_credits_used = nb_credits_used + %d where id_person = %d",
        mysql_real_escape_string($nb_credits),
        mysql_real_escape_string($nb_credits),
        mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    return true;
  }
  
  /**
   * Recherche les membres
   * 
   * @param string $firstname si != "", recherche les membres dont le prénom contient $firstname
   * @param string $lastname si != "", recherche les membres dont le nom de famille contient $lastname
   * @param string $npa si != "", recherche les membres dont le code postal contient $npa
   * @param string $city si != "", recherche les membres dont la localité contient $city
   * @param string $dept si != "", recherche les membres dont le département/région contient $dept
   * @param string $country si != "", recherche les membres dont le pays contient $country
   * @param string $email si != "", recherche les membres dont l'adresse e-mail contient $email
   * @param string $username si != "", recherche les membres dont le nom d'utilisateur (issu de Miki_account) contient $username
   * @param string $category si != "", recherche les membres dont le type de compte (Miki_account) = $category
   * @param string $company si != "", recherche les membres dont le nom de la société (Miki_company) contient $company
   * @param boolean $active si true, recherche les membres dont le compte (Miki_account) est actif. Si false, recherche également les membres dont le compte est inactif.
   * @param int $limit si != 0, recherche x membres où x = $limit
   * @param int $page Si $limit != 0 Utilisé pour la pagination. Recherche les x membres de la page y où x = $limit et y = $page            
   *      
   * @static    
   * @see Miki_account
   * @see Miki_company       
   * 
   * @return mixed Un tableau d'éléments de type Miki_person représentant les membres trouvés        
   */ 
  public static function search($firstname = "", $lastname = "", $npa = "", $city = "", $dept = "", $country = "", $email = "", $username = "", $category = "", $company = "", $active = true, $limit = 0, $page = 1){
    $return = array();
    $sql = "SELECT mp.*, ma.username username
            FROM miki_person mp, miki_account ma, miki_company mc
            WHERE ma.person_id = mp.id AND
                  mc.id = mp.company_id";
    
    $firstname  = mb_strtolower($firstname, 'UTF-8');
    $lastname   = mb_strtolower($lastname, 'UTF-8');
    $npa        = mb_strtolower($npa, 'UTF-8');
    $city       = mb_strtolower($city, 'UTF-8');
    $dept       = mb_strtolower($dept, 'UTF-8');
    $country    = mb_strtolower($country, 'UTF-8');
    $email      = mb_strtolower($email, 'UTF-8');
    $username   = mb_strtolower($username, 'UTF-8');
    $category   = mb_strtolower($category, 'UTF-8');
    $company    = mb_strtolower($company, 'UTF-8');

    $search = "";
    if ($firstname != "")
      $search .= "LOWER(mp.firstname) LIKE '%$firstname%' OR ";
    if ($lastname != "")
      $search .= "LOWER(mp.lastname) LIKE '%$lastname%' OR ";
    if ($npa != "")
      $search .= "LOWER(mp.npa) LIKE '%$npa%' OR ";
    if ($city != "")
      $search .= "LOWER(mp.city) LIKE '%$city%' OR ";
    if ($dept != "")
      $search .= "LOWER(mp.dept) LIKE '%$dept%' OR ";
    if ($country != "")
      $search .= "LOWER(mp.country) LIKE '%$country%' OR ";
    if ($email != "")
      $search .= "LOWER(mp.email1) LIKE '%$email%' OR ";
    if ($username != "")
      $search .= "LOWER(ma.username) LIKE '%$username%' OR ";
    if ($company != "")
      $search .= "LOWER(mc.name) LIKE '%$company%' OR ";
      
    if ($search != "")
      $search = " AND  (" .mb_substr($search, 0, mb_strlen($search) - 4) .")";
      
    $sql .= $search;      
    
    if ($category == 1 || $category == 9){
      $sql .= " AND  ma.type in (1,9)";
    }  
    elseif ($category != ""){
      $sql .= " AND  ma.type = $category";
    } 
    
    if ($active){
      $sql .= " AND  ma.state = 1";
    }
    
    $sql .= " ORDER BY ma.type, ma.username asc";
    
    if ($limit != 0){
      $start = $limit * ($page - 1);
      $sql .= " LIMIT $start, $limit";
    }

    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_person($row[0]);
    }
    return $return;
  }
  
  /**
   * Recherche les membres
   *
   * @param boolean $all Si false, ne récupert que les membres dont le compte (Miki_account) est actif
   * @param string $order Par quel champ les membres trouvés seront triés (firstname, lastname, date). Si vide, on tri selon la date de création.
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)   
   *          
   * @static
   * @see Miki_account   
   * 
   * @return mixed Un tableau d'éléments de type Miki_person représentant les membres trouvés
   */               
  public static function get_all_persons($all = true, $order = "", $order_type = "asc"){
    $return = array();
    $sql = "SELECT mp.* 
            FROM miki_person mp, miki_account ma
            WHERE mp.id = ma.person_id";
            
    if (!$all)
      $sql .= " AND  ma.state = 1";
    
    if ($order == "firstname")
      $sql .= " ORDER BY mp.firstname " .$order_type;
    elseif ($order == "lastname")
      $sql .= " ORDER BY mp.lastname " .$order_type;
    elseif ($order == "date")
      $sql .= " ORDER BY ma.date_created " .$order_type;
    else
      $sql .= " ORDER BY ma.date_created " .$order_type;
    
    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_person($row['id']);
    }
    return $return;
  }
  
  /**
   * Recherche le nombre total de membres
   *    
   * @param boolean $all Si false, ne recherche que les membres dont le compte (Miki_account) est actif
   * 
   * @static
   * 
   * @return int      
   */         
  public static function get_nb_persons($all = true){
    $return = array();
    $sql = "SELECT count(*) 
            FROM miki_person mp, miki_account ma
            WHERE mp.id = ma.person_id";
            
    if (!$all)
      $sql .= " AND  ma.state = 1";
      
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row[0];
  }
}