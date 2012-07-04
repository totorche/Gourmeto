<?php
/**
 * Classe Miki_shop
 * @package Miki
 */ 

/**
 * Représentation d'un shop.
 * 
 * Un shop peut être lié à un membre (Miki_person) dans le cas d'une gestion multi-shops. 
 * Une gestion multi-shops permet à chaque membre d'ouvrir leur propre shop et de vendre leur propre produits.
 * 
 * @see Miki_person
 *  
 * @package Miki  
 */
class Miki_shop{

  /**
   * Id du shop
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom du shop
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Etat du shop (0 = désactivé, 1 = activé)
   *      
   * @var int
   * @access public   
   */
  public $state;
  
  /**
   * Description du shop
   *      
   * @var string
   * @access public   
   */
  public $description;
  
  /**
   * Id du membre possédant le shop
   *      
   * @var int
   * @access public   
   */
  public $id_person;
  
  /**
   * Date de création du shop
   *      
   * @var string
   * @access public   
   */
  public $date_created;
  
  /**
   * Type de gestion des frais de port
   *      
   * @var int
   * @access public   
   */
  public $shipping_method;
  
  /**
   * N° Iban du propriétaire du shop
   *      
   * @var int
   * @access public   
   */
  public $iban;
  
  /**
   * N° BIC/SWIFT du propriétaire du shop
   *      
   * @var int
   * @access public   
   */
  public $bic;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le shop dont l'id a été donné
   * 
   * @param int $id Id du shop à charger (optionnel)
   */
  function __construct($id = ""){
    // charge le shop si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un shop depuis un id
   *    
   * Si le shop n'existe pas, une exception est levée.
   *    
   * @param int $id id du shop à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_shop WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le shop demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->state = $row['state'];
    $this->description = $row['description'];
    $this->id_person = $row['id_person'];
    $this->date_created = $row['date_created'];
    $this->shipping_method = $row['shipping_method'];
    $this->iban = $row['iban'];
    $this->bic = $row['bic'];
    return true;
  }
  
  /**
   * Charge un shop depuis un membre
   *    
   * Si le shop n'existe pas, une exception est levée.
   *    
   * @param int $id_person id du membre propriétaire du shop à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load_from_person($id_person){
    $sql = sprintf("SELECT * FROM miki_shop WHERE id_person = %d",
      mysql_real_escape_string($id_person));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le shop demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->state = $row['state'];
    $this->description = $row['description'];
    $this->id_person = $row['id_person'];
    $this->date_created = $row['date_created'];
    $this->shipping_method = $row['shipping_method'];
    $this->iban = $row['iban'];
    $this->bic = $row['bic'];
    return true;
  }

  /**
   * Sauvegarde le shop dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    // si l'id du shop existe, c'est que le shop existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // vérifie que le shop soit assigné à un utilisateur
    if (!isset($this->id_person) || $this->id_person == ""){
      $this->id_person = "NULL";
      //throw new Exception(_("Le shop doit être assigné à un utilisateur avant de pouvoir continuer."));
    }
      
    $sql = sprintf("INSERT INTO miki_shop (name, state, description, id_person, date_created, shipping_method, iban, bic) VALUES('%s', %d, '%s', %s, NOW(), '%s', '%s', '%s')",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->description),
      mysql_real_escape_string($this->id_person),
      mysql_real_escape_string($this->shipping_method),
      mysql_real_escape_string($this->iban),
      mysql_real_escape_string($this->bic));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion du shop dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge le shop
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour le shop dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    // si aucun id existe, c'est que le shop n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
    
    $sql = sprintf("UPDATE miki_shop SET name = '%s', state = %d, description = '%s', id_person = %s, shipping_method = '%s', iban = '%s', bic = '%s' WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->description),
      mysql_real_escape_string($this->id_person),
      mysql_real_escape_string($this->shipping_method),
      mysql_real_escape_string($this->iban),
      mysql_real_escape_string($this->bic),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du shop dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime le shop
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_shop WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du shop : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Recherche toutes les promotions (Miki_shop_promotion) du shop
   * 
   * @see Miki_shop_promotion
   * @return mixed Un tableau d'éléments Miki_shop_promotion représentant les promotions du shop     
   */   
  public function get_promotions(){
    $return = array();
    $sql = sprintf("SELECT miki_shop_promotion.id FROM miki_shop_article, miki_shop_promotion 
                    WHERE miki_shop_article.id_shop = %d AND  miki_shop_promotion.id_article = miki_shop_article.id AND  miki_shop_promotion.date_start <= NOW() AND  miki_shop_promotion.date_stop >= NOW()",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $item = new Miki_shop_promotion($row['id']);
      $return[] = $item;
    }
    return $return;
  }
  
  /**
   * Récupert les différentes taxes configurées
   * 
   * @param string $country Si != "", recherche uniquement les taxes pour le pays donné. Si = "", recherche toutes les taxes.
   * 
   * @static       
   * @return mixed Un tableau à 2 dimensions dont les 1ers indices correspondent au nom de la taxe et les seconds indices au pays configuré et les valeurs correspondent au prix de la taxe liée
   */ 
  public static function get_taxes($country = ""){
    // recherche toutes les taxes configurées
    $sql = "SELECT mt.name name, mtv.country country, mtv.value value 
            FROM miki_shop_tax mt 
            LEFT JOIN miki_shop_tax_value mtv ON mt.id = mtv.id_tax";
    
    $sql .= " ORDER BY mt.id, mtv.id ASC";
    
    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql : <br />$sql<br />");
    
    $taxes_temp = array();
    $taxes = array();
    
    // parcourt les résultats
    while($row = mysql_fetch_array($result)){
      $taxes_temp[$row['name']][$row['country']] = $row['value'];
    }
    
    // si un pays a été demandé, on ne retourne que les valeurs des taxes pour le pays concerné
    if ($country != ""){
      // parcourt les différents types de taxes
      foreach($taxes_temp as $index=>$tax){
        // si une valeur est définie pour le pays donné dans le type de taxe en cours, on prend cette valeur
        if (isset($tax[$country])){
          $taxes[$index][$country] = $tax[$country];
        }
        // sinon on prend la valeur par défaut pour la taxe en cours
        elseif (isset($tax["all"])){
          $taxes[$index][$country] = $tax["all"];
        }
        // si aucune valeur par défaut de définie (problème quelque part) on donne la valeur '0' au pays concerné
        else{
          $taxes[$index][$country] = 0;
        }
      }
    }
    // si aucun pays n'est demandé, on retourne toutes les valeurs des taxes
    else{
      $taxes = $taxes_temp;
    }
    
    return $taxes;
  }
  
  /**
   * Supprime les taxes
   *
   * @param string $name Le nom de la taxe à supprimer. Si vide, supprime toutes les taxes
   *      
   * @static   
   * @return boolean TRUE si la suppression s'est effectuée avec succès, FALSE sinon
   */                    
  public static function remove_taxes($tax_name = ""){
    $sql = "DELETE FROM miki_shop_tax";
    
    if ($tax_name != ""){
      $sql .= sprintf(" WHERE name = '%s'",
               mysql_real_escape_string($tax_name));
    }
    
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la suppression des taxes demandées : ") ."<br />" .mysql_error());
    
    return true;
  }
  
  /**
   * Ajoute une valeur pour une taxe donnée
   * 
   * @param string $tax_name Le nom de la taxe pour laquelle on veut ajouter une valeur
   * @param string $country Le pays concerné par la taxe   
   * @param float $value La valeur de la taxe
   *
   * @static   
   * @return boolean TRUE si l'ajout s'est effectué avec succès, FALSE sinon
   */                    
  public static function add_tax($tax_name, $country, $value){
    // vérifie que la valeur de la taxe soit bien un nombre
    if (!is_numeric($value))
      return false;
    
    // récupert l'id de la taxe définie par le nom donné en paramètre
    $sql = sprintf("SELECT id FROM miki_shop_tax WHERE name = '%s'",
              mysql_real_escape_string($tax_name));
    $result = mysql_query($sql);

    if (mysql_num_rows($result) == 1){
      $row = mysql_fetch_array($result);
      $id_tax = $row['id'];
    }
    else{
      // recherche toutes les taxes configurées
      $sql = sprintf("INSERT INTO miki_shop_tax (name) VALUES ('%s')",
                mysql_real_escape_string($tax_name));
      $result = mysql_query($sql);
      if (!$result)
        throw new Exception(_("Erreur lors de l'ajout de la taxe : ") ."<br />" .mysql_error());
      // récupert l'id
      $id_tax = mysql_insert_id();
    }
    
    $sql = sprintf("INSERT INTO miki_shop_tax_value (id_tax, country, value) VALUES (%d, '%s', %F)
                    ON DUPLICATE KEY UPDATE value = %F",
              mysql_real_escape_string($id_tax),
              mysql_real_escape_string($country),
              mysql_real_escape_string($value),
              mysql_real_escape_string($value));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'ajout de la taxe : ") ."<br />" .mysql_error());
    
    return true;
  }
  
  /**
   * Récupert la méthode de frais de port choisie pour ce shop pour un transporteur donné
   * 
   * @param int $id_shop_transporter Id du transporteur pour lequel on veut récupérer la méthode de frais de port du shop
   * @return mixed La méthode de frais de port du shop pour le transporteur donné ou FALSE si aucune méthode trouvée 
   */
  public function get_shipping_method($id_shop_transporter){
    $sql = sprintf("SELECT shipping_method FROM miki_shop_transporter_s_miki_shop WHERE id_shop_transporter = %d AND id_shop = %d",
      mysql_real_escape_string($id_shop_transporter),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    
    if (mysql_num_rows($result) == 0)
      return false;
      
    $row = mysql_fetch_array($result);
    return $row['shipping_method'];
  }
  
  /**
   * Recherche les shops
   * 
   * @param string $search Critères de recherche (recherche dans éle nom du shop)
   * @param string $order Par quel champ les activités trouvées seront triées (name). Si vide, on tri selon leur id.
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)      
   * 
   * @static
   * @return mixed un tableau d'élément Miki_shop représentant tous les shops trouvés
   */      
  public static function search($search = "", $order = "name", $order_type = "ASC"){
    $return = array();
    
    $sql = "SELECT id 
            FROM miki_shop
            WHERE true";
    
    // recherche dans le nom de l'article
    if ($search !== ""){
      $search = strtolower($search);
      
      $sql .= sprintf(" AND LOWER(name) LIKE '%%%s%%'",
                mysql_real_escape_string($search),
                mysql_real_escape_string($search));
    }
    
    // pour l'ordre de tri
    if ($order == "name"){
      $sql .= sprintf(" ORDER BY name %s",
                mysql_real_escape_string($order_type));
    }
    elseif ($order == ""){
      $sql .= sprintf(" ORDER BY id %s",
                mysql_real_escape_string($order_type));
    }

    $result = mysql_query($sql) or die("Erreur : $sql");
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_shop($row['id']);
    }
    return $return;
  }
  
  /**
   * Recherche le nombre de shops
   * 
   * @static
   * @return int
   */            
  public static function get_nb_shops(){
    $return = array();
    $sql = "select count(*) from miki_shop";
    $result = mysql_query($sql);
    
    $row = mysql_fetch_array($result);
    return $row[0];
  }

  /**
   * Récupert tous les shops
   * 
   * @static
   * @return mixed un tableau d'élément Miki_shop représentant tous les shops trouvés
   */
  public static function get_all_shops(){
    $return = array();
    $sql = "SELECT * FROM miki_shop";
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $item = new Miki_shop($row['id']);
      $return[] = $item;
    }
    return $return;
  }
}
?>