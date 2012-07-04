<?php
/**
 * Classe Miki_comment
 * @package Miki
 */ 

/**
 * Représentation d'un commentaire.
 * 
 * Un commentaire est lié à un élément : une page (Miki_page), une actualité (Miki_news), un article du shop (Miki_shop_article), un événement (Miki_event), un album photo (Miki_album), une photo (Miki_album_picture)  
 * Un commentaire possède un état : 1 - Approuvé; 2 - En attente de validation; 3 - Indésirable 
 * Un commentaire peut être directement lié à une personne (Miki_person) dans le cas où il faut être membre du site Internet pour pouvoir laisser un commentaire 
 * S'il n'est pas obligatoire d'être membre du site Internet pour pouvoir laisser un commentaire, 
 * les informations de la personne ayant déposé le commentaire sont enregistrée directement dans le commentaire lui-même
 * 
 * @see Miki_page, Miki_news, Miki_shop_article, Miki_event, Miki_album, Miki_album_picture, Miki_person
 *  
 * @package Miki  
 */
class Miki_comment{
  
  /**
   * Id du commentaire
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Id de la person (Miki_person) qui a déposé le commentaire (dans le cas où il ne faut pas obligatoirement être membre du site Internet pour pouvoir laisser un commentaire) 
   *
   * @see Miki_person
   * @var int
   * @access private
   */
  public $id_person;
  
  /**
   * Id de l'objet lié au commentaire. Cet objet peut être de type aussi divers que les différents types de commentaires possibles. 
   *
   * @var int
   * @access public
   */
  public $id_object;
  
  /**
   * Etat du commentaire : 1 - Approuvé; 2 - En attente de validation; 3 - Indésirable
   *
   * @var int
   * @access public
   */
  public $state;
    
  /**
   * Classe de l'objet auquel le commentaire est lié
   * 
   * Exemple : 
   *  - une page du site (Miki_page)
   *  - une actualité (Miki_news)
   *  - un article du shop (Miki_shop_article)
   *  - un événement (Miki_event) 
   *  - un album photo (Miki_album)
   *  - une photo (Miki_album_picture)
   *  - etc.            
   * 
   * @var string
   * @access public   
   */
  public $object_class;
  
  /**
   * Commentaire laissé
   *
   * @var string
   * @access public
   */
  public $comment;
  
  /**
   * Date à laquelle le commentaire a été déposé
   *
   * @var string
   * @access public
   */
  public $date;
  
  /**
   * La note (optionnelle) associée au commentaire. Si = 0 --> pas de note.
   * 
   * @var int
   * @access public
   */
   public $rating = 0;
  
  /**
   * Si la personne ayant posté le commentaire désire être informée lorsqu'un nouveau commentaire est posté sur le même objet
   *
   * @var boolean
   * @access public
   */
  public $is_subscribed;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le commentaire dont l'id a été donné
   * 
   * @param int $id Id du commentaire à charger (optionnel)
   */
  function __construct($id = ""){
    // charge le commentaire si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un commentaire depuis un id
   *    
   * Si le commentaire n'existe pas, une exception est levée.
   *    
   * @param int $id id du commentaire à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT *
                    FROM miki_comment
                    WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le commentaire demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id            = $row['id'];
    $this->id_person     = $row['id_person'];
    $this->id_object     = $row['id_object'];
    $this->state         = $row['state'];
    $this->object_class  = $row['object_class'];
    $this->comment       = $row['comment'];
    $this->date          = $row['date'];
    $this->rating        = $row['rating'];
    $this->is_subscribed = $row['is_subscribed'] == 1;
    return true;
  }
  
  /**
   * Sauvegarde le commentaire dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    // si l'id du commentaire existe, c'est que le commentaire existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
    
    // vérifie si la personne s'inscrit aux commentaires de l'objet
    if ($this->is_subscribed)
      $subscribe = 1;
    else
      $subscribe = 0;
      
    $sql = sprintf("INSERT INTO miki_comment (id_person, id_object, state, object_class, comment, date, rating, is_subscribed) 
                    VALUES (%s, %s, %d, '%s', '%s', NOW(), %d, %d)",
      mysql_real_escape_string($this->id_person),
      mysql_real_escape_string($this->id_object),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->object_class),
      mysql_real_escape_string($this->comment),
      mysql_real_escape_string($this->rating),
      mysql_real_escape_string($subscribe));
   
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion du commentaire dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge le commentaire
    $this->load($this->id);
  }
  
  /**
   * Met à jour le commentaire dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    // si aucun id existe, c'est que le commentaire n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // vérifie si la personne s'inscrit aux commentaires de l'objet
    if ($this->is_subscribed)
      $subscribe = 1;
    else
      $subscribe = 0;
    
    $sql = sprintf("UPDATE miki_comment SET id_person = %s, id_object = %s, state = %d, object_class = '%s', comment = '%s', rating = %d, is_subscribed = %d WHERE id = %d",
      mysql_real_escape_string($this->id_person),
      mysql_real_escape_string($this->id_object),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->object_class),
      mysql_real_escape_string($this->comment),
      mysql_real_escape_string($this->rating),
      mysql_real_escape_string($subscribe),
      mysql_real_escape_string($this->id));

    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du commentaire dans la base de données : ") ."<br />" .mysql_error());
  }
  
  /**
   * Supprime le commentaire 
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){    
    $sql = sprintf("DELETE FROM miki_comment WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du commentaire : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime tous les commentaires d'une personne donnée
   * 
   * Si une erreur survient, une exception est levée.
   * 
   * @param int $id_person Supprime tous les commentaires de la personne dont l'id est donné
   * @return boolean
   */
  public static function delete_from_person($id_person){
    if ($id_person != "" && is_numeric($id_person)){
      $sql = sprintf("DELETE FROM miki_comment WHERE id_person = %d",
        mysql_real_escape_string($id_person));
      if (!mysql_query($sql))
        throw new Exception(_("Erreur pendant la suppression des commentaires : ") ."<br />" .mysql_error());
      return true;
    }
    else
      return false;
  }
  
  /**
   * Supprime tous les commentaires d'un objet donné
   * 
   * Si en plus de l'id de l'objet et de sa classe, l'id d'une personne est donné, on supprime uniquement
   * les commentaires de l'objet donné qui ont été postés par la personne dont l'id est donné.
   *             
   * Si une erreur survient, une exception est levée.
   * 
   * @param int $id_object Si donné, supprime tous les commentaires de la personne dont l'id est donné
   * @param string $object_class Classe de l'objet dont on veut supprimer les commentaires
   * @param int $id_person Si donné, supprime uniquement les commentaires de la personne dont l'id est donné
   *
   * @return boolean
   */
  public static function delete_from_object($id_object, $object_class, $id_person = ""){
    if (isset($id_object) && is_numeric($id_object) && isset($object_class)){
      
      $sql = sprintf("DELETE FROM miki_comment WHERE id_object = %d AND object_class = '%s'",
        mysql_real_escape_string($id_object),
        mysql_real_escape_string($object_class));
      
      // si l'id d'une personne est donné, on supprime uniquement les commentaires de cette personne
      if ($id_person != "" && is_numeric($id_person)){
        $sql .= sprintf(" AND id_person = %d", mysql_real_escape_string($id_person));
      }
        
      if (!mysql_query($sql))
        throw new Exception(_("Erreur pendant la suppression des commentaires : ") ."<br />" .mysql_error());
      return true;
    }
    else
      return false;
  }
  
  /**
   * Retourne la personne (Miki_person) ayant déposé le commentaire.
   * 
   * @return Miki_person La personne ayant laissé le commentaire (on ajoute un champ "web" à Miki_person pour l'occasion)
   */     
  public function get_person(){
    // si on doit être membre pour pouvoir laisser un commentaire
    if ($this->id_person != "" && is_numeric($this->id_person)){
      try{
        return new Miki_person($this->id_person);
      }
      catch(Exception $e){
        return false;
      }
    }
    else{
      return false;
    }
  }
  
  /**
   * Vérifie si le commentaire est un spam ou non via l'outil Akismet.
   * 
   * Le résultat est retourné via un Boolean mais est également défini dans le commentaire via la variable $state.
   *      
   * @return boolean TRUE si c'est un spam, FALSE sinon      
   */     
  public function test_spam(){
    
    require_once("scripts/akismet.class.php");
    
    // récupert la clé API d'Akismet et l'url du site web
    $APIKey = Miki_configuration::get('akismet_api');//'b524b0183c5d';
    $MyURL = Miki_configuration::get('site_url');
    
    // récupert la personne
    $person = new Miki_person($this->id_person);
     
    // instancie Akismet
    $akismet = new Akismet($MyURL, $APIKey);
    
    // vérifie que la clé API est valide
    if (!$akismet->isKeyValid()){
      return false;
    }
    
    // configure Akismet
    $akismet->setCommentAuthor($person->firstname ." " .$person->lastname);
    $akismet->setCommentAuthorEmail($person->email1);
    $akismet->setCommentAuthorURL($person->web);
    $akismet->setCommentContent($this->comment);
    $akismet->setPermalink($_SESSION['url_back']);
     
    // détermine s'il s'agit d'un Spam
    if($akismet->isCommentSpam()){
      $this->state = 3;
      $this->update();
    }
    
    return $this->state == 3;
  }
  
  /**
   * Recherche tous les commentaires
   * 
   * La variable $total sera modifiée avec le nombre total d’enregistrements qui seraient retournés par la requête sans la clause LIMIT
   * 
   * @param string $search Critère de recherche pour le nom, le prénom ou l'adresse e-mail de la personne ayant posté le commentaire ou pour le texte du commentaire.
   * @param int $id_person Ne récupert que les commentaires de la person (Miki_person) dont l'id est donné. Si = "", on récupert tous les commentaires.
   * @param int $id_object Ne récupert que les commentaires de l'objet donné.   
   * @param int $state Ne récupert que les commentaires dans l'état donné. Si = "", on récupert tous les commentaires.
   * @param int $object_class Ne récupert que les commentaires de la classe donnée. Si = "", on récupert tous les commentaires.
   * @param string $order Par quel commentaire les commentaires trouvés seront triés (author, source, firstname, lastname, state, type, date). Si vide, on tri selon la position.
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * @param int $nb nombre de commentaires à retourner par page. Si = "" on retourne tous les commentaires
   * @param int $page numéro de la page à retourner        
   * @param int $total Cette variable sera modifiée avec le nombre total d’enregistrements qui seraient retournés par la requête sans la clause LIMIT   
   * 
   * @return mixed Un tableau d'éléments de type Miki_comment                  
   */
  public static function search($search = "", $id_person = "", $id_object = "", $state = "", $object_class = "", $order = "", $order_type = "asc", $nb = "", $page = 1, &$total){
    $sql = "SELECT SQL_CALC_FOUND_ROWS mc.id id, mc.object_class object_class, mc.state state, mc.date date, mp.firstname firstname, mp.lastname lastname
            FROM miki_comment mc
            LEFT JOIN miki_person mp ON mc.id_person = mp.id 
            WHERE 1";
    
    // Applique les critères de recherche
    if ($search != ""){
      $search = mb_strtolower($search, 'UTF-8');
      $sql .= sprintf(" AND (LOWER(mc.comment) LIKE '%%%s%%' OR 
                             LOWER(mp.firstname) LIKE '%%%s%%' OR 
                             LOWER(mp.lastname) LIKE '%%%s%%' OR
                             LOWER(mp.email1) LIKE '%%%s%%')",
                mysql_real_escape_string($search),
                mysql_real_escape_string($search),
                mysql_real_escape_string($search),
                mysql_real_escape_string($search));
    }
    
    // ne récupert que les commentaires de la personne donnée
    if ($id_person != "" && is_numeric($id_person)){
      $sql .= sprintf(" AND mc.id_person = %d", 
        mysql_real_escape_string($id_person));
    }
    
    // ne récupert que les commentaires de l'object donné
    if ($id_object != "" && is_numeric($id_object)){
      $sql .= sprintf(" AND mc.id_object = %d", 
        mysql_real_escape_string($id_object));
    }
    
    // ne récupert que les commentaires dans l'état donné
    if ($state != "" && is_numeric($state)){
      $sql .= sprintf(" AND mc.state = %d", 
        mysql_real_escape_string($state));
    }
    
    // ne récupert que les commentaires de la classe donnée
    if ($object_class != ""){
      $sql .= sprintf(" AND mc.object_class = '%s'", 
        mysql_real_escape_string($object_class));
    }
    
    // ordonne les résultats
    if ($order == "author")
      $sql .= " ORDER BY LCASE(lastname), LCASE(firstname) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "source")
      $sql .= " ORDER BY object_class, id_object " .$order_type;
    elseif ($order == "firstname")
      $sql .= " ORDER BY LCASE(firstname) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "lastname")
      $sql .= " ORDER BY LCASE(lastname) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "state")
      $sql .= " ORDER BY state " .$order_type;
    elseif ($order == "rating")
      $sql .= " ORDER BY rating " .$order_type;
    elseif ($order == "class")
      $sql .= " ORDER BY object_class " .$order_type;
    elseif ($order == "date")
      $sql .= " ORDER BY date " .$order_type;
    else
      $sql .= " ORDER BY id " .$order_type;

    // calcul le nombre de résultats total, sans la clause LIMIT
    $result = mysql_query($sql);
    $total = mysql_num_rows($result);
    
    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }
    
    $return = array();
    $result = mysql_query($sql);
    
    // calcul le nombre de résultats total, sans la clause LIMIT
    $result2 = mysql_query("SELECT FOUND_ROWS()");
    $row2 = mysql_fetch_array($result2);
    $total = $row2[0];
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_comment($row['id']);
    }
    
    return $return;
  }
  
  /**
   * Recherche tous les commentaires
   * 
   * La variable $total sera modifiée avec le nombre total d’enregistrements qui seraient retournés par la requête sans la clause LIMIT      
   * 
   * @param int id_person Ne récupert que les commentaires de la person (Miki_person) dont l'id est donné. Si = "", on récupert tous les commentaires.
   * @param int id_object Ne récupert que les commentaires de l'objet donné.   
   * @param int $state Ne récupert que les commentaires dans l'état donné. Si = "", on récupert tous les commentaires.
   * @param int $object_class Ne récupert que les commentaires de la classe donnée. Si = "", on récupert tous les commentaires.   
   * @param string $order Par quel champ les commentaires trouvés seront triés (author, source, firstname, lastname, state, type, date). Si vide, on tri selon la position.
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * @param int $total Cette variable sera modifiée avec le nombre total d’enregistrements qui seraient retournés par la requête sans la clause LIMIT   
   * 
   * @return mixed Un tableau d'éléments de type Miki_comment                  
   */
  public static function get_all_comments($id_person = "", $id_object = "", $state = "", $object_class = "", $order = "", $order_type = "asc", &$total){
    $sql = "SELECT SQL_CALC_FOUND_ROWS mc.id id, mc.object_class object_class, mc.state state, mc.date date, mp.firstname firstname, mp.lastname lastname 
            FROM miki_comment mc
            LEFT JOIN miki_person mp ON mc.id_person = mp.id 
            WHERE 1";
    
    // ne récupert que les commentaires de la personne donnée
    if ($id_person != "" && is_numeric($id_person)){
      $sql .= sprintf(" AND mc.id_person = %d", 
        mysql_real_escape_string($id_person));
    }
    
    // ne récupert que les commentaires de l'object donné
    if ($id_object != "" && is_numeric($id_object)){
      $sql .= sprintf(" AND mc.id_object = %d", 
        mysql_real_escape_string($id_object));
    }
    
    // ne récupert que les commentaires dans l'état donné
    if ($state != "" && is_numeric($state)){
      $sql .= sprintf(" AND mc.state = %d", 
        mysql_real_escape_string($state));
    }
    
    // ne récupert que les commentaires de la classe donnée
    if ($object_class != ""){
      $sql .= sprintf(" AND mc.object_class = '%s'", 
        mysql_real_escape_string($object_class));
    }
    
    // ordonne les résultats
    if ($order == "author")
      $sql .= " ORDER BY LCASE(lastname), LCASE(firstname) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "source")
      $sql .= " ORDER BY object_class, id_object " .$order_type;
    elseif ($order == "firstname")
      $sql .= " ORDER BY LCASE(firstname) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "lastname")
      $sql .= " ORDER BY LCASE(lastname) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "state")
      $sql .= " ORDER BY state " .$order_type;
    elseif ($order == "class")
      $sql .= " ORDER BY object_class " .$order_type;
    elseif ($order == "date")
      $sql .= " ORDER BY date " .$order_type;
    else
      $sql .= " ORDER BY id " .$order_type;
    
    $return = array();
    $result = mysql_query($sql);

    // calcul le nombre de résultats total, sans la clause LIMIT
    $result2 = mysql_query("SELECT FOUND_ROWS()");
    $row2 = mysql_fetch_array($result2);
    $total = $row2[0];

    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_comment($row['id']);
    }
    
    return $return;
  }
}
?>