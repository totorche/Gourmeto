<?php
/**
 * Classe Miki_shop_article
 * @package Miki
 */ 

/**
 * Inclut les fonctions d'envoi d'e-mail
 */ 
require_once("class.phpmailer.php");

/**
 * Inclut les fonctions d'importation et de traitement des images
 */ 
require_once("functions_pictures.php");

/**
 * Représentation d'un article d'un shop.
 * 
 * Un article fait partie d'un shop (Miki_shop). 
 * 
 * Un article fait partie d'une catégorie d'articles (Miki_shop_article_category).
 * 
 * Un article peut avoir des attributs.
 * Les attributs d'un articles peuvent être p.ex. la couleur, la taille, etc. 
 * Ils sont séparés par "&&". Exemple : couleur=bleu&&grandeur=L (couleur ou grandeur = code de l'attribut et bleu ou L = valeur de l'attribut)
 * Il existe 3 types d'attributs : 0 = champ texte, 1 = Oui/Non, 2 = Liste déroulante    
 * 
 * @see Miki_shop
 * @see Miki_shop_article_category 
 *  
 * @package Miki  
 */
class Miki_shop_article{

  /**
   * Id de l'article
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Type de l'article. 1 = Article simple   2 = Article configurable
   *      
   * @var int
   * @access public   
   */
  public $type;
  
  /**
   * Référence de l'article
   *      
   * @var string
   * @access public   
   */
  public $ref;
  
  /**
   * Id du shop auquel appartient l'article
   *      
   * @var int
   * @access public   
   */
  public $id_shop;
  
  /**
   * Id de la catégorie à laquelle appartient l'article
   *      
   * @var int
   * @access public   
   */
  public $id_category;
  
  /**
   * Date de début de vente de l'article
   *      
   * @var string
   * @access public   
   */
  public $date_start;
  
  /**
   * Date de fin de vente de l'article
   *      
   * @var string
   * @access public   
   */
  public $date_stop;
  
  /**
   * Poid de l'article
   *      
   * @var float
   * @access public   
   */
  public $weight;
  
  /**
   * Prix de revient de l'article
   *      
   * @var float
   * @access public   
   */
  public $cost_price;
  
  /**
   * Prix de l'article
   *      
   * @var float
   * @access public   
   */
  public $price;
  
  /**
   * Etat de l'article (0 = suspendu, 1 = en vente)
   *      
   * @var int
   * @access public   
   */
  public $state;
  
  /**
   * Quantité disponible
   *      
   * @var int
   * @access public   
   */
  public $quantity;
  
  /**
   * Nom de l'article. 
   * 
   * Un tableau comportant le nom dans les différentes langues. L'indice du tableau correspond au code de la langue (Code ISO 639-1 ({@link http://fr.wikipedia.org/wiki/Liste_des_codes_ISO_639-1})).
   *      
   * @var mixed
   * @access public   
   */
  public $name;
  
  /**
   * Description de l'article.
   * 
   * Un tableau comportant la description dans les différentes langues. L'indice du tableau correspond au code de la langue (Code ISO 639-1 ({@link http://fr.wikipedia.org/wiki/Liste_des_codes_ISO_639-1})).
   *      
   * @var mixed
   * @access public   
   */
  public $description;
  
  /**
   * Images de l'article.
   * 
   * Cette variable est un tableau comportant la différentes images de l'article.
   *      
   * @var mixed
   * @access public   
   */
  public $pictures;
  
  /**
   * Le chemin des images de l'article depuis la console d'administration (Miki).
   *      
   * @var string
   * @access private
   */       
  private $picture_path = "../pictures/shop_articles/";
  

  
  /**
   * Constructeur. Si la variable $id est renseignée, charge l'article dont l'id a été donné
   * 
   * @param int $id Id de l'article à charger (optionnel)
   */
  function __construct($id = ""){
    // charge l'article si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un article depuis un id
   *    
   * Si l'article n'existe pas, une exception est levée.
   *    
   * @param int $id id de l'article à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_shop_article WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("L'article demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->type = $row['type'];
    $this->ref = $row['ref'];
    $this->id_shop = $row['id_shop'];
    $this->id_category = $row['id_category'];
    $this->date_start = $row['date_start'];
    $this->date_stop = $row['date_stop'];
    $this->weight = $row['weight'];
    $this->cost_price = $row['cost_price'];
    $this->price = $row['price'];
    $this->state = $row['state'];
    $this->quantity = $row['quantity'];
    $this->pictures = $row['pictures'] != "" ? explode("&&", $row['pictures']) : array();
    
    // recherche les noms de l'article
    $sql = sprintf("SELECT * FROM miki_shop_article_name WHERE id_article = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $this->name[$row['language']] = $row['name'];
    }
    
    // recherche les descriptions de l'article
    $sql = sprintf("SELECT * FROM miki_shop_article_description WHERE id_article = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $this->description[$row['language']] = $row['description'];
    }
    
    return true;
  }
  
  /**
   * Charge un article depuis sa référence
   *    
   * Si l'article n'existe pas, une exception est levée.
   *    
   * @param int $ref Référence de l'article à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load_by_ref($ref){
    $sql = sprintf("SELECT * FROM miki_shop_article WHERE ref = '%s'",
      mysql_real_escape_string($ref));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("L'article demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->type = $row['type'];
    $this->ref = $row['ref'];
    $this->id_shop = $row['id_shop'];
    $this->id_category = $row['id_category'];
    $this->date_start = $row['date_start'];
    $this->date_stop = $row['date_stop'];
    $this->weight = $row['weight'];
    $this->cost_price = $row['cost_price'];
    $this->price = $row['price'];
    $this->state = $row['state'];
    $this->quantity = $row['quantity'];
    $this->pictures = $row['pictures'] != "" ? explode("&&", $row['pictures']) : array();
    
    // recherche les noms de l'article
    $sql = sprintf("SELECT * FROM miki_shop_article_name WHERE id_article = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $this->name[$row['language']] = $row['name'];
    }
    
    // recherche les descriptions de l'article
    $sql = sprintf("SELECT * FROM miki_shop_article_description WHERE id_article = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $this->description[$row['language']] = $row['description'];
    }
    
    return true;
  }
  
  /**
   * Sauvegarde l'article dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * L'article doit posséder un nom unique dans chaque langue. Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->id_shop))
      throw new Exception(_("L'article n'est attribué à aucun shop"));
      
    // si un l'id de l'article existe, c'est que l'article existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
    
    // concatène les images
    $tab_temp = array();
    if (is_array($this->pictures)){
      foreach($this->pictures as $lang => $pic){
        if (!empty($pic))
          $tab_temp[] = $pic;
      }
      $pictures = implode("&&", $tab_temp);
    }
    else
      $pictures = "";
    
    // débute la transaction
    mysql_query("START TRANSACTION");
    
    // Si la référence de l'article est spécifiée, vérifie qu'aucun article avec la même référence n'existe déjà
    if ($this->ref != ""){
      $sql = sprintf("SELECT id FROM miki_shop_article WHERE ref = '%s'",
        mysql_real_escape_string($this->ref));
      $result = mysql_query($sql);
      if (mysql_num_rows($result) > 0){
        // annule les modifications dans la bdd
        mysql_query("ROLLBACK"); 
        throw new Exception(_("Un article possédant la même référence existe déjà dans la base de données"));
      }
    }
      
    $sql = sprintf("INSERT INTO miki_shop_article (type, ref, id_shop, id_category, date_start, date_stop, weight, cost_price, price, state, quantity, pictures, date_created) 
                    VALUES(%d, '%s', %d, %d, '%s', '%s', %d, '%F', '%F', %d, %d, '%s', NOW())",
      mysql_real_escape_string($this->type),
      mysql_real_escape_string($this->ref),
      mysql_real_escape_string($this->id_shop),
      mysql_real_escape_string($this->id_category),
      mysql_real_escape_string($this->date_start),
      mysql_real_escape_string($this->date_stop),
      mysql_real_escape_string($this->weight),
      mysql_real_escape_string($this->cost_price),
      mysql_real_escape_string($this->price),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->quantity),
      mysql_real_escape_string($pictures));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications dans la bdd
      mysql_query("ROLLBACK"); 
      throw new Exception(_("Erreur lors de l'insertion de l'article dans la base de données : ") .mysql_error());
    }
    // récupert l'id
    $this->id = mysql_insert_id();

    // insère les noms de l'article dans la base de données
    foreach($this->name as $key=>$name){
      // vérifie qu'aucun article avec le même nom n'existe déjà
      $sql = sprintf("SELECT id_article FROM miki_shop_article_name WHERE name = '%s' AND  language= '%s'",
        mysql_real_escape_string($name),
        mysql_real_escape_string($key));
      $result = mysql_query($sql);
      if (mysql_num_rows($result) > 0){
        // annule les modifications dans la bdd
        mysql_query("ROLLBACK"); 
        throw new Exception(_("Un article du même nom existe déjà dans la base de données"));
      }
    
      $sql = sprintf("INSERT INTO miki_shop_article_name (id_article, language, name) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string($name));
      $result = mysql_query($sql);
      if (!$result){
        // annule les modifications dans la bdd
        mysql_query("ROLLBACK"); 
        throw new Exception(_("Erreur lors de l'insertion du nom de l'article dans la base de données : ") ."<br />" .mysql_error());
      }
    }
    
    // insère les descriptions de l'article dans la base de données
    foreach($this->description as $key=>$description){        
      $sql = sprintf("INSERT INTO miki_shop_article_description (id_article, language, description) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string($description));
      $result = mysql_query($sql);
      if (!$result){
        // annule les modifications dans la bdd
        mysql_query("ROLLBACK"); 
        throw new Exception(_("Erreur lors de l'insertion de la description de l'article dans la base de données : ") ."<br />" .mysql_error());
      }
    }
    
    // termine la transaction
    mysql_query("COMMIT");
    
    // recharge l'article
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour l'article dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * L'article doit posséder un nom unique dans chaque langue. Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->id_shop))
      throw new Exception(_("L'article n'est attribué à aucun shop"));
      
    // si aucun id existe, c'est que l'article n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // concatène les images
    $tab_temp = array();
    if (is_array($this->pictures)){
      foreach($this->pictures as $lang => $pic){
        if (!empty($pic))
          $tab_temp[] = $pic;
      }
      $pictures = implode("&&", $tab_temp);
    }
    else
      $pictures = "";
      
    // débute la transaction
    mysql_query("START TRANSACTION");
    
    $sql = sprintf("UPDATE miki_shop_article SET type = %d, ref = '%s', id_shop = %d, id_category = %d, date_start = '%s', date_stop = '%s', weight = '%F', cost_price = '%F', price = '%F', state = %d, quantity = %d, pictures = '%s' WHERE id = %d",
      mysql_real_escape_string($this->type),
      mysql_real_escape_string($this->ref),
      mysql_real_escape_string($this->id_shop),
      mysql_real_escape_string($this->id_category),
      mysql_real_escape_string($this->date_start),
      mysql_real_escape_string($this->date_stop),
      mysql_real_escape_string($this->weight),
      mysql_real_escape_string($this->cost_price),
      mysql_real_escape_string($this->price),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->quantity),
      mysql_real_escape_string($pictures),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      throw new Exception(_("Erreur lors de la mise à jour de l'article dans la base de données : ") ."<br />" .mysql_error());
      // annule les modifications dans la bdd
      mysql_query("ROLLBACK"); 
    }
    
    // insère les noms de l'article dans la base de données
    foreach($this->name as $key=>$name){
      // vérifie qu'aucun article avec le même nom n'existe déjà
      $sql = sprintf("SELECT id_article FROM miki_shop_article_name WHERE name = '%s' AND  language= '%s' AND  id_article != %d",
        mysql_real_escape_string($name),
        mysql_real_escape_string($key),
        mysql_real_escape_string($this->id));
      $result = mysql_query($sql);
      if (mysql_num_rows($result) > 0)
        throw new Exception(_("Un article du même nom existe déjà dans la base de données"));
      
      $sql = sprintf("INSERT INTO miki_shop_article_name (id_article, language, name) VALUES (%d, '%s', '%s')
                      ON DUPLICATE KEY UPDATE name = '%s'",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string($name),
        mysql_real_escape_string($name));
      $result = mysql_query($sql);
      if (!$result){
        throw new Exception(_("Erreur lors de la mise à jour du nom de l'article dans la base de données : ") ."<br />" .mysql_error());
        // annule les modifications dans la bdd
        mysql_query("ROLLBACK"); 
      }
    }
    
    // insère les descriptions de l'article dans la base de données
    foreach($this->description as $key=>$description){
      $sql = sprintf("INSERT INTO miki_shop_article_description (id_article, language, description) VALUES (%d, '%s', '%s')
                      ON DUPLICATE KEY UPDATE description = '%s'",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string($description),
        mysql_real_escape_string($description));
      $result = mysql_query($sql);
      if (!$result){
        throw new Exception(_("Erreur lors de la mise à jour de la description de l'article dans la base de données : ") .mysql_error());
        // annule les modifications dans la bdd
        mysql_query("ROLLBACK"); 
      }
    }
    
    // termine la transaction
    mysql_query("COMMIT");
    
    return true;
  }
  
  /**
   * Supprime l'article
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_shop_article WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de l'article : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Recherche le nom de l'article dans la langue dont le code est passée en paramètre
   * 
   * Les codes de langues sont au format ISO 639-1 ({@link http://fr.wikipedia.org/wiki/Liste_des_codes_ISO_639-1})
   * 
   * Si une erreur survient, une exception est levée.      
   * 
   * @param string $lang Le code de la langue dans laquelle on doit récupérer le nom de l'article
   * @return string Le nom de l'article dans la langue demandée               
   */   
  public function get_name($lang){
    $sql = sprintf("SELECT name FROM miki_shop_article_name WHERE id_article = %d AND  language = '%s'",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($lang));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      return "";
    $row = mysql_fetch_array($result);
    return stripslashes($row[0]);
  }
  
  /**
   * Recherche la description de l'article dans la langue dont le code est passée en paramètre
   * 
   * Les codes de langues sont au format ISO 639-1 ({@link http://fr.wikipedia.org/wiki/Liste_des_codes_ISO_639-1})
   * 
   * Si une erreur survient, une exception est levée.      
   * 
   * @param string $lang Le code de la langue dans laquelle on doit récupérer la description de l'article
   * @param string $nb_char Si != "", on tronque la description après x caractères ou x = $nb_char
   * @param boolean $full_word Si true, on tronque par mot entier. Si false, on tronque par caractère         
   * @return string Le nom de l'article dans la langue demandée               
   */
  public function get_description($lang, $nb_char = "", $full_word = true){
    $sql = sprintf("SELECT description FROM miki_shop_article_description WHERE id_article = %d AND  language = '%s'",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($lang));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      return "";
    $row = mysql_fetch_array($result);
    $description = stripslashes($row[0]);
    
    // si on prend toute la description on la retourne en entier
    if ($nb_char == "")
      return $description;
    
    // sinon on la coupe de la façon demandée
    if ($nb_char < mb_strlen($description)){
      if ($full_word === false)
        $stop = $nb_char;
      else{
        $stop = mb_strpos($description, ' ', $nb_char);
        if ($stop === false){
          $stop = mb_strlen($description);
        }
      }
      return mb_substr($description, 0, $stop);
    }
    else
      return $description;
  }
  
  /**
   * Recherche les frais de port de l'article en fonction de la personne qui le commande
   * 
   * @param int $id_person Personne ayant commandé l'article
   *         
   * @return float    
   */   
  public function get_shipping($id_person){
    $shop = new Miki_shop($this->id_shop);
    $person = new Miki_person($id_person);
    
    if ($shop->shipping_method == 0){
      $shop_shipping = new Miki_shop_shipping_weight_price(0, $this->id_shop);
      return $shop_shipping->get_shipping($this, $person);
    }
  }
  
  /**
   * Change l'état de l'article
   * 
   * @param int $state Nouvel état de l'article      
   */   
  public function change_state($state){
    $this->state = $state;
    $this->update();
  }
  
  /**
   * Détermine si l'article possède l'attribut dont l'id est passé en paramètre
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @param int $id_attribute Id de l'attribut à vérifier
   *      
   * @return boolean      
   */   
  public function has_attribute($id_attribute){
    $sql = sprintf("SELECT count(*) FROM miki_shop_article_attribute_s_miki_shop_article 
                    WHERE miki_shop_article_attribute_id = %d 
                      AND miki_shop_article_id = %d",
              mysql_real_escape_string($id_attribute),
              mysql_real_escape_string($this->id)); 
              
    $result = mysql_query($sql);
    
    // s'il y a une erreur dans la requête
    if (!$result){
      throw new Exception(_("Erreur lors de la recherche des attributs de l'article : ") .mysql_error());
    }
    
    $row = mysql_fetch_array($result);
    return $row[0] > 0;
  }
  
  /**
   * Ajoute un attribut à l'article
   * 
   * Si une erreur survient, une exception est levée.
   * 
   * @param int $id_attribute Id de l'attribut à ajouter
   * 
   * @return boolean      
   */   
  public function add_attribute($id_attribute){
    $sql = sprintf("INSERT INTO miki_shop_article_attribute_s_miki_shop_article 
                    (miki_shop_article_attribute_id, miki_shop_article_id)
                    VALUES (%d, %d)",
            mysql_real_escape_string($id_attribute),
            mysql_real_escape_string($this->id)); 
              
    $result = mysql_query($sql);
    
    // s'il y a une erreur dans la requête
    if (!$result){
      throw new Exception(_("Erreur lors de l'ajout de l'attribut à l'article : ") .mysql_error());
    }
    
    return true;
  }
  
  /**
   * Supprime tous les attributs de l'article
   * 
   * Si une erreur survient, une exception est levée.
   *     
   * @return boolean
   */         
  public function delete_all_attributes(){
    $sql = sprintf("DELETE FROM miki_shop_article_attribute_s_miki_shop_article 
                    WHERE miki_shop_article_id = %d",
            mysql_real_escape_string($this->id)); 
              
    $result = mysql_query($sql);
    
    // s'il y a une erreur dans la requête
    if (!$result){
      throw new Exception(_("Erreur lors de la suppression des attributs de l'article : ") .mysql_error());
    }
    
    return true;
  }
  
 /**
  * Recherche tous les attributs de l'article
  *
  * Si une erreur survient, une exception est levée.
  * 
  * @return mixed Un tableau à 2 dimensions. L'indice de la première dimension contient l'id de l'attribut. La 2è dimension contient les éléments (de l'attribut) suivants : name, code, type et value       
  */                    
  public function get_attributes(){
    $sql = sprintf("SELECT * 
                    FROM miki_shop_article_attribute ma,
                         miki_shop_article_attribute_s_miki_shop_article maa
                    WHERE maa.miki_shop_article_id = %d
                      AND maa.miki_shop_article_attribute_id = ma.id",
            mysql_real_escape_string($this->id)); 
    $result = mysql_query($sql);
    
    // s'il y a une erreur dans la requête
    if (!$result){
      throw new Exception(_("Erreur lors de la recherche des attributs d'articles : ") .mysql_error());
    }
    
    $attributes = array();
    
    while($row = mysql_fetch_array($result)){
      $attributes[$row['id']]['name'] = $row['name'];
      $attributes[$row['id']]['code'] = $row['code'];
      $attributes[$row['id']]['type'] = $row['type'];
      $attributes[$row['id']]['value'] = $row['value'];
    }
    
    return $attributes;
  }

 /**
  * Recherche tous les attributs disponibles
  * 
  * Si une erreur survient, une exception est levée.
  *    
  * @return mixed Un tableau à 2 dimensions. La 2è dimension contient les éléments (de l'attribut) suivants : id, name, code, type et value
  */                    
  public static function get_all_attributes(){
    $sql = "SELECT * FROM miki_shop_article_attribute"; 
    $result = mysql_query($sql);
    
    // s'il y a une erreur dans la requête
    if (!$result){
      throw new Exception(_("Erreur lors de la recherche des attributs d'articles : ") .mysql_error());
    }
    
    $attributes = array();
    
    $x = 0;
    while($row = mysql_fetch_array($result)){
      $attributes[$x]['id'] = $row['id'];
      $attributes[$x]['name'] = $row['name'];
      $attributes[$x]['code'] = $row['code'];
      $attributes[$x]['type'] = $row['type'];
      $attributes[$x]['value'] = $row['value'];
      $x++;
    }
    
    return $attributes;
  }
  
  /**
   * Ajoute un set d'option (Miki_shop_article_option_set) à l'article
   * 
   * Si une erreur survient, une exception est levée.  
   * 
   * @param int $id_set Id du set à ajouter à l'article   
   * @see Miki_shop_article_option_set
   * @return boolean
   */      
  public function add_set($id_set){
    // si l'article n'existe pas encore dans la base de données, on essaye de l'y insérer
    if (!isset($this->id)){
      try{
        $this->save();
      }catch(Exception $e){
        throw new Exception(_("L'article n'a pas pu être ajouté"));
      }
    }
    // insert le set dans l'article. Si il y est déjà, ne fait rien (mot-clé "ignore")
    $sql = sprintf("INSERT ignore INTO miki_shop_article_option_set_s_miki_shop_article (miki_shop_article_id, miki_shop_article_option_set_id) VALUES(%d, %d)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($id_set));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant l'ajout du set à l'article"));
    
    return true;
  }
  
  /**
   * Supprime un set d'options (Miki_shop_article_option_set) de l'article
   * 
   * Si une erreur survient, une exception est levée.  
   * 
   * @param int $id_set Id du set à supprimer de l'article   
   * @see Miki_shop_article_option_set
   * @return boolean
   */ 
  public function remove_set($id_set){
    $sql = sprintf("DELETE FROM miki_shop_article_option_set_s_miki_shop_article WHERE miki_shop_article_id = %d AND miki_shop_article_option_set_id = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($id_set));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du set de l'article"));
      
    return true;
  }
  
  /**
   * Supprime tous les sets (Miki_shop_article_option_set) de l'article
   * 
   * Si une erreur survient, une exception est levée.
   * 
   * @see Miki_shop_article_option_set
   * @return boolean
   */ 
  public function remove_all_sets(){
    $sql = sprintf("DELETE FROM miki_shop_article_option_set_s_miki_shop_article WHERE miki_shop_article_id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression des sets de l'article"));
      
    return true;
  }
  
  /**
   * Détermine si le set dont l'id est donné est dans l'article en cours
   * 
   * @param int $id_set Le set à tester
   * @return boolean
   */
  public function has_set($id_set){
    $sql = sprintf("SELECT count(*) 
                    FROM miki_shop_article_option_set_s_miki_shop_article 
                    WHERE miki_shop_article_id = %d AND miki_shop_article_option_set_id = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($id_set));
    $result = mysql_query($sql);
    $row = mysql_fetch_row($result);
    return $row[0] > 0;
  }
  
  /**
   * Recherche tous les sets faisant partie de l'article
   * 
   * @see Miki_shop_article_option_set   
   * @return mixed Un tableau d'éléments Miki_shop_article_option_set représentant les sets faisant partie de l'article
   */          
  public function get_sets(){
    $return = array();
    $sql = sprintf("SELECT miki_shop_article_option_set_id 
                    FROM miki_shop_article_option_set_s_miki_shop_article 
                    WHERE miki_shop_article_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_row($result)){
      $return[] = new Miki_shop_article_option_set($row[0]);
    }
    return $return;
  }
  
  /**
   * Ajoute une option (Miki_shop_article_option) à l'article
   * 
   * Si une erreur survient, une exception est levée.  
   * 
   * @param int $id_option Id de l'option à ajouter à l'article
   * @param int $position La position de l'option pour l'affichage      
   * @see Miki_shop_article_option
   * @return boolean
   */      
  public function add_option($id_option, $position = 0){
    // si l'article n'existe pas encore dans la base de données, on essaye de l'y insérer
    if (!isset($this->id)){
      try{
        $this->save();
      }catch(Exception $e){
        throw new Exception(_("L'article n'a pas pu être ajouté"));
      }
    }
    // insert l'option dans l'article. Si il y est déjà, ne fait rien (mot-clé "ignore")
    $sql = sprintf("INSERT ignore INTO miki_shop_article_option_s_miki_shop_article (miki_shop_article_option_id, miki_shop_article_id, position) VALUES(%d, %d, %d)",
      mysql_real_escape_string($id_option),
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($position));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant l'ajout de l'option"));
    
    return true;
  }
  
  /**
   * Supprime une option (Miki_shop_article_option) de l'article
   * 
   * Si une erreur survient, une exception est levée.  
   * 
   * @param int $id_option Id de l'option à supprimer de l'article   
   * @see Miki_shop_article_option
   * @return boolean
   */ 
  public function remove_option($id_option){
    $sql = sprintf("DELETE FROM miki_shop_article_option_s_miki_shop_article WHERE miki_shop_article_option_id = %d AND miki_shop_article_id = %d",
      mysql_real_escape_string($id_option),
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de l'option"));
      
    return true;
  }
  
  /**
   * Supprime toutes les options (Miki_shop_article_option) de l'article
   * 
   * Si une erreur survient, une exception est levée.  
   * 
   * @param int $id_option_set Si donné, ne supprime que les options du set correspondant à l'id donné
   * @see Miki_shop_article_option
   * @return boolean
   */ 
  public function remove_all_options($id_option_set = ""){
    if (is_numeric($id_option_set)){
      $sql = sprintf("DELETE FROM miki_shop_article_option_s_miki_shop_article 
                      WHERE miki_shop_article_option_id IN (SELECT miki_shop_article_option_id FROM miki_shop_article_option_s_miki_shop_article_option_set WHERE miki_shop_article_option_set_id = %d)
                      AND miki_shop_article_id = %d",
        mysql_real_escape_string($id_option_set),
        mysql_real_escape_string($this->id)); 
    }
    else{ 
      $sql = sprintf("DELETE FROM miki_shop_article_option_s_miki_shop_article WHERE miki_shop_article_id = %d",
        mysql_real_escape_string($this->id));
    }
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression des options"));
      
    return true;
  }
  
  /**
   * Détermine si l'article possède l'option dont l'id est donné
   * 
   * @param int $id_option L'option à tester
   * @return boolean
   */
  public function has_option($id_option){
    $sql = sprintf("SELECT count(*) 
                    FROM miki_shop_article_option_s_miki_shop_article 
                    WHERE miki_shop_article_id = %d AND miki_shop_article_option_id = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($id_option));
    $result = mysql_query($sql);
    $row = mysql_fetch_row($result);
    return $row[0] > 0;
  }
  
  /**
   * Recherche toutes les options faisant partie de l'article
   *
   * @param int $id_set Si définit, ne recherche que les options du set dont l'id a été donné.
   * @param boolean $in_stock Si TRUE, ne prend que les options disponibles. Si FALSE, prend toutes les options.   
   * @see Miki_shop_article_option
   * @return mixed Un tableau d'éléments Miki_shop_article_option représentant les options faisant partie de l'article
   */          
  public function get_options($id_set = "", $in_stock = false){
    $return = array();
    
    // prépare la requête SQL en fonction de si on doit rechercher toutes les options ou seulement celles d'un set donné
    if (is_numeric($id_set)){
      $sql = sprintf("SELECT mo.id 
                      FROM miki_shop_article_option_s_miki_shop_article ma,
                           miki_shop_article_option_s_miki_shop_article_option_set ms,
                           miki_shop_article_option mo
                      WHERE ma.miki_shop_article_id = %d AND 
                            ma.miki_shop_article_option_id = ms.miki_shop_article_option_id AND
                            mo.id = ma.miki_shop_article_option_id AND
                            ms.miki_shop_article_option_set_id = %d",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($id_set));
    }
    else{
      $sql = sprintf("SELECT miki_shop_article_option_id 
                      FROM miki_shop_article_option_s_miki_shop_article,
                           miki_shop_article_option mo
                      WHERE mo.id = ma.miki_shop_article_option_id AND 
                            miki_shop_article_id = %d",
        mysql_real_escape_string($this->id));
    }
    
    // si on ne prend que les options disponibles
    if ($in_stock){
      $sql .= " AND (mo.use_stock = 0 OR mo.quantity > 0)";
    }
    
    // tri par la position définie
    $sql .= " ORDER BY position ASC";
    
    $result = mysql_query($sql);
    while($row = mysql_fetch_row($result)){
      $return[] = new Miki_shop_article_option($row[0]);
    }
    return $return;
  }
  
  /**
   * Supprime une photo de l'article
   * 
   * Si une erreur survient, une exception est levée.      
   *
   * @param string $name Nom de la photo à supprimer   
   * 
   * @return boolean      
   */   
  public function delete_picture($name){
    
    $tab_temp = array();
    
    foreach($this->pictures as $p){
      if ($p === $name && !is_dir($p) && $p != "no-picture.gif"){
        $path = $this->picture_path .$p;
        if (file_exists($path)){
          if (!unlink($path)){
            throw new Exception("Erreur pendant la suppression de la photo");
          }
        }
        $path = $this->picture_path .'thumb/' .$p;
        if (file_exists($path)){
          if (!unlink($path)){
            throw new Exception("Erreur pendant la suppression de la miniature de la photo");
          }
        }
      }
      elseif($p != "no-picture.gif" || $p != $name){
        $tab_temp[] = $p;
      }
    }
    $this->pictures = $tab_temp;
    $this->update();
    return true;
  }
   
  /**
   * Ajoute une image à l'article
   * 
   * Ajoute une image à l'article, l'upload et le créé une miniature de taille maximale de 200px de large ou de long
   * 
   * Si une erreur survient, une exception est levée.      
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   * @param string $nom_destination Nom de destination de l'image finale
   *    
   * @return boolean
   */    
  public function upload_picture($fichier, $nom_destination){
    
    // traite le nom de destination
    $nom_destination = decode($nom_destination);  
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
  		
  	// pas d'erreur --> upload
  	if ((isset($fichier['tmp_name']))&&($fichier['error'] == UPLOAD_ERR_OK)) {
  		if (!move_uploaded_file($fichier['tmp_name'], $this->picture_path .$nom_destination))
        exit();
  	}
  	
  	// redimensionne l'image
  	createthumb($this->picture_path .$nom_destination, $this->picture_path ."thumb/" .$nom_destination, 200, 200, false);

  	$this->pictures[] = $nom_destination;
  	return true;
  }
  
  /**
   * Envoi un mail au blog du site Internet pour mettre l'article sur le blog
   * 
   * Publie l'article sur le blog lors de l'ajout ou de la modification de l'article via le Miki (console d'administration).
   * Publie uniquement si "Publier les articles du shop sur le blog" est coché dans la partie "Administration -> Configurer le site Internet" du Miki.
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
    
    // récupert l'image et sa taille de l'article en cours
    $image = "";
    if (sizeof($this->pictures) == 0)
      $image = "";
    else{
      $pics_temp = $this->pictures;
      while($image == "" && sizeof($pics_temp) > 0){
        $image = array_shift($pics_temp);
      }
    }
    
    if ($image != "" && $image != "no-picture.gif")
      $size = $this->get_image_size("$site_url/pictures/shop_articles/$image", 300, 300);
    
    $promo = $this->get_promotion();
    
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
    $mail->PluginDir = "../scripts/";
    $mail->isHTML(true);
    
    /*******************************************************************
     *
     * Post l'article sur le blog Bleu Blog via envoi par e-mail
     * 
     *******************************************************************/
                   
    $subject = stripslashes($this->get_name(Miki_language::get_main_code()));
        
    // contenu html
    $body = "<a href='$site_url/" .stripslashes($this->get_url_simple()) ."' title='" .stripslashes($this->get_url_simple()) ."'>" .stripslashes($this->get_name('fr')) ."</a> à vendre<br /><br />";
            
            if ($image != "")
              $body .= "<img src='$site_url/pictures/shop_articles/" .$image ."' alt=\"image de l'article \" style='border:0;width:" .$size[0] ."px;height:" .$size[1] ."px' /><br /><br />";
              
    /*$body .= stripslashes($this->get_description('fr')) ."<br /><br />
             Prix sur <a href='http://www.pimus.ch/" .stripslashes($this->get_url_simple()) ."' title='" .stripslashes($this->get_url_simple()) ."'>http://www.pimus.ch</a> : " .number_format($this->price,2,'.',"'") ." CHF";*/
             
    
    $body .= "<table>  
                <tr>
                  <td style='vertical-align:top;font-weight:bold;min-width:150px'>Nom :</td>
                  <td>" .$this->get_name('fr') ."</td>
                </tr>
                <tr>
                  <td style='vertical-align:top;font-weight:bold'>Description :</td>
                  <td style='white-space:pre-wrap'>" .$this->get_description('fr') ."</td>
                </tr>
                <tr>
                  <td style='vertical-align:top;font-weight:bold'>Prix :</td>
                  <td>";
                    
                    if ($promo)
                      $body .= '<del>' .number_format($this->price,2,'.',"'") ." CHF</del><span style='padding-left:10px;color:#ff0000'>" .number_format($promo,2,'.',"'") .' CHF</span>';
                    else
                      $body .= number_format($this->price,2,'.',"'") .' CHF';
                    
        $body .= "</td>
                </tr>
              </table>";
    
    $mail->Subject = $subject;
    $mail->Body = $body;

    $mail->AddAddress($publish_email_address);
    //$mail->AddAddress("herve@fbw-one.com");
    
    if(!$mail->Send())
      throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);

    $mail->ClearAddresses();
  }
  
  /**
   * Retourne l'url de l'article en fonction de sa catégorie et de son nom (utilisée via l'URL Rewritting)
   * 
   * @return string      
   */   
  public function get_url_simple(){
    if (isset($_SESSION['lang']) && Miki_language::exist($_SESSION['lang'])) {
    	$lang = $_SESSION['lang'];
    }
    else{
      $lang = Miki_language::get_main_code();
    }
       
    // insert les catégories dans l'url
    $category = new Miki_shop_article_category($this->id_category);
    $category_name = decode(htmlspecialchars_decode($category->name[$lang], ENT_NOQUOTES));
    $parents = $category->get_parents();
    foreach($parents as $p){
      $category_name = decode(htmlspecialchars_decode($p->name[$lang], ENT_NOQUOTES)) .'/' .$category_name;
    }
    
    $url = 'article/' .$category_name .'/' .decode($this->get_name($lang)) .'-' .$this->id .'?l=' .$lang;
        
    return $url;
  }
  
  /**
   * Recherche si l'article est en promotion actuellement. Si oui, on retourne le prix en promotion.
   * 
   * @return mixed Retourne le prix en promotion si l'article est actuellement en promotion, false sinon.
   */     
  public function get_promotion(){
    $sql = sprintf("SELECT price 
                    FROM miki_shop_promotion 
                    WHERE id_article = %d AND
                    date_start <= CURDATE() AND
                    date_stop >= CURDATE()",
            mysql_real_escape_string($this->id));
    
    $result = mysql_query($sql);

    $price = false;
    while($row = mysql_fetch_array($result)){
      $price = $row[0];
    }
    return $price;
  }
  /**
   *  récupert les commentaires de l'article
   *  
   *  @param $score si true, ne retourne que les commentaires qui ont une note, si false, retourne tous les commentaires
   *  @param $spam si true, récupert également les spams, si false, ne récupert pas les spams         
   *  @param $limit si > 0, on retourne les $limit derniers commentaires, si = 0, on retourne tous les commentaires     
   *  @param $page retourne la page n° $page des résultats               
   *  @return : tableau 2 dimensions
   *              - index 'id' = id du commentaire
   *              - index 'id_person' = id de la personne
   *              - index 'comment' = commentaire
   *              - index 'score' = score donnée par la personne   
   *              - index 'date' = date du commentaire      
   **************************************************************************/                                
  public function get_comments($score = false, $spam = false, $limit = 0, $page = 1){
    $sql = sprintf("SELECT id, person_firstname, person_lastname, comment, score, date FROM miki_shop_article_comment WHERE id_article = %d",
      mysql_real_escape_string($this->id));
     
    // si on ne prend que les commentaires possédant une note 
    if ($score){
      $sql .= " AND score != 'NULL'";
    }
    
    // si on ne prend pas les spams 
    if (!$spam){
      $sql .= " AND spam = 0";
    }
    
    $sql .= " ORDER BY date DESC";
    
    // si on ne doit rendre que les x derniers commentaires
    if ($limit > 0){
      $start = $limit * ($page - 1);
      $sql .= sprintf(" LIMIT %d, %d",
        mysql_real_escape_string($start),
        mysql_real_escape_string($limit));
    }
    
    $result = mysql_query($sql);
    
    $return = array();
    $x = 0;
    
    while($row = mysql_fetch_array($result)){
      $return[$x]['id'] = $row[0];
      $return[$x]['person_firstname'] = $row[1];
      $return[$x]['person_lastname'] = $row[1];
      $return[$x]['comment'] = $row[2];
      $return[$x]['score'] = $row[3];
      $return[$x]['date'] = $row[4];
      $x++;
    }
    
    return $return;
  }
  
  /**
   *  Ajoute un commentaire à l'article
   *  
   *  Si un erreur survient lors de l'ajout, une exception est levée 
   *  @param string $person_firstname prénom de l'auteur du commentaire
   *  @param string $person_lastname nom de l'auteur du commentaire   
   *  @param $comment texte du commentaire    
   *  @param $score score donné à l'article   
   *  @param $spam pour savoir si le commentaire a été défini comme SPAM par Askimet       
   *  @return boolean
   */  
  public function add_comment($person_firstname, $person_lastname, $comment, $score, $spam = 0){
    $sql = sprintf("INSERT INTO miki_shop_article_comment(id_article, person_firstname, person_lastname, comment, score, date, spam) VALUES(%d, '%s', '%s', '%s', %d, NOW(), %d)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($person_firstname),
      mysql_real_escape_string($person_lastname),
      mysql_real_escape_string($comment),
      mysql_real_escape_string($score),
      mysql_real_escape_string($spam));
    $result = mysql_query($sql);
    
    if (!$result){
      throw new Exception(_("Erreur lors de l'ajout du commentaire") ."<br />" .mysql_error());
    }
    
    // récupert l'id du commentaire qui a été posté
    $id_comment = mysql_insert_id();
    
    return true;
  }
  
  /**
   *  Supprime un commentaire de l'article
   *  
   *  Si un erreur survient lors de l'ajout, une exception est levée      
   *  @param $id_comment id du commentaire à supprimer
   *  @return boolean   
   */
  public function remove_comment($id_comment){
    $sql = sprintf("DELETE FROM miki_shop_article_comment WHERE id = %d",
      mysql_real_escape_string($id_comment));
    $result = mysql_query($sql);
    
    if (!$result){
      throw new Exception(_("Erreur lors de la suppression du commentaire") ."<br />" .mysql_error());
    }
    
    return true;
  }

  /**
   * Recherche les $nb derniers articles (seulement ceux du shop spécifié si $id_shop != "")
   * 
   * @param int $nb Recherche le x derniers articles où x = $nb 
   * @param int $id_shop Si != "", recherche seulement les articles du shop dont l'id est donné   
   * 
   * @static
   * @return mixed Un tableau d'éléments Miki_shop_article représentant les $nb derniers articles         
   */   
  public static function get_last_articles($nb, $id_shop = ""){
    $return = array();
    $sql = "SELECT * FROM miki_shop_article";
    
    if ($id_shop !== "" && is_numeric($id_shop))
      $sql .= " WHERE id_shop = $id_shop";
      
    $sql .= " ORDER BY date_created DESC LIMIT 0, $nb";
    
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $item = new Miki_shop_article($row['id']);
      $return[] = $item;
    }
    return $return;
  }


  /**
   * Recherche des articles
   * 
   * @param int $id_shop Si != "", récupère seulement ceux du shop dont l'id est spécifié
   * @param string $search Critères de recherche. Recherche dans le nom (name) et la référence (ref) de l'article
   * @param string $lang Si != "", recherche uniquement dans la langue dont le code est donné      
   * @param int $cat Si != "", récupère seulement ceux de la catégorie spécifiée
   * @param int $type Si != "", récupère seulement ceux du type spécifié   
   * @param int $valid Si = true, récupère seulement les article en vente (state = 1)
   * @param int $promo Si = true, récupère seulement les articles en promotion
   * @param int $in_stock Si = true, récupère seulement les articles en stock
   * @param string $order Par quel champ les articles trouvés seront triés (ref, name, state, price, quantity). Si vide, on tri selon leur nom.
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)               
   *
   * @static         
   * @return mixed Un tableau d'éléments de type Miki_shop_article représentant les articles trouvés 
   */      
  public static function search($id_shop = "", $search = "", $lang = "", $cat = "", $type = "", $valid = true, $promo = false, $in_stock = true, $order = "", $order_type = "asc"){
    $return = array();
    
    if ($promo){
      $sql = "SELECT DISTINCT ma.id
              FROM miki_shop_article ma, 
                   miki_shop_article_name man,
                   miki_shop_article_category mc,
                   miki_shop_promotion mp
              WHERE man.id_article = ma.id AND
                    mp.id_article = ma.id AND
                    mc.id = ma.id_category";
    }
    else{
      $sql = "SELECT DISTINCT ma.id
              FROM miki_shop_article ma, 
                   miki_shop_article_name man,
                   miki_shop_article_category mc
              WHERE man.id_article = ma.id AND
                    mc.id = ma.id_category";
    }
    
    if ($id_shop !== "" && is_numeric($id_shop))
      $sql .= sprintf(" AND ma.id_shop = %d",
                mysql_real_escape_string($id_shop));
    
    // recherche dans le nom et la référence de l'article
    if ($search !== ""){
      $search = mb_strtolower($search, 'UTF-8');
                
      $sql .= sprintf(" AND (LOWER(man.name) LIKE '%%%s%%' OR 
                             LOWER(ma.ref) LIKE '%%%s%%')",
                mysql_real_escape_string($search),
                mysql_real_escape_string($search));
    }
    
    // spécifie la langue dans laquelle on recherche les articles
    if ($lang !== ""){
      $sql .= sprintf(" AND man.language = '%s'",
                mysql_real_escape_string($lang));
    }
    else{
      $main_language = Miki_language::get_main_code();
      $sql .= sprintf(" AND man.language = '%s'",
                mysql_real_escape_string($main_language));
    }
    
    // spécifie la catégorie dans laquelle on recherche les articles
    if ($cat !== ""){
      // récupert toutes les catégories possibles (catégorie donnée + ses enfants récursifs)
      $category = new Miki_shop_article_category($cat);
      $children = $category->get_all_children();
      $list_children = "$cat";
      foreach($children as $p){
        $list_children .= ", $p->id";
      }
      
      // recherche uniquement dans cette liste de catégories
      if ($list_children !== ""){
        $sql .= sprintf(" AND mc.id in (%s)",
                  mysql_real_escape_string($list_children));
      }
    }
    
    // recherche uniquement les articles du type donné
    if (is_numeric($type)){
      $sql .= sprintf(" AND ma.type = %d",
                  mysql_real_escape_string($type));
    }
    
    if ($valid){
      $sql .= " AND ma.state = 1";
    }
    
    if ($in_stock){
      $sql .= " AND ma.quantity > 0";
    }
    
    if ($order == "ref")
      $sql .= " ORDER BY LCASE(ma.ref) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "name")
      $sql .= " ORDER BY LCASE(man.name) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "type")
      $sql .= " ORDER BY LCASE(ma.type) " .$order_type;
    elseif ($order == "state")
      $sql .= " ORDER BY LCASE(ma.state) " .$order_type;
    elseif ($order == "price")
      $sql .= " ORDER BY LCASE(ma.price) " .$order_type;
    elseif ($order == "quantity")
      $sql .= " ORDER BY LCASE(ma.quantity) " .$order_type;
    else
      $sql .= " ORDER BY LCASE(man.name) COLLATE utf8_general_ci " .$order_type;

    $result = mysql_query($sql) or die("Erreur : $sql");
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_shop_article($row['id']);
    }
    return $return;
  }
  
  /**
   * Recherche le nombre d'articles total
   * 
   * @param int $id_shop Si != "", recherche seulement les articles dans le shop dont l'id est spécifié
   *      
   * @static
   * @return int   
   */   
  public static function get_nb_articles($id_shop = ""){
    $sql = "SELECT count(*) FROM miki_shop_article WHERE 1";
    
    if ($id_shop !== "" && is_numeric($id_shop))
      $sql .= " AND id_shop = $id_shop";
    
    $result = mysql_query($sql);
    
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * fonction statique récupérant tous les articles (seulement ceux du shop spécifié si $id_shop != "")
   * 
   * @param int $id_shop Si != "", recherche seulement les articles dans le shop dont l'id est spécifié
   *      
   * @static
   * @return mixed Un tableau d'éléments de type Miki_shop_article représentant les articles trouvés   
   */ 
  public static function get_all_articles($id_shop = ""){
    $return = array();
    $sql = "SELECT id FROM miki_shop_article";
    
    if ($id_shop !== "" && is_numeric($id_shop))
      $sql .= " WHERE id_shop = $id_shop";
    
    $result = mysql_query($sql) or die("Erreur sql : <br />$sql");
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_shop_article($row[0]);
    }
    return $return;
  }
}
?>