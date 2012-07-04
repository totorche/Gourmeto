<?php
/**
 * Classe Miki_alert_result
 * @package Miki
 */ 

/**
 * Représentation d'un résultat d'une alerte (Miki_alert)
 *
 * Une alerte permet d'être averti automatiquement lors de l'insertion de nouveaux éléments 
 * contenant ou ne contenant pas (selon les choix de l'utilisateur) certains mots-clés. 
 * 
 * Cette classe représente un élément qui est ressorti d'une alerte.  
 *  
 * @see Miki_alert 
 * @package Miki  
 */
class Miki_alert_result{

  /**
   * Id du résultat de l'alerte
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Id de l'alerte ayant fait ressortir le résultat
   *      
   * @var int
   * @access public   
   */
  public $id_alert;
  
  /**
   * Type d'élément
   * 
   * Les éléments recherchés peuvent être de différents type : 
   * 
   * 1 = Recherche dans les événements (Miki_event)
   * 2 = Recherche dans les groupes de discussions (Miki_social_group)
   * 3 = Recherche dans les articles des groupes de discussion (Miki_social_group_article)   
   * 4 = Recherche dans les annonces (Miki_ad)
   * 
   * Des éléments pourront être ajoutés par la suite.            
   *      
   * @var int
   * @access public   
   */
  public $element_type;
  
  /**
   * Id de l'élément
   *      
   * @var int
   * @access public   
   */
  public $element_id;
  
  /**
   * L'élément doit-il être ignoré dans les futures résultats ou pas. 
   * 
   * Si true, l'élément sera ignoré lors des prochaine recherche. Si false, il sera pris en compte.
   * 
   * Cela permet notamment de ne prendre en compte qu'une seule fois chaque résultat.            
   *      
   * @var boolean
   * @access public   
   */
  public $ignored;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le résultat dont l'id a été donné
   * 
   * @param int $id Id de l'alerte à charger (optionnel)
   */
  function __construct($id = ""){
    
    // charge l'alerte si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un résultat depuis un id
   *    
   * Si le résultat n'existe pas, une exception est levée.
   *    
   * @param int $id id de le résultat à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_alert_result WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le résultat demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id            = $row['id'];
    $this->id_alert      = $row['id_alert'];
    $this->element_type  = $row['element_type'];
    $this->element_id    = $row['element_id'];
    $this->ignored        = ($row['ignored'] == 0) ? false : true;

    return true;
  }
  
  /**
   * Charge un résultat depuis les paramètres id_alert, element_type et element_id.
   * 
   * Il ne peut pas y avoir 2 résultats possédant les mêmes paramètres id_alert, element_type et element_id.   
   *    
   * Si le résultat n'existe pas, une exception est levée.
   *    
   * @param int $id_alert id de l'alerte
   * @param int $element_type type de l'élément
   * @param int $element_id id de l'élément
   *          
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load_from_params($id_alert, $element_type, $element_id){
    $sql = sprintf("SELECT * FROM miki_alert_result WHERE id_alert = %d AND element_type = %d AND element_id = %d",
      mysql_real_escape_string($id_alert),
      mysql_real_escape_string($element_type),
      mysql_real_escape_string($element_id));
    $result = mysql_query($sql) or die("erreur : <br /><br />$sql");
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Le résultat demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id            = $row['id'];
    $this->id_alert      = $row['id_alert'];
    $this->element_type  = $row['element_type'];
    $this->element_id    = $row['element_id'];
    $this->ignored        = ($row['ignored'] == 0) ? false : true;

    return true;
  }
  
  /**
   * Sauvegarde le résultat dans la base de données.
   *    
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   * 
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */
  public function save(){
    // si un l'id du résultat existe, c'est qu'il existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
    
    if ($this->ignored)
      $ignored = 1;
    else
      $ignored = 0;
      
    $sql = sprintf("INSERT INTO miki_alert_result
                    (id_alert, element_type, element_id, ignored) 
                    VALUES(%d, %d, %d, %d)",
      mysql_real_escape_string($this->id_alert),
      mysql_real_escape_string($this->element_type),
      mysql_real_escape_string($this->element_id),
      mysql_real_escape_string($ignored));

    $result = mysql_query($sql);
    if (!$result){
      throw new Exception(_("Erreur lors de l'insertion du résultat dans la base de données : <br />$sql<br />") .mysql_error());
    }
    // récupert l'id
    $this->id = mysql_insert_id();
    
    // recharge l'alerte
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour le résultat dans la base de données.
   * 
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si une erreur survient, une exception est levée   
   *    
   * @return boolean   
   */
  public function update(){
    // si aucun id existe, c'est que le résultat n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    if ($this->ignored)
      $ignored = 1;
    else
      $ignored = 0;
    
    $sql = sprintf("UPDATE miki_alert_result SET 
                    id_alert = %d, element_type = %d, element_id = %d, ignored = %d
                    WHERE id = %d",
      mysql_real_escape_string($this->id_alert),
      mysql_real_escape_string($this->element_type),
      mysql_real_escape_string($this->element_id),
      mysql_real_escape_string($ignored),
      mysql_real_escape_string($this->id));
    
    $result = mysql_query($sql);
    if (!$result){
      throw new Exception(_("Erreur lors de la mise à jour du résultat dans la base de données : ") .mysql_error());
    }
    
    return true;
  }
  
  /**
   * Supprime le résultat
   * 
   * @return boolean
   */         
  public function delete(){
    $sql = sprintf("DELETE FROM miki_alert_result WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du résultat : ") ."<br />" .mysql_error());
      
    return true;
  }
}

?>