<?php
/**
 * Classe Miki_document
 * @package Miki
 */ 

/**
 * Inclut les fonctions d'importation et de traitement des images
 */ 
require_once("functions_pictures.php");

/**
 * Représentation d'un document. Un document contient un fichier.
 * 
 * @package Miki  
 */ 
class Miki_document{

  /**
   * Id du document
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Etat du document (0 = non-publié, 1 = publié)
   * 
   * @var int
   * @access public         
   */     
   public $state;
  
  /**
   * Titre du document. Tableau dont la clé représente le code de la langue
   *      
   * @var mixed
   * @access public   
   */
  public $title;
  
  /**
   * Description du document. Tableau dont la clé représente le code de la langue
   *      
   * @var mixed
   * @access public   
   */
  public $description;
  
  /**
   * Fichier représenté par le document
   *       
   * @var mixed
   * @access public   
   */
  public $file;
  
  /**
   * Catégorie du document
   *       
   * @var string
   * @access public   
   */
  public $category;
  
  /**
   * Date de création du document (format yyyy-mm-dd hh:mm:ss)
   *      
   * @var string
   * @access public   
   */
  public $date_created;
  
  /**
   * Répertoire où sont situées les images du document
   *      
   * @var string
   * @access private   
   */
  private $file_path = "../docs/";
  
  /**
   * Position du document
   *      
   * @var int
   * @access public   
   */
  public $position;
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge le document dont l'id a été donné
   * 
   * @param int $id Id du document à charger (optionnel)
   */
  function __construct($id = ""){
    // charge le document si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge un document depuis un id
   *    
   * Si le document n'existe pas, une exception est levée.
   *    
   * @param int $id id du document à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT md.*, temp.title title, temp.description description, temp.language_code code
                    FROM miki_document md
     
                    LEFT OUTER JOIN (SELECT mdt1.title, mdd1.description, mdt1.language_code, mdt1.id_document
                    FROM miki_document_title mdt1,
                         miki_document_description mdd1
                    WHERE mdt1.id_document = mdd1.id_document
                      AND mdt1.language_code = mdd1.language_code) temp ON temp.id_document = md.id
     
                    WHERE md.id = %d",
      mysql_real_escape_string($id));

    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql : <br /><br />$sql<br /><br />");
    
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("le document demandé n'existe pas"));
    while ($row = mysql_fetch_array($result)){
      $this->id                               = $row['id'];
      $this->state                            = $row['state'];
      $this->title[$row['code']]              = stripslashes($row['title']);
      $this->description[$row['code']]        = stripslashes($row['description']);
      $this->category                         = stripslashes($row['category']);
      $this->date_created                     = $row['date_created'];
      $this->file                             = $row['file'];
      $this->position                         = $row['position'];
    }
    
    return true;
  }
  
  /**
   * Sauvegarde le document dans la base de données.
   *    
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   * 
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */
  public function save(){
    // si l'id du document existe, c'est que le document existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // débute la transaction
    mysql_query("START TRANSACTION");

    $sql = sprintf("INSERT INTO miki_document (state, file, category, date_created, position) VALUES(%d, '%s', %d, NOW(), %d)",
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->file),
      mysql_real_escape_string($this->category),
      mysql_real_escape_string($this->position));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'ajout du document dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    
    // ajoute le titre du document dans chaque langue
    if (is_array($this->title)){
      foreach($this->title as $key => $t){
        $sql = sprintf("INSERT INTO miki_document_title (id_document, language_code, title) VALUES(%d, '%s', '%s')",
          mysql_real_escape_string($this->id),
          mysql_real_escape_string($key),
          mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
        $result = mysql_query($sql);
        
        if (!$result){
          // annule les modifications
          mysql_query("ROLLBACK");

          throw new Exception(_("Erreur lors de l'ajout du document dans la base de données :") ."<br />" .mysql_error());
        }
      }
    }
    
    // ajoute la description du document dans chaque langue
    if (is_array($this->description)){
      foreach($this->description as $key => $t){
        $sql = sprintf("INSERT INTO miki_document_description (id_document, language_code, description) VALUES(%d, '%s', '%s')",
          mysql_real_escape_string($this->id),
          mysql_real_escape_string($key),
          mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
        $result = mysql_query($sql);
        if (!$result){
          // annule les modifications
          mysql_query("ROLLBACK");

          throw new Exception(_("Erreur lors de l'ajout du document dans la base de données :") ."<br />" .mysql_error());
        }
      }
    }
    
    // applique les modifications
    mysql_query("COMMIT");
    
    // recharge le document
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour le document dans la base de données.
   * 
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si une erreur survient, une exception est levée   
   *    
   * @return boolean   
   */  
  public function update(){
    // si aucun id existe, c'est que le document n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // débute la transaction
    mysql_query("START TRANSACTION");
    
    $sql = sprintf("UPDATE miki_document SET state = %d, file = '%s', category = %d, position = %d WHERE id = %d",
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->file),
      mysql_real_escape_string($this->category),
      mysql_real_escape_string($this->position),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour du document dans la base de données : ") ."<br />" .mysql_error());
    }
      
      
    // supprime le titre du document dans chaque langue
    $sql = sprintf("DELETE FROM miki_document_title WHERE id_document = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour du document dans la base de données : ") ."<br />" .mysql_error());
    }
    
    // ajoute le titre du document dans chaque langue
    foreach($this->title as $key => $t){
      $sql = sprintf("INSERT INTO miki_document_title (id_document, language_code, title) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
      $result = mysql_query($sql);
      if (!$result){
        // annule les modifications
        mysql_query("ROLLBACK");
      
        throw new Exception(_("Erreur lors de la mise à jour du document dans la base de données : ") ."<br />" .mysql_error());
      }
    }
    
    
    // supprime la description du document dans chaque langue
    $sql = sprintf("DELETE FROM miki_document_description WHERE id_document = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour du document dans la base de données : ") ."<br />" .mysql_error());
    }
    
    // ajoute la description du document dans chaque langue
    foreach($this->description as $key => $t){
      $sql = sprintf("INSERT INTO miki_document_description (id_document, language_code, description) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
      $result = mysql_query($sql);
      if (!$result){
        // annule les modifications
        mysql_query("ROLLBACK");
      
        throw new Exception(_("Erreur lors de la mise à jour du document dans la base de données : ") ."<br />" .mysql_error());
      }
    }
    
    // applique les modifications
    mysql_query("COMMIT");
    
    return true;
  }
  
  /**
   * Supprime le document
   * 
   * Supprime également le fichier du document.      
   * 
   * Si une erreur survient, une exception est levée       
   * 
   * @return boolean
   */         
  public function delete(){
    // Supprime le fichier du document
    $this->delete_file();
    
    // supprime le document de la base de données
    $sql = sprintf("DELETE FROM miki_document WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression du document : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Change l'état du document. Si il était publié, on le dépublie et inversément.
   * 
   * Si une erreur survient, une exception est levée
   * 
   * @param int $state Si = "", l'état est simplement inversé. Sinon, l'état affecté sera l'état correspondant à $state              
   * 
   * @return boolean
   */         
  public function change_state($state = ""){
    if (is_numeric($state))
      $this->state = $state;
    else
      $this->state = ($this->state + 1) % 2;
      
    // puis met à jour le document
    $this->update();
  }
  
  /**
   * Ajoute un fichier au document
   * 
   * Ajoute un fichier au document. S'il s'agit d'une image, on la redimensionne (maximum 1000x1000px) et créé une miniature (maximum 100x100px)
   * 
   * Si une erreur survient, une exception est levée       
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   * @return boolean
   */
  public function upload_file($fichier){
    
    $system = explode('.',mb_strtolower($fichier['name'], 'UTF-8'));
    // récupert le nom du fichier ainsi que son extension
    $nom_destination = decode(implode("_", array_slice($system, 0, sizeof($system)-1)));
    $ext = $system[sizeof($system)-1];
    $nom_destination = $nom_destination ."-" .uniqid()  ."." .$ext;

    // teste s'il y a eu une erreur
  	if ($fichier['error']) {
  		switch ($fichier['error']){
  			case 1: // UPLOAD_ERR_INI_SIZE
  				throw new Exception(_("Le fichier dépasse la limite autorisée (10Mo)"));
  				break;
  			case 2: // UPLOAD_ERR_FORM_SIZE
  				throw new Exception(_("Le fichier dépasse la limite autorisée (10Mo)"));
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

  	if (!is_uploaded_file($fichier['tmp_name']) || $fichier['size'] > 10 * 1024 * 1024)
  		throw new Exception(_("Veuillez uploader des images plus petites que 10Mb"));

  	// pas d'erreur --> upload
  	if ((isset($fichier['tmp_name']))&&($fichier['error'] == UPLOAD_ERR_OK)) {
  		if (!move_uploaded_file($fichier['tmp_name'], $this->file_path .$nom_destination))
        exit();
  	}
  	
  	// s'il s'agisse d'une image, on créé une vignette
    if (preg_match('/jpg|jpeg/i',$system[sizeof($system)-1]) ||
        preg_match('/png/i',$system[sizeof($system)-1]) ||
        preg_match('/gif/i',$system[sizeof($system)-1])){
      
      // redimensionne l'image
  	  createthumb($this->file_path .$nom_destination, $this->file_path ."thumb/" .$nom_destination, 1000, 1000, false);
  	
      // puis créé une vignette
  	  createthumb($this->file_path ."thumb/" .$nom_destination, $this->file_path ."thumb2/" .$nom_destination, 100, 100, false);
    }
  	
  	// puis ajoute le fichier
  	$this->file = $nom_destination;
  	return true;
  }
  
  /**
   * Supprime le fichier du document
   * 
   * Si une erreur survient, une exception est levée            
   * 
   * @return boolean            
   */   
  public function delete_file(){
    if ($this->file != ""){
      $path = $this->file_path .$this->file;
      if (file_exists($path)){
        if (!unlink($path)){
          throw new Exception("Erreur pendant la suppression de la photo");
        }
      }
      
      $path_thumb = $this->file_path ."thumb/" .$this->file;
      if (file_exists($path_thumb)){
        if (!unlink($path_thumb)){
          throw new Exception("Erreur pendant la suppression de la photo");
        }
      }
          
      $this->file = "";
      $this->update();
    }
    return true;
  }
  
  /**
   * Vérifie si le document est une image
   * 
   * @return boolean      
   */     
  public function is_picture(){
    if ($this->file == ""){
      return false;
    }
    
    $system = explode('.',mb_strtolower($this->file, 'UTF-8'));
    if (preg_match('/jpg|jpeg/i',$system[sizeof($system)-1]) ||
        preg_match('/png/i',$system[sizeof($system)-1]) ||
        preg_match('/gif/i',$system[sizeof($system)-1])){
      
      return true;   
    }
    else{
      return false;
    }
  }
  
  /**
   * Vérifie si le document est un PDF
   * 
   * @return boolean      
   */     
  public function is_pdf(){
    if ($this->file == ""){
      return false;
    }
    
    $system = explode('.',mb_strtolower($this->file, 'UTF-8'));
    if (preg_match('/pdf/i',$system[sizeof($system)-1])){
      return true;   
    }
    else{
      return false;
    }
  }
  
  /**
   * Vérifie si le document est un fichier Word
   * 
   * @return boolean      
   */     
  public function is_word(){
    if ($this->file == ""){
      return false;
    }
    
    $system = explode('.',mb_strtolower($this->file, 'UTF-8'));
    if (preg_match('/doc/i',$system[sizeof($system)-1]) ||
        preg_match('/docx/i',$system[sizeof($system)-1])){
      return true;   
    }
    else{
      return false;
    }
  }
  
  /**
   * Vérifie si le document est un fichier Excel
   * 
   * @return boolean      
   */     
  public function is_excel(){
    if ($this->file == ""){
      return false;
    }
    
    $system = explode('.',mb_strtolower($this->file, 'UTF-8'));
    if (preg_match('/xls/i',$system[sizeof($system)-1]) ||
        preg_match('/xlsx/i',$system[sizeof($system)-1])){
      return true;   
    }
    else{
      return false;
    }
  }
  
  /**
   * Recherche le nombre de documents total
   * 
   * @static   
   * @return int      
   */     
  public static function get_nb_documents(){
    $sql = "SELECT COUNT(*) FROM miki_document";
    
    $result = mysql_query($sql);
    
    $row = mysql_fetch_array($result);
    return $row[0];
  }

  /**
   * Recherche tous les documents selon certains critères
   * 
   * @param string $search Critère de recherche qui sera appliqué sur le nom (dans toutes les langues)
   * @param int $category Recherche seulement les documents de la catégorie donnée      
   * @param boolean $all si true, on prend tous les documents, si false on ne prend que les documents publiés (state = 1)
   * @param string $order Par quel champ les documents trouvés seront triés (category, date_created, state). Si vide, on tri selon la date de création (date_created).
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * @param int $nb nombre de documents à retourner par page. Si = "" on retourne tous les documents
   * @param int $page numéro de la page à retourner     
   * 
   * @static
   * @return mixed un tableau d'élément miki_document représentant tous les documents trouvés   
   */             
  public static function search($search = "", $category = "", $all = true, $order = "", $order_type = "asc", $nb = "", $page = 1){
    $return = array();
    $sql = "SELECT DISTINCT(md.id) 
            FROM miki_document md, miki_document_title mdt
            WHERE mdt.id_document = md.id";
    
    // Applique les critères de recherche
    if ($search !== ""){
      $search = mb_strtolower($search, 'UTF-8');
      $sql .= sprintf(" AND LOWER(mdt.title) LIKE '%%%s%%'",
                mysql_real_escape_string($search));
    }
    
    // Recherche selon la catégorie
    if ($category !== ""){
      $category = mb_strtolower($category, 'UTF-8');
      $sql .= sprintf(" AND md.category = %s",
                mysql_real_escape_string($category));
    }
    
    // ne prend que les documents dans l'état 'publiés'
    if (!$all){
      $sql .= " AND state = 1";
    }
    
    if ($order == "category")
      $sql .= " ORDER BY md.category " .$order_type;
    elseif ($order == "date_created")
      $sql .= " ORDER BY md.date_created " .$order_type;
    elseif ($order == "state")
      $sql .= " ORDER BY md.state " .$order_type;
    elseif ($order == "position")
      $sql .= " ORDER BY md.position " .$order_type;
    else
      $sql .= " ORDER BY md.position " .$order_type;

    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }

    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $return[] = new miki_document($row['id']);
    }
    return $return;
  }
  
  /**
   * Recherche tous les documents
   * 
   * @param boolean $all si true, on prend tous les documents, si false on ne prend que les documents publiés (state = 1)
   * @param string $order Par quel champ les documents trouvés seront triés (category, date_created, state). Si vide, on tri selon la date de création (date_created).
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * @param int $nb Nombre de documents à retourner par page. Si = "" on retourne tous les documents
   * @param int $page Numéro de la page à retourner     
   * 
   * @static
   * @return mixed un tableau d'élément miki_document représentant tous les documents trouvés   
   */             
  public static function get_all_documents($all = true, $order = "", $order_type = "asc", $nb = "", $page = 1){
    $return = array();
    $sql = "SELECT * FROM miki_document";
    
    // ne prend que les documents dans l'état 'publiés'
    if (!$all){
      $sql .= " WHERE state = 1";
    }
    
    elseif ($order == "category")
      $sql .= " ORDER BY category " .$order_type;
    elseif ($order == "date_created")
      $sql .= " ORDER BY date_created " .$order_type;
    elseif ($order == "state")
      $sql .= " ORDER BY state " .$order_type;
    elseif ($order == "position")
      $sql .= " ORDER BY position " .$order_type;
    else
      $sql .= " ORDER BY position " .$order_type;
      
    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }
    
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = new miki_document($row['id']);
    }
    return $return;
  }
}
?>