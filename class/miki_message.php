<?php
/**
 * Classe Miki_message
 * @package Miki
 */ 

/**
 * Représentation d'un message
 * 
 * Un message est envoyé par un membre (Miki_person) à un autre membre  
 * 
 * @see Miki_person
 *  
 * @package Miki  
 */ 
class Miki_message{
  
  /**
   * Id du message
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Id de la personne (Miki_person) ayant envoyé le message
   *    
   * @var int
   * @see Miki_person   
   * @access public   
   */
  public $message_from;
  
  /**
   * Id de la personne (Miki_person) à qui le message a été envoyé
   *    
   * @var int
   * @see Miki_person   
   * @access public   
   */
  public $message_to;
  
  /**
   * Sujet du message
   *      
   * @var string
   * @access public   
   */
  public $subject;
  
  /**
   * Texte du message
   *      
   * @var string
   * @access public   
   */
  public $text;
  
  /**
   * Date à laquelle le message a été envoyé
   *      
   * @var string
   * @access public   
   */
  public $date;
  
  /**
   * Flag permettant de savoir si on a répondu au message
   *      
   * @var int
   * @access public   
   */
  public $answered = 0;
  
  /**
   * Etat du message. 0 = Non lu, 1 = lu
   *      
   * @var int
   * @access public   
   */
  public $state = 0;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le message dont l'id a été donné
   * 
   * @param int $id Id du message à charger (optionnel)
   */
  function __construct($id = ""){
    // charge le message si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un message depuis un id
   *    
   * Si le message n'existe pas, une exception est levée.
   *    
   * @param int $id id du message à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_message WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le message demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id           = $row['id'];
    $this->message_from = $row['message_from'];
    $this->message_to   = $row['message_to'];
    $this->subject      = $row['subject'];
    $this->text         = $row['text'];
    $this->date         = $row['date'];
    $this->answered     = $row['answered'];
    $this->state        = $row['state'];
    return true;
  }
  
  /**
   * Sauvegarde le message dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){      
    // si un l'id du message existe, c'est que le message existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    $sql = sprintf("INSERT INTO miki_message (message_from, message_to, subject, text, date, answered, state) VALUES(%d, %d, '%s', '%s', NOW(), %d, %d)",
      mysql_real_escape_string($this->message_from),
      mysql_real_escape_string($this->message_to),
      mysql_real_escape_string($this->subject),
      mysql_real_escape_string($this->text),
      mysql_real_escape_string($this->answered),
      mysql_real_escape_string($this->state));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion du message dans la base de données : ") ."<br />$sql<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge le message
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour le message dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){      
    // si aucun id existe, c'est que le message n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
    
    $sql = sprintf("UPDATE miki_message SET message_from = %d, message_to = %d, subject = '%s', text = '%s', answered = %d, state = %d WHERE id = %d",
      mysql_real_escape_string($this->message_from),
      mysql_real_escape_string($this->message_to),
      mysql_real_escape_string($this->subject),
      mysql_real_escape_string($this->text),
      mysql_real_escape_string($this->answered),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du message dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime le message 
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_message WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du message : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Définit le message comme ayant été lu
   */     
  public function set_answered(){
    $this->answered = 1;
    $this->update();
  }
  
  /**
   * Envoi un e-mail au destinataire du message l'informanque qu'il a reçu un message dans sa boîte de réception
   */   
  public function send(){
    // enregistre le message
    $this->save();
    
    $sitename = Miki_configuration::get('sitename');
    $site_url = Miki_configuration::get('site_url');
    $email_answer = Miki_configuration::get('email_answer');
    
    // récupert les sociétés émettrice et réceptrice
    $person_from = new Miki_person($this->message_from);
    $company_from = new Miki_company($person_from->company_id);
    $person_to = new Miki_person($this->message_to);
    $company_to = new Miki_company($person_to->company_id);

    // création du mail
    $mail = new miki_email('envoi_message', $person_to->language);
  
    $mail->From     = $email_answer;
    $mail->FromName = $sitename;
    
    // prépare les variables nécessaires à la création de l'e-mail
    $vars_array['person_to'] = $person_to;
    $vars_array['person_from'] = $person_from;
    $vars_array['message'] = $this;
    $vars_array['sitename'] = $sitename;
    $vars_array['site_url'] = $site_url;
    
    // initialise le contenu de l'e-mail
    $mail->init($vars_array);

    $mail->AddAddress($person_to->email1);
    //$mail->AddAddress("herve@fbw-one.com");
    if(!$mail->Send())
      throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
    $mail->ClearAddresses();
  }
  
  /**
   * Retourne une partie du texte du message
   * 
   * Si le texte du message est plus court que la partie demandée, le texte du message est retourné en entier.       
   * 
   * @param int $nb_char Nombre de caractères désirés
   * @param boolean $full_word Si true, le texte est coupé par mots entiers. Si false, les mots peuvent être coupé en plein milieu 
   *    
   * @return string La partie demandée du texte du message       
   */
  public function get_text($nb_char, $full_word = true){
    if ($nb_char < mb_strlen($this->text)){
      if ($full_word === false)
        $stop = $nb_char;
      else{
        $stop = mb_strpos($this->text, ' ', $nb_char);
        if ($stop === false){
          $stop = mb_strlen($this->text);
        }
      }
      return mb_substr($this->text, 0, $stop);
    }
    else
      return $this->text;
  }
  
  /** 
   * Compare deux messages selon leur date d'expédition
   * 
   * @param Miki_message $a Message à comparer n° 1
   * @param Miki_message $b Message à comparer n° 2   
   * @access private
   * @return int -1 si le message $a a été envoyé après le message $b, 1 sinon
   */     
  private static function cmp($a, $b){
    if ($a->date == $b->date) 
      return 0;
    return ($a->date > $b->date) ? -1 : 1;
  }

  /** 
   * Récupère tous les messages d'un expéditeur (Miki_person) ordonnés selon leur date d'expédition
   *
   * @param int $person_id Id de l'expéditeur dont on doit récupérer les messages
   * 
   * @see Miki_person      
   * @static
   * @return mixed Un tableau d'éléments de type Miki_message représentant les messages de l'expéditeur passé en paramètre
   */     
  public static function get_all_messages_from($person_id){
    $return = array();
    $sql = "SELECT * FROM miki_message WHERE message_from = $person_id";
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_message($row['id']);
    }
    
    // ordonne le tableau en fonction de la date de chaque message
    usort($return, "Miki_message::cmp");
    
    return $return;
  }
  
  /** 
   * Récupère tous les messages destinés au membre (Miki_person) passé en paramètre ordonnés selon leur date d'expédition
   *
   * @param int $person_id Id du destinataire des messages que l'on doit récupérer
   * @param int $state Si 1, on ne prend que les messages lus. Si 0 que les non-lus. Si le $state n'est pas renseigné (ou = ""), on prend tous les messages.
   * 
   * @see Miki_person         
   * @static
   * @return mixed Un tableau d'éléments de type Miki_message représentant les messages destinés à la personne passée en paramètre
   */ 
  public static function get_all_messages_to($person_id, $state = ""){
    $return = array();
    $sql = "SELECT * FROM miki_message WHERE message_to = $person_id";
    
    if ($state !== "" && is_numeric($state))
      $sql .= " AND  state = $state";
    
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $item = new Miki_message($row['id']);
      $return[] = $item;
    }
    
    // ordonne le tableau en fonction de la date de chaque message
    usort($return, "Miki_message::cmp");
    
    return $return;
  }
   
  /** 
   * Récupère tous les messages entre deux personnes passées en paramètre ordonnés selon leur date d'expédition
   *
   * @param int $person_id1 Correspondant n° 1
   * @param int $person_id2 Correspondant n° 2
   * 
   * @see Miki_person
   * @static
   * @return mixed Un tableau d'éléments de type Miki_message représentant les messages entre deux personnes passées en paramètre
   */
  public static function get_all_messages_between($person_id1, $person_id2){
    $return = array();
    
    // recherche de personne1 à personne2
    $sql = "SELECT * FROM miki_message WHERE message_from = $person_id1 AND  message_to = $person_id2";
    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $item = new Miki_message($row['id']);
      $return[] = $item;
    }
    
    // recherche de personne2 à personne1
    $sql = "SELECT * FROM miki_message WHERE message_from = $person_id2 AND  message_to = $person_id1";
    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $item = new Miki_message($row['id']);
      $return[] = $item;
    }
    
    // ordonne le tableau en fonction de la date de chaque message
    usort($return, "Miki_message::cmp");
    
    return $return;
  }
}
?>