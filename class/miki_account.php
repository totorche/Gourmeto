<?php
/**
 * Classe Miki_account
 * @package Miki
 */ 

/**
 * Représentation d'un compte utilisateur
 * 
 * @package Miki 
 */ 
class Miki_account{
  
  /**
   * Id du compte utilisateur
   *      
   * @var int
   * @access public   
   */     
  public $id;
  
  /**
   * Etat du compte utilisateur. Est en général utilisé pour spécifier si un compte est validé (1) ou non (0)
   *      
   * @var int
   * @access public   
   */
  public $state = 0;
  
  /**
   * Type de compte utilisateur. Plusieurs types de comptes utilisateur peuvent être nécessaires (admin, user, ...)
   *      
   * @var int
   * @access public   
   */
  public $type = 0;
  
  /**
   * Nom d'utilisateur du compte utilisateur
   *      
   * @var string
   * @access public   
   */
  public $username;
  
  /**
   * Mot de passe du compte utilisateur (crypté grâce à l'algorithme SHA1)  
   * @var string
   * @access public   
   */
  public $password;
  
  /**
   * Id de la personne (classe Miki_person)  
   * @var int
   * @access public   
   * @see Miki_person
   */
  public $person_id;
  
  /**
   * Inscrit à la newsletter 1 (0 = non inscrit, 1 = inscrit)
   * @var int
   * @access public   
   * @see Miki_person
   */
  public $newsletter;
  
  /**
   * Inscrit à la newsletter 2 (0 = non inscrit, 1 = inscrit)
   * @var int
   * @access public   
   * @see Miki_person
   */
  public $newsletter2;
  
  /**
   * Date à laquelle le compte utilisateur a été créé (format yyyy-mm-dd)  
   * @var string
   * @access public   
   */
  public $date_created;
  
  /**
   * Date de fin de de validité du compte utilisateur (utilisé p.ex. dans le cadre d'abonnement) (format yyyy-mm-dd)  
   * @var string
   * @access public   
   */
  public $date_end;
  
  /**
   * Comment l'utilisateur a connu le site  
   * @var string
   * @access public   
   */
  public $know_us;
  
  /**
   * Date de la dernière visite (format yyyy-mm-dd hh:mm:ss)  
   * @var string
   * @access public   
   */
  public $last_visit;
  
  /**
   * Code de promotion utilisé lors de l'inscription  
   * @var string
   * @access public   
   */
  public $code_promo;
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le compte dont l'id a été donné
   *    
   * @param int $id Id du compte à charger (optionnel)   
   */     
  function __construct($id = ""){
    // charge le compte si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un compte utilisateur depuis un id.
   *    
   * Si le compte n'existe pas, une exception est levée.
   *    
   * @param int $id id du compte à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_account WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le compte demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id           = $row['id'];
    $this->state        = $row['state'];
    $this->type         = $row['type'];
    $this->username     = $row['username'];
    $this->password     = $row['password'];
    $this->newsletter   = $row['newsletter'];
    $this->newsletter2  = $row['newsletter2'];
    $this->person_id    = $row['person_id'];
    $this->date_created = $row['date_created'];
    $this->date_end     = $row['date_end'] == "" ? 'NULL' : $row['date_end'];
    $this->know_us      = $row['know_us'];
    $this->last_visit   = $row['last_visit'];
    $this->code_promo   = $row['code_promo'];
    return true;
  }
  
  /**
   * Charge un compte utilisateur depuis l'id d'une personne (Miki_person)
   *    
   * Si le compte n'existe pas, une exception est levée
   *    
   * @param int $person_id id de la personne liée au compte à charger
   * @see Miki_person   
   * @return boolean true si le chargement s'est déroulé correctement   
   */ 
  public function load_from_person($person_id){
    $sql = sprintf("SELECT * FROM miki_account WHERE person_id = %d",
      mysql_real_escape_string($person_id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le compte demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id           = $row['id'];
    $this->state        = $row['state'];
    $this->type         = $row['type'];
    $this->username     = $row['username'];
    $this->password     = $row['password'];
    $this->newsletter   = $row['newsletter'];
    $this->newsletter2  = $row['newsletter2'];
    $this->person_id    = $row['person_id'];
    $this->date_created = $row['date_created'];
    $this->date_end     = $row['date_end'] == "" ? 'NULL' : $row['date_end'];
    $this->know_us      = $row['know_us'];
    $this->last_visit   = $row['last_visit'];
    $this->code_promo   = $row['code_promo'];
    return true;
  }
  
  /**
   * Charge un compte utilisateur depuis son nom d'utilisateur
   *    
   * Si le compte n'existe pas, une exception est levée
   *    
   * @param string $username nom d'utilisateur du compte à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */ 
  public function load_from_username($username){
    $sql = sprintf("SELECT * FROM miki_account WHERE username = '%s'",
      mysql_real_escape_string($username));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le compte demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id           = $row['id'];
    $this->state        = $row['state'];
    $this->type         = $row['type'];
    $this->username     = $row['username'];
    $this->password     = $row['password'];
    $this->newsletter   = $row['newsletter'];
    $this->newsletter2  = $row['newsletter2'];
    $this->person_id    = $row['person_id'];
    $this->date_created = $row['date_created'];
    $this->date_end     = $row['date_end'] == "" ? 'NULL' : $row['date_end'];
    $this->know_us      = $row['know_us'];
    $this->last_visit   = $row['last_visit'];
    $this->code_promo   = $row['code_promo'];
    return true;
  }
  
  /**
   * Sauvegarde le compte utilisateur dans la base de données.
   *    
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   *    
   * Si le nom d'utilisateur donné existe déjà, une exception est levée
   * Si une erreur survient, une exception est levée   
   *    
   * @return boolean   
   */
  public function save(){      
    // si un l'id du compte existe, c'est que le compte existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // vérifie que le nom de l'utilisateur n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_account WHERE username = '%s'",
      mysql_real_escape_string($this->username));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Un utilisateur avec le même nom existe déjà dans la base de données"));  
    
    $sql = sprintf("INSERT INTO miki_account (state, type, username, password, newsletter, newsletter2, person_id, date_created, date_end, know_us, code_promo) values(%d, %d, '%s', '%s', %d, %d, %d, NOW(), '%s', '%s', '%s')",
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->type),
      mysql_real_escape_string($this->username),
      mysql_real_escape_string($this->password),
      mysql_real_escape_string($this->newsletter),
      mysql_real_escape_string($this->newsletter2),
      mysql_real_escape_string($this->person_id),
      mysql_real_escape_string($this->date_end),
      mysql_real_escape_string($this->know_us),
      mysql_real_escape_string($this->code_promo));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion du compte dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge le compte
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour le compte utilisateur dans la base de données.
   *    
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si le nom d'utilisateur donné existe déjà, une exception est levée
   * Si une erreur survient, une exception est levée     
   *    
   * @return boolean   
   */
  public function update(){      
    // si aucun id existe, c'est que le compte n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // vérifie que le nom de l'utilisateur n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_account WHERE username = '%s' AND  id <> %d",
      mysql_real_escape_string($this->username),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Un utilisateur avec le même nom existe déjà dans la base de données"));  
    
    $sql = sprintf("UPDATE miki_account SET state = %d, type = %d, username = '%s', password = '%s', newsletter = %d, newsletter2 = %d, person_id = %d, date_end = '%s', know_us = '%s', code_promo = '%s' where id = %d",
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->type),
      mysql_real_escape_string($this->username),
      mysql_real_escape_string($this->password),
      mysql_real_escape_string($this->newsletter),
      mysql_real_escape_string($this->newsletter2),
      mysql_real_escape_string($this->person_id),
      mysql_real_escape_string($this->date_end),
      mysql_real_escape_string($this->know_us),
      mysql_real_escape_string($this->code_promo),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du compte dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime le compte utilisateur
   * 
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */     
  public function delete(){
    $sql = sprintf("DELETE FROM miki_account WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du compte : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Affecte un nouveau mot de passe au compte utilisateur
   *    
   * @param string $password nouveau mot de passe (en clair)
   * @return string le mot de passe donné      
   */     
  public function set_password($password){
    $this->password = sha1($password);
  	return $password;
  }
  
  /**
   * Génère un nouveau mot de passe
   *    
   * @return string le mot de passe généré
   */   
  public function new_password(){
  	srand((double)microtime()*1000000); 
  	$password = "";
  	for ($i=0;$i<8;$i++)
  		$password .= chr(rand(97,122));
  		
  	$this->password = sha1($password);
  	$this->update();
  	return $password;
  }
  
  /**
   * Envoie un e-mail à l'utilisateur avec son nouveau mot de passe
   * 
   * Si une erreur survient, une exception est levée    
   *      
   * @return boolean
   */   
  public function send_password(){
    $sitename = Miki_configuration::get('sitename');
    $email_answer = Miki_configuration::get('email_answer');
    
    $password = $this->new_password();
    $person_to = new Miki_person($this->person_id);
      
    // création du mail
    $mail = new miki_email('envoi_mot_de_passe', $person_to->language);
    
    $mail->From     = $email_answer;
    $mail->FromName = $sitename;
    
    // prépare les variables nécessaires à la création de l'e-mail
    $vars_array['person_to'] = $person_to;
    $vars_array['password'] = $password;
    $vars_array['sitename'] = $sitename;
    
    // initialise le contenu de l'e-mail
    $mail->init($vars_array);
    
    $mail->AddAddress($person_to->email1);
    //$mail->AddAddress("herve@fbw-one.com");
    if(!$mail->Send())
      throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
      
    return true;
  }
  
  /**
   * Accepte l'inscription d'un utilisateur
   * 
   * Accepte l'inscription d'un utilisateur si une demande de validation a été faite 
   * et envoi un e-mail de confirmation à l'utilisateur.   
   *    
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */   
  public function accept(){
    $sitename = Miki_configuration::get('sitename');
    $site_url = Miki_configuration::get('site_url');
    $email_answer = Miki_configuration::get('email_answer');
    
    $person_to = new Miki_person($this->person_id);
    
    // création du mail
    $mail = new miki_email('confirmation_inscription', $person_to->language);
    
    $mail->From     = $email_answer;
    $mail->FromName = $sitename;
    
    // prépare les variables nécessaires à la création de l'e-mail
    $vars_array['person_to'] = $person_to;
    $vars_array['sitename'] = $sitename;
    $vars_array['site_url'] = $site_url;
    
    // initialise le contenu de l'e-mail
    $mail->init($vars_array);
    
    $mail->AddAddress($person_to->email1);
    //$mail->AddAddress("herve@fbw-one.com");
    if(!$mail->Send())
      throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
    
    // active le compte
    $this->state = 1;
    $this->update();
    
    return true;
  }
  
  /**
   * Refuse l'inscription d'un utilisateur si une demande de validation a été faite.
   * 
   * Refuse l'inscription d'un utilisateur si une demande de validation a été faite et      
   * envoie un e-mail d'information à l'utilisateur. 
   * 
   * Le compte utilisateur est supprimé de la base de donnée
   *    
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */   
  public function refuse(){
    $sitename = Miki_configuration::get('sitename');
    $email_answer = Miki_configuration::get('email_answer');
      
    $person_to = new Miki_person($this->person_id);
    
    // création du mail
    $mail = new miki_email('refus_inscription', $person_to->language);
    
    $mail->From     = $email_answer;
    $mail->FromName = $sitename;
    
    // prépare les variables nécessaires à la création de l'e-mail
    $vars_array['person_to'] = $person_to;
    $vars_array['sitename'] = $sitename;
    
    // initialise le contenu de l'e-mail
    $mail->init($vars_array);
    
    $mail->AddAddress($person_to->email1);
    //$mail->AddAddress("herve@fbw-one.com");
    if(!$mail->Send())
      throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
    
    // supprime la société si elle existe
    if (isset($person_to->company_id) && is_numeric($person_to->company_id)){
      try{
        $company = new Miki_company($person_to->company_id);
        $company->delete();
      }
      catch(Exception $e){
        // si une erreur survient, c'est que la société n'existe pas et il n'y a donc pas besoin de la supprimer
      }
    }
    
    // supprime la personne et le compte utilisateur
    $person_to->delete();
    
    return true;
  }
  
  /**
   * Met à jour la date de la dernière visite du membre
   *    
   * @return boolean
   */      
  public function visit(){
    $sql = sprintf("UPDATE miki_account SET last_visit = NOW() WHERE id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour de la dernière visite dans la base de données : ") ."<br />" .mysql_error());
      
    $this->last_visit = date("Y-m-d H:i:s");
    return true;
  }
  
  /**
   * Vérifie si un compte avec un nom d'utilisateur donné existe déjà
   *    
   * @param string $username nom d'utilisateur à tester
   * @return boolean true si le un compte existe, false sinon   
   */ 
  public static function username_exists($username){
    $sql = sprintf("SELECT id FROM miki_account WHERE username = '%s'",
      mysql_real_escape_string($username));
    $result = mysql_query($sql);
    return mysql_num_rows($result) > 0;
  }
  
  /**
   * Teste si un utilisateur avec un nom d'utilisateur et un mot de passe donnés existe bien
   *    
   * @static
   * @param string $username nom d'utilisateur à tester
   * @param string $password mot de passe à tester
   * @return int l'id du compte trouvé si un compte a été trouvé, -1 sinon
   */              
  public static function test_user($username, $password){
    // teste le nom d'utilisateur et mot de passe
    $sql = sprintf("SELECT id, person_id, password FROM miki_account WHERE username = '%s'",
      mysql_real_escape_string($username));
	  $result = mysql_query($sql);
	  if (mysql_num_rows($result) > 0){
      $row = mysql_fetch_array($result);
      if ($row['password'] === sha1($password))
        return $row['id'];
      else
        return -1;
    }
    else
      return -1;
  }

  // fonction statique récupérant tous les comptes
  // 
  // si $active = true, on ne récupert que les comptes validés
  // $type = le type de compte que l'on veut récupérer. Si plusieurs type, les séparer par une virgule ','
  /**
   * Récupère tous les comptes utilisateur
   *    
   * @static
   * @param boolean $newsletter Si true, on ne récupert que les comptes inscrits à la newsletter
   * @param boolean $active si true, on ne récupert que les comptes validés
   * @param int|string $type le type de compte que l'on veut récupérer. Si plusieurs type, les séparer par une virgule ','
   * @return mixed un tableau contenant tous les comptes utilisateur récupérés
   */ 
  public static function get_all_accounts($newsletter = false, $active = true, $type = ""){
    $return = array();
    $sql = "SELECT * FROM miki_account";
    
    $search = "";
    
    // si on ne prend que les inscrits à la newsletter
    if ($newsletter)
      $search = " WHERE newsletter = 1";
      
    // si on ne prend que les comptes activés
    if ($active){
      if ($search == "")
        $search = " WHERE state = 1";
      else
        $search .= " AND  state = 1";
    }
    
    // si on ne prend que les comptes activés
    if ($type != ""){
      if ($search == "")
        $search = " WHERE type in ($type)";
      else
        $search .= " AND  type in ($type)";
    }
    
    $sql .= $search;
      
    $sql .= " ORDER BY state asc, date_created desc";
    
    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $item = new Miki_account($row['id']);
      $return[] = $item;
    }
    return $return;
  }
}
?>