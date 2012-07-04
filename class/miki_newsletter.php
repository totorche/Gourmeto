<?php
/**
 * Classe Miki_newsletter
 * @package Miki
 */ 

/**
 * Représentation d'une newsletter.
 * 
 * Une newsletter est formée d'un template (Miki_newsletter_template) dans lequel vient s'intégrer le contenu de la newsletter.
 * Les membres (Miki_newsletter_member) d'une newsletter appartiennent à un ou plusieurs groupes de membres (Miki_newsletter_group). (gestion via le Miki, la console d'administrateur)  
 * Une newsletter peut être envoyée à un groupe d'utilisateurs ou à tous les groupes à la fois.   
 * 
 * @see Miki_newsletter_template
 * @see Miki_newsletter_member
 * @see Miki_newsletter_group
 *  
 * @package Miki  
 */
class Miki_newsletter{

  /**
   * Id de la newsletter
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom de la newsletter
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Etat de la newsletter. 0 = non envoyée, 1 = envoi incomplet, 2 = envoi terminé
   *      
   * @var int
   * @access public   
   */
  public $state = 0;
  
  /**
   * Date de la création de la newsletter
   *      
   * @var string
   * @access public   
   */
  public $date_creation;
  
  /**
   * Id de l'utilisateur (Miki_user) ayant créé la newsletter
   *      
   * @var int
   * @see Miki_user   
   * @access public   
   */
  public $user_creation;
  
  /**
   * Id du template de la newsletter (Miki_newsletter_template)
   *      
   * @var int
   * @see Miki_newsletter_template   
   * @access public   
   */
  public $template_id;
  
  /**
   * Sujet de la newsletter
   *      
   * @var string
   * @access public   
   */
  public $subject;
  
  /**
   * Type de contenu (code ou file). Code veut dire que le contenu sera du code HTML. File veut dire que le contenu sera un nom de fichier qui contiendra le contenu réel.
   *      
   * @var string
   * @access public   
   */
  public $content_type;
  
  /**
   * Contenu de la newsletter
   *      
   * @var string
   * @access public   
   */
  public $content;
  
  /**
   * Date à laquelle la newsletter a été envoyée pour la dernière fois
   *      
   * @var string
   * @access public   
   */
  public $date_sent;
  
  /**
   * Nombre de personne à qui la newsletter a été envoyée
   *      
   * @var int
   * @access public   
   */
  public $nb_send;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge la newsletter dont l'id a été donné
   * 
   * @param int $id Id de la newsletter à charger (optionnel)
   */
  function __construct($id = ""){
    // charge la newsletter si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge une newsletter depuis un id
   *    
   * Si la newsletter n'existe pas, une exception est levée.
   *    
   * @param int $id id de la newsletter à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_newsletter WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La newsletter demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->state = $row['state'];
    $this->date_creation = $row['date_creation'];
    $this->user_creation = $row['user_creation'];
    $this->template_id = $row['template_id'];
    $this->subject = $row['subject'];
    $this->content_type = $row['content_type'];
    $this->content = $row['content'];
    $this->date_sent = $row['date_sent'];
    $this->nb_send = $row['nb_send'];
    return true;
  }
  
  /**
   * Charge une newsletter depuis son nom
   *    
   * Si la newsletter n'existe pas, une exception est levée.
   *    
   * @param string $name Nom de la newsletter à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load_from_name($name){
    $sql = sprintf("SELECT * FROM miki_newsletter WHERE name = '%s'",
      mysql_real_escape_string($name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Aucune newsletter ne correspond au nom donné"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->state = $row['state'];
    $this->date_creation = $row['date_creation'];
    $this->user_creation = $row['user_creation'];
    $this->template_id = $row['template_id'];
    $this->subject = $row['subject'];
    $this->content_type = $row['content_type'];
    $this->content = $row['content'];
    $this->date_sent = $row['date_sent'];
    $this->nb_send = $row['nb_send'];
    return true;
  }
  
  /**
   * Sauvegarde la newsletter dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * La newsletter doit posséder un nom unique (name). Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été donné à la newsletter"));
      
    // si l'id de la newsletter existe, c'est que la page existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // teste qu'une newsletter du même nom n'existe pas déjà
    $sql = sprintf("SELECT * FROM miki_newsletter WHERE name = '%s'",
      mysql_real_escape_string($this->name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) != 0)
      throw new Exception(_("Une newsletter du même nom existe déjà dans la base de données"));
      
    $this->user_creation = $_SESSION['miki_admin_user_id'];
    
    $sql = sprintf("INSERT INTO miki_newsletter (name, state, date_creation, user_creation, template_id, subject, content_type, content) VALUES('%s', %d, NOW(), %d, %d, '%s', '%s', '%s')",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->user_creation),
      mysql_real_escape_string($this->template_id),
      mysql_real_escape_string($this->subject),
      mysql_real_escape_string($this->content_type),
      mysql_real_escape_string($this->content));
   
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion de la newsletter dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la newsletter
    $this->load($this->id);
  }
  
  /**
   * Met à jour la newsletter dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * La newsletter doit posséder un nom unique (name). Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->name))
      throw new Exception(_("La newsletter n'a aucun nom"));
      
    // si aucun id existe, c'est que la newsletter n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // teste qu'une page du même nom n'existe pas déjà
    $sql = sprintf("SELECT * FROM miki_newsletter WHERE name = '%s' AND  id != %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) != 0)
      throw new Exception(_("Une newsletter du même nom existe déjà dans la base de données"));
    
    $sql = sprintf("UPDATE miki_newsletter SET name = '%s', state = %d, template_id = %s, subject = '%s', content_type = '%s', content = '%s' WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->template_id),
      mysql_real_escape_string($this->subject),
      mysql_real_escape_string($this->content_type),
      mysql_real_escape_string($this->content),
      mysql_real_escape_string($this->id));
   
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour de la newsletter dans la base de données : ") ."<br />" .mysql_error() ."<br />$sql");
  }
  
  /**
   * Supprime la newsletter 
   * 
   * Suppression en cascade via les clé étrangères des contenus de la newsletter      
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){    
    $sql = sprintf("DELETE FROM miki_newsletter WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la newsletter : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Test la validité d'une adresse e-mail
   * 
   * @param string $email L'adresse e-mail à tester
   * @return boolean True si l'adresse est valide, false sinon         
   */      
  static function test_email($email){
    $atom   = '[-a-z0-9!#$%&\'*+\\/=?^_`{|}~]';   // caractères autorisés avant l'arobase
    $domain = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)'; // caractères autorisés après l'arobase (nom de domaine)
                                   
    $regex = '/^' . $atom . '+' .   // Une ou plusieurs fois les caractères autorisés avant l'arobase
    '(\.' . $atom . '+)*' .         // Suivis par zéro point ou plus
                                    // séparés par des caractères autorisés avant l'arobase
    '@' .                           // Suivis d'un arobase
    '(' . $domain . '{1,63}\.)+' .  // Suivis par 1 à 63 caractères autorisés pour le nom de domaine
                                    // séparés par des points
    $domain . '{2,63}$/i';          // Suivi de 2 à 63 caractères autorisés pour le nom de domaine
    
    // test de l'adresse e-mail
    if (preg_match($regex, $email)) {
        return true;
    } else {
        return false;
    }
  }
  
  /**
   * Importe des membres depuis un fichier CSV
   *
   * Chaque ligne du fichier CSV correspond à un membre et doit être formatée de la façon suivante : prénom;nom;email    
   *   
   * Les membres importés sont placé dans le goupe donné en paramètre ($id_group)
   * 
   * Attention : La valeur de retour vaut TRUE si l'importation s'est terminé avec succès. 
   * Si il y a eu des erreurs, les erreurs concernées sont retournées sous la forme d'une chaîne de caractère. 
   * Il est donc important de tester le résultat avec l'opérateur "===" PHP qui vérifie le type de données.  
   * 
   * @param mixed $file Fichier CSV envoyé (FILE['nom_de_l_element'])
   * @param int $id_group Id du groupe dans lequel les membres importés seront placés 
   * 
   * @return mixed True si l'importation s'est terminé avec succès. Si il y a eu des erreurs, les erreurs concernées sont retournées sous la forme d'une chaîne de caractère. Il est donc important de tester le résultat avec l'opérateur "===" PHP qui vérifie le type de données       
   */ 
  static function import_csv($file, $id_group){
  
    // pour détecter les fins de ligne depuis un fichier Macintosh
    ini_set('auto_detect_line_endings', true);
    
    // traite le nom de destination
    $system = explode('.',strtolower($file['name']));
    $ext = $system[sizeof($system)-1];
    $nom_fichier = "import_csv_temp" ."." .$ext;

    // le fichier doit être au format jpg, gif ou png
  	if (!preg_match('/csv/i',$ext) && !preg_match('/txt/i',$ext)){
      throw new Exception(_("Le type du fichier n'est pas supporté. Les types supportés sont : CSV et TXT : $nom_fichier - " .$file['name']));
    }
    
    // teste s'il y a eu une erreur
  	if ($file['error']) {
  		switch ($file['error']){
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

  	if (!is_uploaded_file($file['tmp_name']) || $file['size'] > 5 * 1024 * 1024)
  		throw new Exception(_("Veuillez uploader un fichier plus petit que 5Mb"));
  		
  	// pas d'erreur --> upload
  	if ((isset($file['tmp_name'])) && ($file['error'] == UPLOAD_ERR_OK)) {
  		if (!move_uploaded_file($file['tmp_name'], $nom_fichier))
        throw new Exception(_("Erreur pendant l'upload du fichier"));
  	}
  	
    // télécharge les plaques disponibles
    if (!$fp = fopen($nom_fichier,"r")){
      echo "Echec de l'ouverture du fichier";
      exit();
    }
    else{
      // pour la gestion des erreurs
      $wrong_format = 0;
      $wrong_email = 0;
      $missing_email = 0;
      
      $wrong_email_list = array();
      
      // parcourt chaque ligne
      while (!feof($fp)) {
        $l = fgets($fp, 255);
        
        if ($l != ""){
          /*
          récupert toutes les données (prénom;nom;email)
          
          $tab[0] = firstname 
          $tab[1] = lastname 
          $tab[2] = email
          */
          $tab = explode(";", $l);
          
          
          if (isset($tab[2]))
            $tab[2] = trim(strtolower($tab[2]));

          // test s'il y a des erreurs
          if (sizeof($tab) != 3){
            $wrong_format++;
          }
          elseif (!Miki_newsletter::test_email($tab[2])){
            $wrong_email++;
            $wrong_email_list[] = $tab[2];
          }
          elseif ($tab[2] == ""){
            $missing_email++;
          }
          // Tout est OK
          else{
            // ajoute la personne aux abonnés de la newsletter
            $member = new Miki_newsletter_member();
            $member->firstname = $tab[0];
            $member->lastname = $tab[1];
            $member->email = $tab[2];
            
            try{
              $member->save();
            }
            catch(Exception $e){
              // si le membre existe déjà (même adresse e-mail)
              $member = new Miki_newsletter_member();
              $member->load_from_email($tab[2]);
              
              // ajoute le prénom et le nom si pas encore définis
              if ($member->firstname == "" && $tab[0] != "")
                $member->firstname = $tab[0];
                
              if ($member->lastname == "" && $tab[0] != "")
                $member->lastname = $tab[0];
                
              $member->update();
            }
            
            try{
              $member->add_to_group($id_group);
            }
            catch(Exception $e){
              // le membre existe déjà dans le groupe donné, on ne fait donc rien
            }
          }
        }
      }
      
      // supprime le fichier temporaire
      unlink($nom_fichier);
      
      $erreur = "";
      if ($wrong_format > 0)
        $erreur .= "$wrong_format entrées n'étaient pas au bon format !";
        
      if ($wrong_email > 0){
        if ($erreur != "")
          $erreur .= "<br />";
        
        $list = implode("<br />", $wrong_email_list);
        
        $erreur .= "$wrong_email adresses e-mail étaient invalides : <br /><br />$list !";
      }
      
      if ($missing_email > 0){
        if ($erreur != "")
          $erreur .= "<br />";
        
        $erreur .= "$missing_email adresses e-mail étaient manquantes !";
      }
      
      if ($erreur != "")
        return $erreur;
      else
        return true;
    }
  }
  
  /**
   * Ajoute une adresse e-mail à la liste d'envoi
   * 
   * Si une erreur survient, une exception est levée.
   *       
   * @param int $id_member Id du membre (Miki_newsletter_member) dont l'adresse e-mail doit être ajoutée à la liste d'envoi
   * 
   * @see Miki_newsletter_member      
   */
  public function add_to_send_list($id_member){
    $sql = sprintf("INSERT INTO miki_newsletter_temp (id_newsletter, id_member) VALUES (%d, %d)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($id_member));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'ajout de l'adresse e-mail à la liste d'envoi : ") ."<br />" .mysql_error());
  }
  
  /**
   * Prépare l'envoi de la newsletter
   * 
   * Récupert tous les destinataire de la newsletter (sélectionn grâce au paramètre $id_group) et les ajoute à la liste d'envoi      
   *  
   * @param int $id_group Groupe d'utilisateur (Miki_newsletter_group) à qui la newsletter doit être envoyée. Si le paramètre est ois (ou chaîne vide), la newsletter sera envoyée à tous les membres
   * 
   * @see Miki_newsletter_group                          
   */
  public function init_send($id_group = ""){
    $members = Miki_newsletter_member::get_all_members($id_group);
    
    foreach($members as $m){
      try{
        $this->add_to_send_list($m->id);
      }
      catch(Exception $e){
        throw new Exception(_("Erreur lors de l'initialisation de la newsletter : ") ."<br />" .$e->getMessage());
      }
    }
    $sql = sprintf("UPDATE miki_newsletter SET date_sent = NOW() WHERE id = %d",
            mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'initialisation de la newsletter : ") ."<br />" .mysql_error());
  }
  
  /**
   * Récupert la liste des membres à qui la newsletter doit être envoyée
   * 
   * Le résultat retourné sera un tableau à 2 dimensions dont la deuxième dimensions aura le format suivant : 
   *    $tab[]['id'] : L'id de l'e-mail dans la liste d'envoi   
   *    $tab[]['member_id'] : L'id du membre (Miki_newsletter_member)
   *    $tab[]['email'] : L'adresse e-mail du membre
   *    $tab[]['firstname'] : Le prénom du membre ou FALSE si pas de prénom
   *    $tab[]['lastname'] : Le nom du membre ou FALSE si pas de nom
   * 
   * @return mixed Un tableau à deux dimensions. La deuxième dimension contient les détails des membres (voir description ci-dessus).     
   */ 
  public function get_emails(){
    $sql = "SELECT mnt.id id, mnm.id member_id, mnm.firstname firstname, mnm.lastname lastname, mnm.email email 
            FROM miki_newsletter_temp mnt, 
                 miki_newsletter_member mnm 
            WHERE mnt.id_member = mnm.id 
              AND mnt.id_newsletter = $this->id";
    $return = array();
    $result = mysql_query($sql);
    
    $x = 0;
    while($row = mysql_fetch_array($result)){
      $return[$x]['id'] = $row['id'];
      $return[$x]['member_id'] = $row['member_id'];
      $return[$x]['email'] = $row['email'];
      
      if ($row['firstname'] == "" || $row['firstname'] == "NULL")
        $return[$x]['firstname'] = false;
      else
        $return[$x]['firstname'] = $row['firstname'];
      
      if ($row['lastname'] == "" || $row['lastname'] == "NULL")
        $return[$x]['lastname'] = false;
      else
        $return[$x]['lastname'] = $row['lastname'];
      
      $x++;
    }
    return $return;
  }
  
  /**
   * Comptabilise un envoi de la newsletter
   * 
   * Si une erreur survient, une exception est levée.      
   */   
  public function add_email_sent(){
    $sql = sprintf("UPDATE miki_newsletter SET nb_send = nb_send + 1 WHERE id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la comptabilisation de l'envoi de la newsletter : ") ."<br />" .mysql_error());
  }
  
  /**
   * Supprime un email de la liste d'envoi a qui la newsletter doit être envoyée
   * 
   * @param int $id Id de l'e-mail à supprimer de la liste d'envoi        
   * 
   * Si une erreur survient, une exception est levée.      
   */   
  public function remove_email($id){
    $sql = sprintf("DELETE FROM miki_newsletter_temp WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la suppression de l'e-mail : ") ."<br />" .mysql_error());
  }
  
  /**
   * Supprime tous les emails de la liste d'envoi a qui la newsletter doit être envoyée
   * 
   * Si une erreur survient, une exception est levée.          
   */   
  public function clear_emails(){
    $sql = sprintf("DELETE FROM miki_newsletter_temp WHERE id_newsletter = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la suppression des e-mails : ") ."<br />" .mysql_error());
  }
  
  /**
   * Comptabilise une ouverture de la newsletter par le membre donné (Miki_newsletter_member) (pour les statistiques)
   * 
   * Si une erreur survient, une exception est levée.
   * 
   * @see Miki_newsletter_member      
   */   
  public function set_opened($member){
    $sql = sprintf("INSERT INTO miki_newsletter_stats (id_newsletter, id_member, view, date) VALUES (%d, %d, 1, NOW())
                    ON DUPLICATE KEY UPDATE view = view + 1, date = NOW()",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($member));  
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour de la newsletter dans la base de données : ") ."<br />" .mysql_error() ."<br />$sql");
  }
  
  /**
   * Retourne le nombre de fois que la newsletter a été ouverte
   * 
   * @param boolean $distinct Si TRUE, on ne compte qu'une seule ouverture par personne
   * @param int $id_member Si renseigné, on ne compte que les ouvertures de ce membre           
   * 
   * @return int   
   */      
  public function get_opened($distinct = true, $id_member = ""){
    // on ne compte qu'une seule ouverture par personne
    if ($distinct){
      $sql = sprintf("SELECT COUNT(id_member) FROM miki_newsletter_stats WHERE id_newsletter = %d ",
        mysql_real_escape_string($this->id));
    }
    // compte chaque ouverture
    else{
      $sql = sprintf("SELECT SUM(view) FROM miki_newsletter_stats WHERE id_newsletter = %d ",
        mysql_real_escape_string($this->id));
    }
    
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la recherche du nombre d'ouvertures de la newsletter : ") ."<br />" .mysql_error() ."<br />$sql");
    
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Recherche toutes les newsletter
   * 
   * @param string $order Par quel champ les newsletter trouvées seront triées (name, state, date_creation, user_creation ou template). Si vide, on tri selon l'id.
   * @param string $order_type Type de tri (ascendant "asc" ou descendant "desc")   
   *      
   * @static
   * @return mixed Un tableau d'élément Miki_newsletter
   */           
  public static function get_all_newsletter($order = "", $type = "asc"){
    $sql = "SELECT * FROM miki_newsletter";
    
    // ordonne les pages
    if ($order == "name")
      $sql .= " ORDER BY name " .$type;
    elseif ($order == "state")
      $sql .= " ORDER BY state " .$type;
    elseif ($order == "date_creation")
      $sql .= " ORDER BY date_creation " .$type;
    elseif ($order == "user_creation")
      $sql .= " ORDER BY user_creation " .$type;
    elseif ($order == "template")
      $sql .= " ORDER BY template " .$type;
    else
      $sql .= " ORDER BY id " .$type;
      
    $return = array();
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $newsletter = new Miki_newsletter($row['id']);
      $return[] = $newsletter;
     } 
    return $return;
  }
}
?>