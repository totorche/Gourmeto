<?php
/**
 * Classe Miki_order_article
 * @package Miki
 */ 

/**
 * Représentation d'un article dans une commande.
 * 
 * Une commande (Miki_order) comprend une liste d'articles (Miki_shop_article) possédant chacun un nombre d'exemplaires commandés ainsi qu'éventuellement une liste d'attributs.
 * Les attributs d'un articles peuvent être p.ex. la couleur, la taille, etc. 
 * Ils sont séparés par "&&". Exemple : couleur=bleu&&grandeur=L (couleur ou grandeur = code de l'attribut et bleu ou L = valeur de l'attribut) 
 * Il existe 3 types d'attributs : 0 = champ texte, 1 = Oui/Non, 2 = Liste déroulante   
 * 
 * @see Miki_order
 * @see Miki_shop_article
 *  
 * @package Miki  
 */
class Miki_order_article{

  /**
   * Id de l'élément représentant un article dans une commande
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Id de la commande
   *      
   * @var int
   * @access public   
   */
  public $id_order;
  
  /**
   * Id de l'article
   *      
   * @var int
   * @access public   
   */
  public $id_article;
  
  /**
   * Nombre d'exemplaires de l'article
   *      
   * @var int
   * @access public   
   */
  public $nb;
  
  /**
   * Prix de cet article pour la commande (le prix peut être celui de l'article normal ou le prix de l'article en promotion)
   *      
   * @var float
   * @access public   
   */
  public $price;
  
  /**
   * Frais de port pour cet article dans cette commande
   *      
   * @var float
   * @access public   
   */
  public $shipping;
  
  /**
   * Attributs de l'article
   *      
   * @var string
   * @access public   
   */
  public $attributes;
  
  /**
   * Si l'article est un deal
   *      
   * @var boolean
   * @access public   
   */
  public $miki_deal;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge l'article/commande dont l'id a été donné
   * 
   * @param int $id Id de l'article/commande à charger (optionnel)
   */
  function __construct($id = ""){
    // charge l'article si les id de la commande et de l'article sont fournis
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un article/commande depuis un id
   *    
   * Si l'article/commande n'existe pas, une exception est levée.
   *    
   * @param int $id id de l'article/commande à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_order_article WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("L'article demandé n'existe pas"));
      
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->id_order = $row['id_order'];
    $this->id_article = $row['id_article'];
    $this->nb = $row['nb'];
    $this->price = $row['price'];
    $this->shipping = $row['shipping'];
    $this->attributes = $row['attributes'];
    $this->miki_deal = $row['miki_deal'] == 1;
    return true;
  }
  
  /**
   * Calcul le prix des options de l'article
   * 
   * @return float Le prix supplémentaire selon les options liées à l'article 
   */
  public function get_options_price(){
    $price = 0;
    $article = new Miki_shop_article($this->id_article);
    
    // vérifie si l'article est un article configurable
    if ($article->type == 2){
      $options = $this->get_options();
      foreach($options as $option){
        $price += $option->price;
      }
    }
    
    return $price;
  }
  
  /**
   * Calcul le prix de l'article en prenant en compte les options si l'article est un article configurable
   * 
   * @param boolean $use_promo Si TRUE prend en compte les promotions éventuelles. Si une promo existe actuellement pour l'article, on va prendre le prix en promotion. Sinon prend le prix officiel de l'article. Si FALSE, ne prend pas en compte les promotions.   
   * @return float Le prix de l'article 
   */
  public function get_price($use_promo = false){
    $price = $this->price;
    $article = new Miki_shop_article($this->id_article);
    
    // gère les promotions
    if ($use_promo){
      $promo = $article->get_promotion();
      if ($promo)
        $price = $promo;
    }
    
    // vérifie si l'article est un article configurable
    if ($article->type == 2){
      $options = $this->get_options();
      foreach($options as $option){
        $price += $option->price;
      }
    }
    
    return $price;
  }
  
  /**
   * Ajoute une option (Miki_shop_article_option) à l'article
   * 
   * Si une erreur survient, une exception est levée.  
   * 
   * @param int $id_option Id de l'option à ajouter à l'article   
   * @see Miki_shop_article_option
   * @return boolean
   */      
  public function add_option($id_option){
    // si l'article n'existe pas encore dans la base de données, on essaye de l'y insérer
    if (!isset($this->id)){
      try{
        $this->save();
      }catch(Exception $e){
        throw new Exception(_("L'article n'a pas pu être ajouté"));
      }
    }
    
    // vérifie que l'article possède bien l'option que l'on tente d'ajouter
    $article = new Miki_shop_article($this->id_article);
    if (!$article->has_option($id_option)){
      throw new Exception(_("L'article ne possède pas cette option"));
    }
    
    // si l'option utilise la gestion des stocks, vérifie qu'il y ait assez de stock
    $option = new Miki_shop_article_option($id_option);
    
    // vérifie si on utilise la gestion des stock sur le site
    $use_stock = Miki_configuration::get('miki_shop_gestion_stock');
    
    // récupert la commande en cours
    $order = new Miki_order($this->id_order);
    
    // calcule la quantité disponible pour l'option demandée en tenant compte des articles déjà dans la commande en cours
    $quantity_available = $option->get_quantity_available($order, $this->id);
    
    // vérifie si il y a assez de stock
    if ($use_stock && $option->use_stock && $this->nb > $quantity_available){
      throw new Exception(_("La quantité demandée pour cette option est supérieure au stock disponible"));
    }
    
    // insert l'option dans l'article. Si il y est déjà, ne fait rien (mot-clé "ignore")
    $sql = sprintf("INSERT ignore INTO miki_order_article_s_miki_shop_article_option (miki_shop_article_option_id, miki_order_article_id) VALUES(%d, %d)",
      mysql_real_escape_string($id_option),
      mysql_real_escape_string($this->id));
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
    $sql = sprintf("DELETE FROM miki_order_article_s_miki_shop_article_option WHERE miki_shop_article_option_id = %d AND miki_order_article_id = %d",
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
      $sql = sprintf("DELETE FROM miki_order_article_s_miki_shop_article_option 
                      WHERE miki_shop_article_option_id IN (SELECT miki_shop_article_option_id FROM miki_shop_article_option_s_miki_shop_article_option_set WHERE miki_shop_article_option_set_id = %d)",
        mysql_real_escape_string($id_option_set)); 
    }
    else{ 
      $sql = sprintf("DELETE FROM miki_order_article_s_miki_shop_article_option WHERE miki_shop_article_id = %d",
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
                    FROM miki_order_article_s_miki_shop_article_option 
                    WHERE miki_order_article_id = %d AND miki_shop_article_option_id = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($id_option));
    $result = mysql_query($sql);
    $row = mysql_fetch_row($result);
    return $row[0] > 0;
  }
  
  /**
   * Recherche toutes les options faisant partie de l'article
   * 
   * @see Miki_shop_article_option   
   * @return mixed Un tableau d'éléments Miki_shop_article_option représentant les options faisant partie de l'article
   */          
  public function get_options(){
    $return = array();
    $sql = sprintf("SELECT miki_shop_article_option_id 
                    FROM miki_order_article_s_miki_shop_article_option 
                    WHERE miki_order_article_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    while($row = mysql_fetch_row($result)){
      $return[] = new Miki_shop_article_option($row[0]);
    }
    return $return;
  }
  
  /**
   * Retourne le nombre d'exemplaires d'un article donné (avec ses options) dans une commande donnée
   * 
   * @param int $id_article L'id de l'article dont on veut vérifier le nombre d'exemplaires
   * @param int $id_order L'id de la commande à contrôler
   * @param string $options Les options de l'article séparées par "&&". Exemple : 1&&2   
   * @param boolean $is_miki_deal Pour savoir si on a à faire un un deal.   
   *
   * @static
   * @return int Le nombre d'exemplaires de l'article donné dans la commande donnée
   */     
  public static function get_nb_articles_by_order($id_article, $id_order, $options, $is_miki_deal){
    if (!is_numeric($id_article) || !is_numeric($id_order))
      return false;
      
    // créé la requête SQL pour vérifier si l'article existe avec les options données
    $options = explode("&&", $options);
    $options_sql = "";
    foreach($options as $option){
      if (is_numeric($option)){
        $options_sql .= sprintf(" AND EXISTS (SELECT * FROM miki_order_article_s_miki_shop_article_option WHERE miki_order_article_id = miki_order_article.id AND miki_shop_article_option_id = %d)",
          mysql_real_escape_string($option));
      }
    }
          
    $sql = sprintf("SELECT SUM(nb) FROM miki_order_article WHERE id_order = %d AND id_article = %d AND miki_deal = %d",
        mysql_real_escape_string($id_order),
        mysql_real_escape_string($id_article),
        mysql_real_escape_string($is_miki_deal ? 1 : 0));
        
    // ajoute la requête SQL pour vérifier si l'article existe avec les options données
    $sql .= $options_sql;
    
    $result = mysql_query($sql) or die("Erreur SQL");
    
    if (mysql_num_rows($result) == 1){
      $row = mysql_fetch_array($result);
      return $row[0];
    }
    else{
      return false;
    }
  }
}
?>