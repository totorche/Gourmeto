<?php
/**
 * Classe Miki_shop_code_promo
 * @package Miki
 */ 

/**
 * Représentation d'un code promotionnel offrant un rabais
 * 
 * Un code promotionnel est valable dans un délai donné.
 * Il peut s'agir d'un rabais en CHF (ou autre monnaie) ou en pourcent.   
 * 
 * @package Miki  
 */
class Miki_shop_code_promo{
  
  /**
   * Id du code de promotion
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Le code de promotion
   *      
   * @var string
   * @access public   
   */
  public $code;
  
  /**
   * Type du code de promotion. 1 = rabais en francs; 2 = rabais en pourcent
   *      
   * @var int
   * @access public   
   */
  public $type;
  
  /**
   * Rabais octroyé
   *      
   * @var float
   * @access public   
   */
  public $discount;
  
  /**
   * Date de départ de la validité du code
   *      
   * @var string
   * @access public   
   */
  public $date_start;
  
  /**
   * Date de fin de la validité du code
   *      
   * @var string
   * @access public   
   */
  public $date_stop;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le code de promotion dont l'id a été donné
   * 
   * @param int $id Id de la catégorie à charger (optionnel)
   */
  function __construct($id = ""){
    // charge la promotion si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un code de promotion depuis un id
   *    
   * Si le code de promotion n'existe pas, une exception est levée.
   *    
   * @param int $id id du code de promotion à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_shop_code_promo WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le code de promotion demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->code = $row['code'];
    $this->type = $row['type'];
    $this->discount = $row['discount'];
    $this->date_start = $row['date_start'];
    $this->date_stop = $row['date_stop'];
    return true;
  }
  
  // 
  
  /**
   * Charge un code de promotion depuis son code
   *    
   * Si le code de promotion n'existe pas, une exception est levée.
   *    
   * @param string $code Code du code de promotion à charger
   * @param boolean $now Si true, ne prend que les codes de promotion actifs actuellement. Si False, on les prend tous.
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load_from_code($code, $now = false){
    $sql = "SELECT * FROM miki_shop_code_promo WHERE code = '%s'";
    
    // si on ne prend que les codes de promotion actifs actuellement
    if ($now)
      $sql .= " AND  date_start <= NOW() AND  date_stop >= NOW()";
    
    $sql = sprintf($sql,
      mysql_real_escape_string($code));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0){
      throw new Exception(_("Aucune code de promotion ne correspond au code donné"));
    }
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->code = $row['code'];
    $this->type = $row['type'];
    $this->discount = $row['discount'];
    $this->date_start = $row['date_start'];
    $this->date_stop = $row['date_stop'];
    return true;
  }
  
  /**
   * Sauvegarde le code dans la base de données.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    // si l'id du code de promotion existe, c'est que le code de promotion existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    $sql = sprintf("INSERT INTO miki_shop_code_promo (code, type, discount, date_start, date_stop) VALUES('%s', %d, '%F', '%s', '%s')",
      mysql_real_escape_string($this->code),
      mysql_real_escape_string($this->type),
      mysql_real_escape_string($this->discount),
      mysql_real_escape_string($this->date_start),
      mysql_real_escape_string($this->date_stop));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion du code de promotion dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la promotion
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour le code dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    // si aucun id existe, c'est que le code de promotion n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
    
    $sql = sprintf("UPDATE miki_shop_code_promo SET code = '%s', type = %d, discount = '%F', date_start = '%s', date_stop = '%s' WHERE id = %d",
      mysql_real_escape_string($this->code),
      mysql_real_escape_string($this->type),
      mysql_real_escape_string($this->discount),
      mysql_real_escape_string($this->date_start),
      mysql_real_escape_string($this->date_stop),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du code de promotion dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime le code
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_shop_code_promo WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du code de promotion : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Recherche le rabais octroyé par le code de promotion
   * 
   * Si le code est défini en pourcent, on calcul le rabais.
   * 
   * @param float $subtotal Montant sur lequel le rabais sera octroyé.
   * 
   * @return float                 
   */   
  public function get_discount($subtotal){
    if($this->type == 1){
      return $this->discount;
    }elseif($this->type == 2){
      return $subtotal * $this->discount / 100;
    }
  }

  // fonction statique récupérant tous les codes de promotion
  //
  // $now : si "true", 
  
  /**
   * Recherche tous les codes de promotion
   * 
   * @param boolean $now Si true, ne prend que les code de promotions actifs actuellement. Si False, on les prend tous.
   *             
   * @static
   * @return mixed Un tableau d'éléments Miki_shop_code_promo représentant les codes de promotion trouvés
   */
  public static function get_all_codes_promo($now = false){
    $return = array();
    $sql = "SELECT id FROM miki_shop_code_promo";
    
    if ($now){
      // si on ne prend que les promotions actives actuellement
      $sql .= " WHERE date_start <= NOW() AND date_stop >= NOW()";
    }
      
    $sql .= " ORDER by date_start DESC";      
    
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_shop_code_promo($row['id']);
    }
    return $return;
  }
}
?>