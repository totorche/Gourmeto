<?php
/**
 * Classe Miki_shop_promotion
 * @package Miki
 */ 

/**
 * Représentation d'une promotion d'un article.
 * 
 * Un article (Miki_shop_article) peut être promotionné temporairement. Cette classe représente cette promotion.
 * 
 * @see Miki_shop_article
 *  
 * @package Miki  
 */
class Miki_shop_promotion{

  /**
   * Id de la promotion
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Id de l'article en promotion
   *      
   * @var int
   * @access public   
   */
  public $id_article;
  
  /**
   * Permettra d'ajouter un texte à la promotion. N'est pas encore utilisé.
   * 
   * @var string
   * @access public   
   */
  public $flash;
  
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
   * Prix de l'article en promotion
   *      
   * @var float
   * @access public   
   */
  public $price;
  
  
  
  
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
    $sql = sprintf("SELECT * FROM miki_shop_promotion WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La promotion demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->id_article = $row['id_article'];
    $this->flash = $row['flash'];
    $this->date_start = $row['date_start'];
    $this->date_stop = $row['date_stop'];
    $this->price = $row['price'];
    return true;
  }
  
  /**
   * Sauvegarde la promotion dans la base de données.
   * 
   * La promotion doit être liée à un article, sinon une exception est levée
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->id_article))
      throw new Exception(_("Aucun article n'a été attribué à la promotion"));
      
    // si l'id de la promotion existe, c'est que la promotion existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    $sql = sprintf("INSERT INTO miki_shop_promotion (id_article, flash, date_start, date_stop, price) VALUES(%d, '%s', '%s', '%s', '%F')",
      mysql_real_escape_string($this->id_article),
      mysql_real_escape_string($this->flash),
      mysql_real_escape_string($this->date_start),
      mysql_real_escape_string($this->date_stop),
      mysql_real_escape_string($this->price));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion de la promotion dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la promotion
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour la promotion dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * La promotion doit être liée à un article, sinon une exception est levée     
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->id_article))
      throw new Exception(_("Aucun article n'a été attribué à la promotion"));
      
    // si aucun id existe, c'est que la promotion n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
    
    $sql = sprintf("UPDATE miki_shop_promotion SET id_article = %d, flash = '%s', date_start = '%s', date_stop = '%s', price = '%F' WHERE id = %d",
      mysql_real_escape_string($this->id_article),
      mysql_real_escape_string($this->flash),
      mysql_real_escape_string($this->date_start),
      mysql_real_escape_string($this->date_stop),
      mysql_real_escape_string($this->price),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour de la promotion dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime la promotion
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_shop_promotion WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la promotion : ") ."<br />" .mysql_error());
    return true;
  }

  /**
   * Recherche toutes les promotion
   * 
   * @param int $id_article Si != "", ne recherche que les promotions de l'article dont l'id est donné
   * @param boolean $now Si true, ne prend que les promotions actives actuellement. Si False, on les prend toutes.
   *             
   * @static
   * @return mixed Un tableau d'éléments Miki_shop_promotion représentant les promotions trouvées         
   */ 
  public static function get_all_promotion($id_article = "", $now = false){
    $return = array();
    $sql = "SELECT * FROM miki_shop_promotion";
    
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
      $return[] = new Miki_shop_promotion($row['id']);
    }
    return $return;
  }
}
?>