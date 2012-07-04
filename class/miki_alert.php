<?php
/**
 * Classe Miki_alert
 * @package Miki
 */ 

/**
 * Représentation d'une alerte permettant d'être averti lors de l'insertion de nouveaux éléments répondant à certains mots-clés.
 * 
 * Une alerte permet d'être averti automatiquement lors de l'insertion de nouveaux éléments 
 * contenant ou ne contenant pas (selon les choix de l'utilisateur) certains mots-clés. 
 *  
 * @package Miki 
 */  
class Miki_alert{
  
  /**
   * Id de l'alerte
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Id de la personne (Miki_person) ayant créé l'alerte
   *      
   * @var int
   * @see Miki_person   
   * @access public   
   */
  public $id_person;
  
  /**
   * Titre de l'alerte
   *      
   * @var string
   * @access public   
   */
  public $title;
  
  /**
   * Type d'éléments dans lesquels on doit rechercher les termes de l'alerte.
   * 
   * 1 = Recherche dans les événements (Miki_event)
   * 2 = Recherche dans les groupes de discussions (Miki_social_group et Miki_social_group_article)
   * 3 = Recherche dans les articles des groupes de discussion (Miki_social_group_article)   
   * 4 = Recherche dans les annonces (Miki_ad)
   * 
   * Des éléments pourront être ajoutés par la suite.
   * 
   * Plusieurs type d'éléments peuvent être recherchés dans la même alerte en affectant un tableau dont chaque valeur représente un type d'élément à rechercher.                 
   * 
   * Par défaut, tous les types d'éléments sont recherchés.    
   *   
   * var mixed Un tableau d'élément Int
   * @access public          
   */           
  public $elements_type = array(1, 2, 3, 4);
  
  /**
   * Paramètre de recherche : Tous les mots présents dans ce paramètre doivent être trouvés pour remonter l'élément
   *      
   * @var string
   * @access public   
   */
  public $all_words;
  
  /**
   * Paramètre de recherche : Au moins un mot présent dans ce paramètre doit être trouvé pour remonter l'élément
   *      
   * @var string
   * @access public   
   */
  public $one_word;
  
  /**
   * Paramètre de recherche : La phrase exacte contenue dans ce paramètre doit être trouvée pour remonter l'élément
   *      
   * @var string
   * @access public   
   */
  public $sentence;
  
  /**
   * Paramètre de recherche : Aucun des mots contenus dans ce paramètre ne doivent être trouvés pour remonter l'élément
   *      
   * @var string
   * @access public   
   */
  public $no_words;
  
  /**
   * Date à laquelle l'alerte a été créée
   *      
   * @var string
   * @access public   
   */
  public $date_creation;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge l'alerte dont l'id a été donné
   * 
   * @param int $id Id de l'alerte à charger (optionnel)
   */
  function __construct($id = ""){
    // charge l'alerte si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge une alerte depuis un id
   *    
   * Si l'alerte n'existe pas, une exception est levée.
   *    
   * @param int $id id de l'alerte à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_alert WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("L'alerte demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id             = $row['id'];
    $this->id_person      = $row['id_person'];
    $this->title          = $row['title'];
    $this->all_words      = $row['all_words'];
    $this->one_word       = $row['one_word'];
    $this->sentence       = $row['sentence'];
    $this->no_words       = $row['no_words'];
    $this->date_creation  = $row['date_creation'];
    
    // met les types d'éléments sous forme de tableau. 
    $elements_type = $row['elements_type'];
    $elements_type = explode("&&", $elements_type);
    $elements_type = array_unique($elements_type);
    $this->elements_type = array();
    foreach($elements_type as $el){
      $this->elements_type[] = $el;
    }
         
    return true;
  }
  
  /**
   * Sauvegarde l'alerte dans la base de données.
   *    
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   * 
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */
  public function save(){
    // si un l'id de l'alerte existe, c'est qu'elle existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
    
    // met les types d'éléments sous forme de String. Chaque élément est séparé par "&&" 
    if (is_array($this->elements_type)){
      $elements_type = implode("&&", $this->elements_type);
    }
    else{
      $elements_type = $this->elements_type;
    }
      
    $sql = sprintf("INSERT INTO miki_alert 
                    (id_person, title, elements_type, all_words, one_word, sentence, no_words, date_creation) 
                    VALUES(%d, '%s', '%s', '%s', '%s', '%s', '%s', NOW())",
      mysql_real_escape_string($this->id_person),
      mysql_real_escape_string($this->title),
      mysql_real_escape_string($elements_type),
      mysql_real_escape_string($this->all_words),
      mysql_real_escape_string($this->one_word),
      mysql_real_escape_string($this->sentence),
      mysql_real_escape_string($this->no_words));

    $result = mysql_query($sql);
    if (!$result){
      throw new Exception(_("Erreur lors de l'insertion de l'alerte dans la base de données : ") .mysql_error());
    }
    // récupert l'id
    $this->id = mysql_insert_id();
    
    // recharge l'alerte
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour l'alerte dans la base de données.
   * 
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si une erreur survient, une exception est levée   
   *    
   * @return boolean   
   */
  public function update(){
    // si aucun id existe, c'est que l'alerte n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // met les types d'éléments sous forme de String. Chaque élément est séparé par "&&" 
    if (is_array($this->elements_type)){
      $elements_type = implode("&&", $this->elements_type);
    }
    else{
      $elements_type = $this->elements_type;
    }
    
    $sql = sprintf("UPDATE miki_alert SET 
                    id_person = %d, title = '%s', elements_type = '%s', all_words = '%s', one_word = '%s', sentence = '%s', no_words = '%s'
                    WHERE id = %d",
      mysql_real_escape_string($this->id_person),
      mysql_real_escape_string($this->title),
      mysql_real_escape_string($elements_type),
      mysql_real_escape_string($this->all_words),
      mysql_real_escape_string($this->one_word),
      mysql_real_escape_string($this->sentence),
      mysql_real_escape_string($this->no_words),
      mysql_real_escape_string($this->id));
    
    $result = mysql_query($sql);
    if (!$result){
      throw new Exception(_("Erreur lors de la mise à jour de l'alerte dans la base de données : ") .mysql_error());
    }
    
    return true;
  }
  
  /**
   * Supprime l'alerte
   * 
   * @return boolean
   */         
  public function delete(){
    $sql = sprintf("DELETE FROM miki_alert WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de l'alerte : ") ."<br />" .mysql_error());
      
    return true;
  }
  
  /**
   * Recherche tous les événements (Miki_event) correspondant aux critères
   *
   * @param boolean $all Si true prend en compte tous les résultats, si false prend uniquement en compte les résultats dont le paramètre "ignore" est à false (voir Miki_alert_result)
   *
   * @see Miki_event
   * @see Miki_alert_result   
   * @return mixed Un tableau d'éléments Miki_alert_result représentant les résultats trouvés
   */     
  private function get_events($all = true){
    $sql = "SELECT id FROM miki_event WHERE ";
    
    $condition = "";
    
    // recherche tous les termes
    if ($this->all_words != ""){
      $condition .= "(";
      // récupert tous les termes recherchés
      $tab = explode(" ", $this->all_words);
      // recherche chacun de ces termes
      foreach($tab as $t){
        $condition .= "(title LIKE '%$t%' || description LIKE '%$t%' || tags LIKE '%$t%') AND ";
      }
      // enlève le dernier " AND " si besoin
      if (sizeof($tab) > 0)
        $condition = substr($condition, 0, strlen($condition) - 5) .')';
    }
    
    // recherche un des termes
    if ($this->one_word != ""){
      if ($condition !== "")
        $condition .= " AND ";
        
      $condition .= "(";
      // récupert tous les termes recherchés
      $tab = explode(" ", $this->one_word);
      // recherche chacun de ces termes
      foreach($tab as $t){
        $condition .= "(title LIKE '%$t%' || description LIKE '%$t%' || tags LIKE '%$t%') OR ";
      }
      // enlève le dernier " OR " si besoin
      if (sizeof($tab) > 0)
        $condition = substr($condition, 0, strlen($condition) - 4) .')';
    }
    
    // recherche la phrase exacte
    if ($this->sentence != ""){
      if ($condition !== "")
        $condition .= " AND ";
        
      $condition .= "(title LIKE '%$this->sentence%' || description LIKE '%$this->sentence%' || tags LIKE '%$this->sentence%')";
    }
    
    // recherche aucun de ces mots
    if ($this->no_words != ""){
      
      if ($condition !== "")
        $condition .= " AND ";
      
      $condition .= "(";
      // récupert tous les termes recherchés
      $tab = explode(" ", $this->no_words);
      // recherche chacun de ces termes
      foreach($tab as $t){
        $condition .= "title NOT LIKE '%$t%' AND description NOT LIKE '%$t%' AND tags NOT LIKE '%$t%'";
      }
      // enlève le dernier " AND " si besoin
      if (sizeof($tab) > 0)
        $condition = substr($condition, 0, strlen($condition) - 5) .')';
    }
    
    // s'il y a des conditions, on les ajoute à la requête
    if ($condition !== "")
      $sql .= $condition;

    $result = mysql_query($sql) or die("sql : <br><br>$sql<br><br>");
    
    $return = array();
    
    while($row = mysql_fetch_array($result)){
      $alert_result = new Miki_alert_result();
      try{
        // si le résultat existe déjà on le récupert
        $alert_result->load_from_params($this->id, 1, $row['id']);
        
        // s'il doit être pris en compte, on l'ajoute aux résultats trouvés
        if ($all || !$alert_result->ignored){
          $return[] = $alert_result;
        }
      }
      catch(Exception $e){
        // s'il n'existe pas, on le créé
        $alert_result->id_alert = $this->id;
        $alert_result->element_type = 1;
        $alert_result->element_id = $row['id'];
        // on le sauve
        $alert_result->save();
        // puis on l'ajoute aux résultats trouvés
        $return[] = $alert_result;
      }
    }
    
    return $return;
  }
  
  /**
   * Recherche tous les groupes de discussion (Miki_social_group) correspondant aux critères
   *
   * @param boolean $all Si true prend en compte tous les résultats, si false prend uniquement en compte les résultats dont le paramètre "ignore" est à false (voir Miki_alert_result)
   *
   * @see Miki_social_group
   * @see Miki_alert_result   
   * @return mixed Un tableau d'éléments Miki_alert_result représentant les résultats trouvés
   */     
  private function get_social_groups($all = true){
    $sql = "SELECT id FROM miki_social_group WHERE ";
    
    $condition = "";
    
    // recherche tous les termes
    if ($this->all_words != ""){
      $condition .= "(";
      // récupert tous les termes recherchés
      $tab = explode(" ", $this->all_words);
      // recherche chacun de ces termes
      foreach($tab as $t){
        $condition .= "(name LIKE '%$t%' || description LIKE '%$t%') AND ";
      }
      // enlève le dernier " AND " si besoin
      if (sizeof($tab) > 0)
        $condition = substr($condition, 0, strlen($condition) - 5) .')';
    }
    
    // recherche un des termes
    if ($this->one_word != ""){
      if ($condition !== "")
        $condition .= " AND ";
        
      $condition .= "(";
      // récupert tous les termes recherchés
      $tab = explode(" ", $this->one_word);
      // recherche chacun de ces termes
      foreach($tab as $t){
        $condition .= "(name LIKE '%$t%' || description LIKE '%$t%') OR ";
      }
      // enlève le dernier " OR " si besoin
      if (sizeof($tab) > 0)
        $condition = substr($condition, 0, strlen($condition) - 4) .')';
    }
    
    // recherche la phrase exacte
    if ($this->sentence != ""){
      if ($condition !== "")
        $condition .= " AND ";
        
      $condition .= "(name LIKE '%$this->sentence%' || description LIKE '%$this->sentence%')";
    }
    
    // recherche aucun de ces mots
    if ($this->no_words != ""){
      
      if ($condition !== "")
        $condition .= " AND ";
      
      $condition .= "(";
      // récupert tous les termes recherchés
      $tab = explode(" ", $this->no_words);
      // recherche chacun de ces termes
      foreach($tab as $t){
        $condition .= "name NOT LIKE '%$t%' AND description NOT LIKE '%$t%'";
      }
      // enlève le dernier " AND " si besoin
      if (sizeof($tab) > 0)
        $condition = substr($condition, 0, strlen($condition) - 5) .')';
    }
    
    // s'il y a des conditions, on les ajoute à la requête
    if ($condition !== "")
      $sql .= $condition;

    $result = mysql_query($sql) or die("sql : <br><br>$sql<br><br>");
    
    $return = array();
    
    while($row = mysql_fetch_array($result)){
      $alert_result = new Miki_alert_result();
      try{
        // si le résultat existe déjà on le récupert
        $alert_result->load_from_params($this->id, 2, $row['id']);
        
        // s'il doit être pris en compte, on l'ajoute aux résultats trouvés
        if ($all || !$alert_result->ignored){
          $return[] = $alert_result;
        }
      }
      catch(Exception $e){
        // s'il n'existe pas, on le créé
        $alert_result->id_alert = $this->id;
        $alert_result->element_type = 2;
        $alert_result->element_id = $row['id'];
        // on le sauve
        $alert_result->save();
        // puis on l'ajoute aux résultats trouvés
        $return[] = $alert_result;
      }
    }
    
    return $return;
  }
  
  /**
   * Recherche tous les articles des groupes de discussion (Miki_social_group_article) correspondant aux critères
   *
   * @param boolean $all Si true prend en compte tous les résultats, si false prend uniquement en compte les résultats dont le paramètre "ignore" est à false (voir Miki_alert_result)
   *
   * @see Miki_social_group_article
   * @see Miki_alert_result   
   * @return mixed Un tableau d'éléments Miki_alert_result représentant les résultats trouvés
   */     
  private function get_social_group_articles($all = true){
    $sql = "SELECT id FROM miki_social_group_article WHERE ";
    
    $condition = "";
    
    // recherche tous les termes
    if ($this->all_words != ""){
      $condition .= "(";
      // récupert tous les termes recherchés
      $tab = explode(" ", $this->all_words);
      // recherche chacun de ces termes
      foreach($tab as $t){
        $condition .= "(title LIKE '%$t%' || text LIKE '%$t%') AND ";
      }
      // enlève le dernier " AND " si besoin
      if (sizeof($tab) > 0)
        $condition = substr($condition, 0, strlen($condition) - 5) .')';
    }
    
    // recherche un des termes
    if ($this->one_word != ""){
      if ($condition !== "")
        $condition .= " AND ";
        
      $condition .= "(";
      // récupert tous les termes recherchés
      $tab = explode(" ", $this->one_word);
      // recherche chacun de ces termes
      foreach($tab as $t){
        $condition .= "(title LIKE '%$t%' || text LIKE '%$t%') OR ";
      }
      // enlève le dernier " OR " si besoin
      if (sizeof($tab) > 0)
        $condition = substr($condition, 0, strlen($condition) - 4) .')';
    }
    
    // recherche la phrase exacte
    if ($this->sentence != ""){
      if ($condition !== "")
        $condition .= " AND ";
        
      $condition .= "(title LIKE '%$this->sentence%' || text LIKE '%$this->sentence%')";
    }
    
    // recherche aucun de ces mots
    if ($this->no_words != ""){
      
      if ($condition !== "")
        $condition .= " AND ";
      
      $condition .= "(";
      // récupert tous les termes recherchés
      $tab = explode(" ", $this->no_words);
      // recherche chacun de ces termes
      foreach($tab as $t){
        $condition .= "title NOT LIKE '%$t%' AND text NOT LIKE '%$t%'";
      }
      // enlève le dernier " AND " si besoin
      if (sizeof($tab) > 0)
        $condition = substr($condition, 0, strlen($condition) - 5) .')';
    }
    
    // s'il y a des conditions, on les ajoute à la requête
    if ($condition !== "")
      $sql .= $condition;

    $result = mysql_query($sql) or die("sql : <br><br>$sql<br><br>");
    
    $return = array();
    
    while($row = mysql_fetch_array($result)){
      $alert_result = new Miki_alert_result();
      try{
        // si le résultat existe déjà on le récupert
        $alert_result->load_from_params($this->id, 3, $row['id']);
        
        // s'il doit être pris en compte, on l'ajoute aux résultats trouvés
        if ($all || !$alert_result->ignored){
          $return[] = $alert_result;
        }
      }
      catch(Exception $e){
        // s'il n'existe pas, on le créé
        $alert_result->id_alert = $this->id;
        $alert_result->element_type = 3;
        $alert_result->element_id = $row['id'];
        // on le sauve
        $alert_result->save();
        // puis on l'ajoute aux résultats trouvés
        $return[] = $alert_result;
      }
    }
    
    return $return;
  }
  
  /**
   * Recherche toutes les annonces (Miki_ad) correspondant aux critères
   *
   * @param boolean $all Si true prend en compte tous les résultats, si false prend uniquement en compte les résultats dont le paramètre "ignore" est à false (voir Miki_alert_result)
   *
   * @see Miki_social_group_article
   * @see Miki_alert_result   
   * @return mixed Un tableau d'éléments Miki_alert_result représentant les résultats trouvés
   */     
  private function get_ads($all = true){
    $sql = "SELECT id FROM miki_ad WHERE ";
    
    $condition = "";
    
    // recherche tous les termes
    if ($this->all_words != ""){
      $condition .= "(";
      // récupert tous les termes recherchés
      $tab = explode(" ", $this->all_words);
      // recherche chacun de ces termes
      foreach($tab as $t){
        $condition .= "(title LIKE '%$t%' || short_description LIKE '%$t%' || long_description LIKE '%$t%' || tags LIKE '%$t%') AND ";
      }
      // enlève le dernier " AND " si besoin
      if (sizeof($tab) > 0)
        $condition = substr($condition, 0, strlen($condition) - 5) .')';
    }
    
    // recherche un des termes
    if ($this->one_word != ""){
      if ($condition !== "")
        $condition .= " AND ";
        
      $condition .= "(";
      // récupert tous les termes recherchés
      $tab = explode(" ", $this->one_word);
      // recherche chacun de ces termes
      foreach($tab as $t){
        $condition .= "(title LIKE '%$t%' || short_description LIKE '%$t%' || long_description LIKE '%$t%' || tags LIKE '%$t%') OR ";
      }
      // enlève le dernier " OR " si besoin
      if (sizeof($tab) > 0)
        $condition = substr($condition, 0, strlen($condition) - 4) .')';
    }
    
    // recherche la phrase exacte
    if ($this->sentence != ""){
      if ($condition !== "")
        $condition .= " AND ";
        
      $condition .= "(title LIKE '%$this->sentence%' || short_description LIKE '%$this->sentence%' || long_description LIKE '%$this->sentence%' || tags LIKE '%$this->sentence%')";
    }
    
    // recherche aucun de ces mots
    if ($this->no_words != ""){
      
      if ($condition !== "")
        $condition .= " AND ";
      
      $condition .= "(";
      // récupert tous les termes recherchés
      $tab = explode(" ", $this->no_words);
      // recherche chacun de ces termes
      foreach($tab as $t){
        $condition .= "title NOT LIKE '%$t%' AND short_description NOT LIKE '%$t%' AND long_description NOT LIKE '%$t%' AND tags NOT LIKE '%$t%'";
      }
      // enlève le dernier " AND " si besoin
      if (sizeof($tab) > 0)
        $condition = substr($condition, 0, strlen($condition) - 5) .')';
    }
    
    // s'il y a des conditions, on les ajoute à la requête
    if ($condition !== "")
      $sql .= $condition;

    $result = mysql_query($sql) or die("sql : <br><br>$sql<br><br>");
    
    $return = array();
    
    while($row = mysql_fetch_array($result)){
      $alert_result = new Miki_alert_result();
      try{
        // si le résultat existe déjà on le récupert
        $alert_result->load_from_params($this->id, 4, $row['id']);
        
        // s'il doit être pris en compte, on l'ajoute aux résultats trouvés
        if ($all || !$alert_result->ignored){
          $return[] = $alert_result;
        }
      }
      catch(Exception $e){
        // s'il n'existe pas, on le créé
        $alert_result->id_alert = $this->id;
        $alert_result->element_type = 4;
        $alert_result->element_id = $row['id'];
        // on le sauve
        $alert_result->save();
        // puis on l'ajoute aux résultats trouvés
        $return[] = $alert_result;
      }
    }
    
    return $return;
  }
  
  /**
   * Recherche le nombre de résultats correspondant à l'alerte
   *
   * @param boolean $all Si true prend en compte tous les résultats, si false prend uniquement en compte les résultats qui n'ont pas encore été trouvés
   */
  public function search_nb_results($all = true){
    // si l'alerte n'a pas encore été enregistrée, on l'enregistre
    if (!isset($this->id))
      $this->save();
      
    $results = array();
    
    // récupert les résultats selon les type de résultats à récupérer
    if (in_array(1, $this->elements_type)){
      $results = array_merge($results, $this->get_events($all));
    }
    if (in_array(2, $this->elements_type)){
      $results = array_merge($results, $this->get_social_groups($all));
    }
    if (in_array(3, $this->elements_type)){
      $results = array_merge($results, $this->get_social_group_articles($all));
    }
    if (in_array(4, $this->elements_type)){
      $results = array_merge($results, $this->get_ads($all));
    }
    
    return sizeof($results);
  }
  
  // recherche les articles correspondant à l'alerte
  public function search_results($all = true){
    // si l'alerte n'a pas encore été enregistrée, on l'enregistre
    if (!isset($this->id))
      $this->save();
      
    $results = array();
    
    // récupert les résultats selon les type de résultats à récupérer
    if (in_array(1, $this->elements_type)){
      $results = array_merge($results, $this->get_events($all));
    }
    if (in_array(2, $this->elements_type)){
      $results = array_merge($results, $this->get_social_groups($all));
    }
    if (in_array(3, $this->elements_type)){
      $results = array_merge($results, $this->get_social_group_articles($all));
    }
    if (in_array(4, $this->elements_type)){
      $results = array_merge($results, $this->get_ads($all));
    }
    
    return $results;
  }
  
  /** 
   * Recherche toutes les alertes
   *  
   * @param int $person_id Si non-vide et numérique, récupert uniquement les alertes de la personne donnée. 
   *       
   * @static
   * 
   * @return mixed Un tableau d'élément Miki_alert correspondant à toutes les alertes trouvées
   */              
  public static function get_all_alerts($person_id = ""){
    $return = array();
    $sql = "SELECT ma.id 
            FROM miki_alert ma,
                 miki_person mp
            WHERE mp.id = ma.id_person";
              
    // recherche les alertes faites par la person $person_id 
    if ($person_id !== "" && is_numeric($person_id)){
      $sql .= " AND id_person = %d";
      $sql = sprintf($sql, mysql_real_escape_string($person_id));
    }
    
    $sql .= " ORDER BY date_creation ASC";
    
    $result = mysql_query($sql) or die("Erreur sql : <br /><br />$sql");
    
    while($row = mysql_fetch_array($result)){
      $item = new Miki_alert($row['id']);
      $return[] = $item;
    }
    return $return;
  }
}