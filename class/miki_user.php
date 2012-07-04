<?php
/**
 * Classe Miki_user
 * @package Miki
 */ 

/**
 * Représentation d'un utilisateur du Miki (console d'administration du site web)
 * 
 * Un utilisateur fait partie d'un groupe d'utilisateurs (Miki_group).
 * Chaque groupe d'utilisateurs possède un certains nombre de droits à effectuer des actions (Miki_action).    
 * 
 * @see Miki_group
 * @see Miki_action 
 *  
 * @package Miki  
 */
class Miki_user{

  /**
   * Id de l'utilisateur
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom de l'utilisateur
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Mot de passe de l'utilisateur
   *      
   * @var string
   * @access public   
   */
  public $password;
  
  /**
   * Prénom de l'utilisateur
   *      
   * @var string
   * @access public   
   */
  public $firstname;
  
  /**
   * Nom de famille de l'utilisateur
   *      
   * @var string
   * @access public   
   */
  public $lastname;
  
  /**
   * Adresse e-mail de l'utilisateur
   *      
   * @var string
   * @access public   
   */
  public $email;
  
  /**
   * Page par défaut lorsque l'utilisateur se loggue sur le Miki (console d'administration)
   *      
   * @var int
   * @access public   
   */
  public $default_page;
  
  /**
   * Etat de l'utilisateur (0 = désactivé, 1 = activé)
   *      
   * @var int
   * @access public   
   */
  public $state = 0;
  
  /**
   * Clé API de l'utilisateur pour accéder à l'API Miki
   *      
   * @var string
   * @access public   
   */
  public $apikey;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge l'utilisateur dont l'id a été donné
   * 
   * @param int $id Id de l'utilisateur à charger (optionnel)
   */
  function __construct($id = ""){     
    // charge l'utilisateur si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un utilisateur depuis un id
   *    
   * Si l'utilisateur n'existe pas, une exception est levée.
   *    
   * @param int $id Id de l'utilisateur à charger
   * @return boolean True si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_user WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("L'utilisateur demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->password = $row['password'];
    $this->firstname = $row['firstname'];
    $this->lastname = $row['lastname'];
    $this->email = $row['email'];
    $this->default_page = $row['default_page'];
    $this->state = $row['state'];
    $this->apikey = $row['apikey'];
    return true;
  }
  
  /**
   * Charge un utilisateur depuis un nom
   *    
   * Si l'utilisateur n'existe pas, une exception est levée.
   *    
   * @param string $name Nom de l'utilisateur à charger
   * @return boolean True si le chargement s'est déroulé correctement   
   */
  public function load_from_name($name){
    $sql = sprintf("SELECT * FROM miki_user WHERE name = '%s'",
      mysql_real_escape_string($name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      return false;
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->password = $row['password'];
    $this->firstname = $row['firstname'];
    $this->lastname = $row['lastname'];
    $this->email = $row['email'];
    $this->default_page = $row['default_page'];
    $this->state = $row['state'];
    $this->apikey = $row['apikey'];
    return true;
  }
  
  /**
   * Sauvegarde l'utilisateur dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * L'utilisateur doit posséder un nom unique (name). Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été attribué à l'utilisateur"));
      
    // si un l'id de l'utilisateur existe, c'est que l'utilisateur existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // vérifie que le nom de l'utilisateur n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_user WHERE name = '%s'",
      mysql_real_escape_string($this->name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Un utilisateur du même nom existe déjà dans la base de données"));
      
    // sauve l'utilisateur
    $sql = sprintf("INSERT INTO miki_user (name, password, firstname, lastname, email, state, default_page, apikey) VALUES('%s', '%s', '%s', '%s', '%s', %d, %d, '%s')",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->password),
      mysql_real_escape_string($this->firstname),
      mysql_real_escape_string($this->lastname),
      mysql_real_escape_string($this->email),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->default_page),
      mysql_real_escape_string($this->apikey));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion de l'utilisateur dans la base de données : ") ."<br />" .mysql_error());
    
    // récupert l'id
    $this->id = mysql_insert_id();
    
    // recharge la page
    $this->load($this->id);
    return true;
  }
  
  /**
   * Met à jour l'utilisateur dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * L'utilisateur doit posséder un nom unique (name). Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été attribué à l'utilisateur"));
      
    // si aucun id existe, c'est que l'utilisateur n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // vérifie que le nom de l'utilisateur n'existe pas déjà dans la base de données
    $sql = sprintf("SELECT id FROM miki_user WHERE name = '%s' AND  id != %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0)
      throw new Exception(_("Un utilisateur du même nom existe déjà dans la base de données"));
    
    // met à jour l'utilisateur
    $sql = sprintf("UPDATE miki_user SET name = '%s', password = '%s', firstname = '%s', lastname = '%s', email = '%s', state = %d, default_page = %s, apikey = '%s' WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->password),
      mysql_real_escape_string($this->firstname),
      mysql_real_escape_string($this->lastname),
      mysql_real_escape_string($this->email),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->default_page),
      mysql_real_escape_string($this->apikey),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour de l'utilisateur dans la base de données : ") ."<br />" .mysql_error());
    
    return true;
  }
  
  /**
   * Supprime l'utilisateur 
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){    
    $sql = sprintf("DELETE FROM miki_user WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de l'utilisateur : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Change l'état de l'utilisateur : si actif -> inactif et si inactif -> actif. 
   * 
   * Si une erreur survient, une exception est levée.
   * 
   * @param int $action si != "", le nouvel état de l'utilisateur sera égal à $action            
   * 
   * @return boolean      
   */ 
  public function change_state($action = ""){     
    if ($action != "")
      $this->state = $action;
    else
      $this->state = ($this->state + 1) % 2;
    
    $sql = sprintf("UPDATE miki_user SET state = %d WHERE id = %d",
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      throw new Exception(_("Erreur pendant la modification de l'état de l'utilisateur : ") ."<br />" .mysql_error());
    }
    return true;
  }
  
  /**
   * Ajoute l'utilisateur dans le groupe donné
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @param Miki_group $group Le groupe dans lequel l'utilisateur doit être ajouté.
   * 
   *
   * @return boolean          
   */   
  public function add_group(Miki_group $group){
    // si l'utilisateur n'existe pas encore dans la base de données, on essaye de l'y insérer
    if (!isset($this->id)){
      try{
        $this->save();
      }catch(Exception $e){
        throw new Exception(_("L'utilisateur n'a pas pu être ajouté à la base de données."));
      }
    }
    // insert l'utilisateur dans la groupe. Si il y est déjà, ne fait rien (mot-clé "ignore")
    $sql = sprintf("insert ignore into miki_user_s_group (user_id, group_id) VALUES(%d, %d)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($group->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant l'ajout de l'utilisateur au groupe : ") ."<br />" .mysql_error());
    
    return true;
  }
  
  /**
   * supprime l'utilisateur de tous les groupes
   * 
   * Si une erreur survient, une exception est levée.
   *       
   * @return boolean      
   */   
  public function remove_groups(){
    $sql = sprintf("DELETE FROM miki_user_s_group WHERE user_id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression des liaisons entre l'utilisateur et les groupes : ") ."<br />" .mysql_error());
      
    return true;
  }
  
  /**
   * Ajoute une permission (Miki_action) à l'utilisateur.
   * 
   * Si une erreur survient, une exception est levée.      
   * 
   * @param Miki_action L'action que l'utilisateur a le droit d'effectuer.
   * 
   * @see Miki_action
   * @return boolean
   */    
  public function add_action(Miki_action $action){
    // si l'utilisateur n'existe pas encore dans la base de données, on essaye de l'y insérer
    if (!isset($this->id)){
      try{
        $this->save();
      }catch(Exception $e){
        throw new Exception(_("L'utilisateur n'a pas pu être ajouté à la base de données."));
      }
    }
    // ajoute la permission à l'utilisateur. Si elle y est déjà, ne fait rien (mot-clé "ignore")
    $sql = sprintf("insert ignore into miki_user_s_action (user_id, action_id) VALUES(%d, %d)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($action->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant l'ajout de la permission à l'utilisateur : ") ."<br />" .mysql_error());
    
    return true;
  }
  
  /**
   * Enlève une permission (Miki_action) à l'utilisateur.
   * 
   * Si une erreur survient, une exception est levée.      
   * 
   * @param Miki_action L'action que l'utilisateur n'a pas le droit d'effectuer.
   * 
   * @see Miki_action
   * @return boolean
   */ 
  public function remove_action(Miki_action $action){
    $sql = sprintf("DELETE FROM miki_user_s_action WHERE user_id = %d AND  action_id = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($action->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la permission de l'utilisateur : ") ."<br />" .mysql_error());
      
    return true;
  }
  
  /**
   * Affecte un password à l'utilisateur.
   * 
   * Les mots de passe sont codés avec l'algorithme SHA1. Il est donc impossible de décoder un mot de passe.
   *          
   * @return string Le mot de passe en clair   
   */   
  public function set_password($password){
    $this->password = sha1($password);
  	return $password;
  }
  
  /**
   * Génère un nouveau mot de passe aléatoire.
   * 
   * @return Le nouveua mot de passe en clair.      
   */   
  public function new_password(){
  	srand((double)microtime()*1000000); 
  	$password = "";
  	for ($i=0;$i<8;$i++)
  		$password .= chr(rand(97,122));
  		
  	$this->password = sha1($password);
  	return $password;
  }
  
  /**
   * Recherche tous les groupes dont l'utilisateur fait partie
   * 
   * @see Miki_group   
   * @return mixed Un tableau d'éléments de type Miki_group représentant les groupes dont l'utilisateur fait partie.      
   */   
  public function get_groups(){
    $return = array();
    $sql = sprintf("SELECT group_id FROM miki_user_s_group WHERE user_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_row($result)){
      $return[] = new Miki_group($row[0]);
    }
    return $return;
  }
  
  /**
   * Vérifie si l'utilisateur fait partie du groupe donné en paramètre
   * 
   * @param Miki_group $group Le groupe dont on veut savoir si l'utilisateur fait partie.
   * 
   * @see Miki_group   
   * @return boolean     
   */   
  public function has_group(Miki_group $group){
    $return = array();
    $sql = sprintf("SELECT count(*) FROM miki_user_s_group WHERE user_id = %d AND  group_id = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($group->id));
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row[0] > 0;
  }
  
  /**
   * Recherche toutes les permissions (Miki_action) de l'utilisateur
   * 
   * @see Miki_action
   * @return mixed Un tableau d'éléments de type Miki_action représentant les permissions de l'utilisateur.
   */            
  public function get_actions(){
    $return = array();
    $groups = $this->get_groups();
    foreach($groups as $group){
      $return = array_merge($return, $group->get_actions());
    }
    return $return;
  }
  
  /**
   * Teste si l'utilisateur a le droit d'effectuer l'action (Miki_action) passée en paramètre
   *  
   * @param int Id de l'action que l'on veut savoir si l'utilisateur a le droit d'effectuer.
   * 
   * @see Miki_action
   * @return boolean
   */                   
  public function can($action_id){
    $groups = $this->get_groups();
    foreach($groups as $group){
      if ($group->can($action_id))
        return true;
    }
    return false;
  }
  
  /**
   * Envoie un e-mail à l'utilisateur avec son nouveau mot de passe
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @param boolean $new Si true, on créé un nouveau mot de passe, sinon on envoit celui passé en paramètre.
   * @param string Password à envoyer à l'utilisateur si on ne doit pas lui en générer un nouveau ($new = false).
   * 
   * @return boolean            
   */   
  public function send_password($new = true, $pass = ""){
    $sitename = Miki_configuration::get('sitename');
    $email_answer = Miki_configuration::get('email_answer');
    
    if ($new)
      $password = $this->new_password();
    else
      $password = $pass;
      
    // création du mail
    $mail = new phpmailer();
    
    if (isset($_SESSION['lang']))
      $mail->SetLanguage($_SESSION['lang']);
    else
      $mail->SetLanguage('fr');
      
    $mail->CharSet	=	"UTF-8";
    $mail->From     = $email_answer;
    $mail->FromName = $sitename;
    $mail->IsMail();
    
    $subject = _("Votre nouveau mot de passe pour le site Internet ") .$sitename;   
    
    // contenu html
    $body = "<span style='font-size:14px'>"
           ._("Bonjour ") .$this->firstname ." " .$this->lastname .",<br /><br />"
           ._("Vous nous avez demandé de vous renvoyer votre mot de passe :") ."<br /><br />"
           ."<span style='font-weight:bold'>" ._("Votre Mot de passe est le suivant : ") .$password ."</span><br /><br />"
           ._("Merci de traiter ces informations avec confidentialité et n'oubliez pas que vous pouvez à tout moment modifier votre mot de passe dans les paramètres de votre compte utilisateur") 
           ."<br /><br /><br />" ._("Meilleures salutations.") ."<br /><br />"
           ."<span style='font-weight:bold'>" .$sitename ."</span><br /><br />"
           ."<hr style='height:1px'></span><span style='font-size:12px'>"
           ."<span style='font-weight:bold'>" ._("Remarque : ") ."</span><br />" 
           ._("Ce message a été envoyé à ") .$this->firstname ." " .$this->lastname ._(" par ") .$sitename 
           ._(". La présence de votre nom et prénom atteste que l'expéditeur de ce message est bien ") .$sitename
           ._(". Ceci est un e-mail automatique, vous ne pouvez donc pas y répondre.") ."<hr style='height:1px'></span>";
    
    // contenu text      
    $text_body = _("Bonjour ") .$this->firstname ." " .$this->lastname .",\n\n"
                ._("Vous nous avez demandé de vous renvoyer votre mot de passe :") ."\n\n"
                ._("Votre Mot de passe est le suivant : ") .$this->password ."\n\n"
                ._("Merci de traiter ces informations avec confidentialité et n'oubliez pas que vous pouvez à tout moment modifier votre mot de passe dans les paramètres de votre compte utilisateur") 
                ."\n\n\n" ._("Meilleures salutations.") ."\n\n" .$sitename ."\n\n"
                ._("Remarque : ") ."\n" 
                ._("Ce message a été envoyé à ") .$this->firstname ." " .$this->lastname ._(" par ") .$sitename 
                ._(". La présence de votre nom et prénom atteste que l'expéditeur de ce message est bien ") .$sitename 
                ._(". Ceci est un e-mail automatique, vous ne pouvez donc pas y répondre.");
                
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AltBody = $text_body;
    $mail->AddAddress($this->email);
    if(!$mail->Send())
      throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
      
    return true;
  }
  
  /**
   * Recherche tous les utilisateurs
   * 
   * @param boolean $all Si true, récupert tous les utilisateurs. Si false, ne récupert que les utilisateurs actifs (state > 0).
   *      
   * @static
   * @return mixed Un tableau d'éléments de type Miki_user représentant les utilisateurs récupérés.
   */            
  public static function get_all_users($all = true){
    $return = array();
    $sql = "SELECT * FROM miki_user";
    
    if (!$all)
      $sql .= " WHERE state > 0";
    
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_user($row['id']);
    }
    return $return;
  }
  
  /**
   * Teste si un utilisateur avec un nom et mot de passe donnés existe bien
   * 
   * @param $name Nom de l'utilisateur à tester
   * @param $password Mot de passe à tester (codé vis SHA1)   
   * 
   * @static
   * @return boolean               
   */   
  public static function test_user($name, $password){
    $sql = sprintf("SELECT id, password FROM miki_user WHERE name = '%s'",
      mysql_real_escape_string($name));
	  $result = mysql_query($sql);
	  if (mysql_num_rows($result) > 0){
      $row = mysql_fetch_array($result);
      if ($row['password'] === $password)
        return $row['id'];
      else
        return -1;
    }
    else
      return -1;
  }
}
?>