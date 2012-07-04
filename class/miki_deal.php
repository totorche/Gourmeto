<?php
/**
 * Classe Miki_deal
 * @package Miki
 */ 

/**
 * Représentation d'un deal
 * 
 * Un deal est une promotion temporaire d'un article (Miki_shop_article) caractérisée par un prix spécial pour une quantité définie durant un espace temps donné.
 * 
 * @see Miki_shop_article
 *  
 * @package Miki  
 */
class Miki_deal{

  /**
   * Id de la promotion
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Id de l'article en deal
   *      
   * @var int
   * @access public   
   */
  public $id_article;
  
  /**
   * Prix du deal
   *      
   * @var float
   * @access public   
   */
  public $price;
  
  /**
   * Quantité de base pour le deal
   *      
   * @var int
   * @access public   
   */
  public $quantity_start;
  
  /**
   * Quantité disponible pour le deal
   *      
   * @var int
   * @access public   
   */
  public $quantity;
  
  /**
   * Date de début de la promotion
   *      
   * @var string
   * @access public   
   */
  public $date_start;
  
  /**
   * Date de fin de la promotion
   *      
   * @var string
   * @access public   
   */
  public $date_stop;
  
  /**
   * Date de création du deal
   *      
   * @var string
   * @access public   
   */
  public $date_created;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge la promotion dont l'id a été donné
   * 
   * @param int $id Id de la catégorie à charger (optionnel)
   */
  function __construct($id = ""){
    // charge la promotion si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge une promotion depuis un id
   *    
   * Si la promotion n'existe pas, une exception est levée.
   *    
   * @param int $id id de la promotion à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_deal WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La promotion demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->id_article = $row['id_article'];
    $this->price = $row['price'];
    $this->quantity_start = $row['quantity_start'];
    $this->quantity = $row['quantity'];
    $this->date_start = $row['date_start'];
    $this->date_stop = $row['date_stop'];
    $this->date_created = $row['date_created'];

    return true;
  }
  
  /**
   * Sauvegarde le deal dans la base de données.
   * 
   * Le deal doit être lié à un article, sinon une exception est levée
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->id_article))
      throw new Exception(_("Aucun article n'a été attribué au deal"));
      
    // si l'id du deal existe, c'est que le deal existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    $sql = sprintf("INSERT INTO miki_deal (id_article, price, quantity_start, quantity, date_start, date_stop, date_created) VALUES(%d, '%F', '%d', '%d', '%s', '%s', '%s')",
      mysql_real_escape_string($this->id_article),
      mysql_real_escape_string($this->price),
      mysql_real_escape_string($this->quantity_start),
      mysql_real_escape_string($this->quantity),
      mysql_real_escape_string($this->date_start),
      mysql_real_escape_string($this->date_stop),
      mysql_real_escape_string($this->date_created));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion du deal dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la promotion
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour le deal dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Le deal doit être lié à un article, sinon une exception est levée     
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->id_article))
      throw new Exception(_("Aucun article n'a été attribué au deal"));
      
    // si aucun id existe, c'est que le deal n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
    
    $sql = sprintf("UPDATE miki_deal SET id_article = %d, price = '%F', quantity_start = '%d', quantity = '%d', date_start = '%s', date_stop = '%s', date_created = '%s' WHERE id = %d",
      mysql_real_escape_string($this->id_article),
      mysql_real_escape_string($this->price),
      mysql_real_escape_string($this->quantity_start),
      mysql_real_escape_string($this->quantity),
      mysql_real_escape_string($this->date_start),
      mysql_real_escape_string($this->date_stop),
      mysql_real_escape_string($this->date_created),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du deal dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime le deal
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_deal WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du deal : ") ."<br />" .mysql_error());
    return true;
  }

  /**
   * Recherche tous les deals
   * 
   * @param int $id_article Si != "", ne recherche que les deals de l'article dont l'id est donné
   * @param boolean $now Si true, ne prend que les deals actifs actuellement. Si False, on les prend tous.
   *             
   * @static
   * @return mixed Un tableau d'éléments Miki_deal représentant les deals trouvés         
   */ 
  public static function get_all_deals($id_article = "", $now = false){
    $return = array();
    $sql = "SELECT * FROM miki_deal";
    
    $criteres = "";
    // si on ne prend que les promotions actives actuellement
    if ($now)
      $criteres .= " date_start <= NOW() AND date_stop >= NOW()";
      
    if (is_numeric($id_article)){
      if ($criteres != "")
        $criteres .= " AND";
      $criteres .= sprintf(" id_article = %d",
                    mysql_real_escape_string($id_article));
    }
    
    if ($criteres != "")
      $sql .= " WHERE $criteres";
      
     $sql .= " ORDER by date_start DESC";      
    
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_deal($row['id']);
    }
    return $return;
  }
}
?>