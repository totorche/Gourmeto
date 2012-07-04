<?php
/**
 * Classe Miki_event
 * @package Miki
 */ 

/**
 * Inclut les fonctions d'importation et de traitement des images
 */ 
require_once("functions_pictures.php");

/**
 * Représentation d'un événement
 * 
 * @package Miki  
 */ 
class Miki_event{

  /**
   * Id de l'événement
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Type de l'événement
   *      
   * @var int
   * @access public   
   */
  public $type;
  
  /**
   * Titre de l'événement. Tableau dont la clé représente le code de la langue
   *      
   * @var mixed
   * @access public   
   */
  public $title;
  
  /**
   * Description de l'événement. Tableau dont la clé représente le code de la langue
   *      
   * @var mixed
   * @access public   
   */
  public $description;
  
  /**
   * Catégorie de l'événement
   *      
   * @var string
   * @access public   
   */
  public $category;
  
  /**
   * Tags de l'événement (séparés par des virgules, points-virgule ou espace)
   *      
   * @var string
   * @access public   
   */
  public $tags;
  
  /**
   * Nom de l'organisateur de l'événement
   *      
   * @var string
   * @access public   
   */
  public $organizer;
  
  /**
   * Id de la personne (Miki_person) ayant posté l'événement
   *      
   * @var int
   * @see Miki_person   
   * @access public   
   */
  public $poster;
  
  /**
   * Date de début de l'événement
   *      
   * @var string
   * @access public   
   */
  public $date_start;
  
  /**
   * Date de fin de l'événement
   *      
   * @var string
   * @access public   
   */
  public $date_stop;
  
  /**
   * Nombre de places disponibles pour l'événement
   *      
   * @var int
   * @access public   
   */
  public $place;
  
  /**
   * Adresse du lieu de l'événement
   *      
   * @var string
   * @access public   
   */
  public $address;
  
  /**
   * Case postale du lieu de l'événement
   *      
   * @var int
   * @access public   
   */
  public $npa;
  
  /**
   * Localité du lieu de l'événement
   *      
   * @var string
   * @access public   
   */
  public $city;
  
  /**
   * Région du lieu de l'événement
   *      
   * @var string
   * @access public   
   */
  public $region;
  
  /**
   * Pays du lieu de l'événement
   *      
   * @var string
   * @access public   
   */
  public $country;
  
  /**
   * Latitude du lieu de l'event 
   *      
   * @var float
   * @access public   
   */
  public $latitude;
  
  /**
   * Longitude du lieu de l'event 
   *      
   * @var float
   * @access public   
   */
  public $longitude;
  
  /**
   * Nombre d'accompagnants permis par personne inscrite à l'événement
   *      
   * @var int
   * @access public   
   */
  public $accompanist;
  
  /**
   * Si les inscriptions online sont acceptées (= 1) ou non (= 2)
   *      
   * @var int
   * @access public   
   */
  public $online_subscription;
  
  /**
   * Si un e-mail d'information doit être envoyé lors de chaque inscription
   *      
   * @var int
   * @access public   
   */
  public $subscription_information;
  
  /**
   * Adresse e-mail à laquelle l'e-mail d'information doit être envoyé lors de chaque inscription
   *      
   * @var string
   * @access public   
   */
  public $subscription_information_email;
  
  /**
   * Type d'entrée à l'événement (0 = entrée libre, 1 = entrée payante)
   *      
   * @var int
   * @access public   
   */
  public $entrance_type;
  
  /**
   * Prix de l'entrée à l'événement
   *      
   * @var float
   * @access public   
   */
  public $entrance_price;
  
  /**
   * Monnaie du prix de l'entrée à l'événement
   *      
   * @var string
   * @access public   
   */
  public $entrance_currency;
  
  /**
   * Commentaire concernant l'entrée à l'événement
   *      
   * @var string
   * @access public   
   */
  public $entrance_text;
  
  /**
   * Comment se fait le paiement : 0 = pas de paiement online; 1 = paiement online facultatif; 2 = paiement online obligatoire 
   *  
   * @var int
   * @access public   
   */
  public $payement_online;
  
  /**
   * Nombre de participants maximum à l'événement
   *      
   * @var int
   * @access public   
   */
  public $max_participants = 0;
  
  /**
   * Lien Internet relatif à l'événement
   *      
   * @var string
   * @access public   
   */
  public $web;
  
  /**
   * Date de modification de l'événement
   *      
   * @var string
   * @access public   
   */
  public $date_modification;
  
  /**
   * Fichiers liés à l'article (Array)
   *      
   * @var mixed
   * @access public   
   */
  public $files;
  
  /**
   * Noms des fichiers liés à l'article (Array)
   *       
   * @var mixed
   * @access public   
   */
  public $files_name;
  
  /**
   * Répertoire dans lequel sont stockés les fichiers liés à l'article.
   *      
   * @var string
   * @access private   
   */
  public $file_path = "pictures/events/";
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge l'événement dont l'id a été donné
   * 
   * @param int $id Id de l'événement à charger (optionnel)
   */
  function __construct($id = ""){
    // charge la personne si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un événement depuis un id
   *    
   * Si l'événement n'existe pas, une exception est levée.
   *    
   * @param int $id id de l'événement à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT me.*, temp.title title, temp.description description, temp.language_code code
                    FROM miki_event me
     
                    LEFT OUTER JOIN (SELECT met1.title, med1.description, met1.language_code, met1.id_event
                    FROM miki_event_title met1,
                         miki_event_description med1
                    WHERE met1.id_event = med1.id_event
                      AND met1.language_code = med1.language_code) temp ON temp.id_event = me.id
     
                    WHERE me.id = %d",
      mysql_real_escape_string($id));

    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql");
    
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("L'événement demandé n'existe pas"));
    while ($row = mysql_fetch_array($result)){
      $this->id                             = $row['id'];
      $this->type                           = $row['type'];
      $this->title[$row['code']]            = stripslashes($row['title']);
      $this->description[$row['code']]      = stripslashes($row['description']);
      $this->category                       = $row['category'];
      $this->tags                           = $row['tags'];
      $this->organizer                      = $row['organizer'];
      $this->poster                         = $row['poster'];
      $this->date_start                     = $row['date_start'];
      $this->date_stop                      = $row['date_stop'];
      $this->place                          = $row['place'];
      $this->address                        = $row['address'];
      $this->npa                            = $row['npa'];
      $this->city                           = $row['city'];
      $this->region                         = $row['region'];
      $this->country                        = $row['country'];
      $this->latitude                       = $row['latitude'];
      $this->longitude                      = $row['longitude'];
      $this->accompanist                    = $row['accompanist'];
      $this->online_subscription            = $row['online_subscription'];
      $this->subscription_information       = $row['subscription_information'];
      $this->subscription_information_email = $row['subscription_information_email'];
      $this->entrance_type                  = $row['entrance_type'];
      $this->entrance_price                 = $row['entrance_price'];
      $this->entrance_currency              = $row['entrance_currency'];
      $this->entrance_text                  = $row['entrance_text'];
      $this->payement_online                = $row['payement_online'];
      $this->max_participants               = $row['max_participants'];
      $this->files                          = explode("&&", $row['files']);
      $this->files_name                     = explode("&&", $row['files_name']);
      $this->date_modification              = $row['date_modification'];
      $this->web                            = $row['web'];
    }
    
    // traite les fichiers récupérés
    $tab_temp = array();
    foreach($this->files as $f){
      if ($f != ""){
        if (strstr($f, "%%") !== false){
          $file = explode("%%", $f);
          $tab_temp[$file[0]][] = $file[1];
        }
        else{
          $tab_temp['fr'][] = $f;
        }
      }
    }
    $this->files = $tab_temp;
    
    // traite les noms de fichier récupérés
    $tab_temp = array();
    foreach($this->files_name as $f){
      if ($f != ""){
        if (strstr($f, "%%") !== false){
          $file = explode("%%", $f);
          $tab_temp[$file[0]][] = $file[1];
        }
        else{
          $tab_temp['fr'][] = $f;
        }
      }
    }
    $this->files_name = $tab_temp;
    return true;
  }
  
  /**
   * Sauvegarde l'événement dans la base de données.
   *    
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   * 
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */
  public function save(){      
    // si un l'id de l'événement existe, c'est que l'événement existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // concatène les fichiers
    $tab_temp = array();
    if (is_array($this->files)){
      foreach($this->files as $lang => $files){
        foreach($files as $f){
          $tab_temp[] = "$lang%%$f";
        }
      }
      $files = implode("&&", $tab_temp);
    }
    else
      $files = "";
    
    // et les noms de fichiers  
    $tab_temp = array();
    if (is_array($this->files_name)){
      foreach($this->files_name as $lang => $files){
        foreach($files as $f){
          $tab_temp[] = "$lang%%$f";
        }
      }
      $files_name = implode("&&", $tab_temp);
    }
    else
      $files_name = "";
      
    if ($this->poster == '')
      $this->poster = 'NULL';
    
    // débute la transaction
    mysql_query("START TRANSACTION");
      
    $sql = sprintf("INSERT INTO miki_event 
                    (type, category, tags, organizer, poster, date_start, date_stop, place, address, npa, city, region, country, latitude, longitude, accompanist, online_subscription, subscription_information, subscription_information_email, entrance_type, entrance_price, entrance_currency, entrance_text, payement_online, max_participants, web, files, files_name, date_modification) 
                    VALUES(%d, '%s', '%s', '%s', %s, '%s', '%s', '%s', '%s', %d, '%s', '%s', '%s', '%F', '%F', %d, %d, %d, '%s', %d, '%F', '%s', '%s', %d, %d, '%s', '%s', '%s', NOW())",
      mysql_real_escape_string($this->type),
      mysql_real_escape_string($this->category),
      mysql_real_escape_string($this->tags),
      mysql_real_escape_string($this->organizer),
      mysql_real_escape_string($this->poster),
      mysql_real_escape_string($this->date_start),
      mysql_real_escape_string($this->date_stop),
      mysql_real_escape_string($this->place),
      mysql_real_escape_string($this->address),
      mysql_real_escape_string($this->npa),
      mysql_real_escape_string($this->city),
      mysql_real_escape_string($this->region),
      mysql_real_escape_string($this->country),
      mysql_real_escape_string($this->latitude),
      mysql_real_escape_string($this->longitude),
      mysql_real_escape_string($this->accompanist),
      mysql_real_escape_string($this->online_subscription),
      mysql_real_escape_string($this->subscription_information),
      mysql_real_escape_string($this->subscription_information_email),
      mysql_real_escape_string($this->entrance_type),
      mysql_real_escape_string($this->entrance_price),
      mysql_real_escape_string($this->entrance_currency),
      mysql_real_escape_string($this->entrance_text),
      mysql_real_escape_string($this->payement_online),
      mysql_real_escape_string($this->max_participants),
      mysql_real_escape_string($this->web),
      mysql_real_escape_string($files),
      mysql_real_escape_string($files_name));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      //mysql_query("ROLLBACK");
          
      throw new Exception(_("Erreur lors de l'insertion de l'événement dans la base de données : <br />") ."<br />" .mysql_error());
    }
    // récupert l'id
    $this->id = mysql_insert_id();
    
    // ajoute le titre de l'événement dans chaque langue
    if (is_array($this->title)){
      foreach($this->title as $key => $t){
        $sql = sprintf("INSERT INTO miki_event_title (id_event, language_code, title) VALUES(%d, '%s', '%s')",
          mysql_real_escape_string($this->id),
          mysql_real_escape_string($key),
          mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
        $result = mysql_query($sql);
        if (!$result){
          // annule les modifications
          mysql_query("ROLLBACK");

          throw new Exception(_("Erreur lors de l'ajout de l'événement dans la base de données :") ."<br />" .mysql_error());
        }
      }
    }
    
    // ajoute la description de l'événement dans chaque langue
    if (is_array($this->description)){
      foreach($this->description as $key => $t){
        $sql = sprintf("INSERT INTO miki_event_description (id_event, language_code, description) VALUES(%d, '%s', '%s')",
          mysql_real_escape_string($this->id),
          mysql_real_escape_string($key),
          mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
        $result = mysql_query($sql);
        if (!$result){
          // annule les modifications
          mysql_query("ROLLBACK");

          throw new Exception(_("Erreur lors de l'ajout de l'événement dans la base de données :") ."<br />" .mysql_error());
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
   * Met à jour l'événement dans la base de données.
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

    // concatène les fichiers
    $tab_temp = array();
    if (is_array($this->files)){
      foreach($this->files as $lang => $tab_files){
        foreach($tab_files as $f){
          $tab_temp[] = "$lang%%$f";
        }
      }
      $files = implode("&&", $tab_temp);
    }
    else
      $files = "";
    
    // et les noms de fichiers  
    $tab_temp = array();
    if (is_array($this->files_name)){
      foreach($this->files_name as $lang => $tab_files){
        foreach($tab_files as $f){
          $tab_temp[] = "$lang%%$f";
        }
      }
      $files_name = implode("&&", $tab_temp);
    }
    else
      $files_name = "";
    
    if ($this->poster == '')
      $this->poster = 'NULL';
    
    // débute la transaction
    mysql_query("START TRANSACTION");
    
    $sql = sprintf("UPDATE miki_event SET type = %d, category = '%s', tags = '%s', organizer = '%s', poster = %s, date_start = '%s', date_stop = '%s', place = '%s', address = '%s', npa = %d, city = '%s', region = '%s', country = '%s', latitude = '%F', longitude = '%F', accompanist = %d, online_subscription = %d, subscription_information = %d, subscription_information_email = '%s', entrance_type = %d, entrance_price = '%F', entrance_currency = '%s', entrance_text = '%s', payement_online = %d, max_participants = %d, web = '%s', files = '%s', files_name = '%s', date_modification = NOW() WHERE id = %d",
      mysql_real_escape_string($this->type),
      mysql_real_escape_string($this->category),
      mysql_real_escape_string($this->tags),
      mysql_real_escape_string($this->organizer),
      mysql_real_escape_string($this->poster),
      mysql_real_escape_string($this->date_start),
      mysql_real_escape_string($this->date_stop),
      mysql_real_escape_string($this->place),
      mysql_real_escape_string($this->address),
      mysql_real_escape_string($this->npa),
      mysql_real_escape_string($this->city),
      mysql_real_escape_string($this->region),
      mysql_real_escape_string($this->country),
      mysql_real_escape_string($this->latitude),
      mysql_real_escape_string($this->longitude),
      mysql_real_escape_string($this->accompanist),
      mysql_real_escape_string($this->online_subscription),
      mysql_real_escape_string($this->subscription_information),
      mysql_real_escape_string($this->subscription_information_email),
      mysql_real_escape_string($this->entrance_type),
      mysql_real_escape_string($this->entrance_price),
      mysql_real_escape_string($this->entrance_currency),
      mysql_real_escape_string($this->entrance_text),
      mysql_real_escape_string($this->payement_online),
      mysql_real_escape_string($this->max_participants),
      mysql_real_escape_string($this->web),
      mysql_real_escape_string($files),
      mysql_real_escape_string($files_name),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de l'événement dans la base de données2 : $sql") ."<br />aa" .mysql_error());
    }
    
    // supprime le titre de l'événement dans chaque langue
    $sql = sprintf("DELETE FROM miki_event_title WHERE id_event = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de l'événement dans la base de données : ") ."<br />" .mysql_error());
    }
    
    // ajoute le titre de l'événement dans chaque langue
    foreach($this->title as $key => $t){
      $sql = sprintf("INSERT INTO miki_event_title (id_event, language_code, title) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
      $result = mysql_query($sql);
      if (!$result){
        // annule les modifications
        mysql_query("ROLLBACK");
      
        throw new Exception(_("Erreur lors de la mise à jour de l'événement dans la base de données : ") ."<br />" .mysql_error());
      }
    }
    
    
    // supprime la description de l'événement dans chaque langue
    $sql = sprintf("DELETE FROM miki_event_description WHERE id_event = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de l'événement dans la base de données : ") ."<br />" .mysql_error());
    }
    
    // ajoute la description de l'événement dans chaque langue
    foreach($this->description as $key => $t){
      $sql = sprintf("INSERT INTO miki_event_description (id_event, language_code, description) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
      $result = mysql_query($sql);
      if (!$result){
        // annule les modifications
        mysql_query("ROLLBACK");
      
        throw new Exception(_("Erreur lors de la mise à jour de l'événement dans la base de données : ") ."<br />" .mysql_error());
      }
    }
    
    // applique les modifications
    mysql_query("COMMIT");
    
    return true;
  }
  
  /**
   * Supprime l'événement
   * 
   * Si une erreur survient, une exception est levée
   *      
   * @return boolean
   */
  public function delete(){
    // Supprime toutes les images de l'événement
    $this->delete_all_files();
  
    $sql = sprintf("DELETE FROM miki_event WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de l'événement : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Retourne une partie de la description de l'événement
   * 
   * Si la description de l'événement est plus courte que la partie demandée, la description de l'événement est retournée en entier.      
   * 
   * @param string $lang Langue dans laquelle on veut récupérer la description de l'événement   
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
   * Inscrit une personne (Miki_person) à l'événement
   * 
   * @param int $person_id Id de la personne (Miki_person) à inscrire
   * @param int $nb_persons Nombre de personnes à inscrire
   * @param string $payed Si l'inscription a été payée ou non (True = payée, False = non-payée)
   * @param string $remark Commentaire de l'inscription   
   * @see Miki_person   
   * @return boolean   
   */            
  public function subscribe($person_id, $nb_persons = 1, $payed = false, $remark = ""){
    
    if ($payed)
      $payed = 1;
    else
      $payed = 0;
  
    $sql = sprintf("INSERT INTO miki_event_participant (id_event, id_participant, nb_persons, payed, remark) values (%d, %d, %d, %d, '%s')",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($person_id),
      mysql_real_escape_string($nb_persons),
      mysql_real_escape_string($payed),
      mysql_real_escape_string($remark));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur pendant l'inscription : ") ."<br />" .mysql_error());
    
    // si le créateur de l'événement a demandé à être tenu au courant lors de chaque inscription, on l'informe
    if ($this->subscription_information == 1 && $this->subscription_information_email != ""){
      try{
        // création du mail
        $mail = new miki_email('event_subscription', Miki_language::get_main_code());
        
        // définit l'état actuel de la réservation
        if ($this->is_subscription_validated($person_id) && $this->is_subscription_payed($person_id)) 
          $subscription_state = _("Validée et payée");
        elseif ($this->is_subscription_validated($person_id)) 
          $subscription_state = _("Validée mais non-payée");
        if ($this->is_subscription_payed($person_id)) 
          $subscription_state = _("Payée mais non-validée");
        else
          $subscription_state = _("Non-payée et non-validée");
      
        $mail->From     = EMAIL_ANSWER;
        $mail->FromName = SITENAME;
        
        // prépare les variables nécessaires à la création de l'e-mail
        $vars_array['person'] = new Miki_person($person_id);
        $vars_array['event_name'] = $this->title[Miki_language::get_main_code()];
        $vars_array['subscription_state'] = $subscription_state;
        $vars_array['sitename'] = SITENAME;
        $vars_array['site_url'] = SITE_URL;
        
        // initialise le contenu de l'e-mail
        $mail->init($vars_array);
                
        $mail->AddAddress($this->subscription_information_email);

        if(!$mail->Send())
          throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
        $mail->ClearAddresses();
      }
      catch(Exception $e){throw $e;}
    }
    
    return true;
  }
  
  /**
   * Désinscription d'une personne (Miki_person) à l'événement
   * 
   * @param int $person_id Id de la personne (Miki_person) à désinscrire
   * @see Miki_person   
   * @return boolean
   */      
  public function unsubscribe($person_id){
    $sql = sprintf("DELETE FROM miki_event_participant WHERE id_event = %d AND  id_participant = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($person_id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur pendant la désinscription : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Détermine si une personne (Miki_person) donnée est inscrite à l'événement
   * 
   * @see Miki_person   
   * @return boolean
   */        
  public function is_subscribed($person_id){
    $sql = sprintf("SELECT count(*) FROM miki_event_participant WHERE id_event = %d AND  id_participant = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($person_id));
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row[0] == 1;
  }
  
  /**
   * Définit une inscription comme payée ou non-payée
   * 
   * @param int $person_id Id de la personne (Miki_person) dont on veut modifier l'inscription
   * @param string $payed Si l'inscription a été payée ou non (True = payée, False = non-payée)
   * @see Miki_person   
   * @return boolean   
   */            
  public function set_subscription_payed($person_id, $payed = true){
    
    if ($payed)
      $payed = 1;
    else
      $payed = 0;
      
    $sql = sprintf("UPDATE miki_event_participant SET payed = %d WHERE id_event = %d AND id_participant = %d",
      mysql_real_escape_string($payed),
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($person_id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur pendant la modification de l'inscription : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Recherche si l'inscription a été payée ou non
   * 
   * @param int $person_id Id de la personne (Miki_person) dont on veut vérifier l'inscription
   * @see Miki_person   
   * @return boolean   
   */            
  public function is_subscription_payed($person_id){
    $sql = sprintf("SELECT payed FROM miki_event_participant WHERE id_event = %d AND id_participant = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($person_id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur pendant la recherche d'information à propos de l'inscription : ") ."<br />" .mysql_error());
    
    $row = mysql_fetch_array($result);  
    return $row[0] == 1;
  }
  
  /**
   * Définit une inscription comme validée ou non-validée
   * 
   * @param int $person_id Id de la personne (Miki_person) dont on veut modifier l'inscription
   * @param string $valid Si l'inscription est validée ou non (True = validée, False = non-validée)
   * @see Miki_person   
   * @return boolean   
   */            
  public function validate_subscription($person_id, $valid = true){
    
    if ($valid)
      $valid = 1;
    else
      $valid = 0;
      
    $sql = sprintf("UPDATE miki_event_participant SET state = %d WHERE id_event = %d AND id_participant = %d",
      mysql_real_escape_string($valid),
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($person_id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur pendant la modification de l'inscription : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Recherche si l'inscription a été validée ou non
   * 
   * @param int $person_id Id de la personne (Miki_person) dont on veut vérifier l'inscription
   * @see Miki_person   
   * @return boolean   
   */            
  public function is_subscription_validated($person_id){
    $sql = sprintf("SELECT state FROM miki_event_participant WHERE id_event = %d AND id_participant = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($person_id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur pendant la recherche d'information à propos de l'inscription : ") ."<br />" .mysql_error());
    
    $row = mysql_fetch_array($result);  
    return $row[0] == 1;
  }
  
  /**
   * Recherche le nombre d'inscriptions à l'événement
   * 
   * @return int
   */         
  public function get_nb_participants(){
    $sql = sprintf("SELECT SUM(nb_persons) FROM miki_event_participant WHERE id_event = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);

    if (!empty($row[0]) && $row[0] != "NULL")
      $nb_participants = $row[0];
    else
      $nb_participants = 0;
      
    return $nb_participants;
  }
  
  /**
   * Recherche toutes les inscriptions à l'événement.
   * 
   * Retourne le nombre personnes inscrites pour une inscription ainsi que la personne (Miki_person) ayant effectuée l'inscription.      
   * 
   * @see Miki_person
   * @return mixed Un tableau dont l'index 0 correspond au nombre de personnes inscrites dans l'inscription et l'index 1 à un éléments Miki_person représentant la personne ayant effectué l'inscription
   */   
  public function get_participants(){
    $return = array();
    $sql = sprintf("SELECT id_participant, nb_persons FROM miki_event_participant WHERE id_event = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = array($row[1], new Miki_person($row[0]));
    }
    return $return;
  }
  
  /**
   * Envoi des invitations par e-mail pour l'événement aux personnes données en paramètre ($to = array)
   * 
   * @param Miki_person $from Personne de type Miki_person envoyant l'invitation
   * @param mixed $to Tableau d'éléments de type Miki_person représentant les destinataires de l'invitation
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
      $mail = new miki_email('envoi_invitation_event', $p->language);
    
      $mail->From     = $email_answer;
      $mail->FromName = $sitename;
      
      // prépare les variables nécessaires à la création de l'e-mail
      $vars_array['person_to'] = $p;
      $vars_array['company_from'] = $company_from;
      $vars_array['event'] = $this;
      $vars_array['sitename'] = $sitename;
      $vars_array['site_url'] = $site_url;
      
      // initialise le contenu de l'e-mail
      $mail->init($vars_array);
              
      $mail->AddAddress($p->email1);
      //$mail->AddAddress("herve@fbw-one.com");
      if(!$mail->Send())
        throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
      $mail->ClearAddresses();
    }
  }
  
  /**
   * Ajoute un fichier à l'événement
   * 
   * Ajoute un fichier à l'événement. S'il s'agit d'une image, on la redimensionne (maximum 1000x1000px) et créé une miniature (maximum 100x100px)
   * 
   * Si une erreur survient, une exception est levée       
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   * @param string $titre_fichier Titre du fichier   
   * @param string $lang Code de la langue du fichier    
   * @param boolean $first Si true, on place l'image en première position dans le tableau. Sinon, on la met à la suite.
   *       
   * @return boolean
   */
  public function upload_file($fichier, $titre_fichier, $lang, $first = false){
    
    $system = explode('.',mb_strtolower($fichier['name'], 'UTF-8'));
    // récupert le nom du fichier ainsi que son extension
    $nom_destination = decode(implode("_", array_slice($system, 0, sizeof($system)-1)));
    $ext = $system[sizeof($system)-1];
    $nom_destination = $nom_destination ."-" .uniqid()  ."." .$ext;

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
      
      // redimensionne l'image
  	  createthumb($this->file_path .$nom_destination, $this->file_path .$nom_destination, 1000, 1000, false);
  	
      // puis créé une vignette
  	  createthumb($this->file_path .$nom_destination, $this->file_path ."thumb/" .$nom_destination, 200, 200, false);
    }
  	
  	// si on doit mettre le fichier en première position
  	if ($first){
  	  array_unshift($this->files, $nom_destination);
  	  array_unshift($this->files_name, $titre_fichier);
  	}
  	else{
      $this->files[$lang][] = $nom_destination;
      $this->files_name[$lang][] = $titre_fichier;
    }
  	return true;
  }
  
  /**
   * Supprime un fichier lié à l'événement
   * 
   * @param string $file nom du fichier à supprimer 
   * 
   * @return boolean        
   */   
  public function delete_file($file){
    $tab_temp = array();
    $tab_temp_name = array();
    
    // parcourt tous les fichiers joints à l'article
    foreach($this->files as $lang => $files){
    
      for($x=0; $x < sizeof($files); $x++){
        $f = $files[$x];
        $name = $this->files_name[$lang][$x];
        
        // si c'est le fichier à supprimer, on le supprime
        if ($f != "" && $f == $file){
          $path = $this->file_path .$f;
          if (file_exists($path)){
            if (!unlink($path)){
              throw new Exception("Erreur pendant la suppression du fichier");
            }
          }
          
          $path_thumb = $this->file_path ."thumb/" .$f;
          if (file_exists($path_thumb)){
            if (!unlink($path_thumb)){
              throw new Exception("Erreur pendant la suppression du fichier");
            }
          }
        }
        // sinon on le garde
        else{
          $tab_temp[$lang][] = $f;
          $tab_temp_name[$lang][] = $name;
        }
      }
    }
    $this->files = $tab_temp;
    $this->files_name = $tab_temp_name;
    $this->update();
    return true;
  }
  
  /**
   * Supprime tous les fichiers liés à l'événement
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
        
        $path_thumb = $this->file_path ."thumb/" .$f;
        if (file_exists($path_thumb)){
          if (!unlink($path_thumb)){
            throw new Exception("Erreur pendant la suppression du fichier");
          }
        }
      }
    }
    $this->files = array();
    $this->files_name = array();
    $this->update();
    return true;
  }
  
  /**
   * Récupert les images de l'événement.
   * 
   * Si il n'y a aucune image, retourne False
   * 
   * @param string $lang_code Code de la langue dans laquelle on veut récupérer les images. Si vide (""), on prend les images de toutes les langues
   *      
   * @return mixed Un tableau contenant les images trouvées, False si aucune image            
   */       
  public function get_pictures($lang_code = ""){
  
    $array_temp = array();
    
    foreach($this->files as $lang => $files){
    
      // ne prend que les images de la langue donnée, ou de toutes les langues si $lang_code = ""
      if ($lang == "" || $lang == $lang_code){
        foreach($files as $f){
          $system=explode('.', $f);
          if (preg_match('/jpg|jpeg/i', $system[sizeof($system)-1])){
            $array_temp[] = $f;
        	}
        	else if (preg_match('/png/i', $system[sizeof($system)-1])){
            $array_temp[] = $f;
        	}
        	else if (preg_match('/gif/i',$system[sizeof($system)-1])){
            $array_temp[] = $f;
        	}
        }
      }
    }
    
    if (sizeof($array_temp) > 0)
      return $array_temp;
    else
      return false;
  }

  /**
   * Vérifie si le document est une image
   * 
   * @param string $file Document à tester
   *      
   * @return boolean      
   */     
  public function is_picture($file){
    if ($file == ""){
      return false;
    }
    
    $system = explode('.',mb_strtolower($file, 'UTF-8'));
    if (preg_match('/jpg|jpeg/i',$system[sizeof($system)-1]) ||
        preg_match('/png/i',$system[sizeof($system)-1]) ||
        preg_match('/gif/i',$system[sizeof($system)-1])){
      
      return true;   
    }
    else{
      return false;
    }
  }
  
  /**
   * Vérifie si le document est un PDF
   * 
   * @param string $file Document à tester   
   * 
   * @return boolean      
   */     
  public function is_pdf($file){
    if ($file == ""){
      return false;
    }
    
    $system = explode('.',mb_strtolower($file, 'UTF-8'));
    if (preg_match('/pdf/i',$system[sizeof($system)-1])){
      return true;   
    }
    else{
      return false;
    }
  }
  
  /**
   * Vérifie si le document est un fichier Word
   * 
   * @param string $file Document à tester   
   * 
   * @return boolean      
   */     
  public function is_word($file){
    if ($file == ""){
      return false;
    }
    
    $system = explode('.',mb_strtolower($file, 'UTF-8'));
    if (preg_match('/doc/i',$system[sizeof($system)-1]) ||
        preg_match('/docx/i',$system[sizeof($system)-1])){
      return true;   
    }
    else{
      return false;
    }
  }
  
  /**
   * Vérifie si le document est un fichier Excel
   * 
   * @param string $file Document à tester   
   * 
   * @return boolean      
   */     
  public function is_excel($file){
    if ($file == ""){
      return false;
    }
    
    $system = explode('.',mb_strtolower($file, 'UTF-8'));
    if (preg_match('/xls/i',$system[sizeof($system)-1]) ||
        preg_match('/xlsx/i',$system[sizeof($system)-1])){
      return true;   
    }
    else{
      return false;
    }
  }
  
  /**
   * Recherche le nombre d'événements total
   * 
   * @static   
   * @return int      
   */     
  public static function get_nb_events(){
    $sql = "SELECT COUNT(*) FROM miki_event";
    
    $result = mysql_query($sql);
    
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Envoi un mail au blog du site Internet pour mettre l'événement sur le blog
   * 
   * Publie l'événement sur le blog lors de l'ajout ou de la modification de l'événement.
   * Publie uniquement si "Publier les événements sur le blog" est coché dans la partie "Administration -> Configurer le site Internet" du Miki.
   * L'adresse e-mail de publication doit également être renseignée dans la partie "Administration -> Configurer le site Internet" du Miki.
   */   
  public function send_to_blog(){
    // création du mail
    $mail = new phpmailer();
    if (isset($_SESSION['lang']))
      $mail->SetLanguage($_SESSION['lang']);
    else
      $mail->SetLanguage('fr');
    
    $sitename = Miki_configuration::get('sitename');
    $email_answer = Miki_configuration::get('email_answer');
    $site_url = Miki_configuration::get('site_url');
    $publish_email_address = Miki_configuration::get('publish_email_address');
    
    // recherche la première image de l'événement
    if (is_array($this->files)){
      $logo = "";
      foreach($this->files as $file){
        if ($this->is_picture($file) && $logo == "")
          $logo = $file;
      }
      
      if ($logo != "")
        $size = get_image_size($this->file_path .$logo, 100, 100);
    }
    else{
      $logo = "";
    }
    
    $mail->CharSet	=	"UTF-8";
    //$mail->From     = $email_answer;
    $mail->From = "auto_blog@fbw-one.com";
    $mail->Sender = "auto_blog@fbw-one.com";
    $mail->FromName = "";
    //$mail->FromName = $sitename;
    //$mail->IsMail();
    $mail->IsSMTP();
    $mail->Host = "ns0.ovh.net";
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Password = "courrendlin1";
    $mail->Username = "blog1%fbw-one.com";
    $mail->PluginDir = "scripts/";
    $mail->isHTML(true);
    
    /*******************************************************************
     *
     * Post l'article sur le blog Bleu Blog via envoi par e-mail
     * 
     *******************************************************************/
                   
    $subject = "Événement - " .stripslashes($this->title);
        
    // contenu html
    $body = "";
    
    foreach($this->title as $key => $t){
      $lang = new Miki_language();
      $lang->load_from_code($key);
      
      $body .= "$lang->name : <br /><br />";
      
      $body .= "<a href='$site_url/" .stripslashes($this->get_url_simple()) ."' title='" .stripslashes($this->get_url_simple()) ."'>" .stripslashes($t) ."</a><br /><br />";
            
              if ($logo != "")
                $body .= "<img src='$site_url/pictures/events/$logo' alt=\"image de l'événement\" title=\"$t\" style='border:0;width:" .$size[0] ."px;height:" .$size[1] ."px;float:left;vertical-align:top;margin:0 10px 10px 0' />";
              
      $body .= stripslashes($this->description[$key]) ."<hr style='margin:20px 0'/>";
    }
    
    $mail->Subject = $subject;
    $mail->Body = $body;

    $mail->AddAddress($publish_email_address);
    //$mail->AddAddress("herve@fbw-one.com");
    
    if(!$mail->Send())
      throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);

    $mail->ClearAddresses();
  }
  
  /**
   * Retourne l'url de l'événements en fonction de son titre.
   * 
   * Cette fonction peut être utilisée pour afficher un lien vers l'événement dans une page web dans le cadre du référencement.
   * Le lien vers la page réelle est gérer via l'Url Rewriting    
   * 
   * @return string L'url de l'événement en fonction de son titre   
   */   
  public function get_url_simple(){
    if (isset($_SESSION['lang']) && Miki_language::exist($_SESSION['lang'])) {
    	$lang = $_SESSION['lang'];
    }
    else{
      $lang = Miki_language::get_main_code();
    }
    
    $url = 'events/' .decode($this->title[$lang]) .'-' .$this->id;
    
    return $url;
  }
  
  /**
   * Recherche tous les événements dont le titre, les tags ou la ville correspondent au critère donné
   * 
   * @param string $search Critère de recherche pour le titre, les tags ou la ville de l'événement 
   * @param boolean $all Si true on recherche tous les événements. Si false on ne recherche que les événements actuels et futurs
   * @param string $order Par quel champ les événements trouvés seront triés (category, city, country, date_start, date_stop, entrance_type). Si vide, on tri selon l'id.
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * @param int $nb nombre d'événements à retourner par page. Si = "" on retourne tous les événements
   * @param int $page numéro de la page à retourner
   * @param int $total Cette variable sera modifiée avec le nombre total d’enregistrements qui seraient retournés par la requête sans la clause LIMIT   
   *    
   * @static
   * 
   * @return mixed Un tableau d'élément Miki_event représentant les événements trouvés
   */   
  public static function search($search = "", $all = false, $order = "", $order_type = "asc", $nb = "", $page = 1, &$total){
    $return = array();
    
    $sql = "SELECT DISTINCT(me.id) 
            FROM miki_event me, miki_event_title met
            WHERE met.id_event = me.id";
    
    if (!$all){
      $sql .= " AND me.date_start >= NOW()";
    }
      
    if ($search != ""){
      $search = mb_strtolower($search, 'UTF-8');
      
      $sql .= sprintf(" AND (LOWER(met.title) LIKE '%%%s%%' OR 
                            LOWER(me.tags) LIKE '%%%s%%' OR 
                            LOWER(me.city) LIKE '%%%s%%')",
                  mysql_real_escape_string($search),
                  mysql_real_escape_string($search),
                  mysql_real_escape_string($search));
    }
    
    if ($order == "category")
      $sql .= " ORDER BY me.category " .$order_type;
    elseif ($order == "city")
      $sql .= " ORDER BY me.city " .$order_type;
    elseif ($order == "country")
      $sql .= " ORDER BY me.country " .$order_type;
    elseif ($order == "date_start")
      $sql .= " ORDER BY me.date_start " .$order_type;
    elseif ($order == "date_stop")
      $sql .= " ORDER BY me.date_stop " .$order_type;
    elseif ($order == "entrance_type")
      $sql .= " ORDER BY me.entrance_type " .$order_type;
    else
      $sql .= " ORDER BY id " .$order_type;
      
    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }
    
    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_event($row['id']);
    }
    return $return;
  }
  
  /**
   * Récupére les événements à venir ordonnés par date croissante
   *    
   * @param int $person_id si renseigné, ne retourne que les événements dont le posteur est la personne donnée
   * @param boolean $public si true : ne retourne que les événements de visibilité publique (type = 0). Si false, retourne tous les événements (visibilité privée + publique)
   * @param string $category retourne uniquement les événements de la catégorie donnée
   * @param int $nb nombre d'événements à retourner par page. Si = "" on retourne tous les événements
   * @param int $page numéro de la page à retourner 
   * @param int $total Cette variable sera modifiée avec le nombre total d’enregistrements qui seraient retournés par la requête sans la clause LIMIT   
   * 
   * @static        
   *    
   * @return mixed un tableau d'élément de type Miki_event   
   */     
  public static function get_next($person_id = "", $public = false, $category = "", $nb = "", $page = 1, &$total){
    $return = array();
    $sql = "SELECT * FROM miki_event WHERE date_stop >= NOW()";
    
    if ($person_id != "" && is_numeric($person_id)){
      $sql .= sprintf(" AND poster = %d",
                mysql_real_escape_string($person_id));
    }
    
    if ($category !== ""){
      $sql .= sprintf(" AND category = '%s'",
                mysql_real_escape_string($category));;
    }
      
    if ($public){
      $sql .= " AND type = 0";
    }
          
    $sql .= " ORDER BY CAST(tags AS UNSIGNED) ASC, date_start DESC";

    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }

    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $item = new Miki_event($row['id']);
      $return[] = $item;
    }
    return $return;
  }
  
   /**
   * Récupére les événements d'une date donnée
   *    
   * @param int $date ne retourne que les événements se passant à la date donnée. (date au format j-m-yyyy)
   * @param boolean $public si true : ne retourne que les événements de visibilité publique (type = 0). Si false, retourne tous les événements (visibilité privée + publique)
   * @param int $nb nombre d'événements à retourner par page. Si = "" on retourne tous les événements
   * @param int $page numéro de la page à retourner 
   * @param int $total Cette variable sera modifiée avec le nombre total d’enregistrements qui seraient retournés par la requête sans la clause LIMIT   
   * 
   * @static        
   *    
   * @return mixed un tableau d'élément de type Miki_event   
   */     
  public static function get_from_date($date, $public = false, $nb = "", $page = 1, &$total){
    $date = explode("-", $date);
    $date = $date[2] .'-' .$date[1] .'-' .$date[0];
  
    $return = array();
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM miki_event WHERE DATE(date_stop) >= '$date' AND DATE(date_start) <= '$date'";
    
    if ($public){
      $sql .= " AND type = 0";
    }
          
    $sql .= " ORDER BY date_start DESC";

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
      $item = new Miki_event($row['id']);
      $return[] = $item;
    }
    
    return $return;
  }
  
  /**
   * Récupére tous les événements
   *    
   * @param int $person_id si renseigné, ne retourne que les événements dont le posteur est la personne donnée
   * @param boolean $public si true : ne retourne que les événements de visibilité publique (type = 0). Si false, retourne tous les événements (visibilité privée + publique)
   * @param string $category retourne uniquement les événements de la catégorie donnée   
   * @param int $nb nombre d'événements à retourner par page. Si = "" on retourne tous les événements
   * @param int $page numéro de la page à retourner   
   * @param int $total Cette variable sera modifiée avec le nombre total d’enregistrements qui seraient retournés par la requête sans la clause LIMIT   
   * 
   * @static      
   *    
   * @return mixed Un tableau d'éléments de type Miki_event   
   */     
  public static function get_all_events($person_id = "", $public = false, $category = "", $nb = "", $page = 1, &$total){
    $return = array();
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM miki_event WHERE 1";
    
    if ($person_id != "" && is_numeric($person_id)){
      $sql .= sprintf(" AND poster = %d",
                mysql_real_escape_string($person_id));
    }
      
    if ($public){
      $sql .= " AND type = 0";
    }
    
    if ($category !== ""){
      $sql .= sprintf(" AND category = '%s'",
                mysql_real_escape_string($category));;
    }
          
    $sql .= " ORDER BY id DESC";
    
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
      $item = new Miki_event($row['id']);
      $return[] = $item;
    }
    
    return $return;
  }
}
?>