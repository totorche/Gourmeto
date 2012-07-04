<?php
/**
 * Classe Miki_object_booking
 * @package Miki
 */ 

/**
 * Inclut les fonctions d'envoi d'e-mail
 */ 
require_once("class.phpmailer.php");

/**
 * Représentation d'un objet pouvant être un lieu, un établissement, etc.
 * 
 * Cette classe peut être utilisée pour différents objets.  
 * Un objet peut être réservé par un visiteur.  
 * 
 * @package Miki  
 */ 
class Miki_object_booking{

  /**
   * Id de la réservation
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Id de l'objet lié à la réservation
   * 
   * @var int
   * @access public         
   */     
   public $id_object;
   
   /**
   * Id de la personne ayant effectué la réservation
   * 
   * @var int
   * @access public         
   */     
   public $id_person;
  
  /**
   * Date à laquelle la réservation a été effectuée (format yyyy-mm-dd hh:mm:ss)
   *      
   * @var string
   * @access public   
   */
  public $date_booking;
  
  /**
   * Date à laquelle débute la réservation au format (format yyyy-mm-dd)
   *      
   * @var string
   * @access public   
   */
  public $date_start;
  
  /**
   * Date à laquelle se termine la réservation (format yyyy-mm-dd)
   *      
   * @var string
   * @access public   
   */
  public $date_stop;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge la réservation dont l'id a été donné
   * 
   * @param int $id Id de la réservation à charger (optionnel)
   */
  function __construct($id = ""){
    // charge la réservation si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge une réservation depuis un id
   *    
   * Si la réservation n'existe pas, une exception est levée.
   *    
   * @param int $id id de la réservation à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_object_booking WHERE id = %d",
      mysql_real_escape_string($id));

    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql");
    
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La réservation demandée n'existe pas"));
    while ($row = mysql_fetch_array($result)){
      $this->id                               = $row['id'];
      $this->id_object                        = $row['id_object'];
      $this->id_person                        = $row['id_person'];
      $this->date_booking                     = stripslashes($row['date_booking']);
      $this->date_start                       = stripslashes($row['date_start']);
      $this->date_stop                        = stripslashes($row['date_stop']);
    }
    return true;
  }
  
  /**
   * Sauvegarde la réservation dans la base de données.
   *    
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   * 
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */
  public function save(){
    // si l'id de la réservation existe, c'est que la réservation existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    $sql = sprintf("INSERT INTO miki_object_booking
                    (id_object, id_person, date_booking, date_start, date_stop) VALUES(%d, %d, NOW(), '%s', '%s')",
      mysql_real_escape_string($this->id_object),
      mysql_real_escape_string($this->id_person),
      mysql_real_escape_string($this->date_start),
      mysql_real_escape_string($this->date_stop));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'ajout de la réservation dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    
    // recharge la réservation
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour la réservation dans la base de données.
   * 
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si une erreur survient, une exception est levée   
   *    
   * @return boolean   
   */  
  public function update(){
    // si aucun id existe, c'est que l'objet n'existe pas encore dans la bdd. 
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
    
    $sql = sprintf("UPDATE miki_object_booking SET id_object = %d, id_person = %d, date_booking = '%s', date_start = '%s', date_stop = '%s' WHERE id = %d",
      mysql_real_escape_string($this->id_object),
      mysql_real_escape_string($this->id_person),
      mysql_real_escape_string($this->date_booking),
      mysql_real_escape_string($this->date_start),
      mysql_real_escape_string($this->date_stop));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de la réservation dans la base de données : ") ."<br />" .mysql_error());
    }
    
    // applique les modifications
    mysql_query("COMMIT");
    
    return true;
  }
  
  /**
   * Supprime la réservation
   * 
   * Si une erreur survient, une exception est levée       
   * 
   * @return boolean
   */         
  public function delete(){
    // supprime la réservation de la base de données
    $sql = sprintf("DELETE FROM miki_object_booking WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la réservation : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Envoi la réservation par e-mail à l'adresse donnée
   * 
   * Si une erreur survient, une exception est levée 
   *      
   * @param string $email Adresse e-mail à qui la réservation va être envoyée.
   * @param string $lang Si != "", envoit le mail dans la langue dont le code est donné. Sinon envoit le mail dans la langue principale.   
   *      
   * @return boolean
   */         
  public function send_to($email, $lang = ""){
    $sitename = Miki_configuration::get('sitename');
    $email_answer = Miki_configuration::get('email_answer');
    
    if ($lang == "")
      $lang = Miki_language::get_main_code();
    
    $object = new Miki_object($this->id_object);
    $person = new Miki_person($this->id_person);
    
    // création du mail
    $mail = new miki_email('envoi_object_booking', $lang);
  
    $mail->From     = $email_answer;
    $mail->FromName = $sitename;
    
    $date_start = date("d/m/Y", strtotime($this->date_start));
    $date_stop = date("d/m/Y", strtotime($this->date_stop));
    
    // prépare les variables nécessaires à la création de l'e-mail
    $vars_array['object_title'] = $object->title[$lang];
    $vars_array['date_start'] = $date_start;
    $vars_array['date_stop'] = $date_stop;
    $vars_array['firstname'] = $person->firstname;
    $vars_array['lastname'] = $person->lastname;
    $vars_array['email'] = $person->email1;
    $vars_array['tel'] = $person->tel1;
    $vars_array['sitename'] = $sitename;
    
    // initialise le contenu de l'e-mail
    $mail->init($vars_array);

    $mail->AddAddress($email);
    //$mail->AddAddress("herve@fbw-one.com");
    if(!$mail->Send())
      throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
    $mail->ClearAddresses();
    
    return true;
  }
  
  /**
   * Recherche le nombre de réservations total
   * 
   * @static   
   * @return int      
   */     
  public static function get_nb_object_booking(){
    $sql = "SELECT COUNT(*) FROM miki_object_booking";
    
    $result = mysql_query($sql);
    
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Recherche toutes les réservations selon certains critères
   * 
   * @param string $search Critère de recherche qui sera appliqué sur le nom de l'objet réservé (dans toutes les langues), le prénom et le nom du loueur
   * @param int $id_object si != "", on prend seulement les réservations concernant l'objet dont l'id est donné
   * @param string $order Par quel champ les réservations trouvées seront triées (id_object, date_booking, date_start, date_stop, firstname, lastname). Si vide, on tri selon la date de création (date_booking).
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * @param int $nb nombre de réservations à retourner par page. Si = "" on retourne toutes les réservations.
   * @param int $page numéro de la page à retourner     
   * 
   * @static
   * @return mixed Un tableau d'éléments Miki_object_booking représentant toutes les réservations trouvées   
   */             
  public static function search_object_booking($search = "", $id_object = "", $order = "", $order_type = "asc", $nb = "", $page = 1){
    $return = array();
    $sql = "SELECT DISTINCT(mob.id)
            FROM miki_object_booking mob,
                 miki_object mo,
                 miki_object_title mot
                 miki_person mp
            WHERE mo.id = mob.id_object AND
                  mot.id_object = mo.id AND
                  mp.id = mob.id_person";
    
    // Applique les critères de recherche
    if ($search != ""){
      $search = mb_strtolower($search, 'UTF-8');
      $sql .= sprintf(" AND (LOWER(mot.title) LIKE '%%%s%%' OR 
                            LOWER(mp.firstname) LIKE '%%%s%%' OR
                            LOWER(mp.lastname) LIKE '%%%s%%')",
                mysql_real_escape_string($search),
                mysql_real_escape_string($search),
                mysql_real_escape_string($search));
    }
    
    // ne prend que les réservations concernant l'objet donné
    if (is_numeric($id_object)){
      $sql .= " AND mob.id_object = %d";
      $sql = sprintf($sql, mysql_real_escape_string($this->id_object));
    }
    
    if ($order == "id_object")
      $sql .= " ORDER BY mob.id_object " .$order_type;
    elseif ($order == "date_booking")
      $sql .= " ORDER BY mob.date_booking " .$order_type;
    elseif ($order == "date_start")
      $sql .= " ORDER BY mob.date_start " .$order_type;
    elseif ($order == "date_stop")
      $sql .= " ORDER BY mob.date_stop " .$order_type;
    elseif ($order == "firstname")
      $sql .= " ORDER BY mp.firstname " .$order_type;
      elseif ($order == "lastname")
      $sql .= " ORDER BY mp.lastname " .$order_type;
    else
      $sql .= " ORDER BY mob.date_booking " .$order_type;
      
    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }

    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_object_booking($row['id']);
    }
    return $return;
  }
  
  /**
   * Recherche tous les objets
   * 
   * @param int $id_object si != "", on prend seulement les réservations concernant l'objet dont l'id est donné
   * @param string $order Par quel champ les réservations trouvées seront triées (id_object, date_booking, date_start, date_stop, firstname, lastname). Si vide, on tri selon la date de création (date_booking).
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * @param int $nb Nombre de réservations à retourner par page. Si = "" on retourne toutes les réservations
   * @param int $page Numéro de la page à retourner     
   * 
   * @static
   * @return mixed un tableau d'élément Miki_object_booking représentant toutes les réservations trouvées   
   */             
  public static function get_all_object_booking($id_object = "", $order = "", $order_type = "asc", $nb = "", $page = 1){
    $return = array();
    $sql = "SELECT * FROM miki_object_booking";
    
    // ne prend que les réservations concernant l'objet donné
    if (is_numeric($id_object)){
      $sql .= " WHERE id_object = %d";
      $sql = sprintf($sql, mysql_real_escape_string($this->id_object));
    }
    
    if ($order == "id_object")
      $sql .= " ORDER BY id_object " .$order_type;
    elseif ($order == "date_booking")
      $sql .= " ORDER BY date_booking " .$order_type;
    elseif ($order == "date_start")
      $sql .= " ORDER BY date_start " .$order_type;
    elseif ($order == "date_stop")
      $sql .= " ORDER BY date_stop " .$order_type;
    elseif ($order == "firstname")
      $sql .= " ORDER BY firstname " .$order_type;
      elseif ($order == "lastname")
      $sql .= " ORDER BY lastname " .$order_type;
    else
      $sql .= " ORDER BY date_booking " .$order_type;
      
    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }
    
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_object_booking($row['id']);
    }
    return $return;
  }
}
?>