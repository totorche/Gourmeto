<?php
/**
 * Classe Miki_menudujour
 * @package Miki
 */ 

/**
 * Représentation d'un menu du jour
 * 
 * Un menu du jour représent le menu du jour d'un restaurant. 
 * Un menu du jour est composé de différents plats pouvant être de 3 types différents : 1 : hors d'oeuvre, 2 : entrée, 3 : plat principal, 4 : dessert.
 * Un menu du jour peut également avoir plusieurs prix selon les plats choisis. Un prix possède donc une descrition.
 * 
 * @package Miki  
 */ 
class miki_menudujour{

  /**
   * Id du menu du jour
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Id de la société (restaurant) lié au menu du jour
   *      
   * @var int
   * @access public   
   */
  public $id_company = 'NULL';
  
  /**
   * Nom du menu du jour
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Date pour laquelle le menu du jour est valable
   * 
   * @var string
   * @access public   
   */
  public $date;
  
  /**
   * Date de la création du menu du jour
   *      
   * @var string
   * @access public   
   */
  public $date_creation;
  
  /**
   * Date de la dernière modification
   *      
   * @var string
   * @access public   
   */
  public $date_modification;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le menu du jour dont l'id a été donné
   * 
   * @param int $id Id du menu du jour à charger (optionnel)
   */
  function __construct($id = ""){
    // charge le menu du jour si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un menu du jour depuis un id
   *    
   * Si le menu du jour n'existe pas, une exception est levée.
   *    
   * @param int $id Id du menu du jour à charger
   * @return boolean TRUE si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_menudujour WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("le menu du jour demandé n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->id_company = $row['id_company'];
    $this->name = $row['name'];
    $this->date = $row['date'];
    $this->date_creation = $row['date_creation'];
    $this->date_modification = $row['date_modification'];
    return true;
  }
  
  /**
   * Sauvegarde le menu du jour dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    // si un l'id du menu du jour existe, c'est que le menu du jour existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    if (!is_numeric($this->id_company))
      $this->id_company = 'NULL';
      
    $sql = sprintf("INSERT INTO miki_menudujour (id_company, name, date, date_creation, date_modification) VALUES(%s, '%s', '%s', NOW(), NOW())",
      mysql_real_escape_string($this->id_company),
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->date));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion du menu du jour dans la base de données"));
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge le menu du jour
    $this->load($this->id);
    return true;
  }
  
  /**
   * Met à jour le menu du jour dans la base de données.
   * 
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   * 
   * le menu du jour doit posséder un nom unique (name). Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    // si aucun id existe, c'est que le menu du jour n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    if (!is_numeric($this->id_company))
      $this->id_company = 'NULL';
      
    $sql = sprintf("UPDATE miki_menudujour SET id_company = %s, name = '%s', date = '%s', date_modification = NOW() WHERE id = %d",
      mysql_real_escape_string($this->id_company),
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->date),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour du menu du jour dans la base de données"));
    return true;
  }
  
  /**
   * Supprime le menu du jour 
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_menudujour WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du menu du jour : "));
    return true;
  }
  
  /**
   * Ajoute un plat au menu du jour
   * 
   * @param string $name Le nom du plat
   * @param int $type Le type de plat (1 : hors d'oeuvre, 2 : entrée, 3 : plat principal, 4 : dessert)
   * 
   * @return boolean TRUE si le plat a été ajouté, FALSE sinon        
   */
  public function add_plat($name, $type){
    $sql = sprintf("INSERT INTO miki_menudujour_plat (id_menudujour, name, type) VALUES (%d, '%s', %d)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($name),
      mysql_real_escape_string($type));
    
    $result = mysql_query($sql);
    if (!$result)
      return false;
    
    return true;
  }
  
  /**
   * Récupert les plats du menu du jour
   * 
   * @return mixed Un tableau d'éléments (qui sont également des tableaux) représentant les plats dont les indices sont : "id", "name" (le nom du plat) et "type" (le type de plat - 1 : hors d'oeuvre, 2 : entrée, 3 : plat principal, 4 : dessert)       
   */
  public function get_plats(){
    $return = array();
    
    $sql = sprintf("SELECT id, name, type FROM miki_menudujour_plat WHERE id_menudujour = %d",
      mysql_real_escape_string($this->id));

    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $return[] = $row;
    }
    
    return $return;
  }
  
  /**
   * Récupert le nombre de prix du menu du jour
   * 
   * @return int       
   */
  public function get_nb_plats(){
    $sql = sprintf("SELECT COUNT(*) FROM miki_menudujour_plat WHERE id_menudujour = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Supprime tous les plats du menu du jour
   * 
   * @return boolean      
   */
  public function remove_all_plats(){
    $sql = sprintf("DELETE FROM miki_menudujour_plat WHERE id_menudujour = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression des plats du menu du jour : "));
    return true;
  }     
  
  /**
   * Ajoute un prix au menu du jour
   * 
   * @param string $description La description du prix
   * @param float $price Le prix
   * 
   * @return boolean TRUE si le prix a été ajouté, FALSE sinon        
   */
  public function add_price($description, $price){
    $sql = sprintf("INSERT INTO miki_menudujour_prix (id_menudujour, description, price) VALUES (%d, '%s', '%F')",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($description),
      mysql_real_escape_string($price));

    $result = mysql_query($sql);
    if (!$result)
      return false;
    
    return true;
  }
  
  /**
   * Récupert les prix du menu du jour
   * 
   * @return mixed Un tableau d'éléments (qui sont également des tableaux) représentant les prix dont les indices sont : "id", "description" et "price"       
   */
  public function get_prices(){
    $return = array();
    
    $sql = sprintf("SELECT id, description, price FROM miki_menudujour_prix WHERE id_menudujour = %d",
      mysql_real_escape_string($this->id));

    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $return[] = $row;
    }
    
    return $return;
  }
  
  /**
   * Récupert le nombre de prix du menu du jour
   * 
   * @return int       
   */
  public function get_nb_prices(){
    $sql = sprintf("SELECT COUNT(*) FROM miki_menudujour_prix WHERE id_menudujour = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Supprime tous les prix du menu du jour
   * 
   * @return boolean      
   */
  public function remove_all_prices(){
    $sql = sprintf("DELETE FROM miki_menudujour_prix WHERE id_menudujour = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression des prix du menu du jour : "));
    return true;
  }
  
  /**
   * Récupére tous les menus du jour
   * 
   * @param int $id_company si renseigné, ne retourne que les menus du jour de la société (restaurant) dont l'id est donné
   * @param string $order Par quel champ les menus du jour trouvés seront triés (id, name, company, date). Si vide, on tri selon la date.
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)   
   * @param int $nb nombre de menus du jour à retourner par page. Si = "" on retourne tous les menus du jour.
   * @param int $page numéro de la page à retourner   
   * @param int $total Cette variable sera modifiée avec le nombre total d’enregistrements qui seraient retournés par la requête sans la clause LIMIT
   *      
   * @static
   *    
   * @return mixed Un tableau d'éléments de type Miki_menudujour
   */
  public static function get_all_menus($order = "", $order_type = "asc", $nb = "", $page = 1, &$total){
    $return = array();
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM miki_menudujour WHERE 1";
    
    // ordonne les résultats
    if ($order == "id")
      $sql .= " ORDER BY id " .$order_type;
    elseif ($order == "name")
      $sql .= " ORDER BY LCASE(name) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "company")
      $sql .= " ORDER BY id_company " .$order_type;
    elseif ($order == "date")
      $sql .= " ORDER BY date " .$order_type;
    else
      $sql .= " ORDER BY date " .$order_type;
      
    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }
    
    $result = mysql_query($sql);
    
    // calcul le nombre de résultats total, sans la clause LIMIT
    $result2 = mysql_query("SELECT FOUND_ROWS()");
    $row2 = mysql_fetch_array($result2);
    $total = $row2[0];
    
    while($row = mysql_fetch_array($result)){
      $return[] = new miki_menudujour($row['id']);
    }
    
    return $return;
  }
  
  /**
   * Récupére tous les menus du jour
   * 
   * @param string $search Critère de recherche qui sera appliqué sur le nom du menu du jour
   * @param int $id_company si renseigné, ne retourne que les menus du jour de la société (restaurant) dont l'id est donné
   * @param string $order Par quel champ les menus du jour trouvés seront triés (id, name, company, date). Si vide, on tri selon la date.
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)   
   * @param int $nb nombre de menus du jour à retourner par page. Si = "" on retourne tous les menus du jour.
   * @param int $page numéro de la page à retourner   
   * @param int $total Cette variable sera modifiée avec le nombre total d’enregistrements qui seraient retournés par la requête sans la clause LIMIT
   *      
   * @static
   *    
   * @return mixed Un tableau d'éléments de type Miki_menudujour
   */
  public static function search($search = "", $id_company = "", $order = "", $order_type = "asc", $nb = "", $page = 1, &$total){
    $return = array();
    
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM miki_menudujour WHERE 1";
    
    // Applique les critères de recherche
    if ($search != ""){
      $search = mb_strtolower($search, 'UTF-8');
      $sql .= sprintf(" AND (LOWER(name) LIKE '%%%s%%' OR
                            date LIKE '%%%s%%')",
                mysql_real_escape_string($search),
                mysql_real_escape_string($search));
    }
    
    // si l'id de la société (restaurant) est donné, on ne recherche que les menus du restaurant donné
    if (is_numeric($id_company)){
      $sql .= sprintf(" AND id_company = %s", mysql_real_escape_string($id_company));
    }
    
    // ordonne les résultats
    if ($order == "id")
      $sql .= " ORDER BY id " .$order_type;
    elseif ($order == "name")
      $sql .= " ORDER BY LCASE(name) COLLATE utf8_general_ci " .$order_type;
    elseif ($order == "company")
      $sql .= " ORDER BY id_company " .$order_type;
    elseif ($order == "date")
      $sql .= " ORDER BY date " .$order_type;
    else
      $sql .= " ORDER BY date " .$order_type;
      
    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }
    
    $result = mysql_query($sql);
    
    // calcul le nombre de résultats total, sans la clause LIMIT
    $result2 = mysql_query("SELECT FOUND_ROWS()");
    $row2 = mysql_fetch_array($result2);
    $total = $row2[0];
    
    while($row = mysql_fetch_array($result)){
      $return[] = new miki_menudujour($row['id']);
    }
    
    return $return;
  }
}
?>