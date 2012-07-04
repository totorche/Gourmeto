<?php
/**
 * Classe Miki_page
 * @package Miki
 */ 

/**
 * Représentation d'une page du CMS
 * 
 * Une page possède un template (Miki_template) qui représente le visuel du site Internet. 
 * La page est inclue dans un template grâce à la balise [miki_content] à insérer à l'endroit désiré dans le template.
 * 
 * Une page peut être hierarchisée. Elle peut avoir un parent et des enfants. Ceci peut être utile pour la gestion du menu du site Internet.
 * 
 * Une page possède un contenu (Miki_page_content) par langue définie (Miki_language).
 * 
 * Une page peut être accessible par tout le monde ou peut posséder un accès restreint aux membres (Miki_person) logués. 
 * L'accès se gère via la fonction "test_right_pages" du fichier "functions.php" situé dans le répertoire "scripts" du site Internet      
 * 
 * @see Miki_template
 * @see Miki_page_content
 * @see Miki_language 
 * @see Miki_person 
 *  
 * @package Miki  
 */
class Miki_page{
  
  /**
   * Id de la page
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom de la page
   *      
   * @var string
   * @access public   
   */
  public $name;
  
  /**
   * Etat de la page (0 = désactivée, 1 = active, 2 = page par défaut)
   *      
   * @var int
   * @access public   
   */
  public $state = 0;
  
  /**
   * Date de création de la page
   *      
   * @var string
   * @access public   
   */
  public $date_creation;
  
  /**
   * Id de l'utilisateur (Miki_user) ayant créé la page
   *      
   * @var int
   * @access public   
   */
  public $user_creation;
  
  /**
   * Type d'accès à la page (0 = accès libre, 1 = accès avec login de type 1, 2 = accès avec login de type 2, etc.)
   *      
   * @var int
   * @access public   
   */
  public $login = 0;
  
  /**
   * Id du template de la page
   *      
   * @var int
   * @access public   
   */
  public $template_id;
  
  /**
   * Position de la page dans le CMS (utilisé pour la hiérarchie, fille de, parent de, ...)
   *      
   * @var float
   * @access public   
   */
  public $position;
  
  /**
   * Id de la page parent
   *      
   * @var int
   * @access public   
   */
  public $parent_id;
  
  /**
   * Affichage du code Google Analytics sur la page (0 = non, 1 = oui)
   *      
   * @var int
   * @access public   
   */
  public $analytics;
  
  /**
   * Affichage de la page dans le menu (0 = non, 1 = oui)
   *      
   * @var int
   * @access public   
   */
  public $menu;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge la page dont l'id a été donné
   * 
   * @param int $id Id de la page à charger (optionnel)
   */
  function __construct($id = ""){
    // charge la page si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Génère le fichier sitemap.xml
   * 
   * @return boolean      
   */     
  public function update_sitemap(){
    // vérifie que la bibliothèque cURL existe
    if (function_exists('curl_init') && function_exists('curl_exec')) {
      
      // récupert l'adresse de génération de sitemap
      $url = Miki_configuration::get('site_url');
      if (substr($url, 0, 1) != '/')
        $url .= "/";
      $url .= "gen_sitemap.php";
      
  		// Configuration de cURL
  		$ch = curl_init();
  		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  		curl_setopt($ch, CURLOPT_URL, $url);
  		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
  		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
  		$head  = curl_exec($ch);
  		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
      curl_close($ch);
      
      // teste les retours
      if(!$head)
        return false;
      
      if($httpCode < 400)
        return true; 
      else
        return false; 
      
      // Protect against Zombie children
      pcntl_wait($status);
  	}
  	else
  	 return false;
  }
  
  /**
   * Réécrit le fichier .htaccess pour permettre d'accéder aux pages grâce à leurs alias (via url rewriting)
   * 
   * @return boolean      
   */   
  public function update_htaccess(){
    // si jamais il n'y a pas de fichier .htaccess
    if (!file_exists('../.htaccess'))
      return false;
      
    // lit puis modifie le fichier .htaccess
    $lines = file ('../.htaccess');
    $temp = array();
    $url_rewrite = false;
    $langs = Miki_language::get_all_languages();
    $replace = array();
    $urls = array();
    $urls[] = "# start url rewriting\n";
    
    foreach($langs as $l){
      $replace[$l->code] = false;
    }
    
    foreach ($lines as $line) {
      // détecte le début et la fin de la section "url rewriting"
      if (strstr($line, "# start url rewriting")){
        $url_rewrite = true;
        $line = "";
      }
      elseif (strstr($line, "# stop url rewriting")){
        $url_rewrite = false;
        $line = "";
      }

      // si on est dans la section "url rewriting"
      if ($url_rewrite){
        $replaced = false;
        foreach($langs as $l){
          if (stristr($line, "index.php?pn=$this->name&l=$l->code")){
            $url = $this->get_url($l->code);
            if ($url)
              $urls[] = $url ."\n";
            $replace[$l->code] = true;
            $replaced = true;
          }
        }
        // si la ligne n'a pas été remplacée, on l'ajoute telle quelle au tableau des urls
        if (!$replaced)
          $urls[] = $line;
      }
      else{
        // ajoute la ligne au buffer temporaire
        $temp[] = $line;
      }
    }
    
    // ajoute les urls des langues n'ayant pas été trouvées
    foreach($langs as $l){
      if (!$replace[$l->code]){
        $url = $this->get_url($l->code);
        if ($url)
          $urls[] = $url ."\n";
      }
    }
    
    $urls[] = "# stop url rewriting\n";
    
    // récupert la section s'occupant du multilingue à la fin du fichier htaccess
    $start_multi_language_rewriting = "";
    $length_multi_language_rewriting = 0;
    foreach($temp as $index => $line){
      if (stripos($line, "# start multi-language rewriting") !== false)
        $start_multi_language_rewriting = $index;
      elseif (stripos($line, "# stop multi-language rewriting") !== false)
        $stop_multi_language_rewriting = $index - $start_multi_language_rewriting + 1;
    }
    $multi_language_rewriting = array_merge(array("\n", "\n", "\n"), array_splice($temp, $start_multi_language_rewriting, $stop_multi_language_rewriting));
    
    // ajoute les urls réécrits et la partie multilingue à la suite du fichier htaccess
    $lines = array_merge($temp, $urls, $multi_language_rewriting);

    // réécrit le fichier htaccess
    $fd = @fopen("../.htaccess", "w");
    if ($fd){
      foreach ($lines as $line){
        fwrite($fd, $line);
      }
    }
    else
      return false;

    return true;
  }
  
  /**
   * Définit la position de la page en fonction de son parent
   * 
   * @param string $parent_id Id de la page parent par rapport à laquelle la position de la page sera calculée.      
   */   
  public function get_position($parent_id){
    if ($parent_id == 'NULL' || $parent_id == '' || $parent_id == '-1'){
      $sql = "SELECT max(position) FROM miki_page WHERE parent_id IS NULL";
      $result = mysql_query($sql);
      $pos = mysql_result($result,0) + 1;
      if ($pos == "")
        $pos = 1;
    }
    else{
      /*$sql = sprintf("SELECT position, (SELECT count(*) FROM miki_page WHERE parent_id = %d) FROM miki_page WHERE id = %d",
        mysql_real_escape_string($parent_id),
        mysql_real_escape_string($parent_id));
        
      $result = mysql_query($sql);
      $row = mysql_fetch_array($result);
      $pos_parent = $row[0];
      $nb_children = $row[1];
      $pos = $pos_parent ."." .($nb_children + 1);*/
      
      $sql = sprintf("SELECT max(position) FROM miki_page WHERE parent_id = %d",
        mysql_real_escape_string($parent_id));
      $result = mysql_query($sql);
      $pos = mysql_result($result,0) + 1;
      if ($pos == "")
        $pos = 1;
    }
    $this->position = $pos;
  }
  
  /**
   * Charge une page depuis un id
   *    
   * Si la page n'existe pas, une exception est levée.
   *    
   * @param int $id id de la page à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_page WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La page demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->state = $row['state'];
    $this->date_creation = $row['date_creation'];
    $this->user_creation = $row['user_creation'];
    $this->login = $row['login'];
    $this->template_id = $row['template_id'];
    $this->position = $row['position'];
    $this->parent_id = $row['parent_id'];
    $this->analytics = $row['analytics'];
    $this->menu = $row['menu'];
    return true;
  }
  
  /**
   * Charge une page depuis son nom
   *    
   * Si la page n'existe pas, une exception est levée.
   *    
   * @param string $name Nom de la page à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load_from_name($name){
    $sql = sprintf("SELECT * FROM miki_page WHERE name = '%s'",
      mysql_real_escape_string($name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("Aucune page ne correspond au nom donné"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->name = $row['name'];
    $this->state = $row['state'];
    $this->date_creation = $row['date_creation'];
    $this->user_creaton = $row['user_creation'];
    $this->login = $row['login'];
    $this->template_id = $row['template_id'];
    $this->position = $row['position'];
    $this->parent_id = $row['parent_id'];
    $this->analytics = $row['analytics'];
    $this->menu = $row['menu'];
    return true;
  }
  
  /**
   * Sauvegarde la page dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * La page doit posséder un nom unique (champ "name"). Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été donné à la page"));
      
    // si l'id de la page existe, c'est que la page existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    if ($this->parent_id == "")
      $this->parent_id = 'NULL';
      
    // teste qu'une page du même nom n'existe pas déjà
    $sql = sprintf("SELECT * FROM miki_page WHERE name = '%s'",
      mysql_real_escape_string($this->name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) != 0)
      throw new Exception(_("Une page du même nom existe déjà dans la base de données"));
      
    $this->user_creation = $_SESSION['miki_admin_user_id'];
    $this->get_position($this->parent_id);
    
    $sql = sprintf("INSERT INTO miki_page (name, state, date_creation, user_creation, login, template_id, position, parent_id, analytics, menu) VALUES('%s', %d, NOW(), %d, %d, %s, '%s', %s, %d, %d)",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->user_creation),
      mysql_real_escape_string($this->login),
      mysql_real_escape_string($this->template_id),
      mysql_real_escape_string($this->position),
      mysql_real_escape_string($this->parent_id),
      mysql_real_escape_string($this->analytics),
      mysql_real_escape_string($this->menu));
   
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion de la page dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la page
    $this->load($this->id);
  }
  
  /**
   * Met à jour la page dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * La page doit posséder un nom unique (champ "name"). Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->name))
      throw new Exception(_("La page n'a aucun nom"));
      
    // si aucun id existe, c'est que la page n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    if ($this->parent_id == "")
      $this->parent_id = 'NULL';
      
    // teste qu'une page du même nom n'existe pas déjà
    $sql = sprintf("SELECT * FROM miki_page WHERE name = '%s' AND  id != %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) != 0)
      throw new Exception(_("Une page du même nom existe déjà dans la base de données"));
    
    $sql = sprintf("UPDATE miki_page SET name = '%s', state = %d, login = %d, template_id = %s, position = '%s', parent_id = %s, analytics = %d, menu = %d WHERE id = %d",
      mysql_real_escape_string($this->name),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->login),
      mysql_real_escape_string($this->template_id),
      mysql_real_escape_string($this->position),
      mysql_real_escape_string($this->parent_id),
      mysql_real_escape_string($this->analytics),
      mysql_real_escape_string($this->menu),
      mysql_real_escape_string($this->id));
   
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour de la page dans la base de données : ") ."<br />" .mysql_error());
  }
  
  /**
   * Ajoute un contenu à la page
   * 
   * Si une erreur survient, une exception est levée.      
   * 
   * @param Miki_page_content
   * 
   * @see Miki_page_content
   * @return boolean               
   */   
  public function add_page_content($content){
    // si l'id n'a pas été défini, on sauve la page dans la bdd
    if (!isset($this->id)){
      try{
        $this->save();
      }catch(Exception $e){
        throw new Exception(_("La page n'a pas pu être ajoutée à la base de données."));
      }
    }
    
    // affecte le contenu à la page en cours
    $content->page_id = $this->id;
    
    // puis sauve le contenu
    try{
      $content->save();
    }catch(Exception $e){
      throw new Exception(_("Le contenu n'a pas pu être ajouté à la base de données."));
    }
    
    return true;
  }
  
  /**
   * Supprime la page
   * 
   * Met à jour les positions des autres pages       
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){    
    $sql = sprintf("UPDATE miki_page SET position = position - 1 WHERE position > %d",
      mysql_real_escape_string($this->position));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la page : ") ."<br />" .mysql_error());
  
    $sql = sprintf("DELETE FROM miki_page WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la page : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Recherche tous les contenus (Miki_page_content) de la page
   * 
   * @param string $l Si != "", ne récupert que les contenus dans la langue dont le code correspond à $l
   * 
   * @see Miki_page_content
   * @return mixed Un tableau d'éléments de type Miki_page_content correspondant aux contenus trouvés. False si aucun contenu n'a été trouvé.           
   */       
  public function get_contents($l = ""){
    $return = array();
    $sql = "SELECT distinct miki_page_content.language_id, miki_language.code 
            FROM miki_page_content, miki_language 
            WHERE miki_page_content.page_id = %d AND miki_language.id = miki_page_content.language_id";
    
    // si la langue a été spécifiée, on ne récupert que le contenu dans cette langue
    if ($l != ""){
      $language = new Miki_language();
      $language->load_from_code($l);
      $sql .= " AND miki_page_content.language_id = $language->id";
    }
    
    // recherche les différents languages
    $sql = sprintf($sql,
      mysql_real_escape_string($this->id));
      
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      // recherche le contenu le plus récent dans la langue en cours
      $language_id = $row[0];
      $language_code = mb_strtolower($row[1], 'UTF-8');
      $sql = sprintf("SELECT id FROM miki_page_content WHERE language_id = %d AND page_id = %d AND date_modification = 
                      (SELECT max(date_modification) FROM miki_page_content WHERE page_id = %d AND language_id = %d)",
        mysql_real_escape_string($language_id),
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($language_id));
      $result2 = mysql_query($sql);
      $row2 = mysql_fetch_array($result2);
      $content = new Miki_page_content($row2[0]);
      $return[$language_code] = $content;
    }
    
    // si on est dans l'administration, vérifie qu'il existe un contenu pour chaque langue configurée
    if ($l == "" && isset($_SESSION['miki_admin_user_id'])){
      $languages = Miki_language::get_all_languages(); 
      foreach ($languages as $l){
        if (!isset($return[$l->code])){
          $content = new Miki_page_content();
          $content->page_id = $this->id;
          $content->language_id = $l->id;
          
          $content->title = "";
          $content->description = "";
          $content->keywords = "";
          $content->metas = "";
          $content->alias = "";
          $content->menu_text = "";
          $content->category_id = "NULL";
          $content->noembed = "";
          $content->content_type = "code";
          $content->content = "";
          $content->save();
          $return[$l->code] = $content;
        }
      }
    }
    
    // s'il n'y a pas de résultat, on retourne "false""    
    if (sizeof($return) == 0)
      $return = false;
    
    return $return;
  }
  
  /**
   * Définit la page parent
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @param Miki_page $parent Page parent de la page actuelle
   * 
   * @return boolean
   */               
  public function set_parent(Miki_page $parent){
    $this->parent_id = $parent->id;
    $sql = sprintf("UPDATE miki_page SET parent_id = %d WHERE id = %d",
      mysql_real_escape_string($this->parent_id),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      throw new Exception(_("Erreur lors de l'ajout de la page parent : ") ."<br />" .mysql_error());
    }
    return true;
  }
  
  /**
   * Définit la page comme n'ayant aucun parent.
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean      
   */     
  public function remove_parent(){
    $sql = sprintf("UPDATE miki_page SET parent_id = null WHERE id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      throw new Exception(_("Erreur lors de l'effacement de la page parent : ") ."<br />" .mysql_error());
    }
    return true;
  }
  
  /**
   * Définit la page comme étant la page par défaut
   * 
   * Si une erreur survient, une exception est levée.
   *         
   * @return boolean   
   */   
  public function set_default(){
    mysql_query("START TRANSACTION");
    $sql = "UPDATE miki_page SET state = 1 WHERE state = 2";
    $result = mysql_query($sql);
    if (!$result){
      mysql_query("ROLLBACK");
      throw new Exception(_("Erreur lorsque la page a été définie par défaut : ") ."<br />" .mysql_error());
    }
    
    $this->state = 2;
    $sql = sprintf("UPDATE miki_page SET state = 2 WHERE id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      mysql_query("ROLLBACK");
      throw new Exception(_("Erreur lorsque la page a été définie par défaut : ") ."<br />" .mysql_error());
    }
    mysql_query("COMMIT");
    return true;
  }
  
  /**
   * Change l'état de la page
   * 
   * Si une erreur survient, une exception est levée.
   *        
   * @param int $state Nouvel état de la page. Si == "", l'état de la page est switché : si active -> inactive et si inactive -> active   
   * 
   * @return boolean   
   */   
  public function change_state($state = ""){
    // si c'est la page par défaut, on ne fait rien
    if ($this->state == 2)
      return false;
      
    // change l'état de la page
    if ($state != "")
      $this->state = $state;
    else
      $this->state = ($this->state + 1) % 2;
    
    $sql = sprintf("UPDATE miki_page SET state = %d WHERE id = %d",
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      throw new Exception(_("Erreur pendant la modification de l'état de la page : ") ."<br />" .mysql_error());
    }
    return true;
  }
  
  /** 
   * Monte la page d'une position
   * 
   * Si une erreur survient, une exception est levée.
   * 
   * @return boolean            
   */
  public function move_up(){
    // débute la transaction
    mysql_query("START TRANSACTION");
    if ($this->position != 1){
      
      // descend la page précédente
      $sql = sprintf("UPDATE miki_page SET position = position + 1 WHERE position = %d",
        mysql_real_escape_string($this->position - 1));
        
      // n'affecte que les pages de la même hiérarchie
      if ($this->parent_id == '')
        $sql .= " AND parent_id IS NULL";
      else
        $sql .= sprintf(" AND parent_id = %s", mysql_real_escape_string($this->parent_id));
        
      $result = mysql_query($sql);
      if (!$result){
        mysql_query("ROLLBACK");
        throw new Exception(_("Erreur lors du déplacement de la page : ") ."<br />" .mysql_error());
      }
      
      // déplace la page
      $sql = sprintf("UPDATE miki_page SET position = %d WHERE id = %d",
        mysql_real_escape_string($this->position - 1),
        mysql_real_escape_string($this->id));
      $result = mysql_query($sql);
      if (!$result){
        mysql_query("ROLLBACK");
        throw new Exception(_("Erreur lors du déplacement de la page : ") ."<br />" .mysql_error());
      }
      // termine la transaction
      mysql_query("COMMIT");
      
      $this->position--;
    }
    return true;
  }
  
  /** 
   * Descent la page d'une position
   * 
   * Si une erreur survient, une exception est levée.
   * 
   * @return boolean            
   */
  public function move_down(){
    // débute la transaction
    mysql_query("START TRANSACTION");
    
    // descend la page précédente
    $sql = sprintf("UPDATE miki_page SET position = position - 1 WHERE position = %d",
      mysql_real_escape_string($this->position + 1));
      
    // n'affecte que les pages de la même hiérarchie
    if ($this->parent_id == '')
      $sql .= " AND parent_id IS NULL";
    else
      $sql .= sprintf(" AND parent_id = %s", mysql_real_escape_string($this->parent_id));
      
    $result = mysql_query($sql);
    if (!$result){
      mysql_query("ROLLBACK");
      throw new Exception(_("Erreur lors du déplacement de la page : ") ."<br />" .mysql_error());
    }
    
    // si aucune page n'a été déplacée, c'est que la page en cours est la dernière page, donc on la laisse là
    if (mysql_affected_rows() == 1){
      // déplace la page
      $sql = sprintf("UPDATE miki_page SET position = %d WHERE id = %d",
        mysql_real_escape_string($this->position + 1),
        mysql_real_escape_string($this->id));
      $result = mysql_query($sql);
      if (!$result){
        mysql_query("ROLLBACK");
        throw new Exception(_("Erreur lors du déplacement de la page : ") ."<br />" .mysql_error());
      }
      // termine la transaction
      mysql_query("COMMIT");
      
      $this->position++;
    }
    return true;  
  }
  
  /** 
   * Monte la page en première position
   * 
   * Si une erreur survient, une exception est levée.
   * 
   * @return boolean            
   */
  public function move_top(){
    // débute la transaction
    mysql_query("START TRANSACTION");
    
    if ($this->position != 0){
    
      // descend d'une position les pages précédentes
      $sql = sprintf("UPDATE miki_page SET position = position + 1 WHERE position < %d",
        mysql_real_escape_string($this->position));
        
      // n'affecte que les pages de la même hiérarchie
      if ($this->parent_id == '')
        $sql .= " AND parent_id IS NULL";
      else
        $sql .= sprintf(" AND parent_id = %s", mysql_real_escape_string($this->parent_id));
        
      $result = mysql_query($sql);
      if (!$result){
        mysql_query("ROLLBACK");
        throw new Exception(_("Erreur lors du déplacement de la page : ") ."<br />" .mysql_error());
      }
      
      // déplace la page en première place
      $sql = sprintf("UPDATE miki_page SET position = 1 WHERE id = %d",
        mysql_real_escape_string($this->id));
      $result = mysql_query($sql);
      if (!$result){
        mysql_query("ROLLBACK");
        throw new Exception(_("Erreur lors du déplacement de la page : ") ."<br />" .mysql_error());
      }
      // termine la transaction
      mysql_query("COMMIT");
      
      $this->position = 1;
    }
    return true;
  }
  
  /** 
   * Descend la page en dernière position
   * 
   * Si une erreur survient, une exception est levée.
   * 
   * @return boolean            
   */
  public function move_bottom(){
    // débute la transaction
    mysql_query("START TRANSACTION");
    
    // monte d'une position les pages suivantes
    $sql = sprintf("UPDATE miki_page SET position = position - 1 WHERE position > %d",
      mysql_real_escape_string($this->position));
      
    // n'affecte que les pages de la même hiérarchie
    if ($this->parent_id == '')
      $sql .= " AND parent_id IS NULL";
    else
      $sql .= sprintf(" AND parent_id = %s", mysql_real_escape_string($this->parent_id));
      
    $result = mysql_query($sql);
    if (!$result){
      mysql_query("ROLLBACK");
      throw new Exception(_("Erreur lors du déplacement de la page : ") ."<br />" .mysql_error());
    }
    
    // récupert la dernière position
    $sql = "SELECT max(position) FROM miki_page";
    $result = mysql_query($sql);
    $last_pos = mysql_result($result, 0) + 1;
    
    // déplace la page en première place
    $sql = sprintf("UPDATE miki_page SET position = %d WHERE id = %d",
      mysql_real_escape_string($last_pos),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      mysql_query("ROLLBACK");
      throw new Exception(_("Erreur lors du déplacement de la page : ") ."<br />" .mysql_error());
    }
    // termine la transaction
    mysql_query("COMMIT");
    
    $this->position = $last_pos;
    
    return true;
  }
  
  /** 
   * Place la page à la position donnée
   * 
   * Si une erreur survient, une exception est levée.
   * 
   * @param int $pos Nouvelle position de la page      
   * 
   * @return boolean            
   */
  public function move_to($pos){
    // débute la transaction
    mysql_query("START TRANSACTION");
    
    // si on monte la page
    if ($pos < $this->position){
      // descend d'une position les pages précédentes
      $sql = sprintf("UPDATE miki_page SET position = position + 1 WHERE position < %d AND position >= %d",
        mysql_real_escape_string($this->position),
        mysql_real_escape_string($pos));
      
      // n'affecte que les pages de la même hiérarchie
      if ($this->parent_id == '')
        $sql .= " AND parent_id IS NULL";
      else
        $sql .= sprintf(" AND parent_id = %s", mysql_real_escape_string($this->parent_id));
      
      $result = mysql_query($sql);
      if (!$result){
        mysql_query("ROLLBACK");
        throw new Exception(_("Erreur lors du déplacement de la page : ") ."<br />" .mysql_error());
      }
      
      // déplace la page dans la position donnée
      $sql = sprintf("UPDATE miki_page SET position = %d WHERE id = %d",
        mysql_real_escape_string($pos),
        mysql_real_escape_string($this->id));
      $result = mysql_query($sql);
      if (!$result){
        mysql_query("ROLLBACK");
        throw new Exception(_("Erreur lors du déplacement de la page : ") ."<br />" .mysql_error());
      }
      // termine la transaction
      mysql_query("COMMIT");
      
      $this->position = $pos;
    }
    else{ // si on descend la page
      // descend d'une position les pages précédentes
      $sql = sprintf("UPDATE miki_page SET position = position - 1 WHERE position > %d AND position <= %d",
        mysql_real_escape_string($this->position),
        mysql_real_escape_string($pos));
        
      // n'affecte que les pages de la même hiérarchie
      if ($this->parent_id == '')
        $sql .= " AND parent_id IS NULL";
      else
        $sql .= sprintf(" AND parent_id = %s", mysql_real_escape_string($this->parent_id));
        
      $result = mysql_query($sql);
      if (!$result){
        mysql_query("ROLLBACK");
        throw new Exception(_("Erreur lors du déplacement de la page : ") ."<br />" .mysql_error());
      }
      
      // déplace la page en première place
      $sql = sprintf("UPDATE miki_page SET position = %d WHERE id = %d",
        mysql_real_escape_string($pos),
        mysql_real_escape_string($this->id));
      $result = mysql_query($sql);
      if (!$result){
        mysql_query("ROLLBACK");
        throw new Exception(_("Erreur lors du déplacement de la page : ") ."<br />" .mysql_error());
      }
      // termine la transaction
      mysql_query("COMMIT");
      
      $this->position = $pos;
    }
    
    return true;
  }
  
  /**
   * Vérifie si la page possède des pages enfants
   * 
   * @param mixed $templates_id id du ou des templates que les pages enfants doivent utiliser pour être validées. Si plusieurs id de templates doivent être testés, passer un tableau contenant ces valeurs. Si vide, ne teste pas le template des pages enfants.
   * @param boolean $in_menu Si False, prend toutes les pages enfants en compte. Si True, ne prend que les pages enfants devant être affichées dans le menu
   * @return boolean      
   */   
  public function has_children($templates_id = "", $in_menu = false){
    $sql = sprintf("SELECT count(*) FROM miki_page WHERE parent_id = %d",
      mysql_real_escape_string($this->id));
    
    // si on doit tester le template des pages enfants
    if (is_array($templates_id) || $templates_id != ""){
    
      // si on doit tester plusieurs templates
      if (is_array($templates_id)){
        $templates_id = implode(", ", $templates_id);
      }
      
      // ajoute les tests à la requête
      $sql .= sprintf(" AND template_id IN (%s)",
              mysql_real_escape_string($templates_id));
    }
    
    if ($in_menu){
      $sql .= " AND menu = 1 AND state > 0";
    } 
    
    $result = mysql_query($sql);
    $nb = mysql_result($result,0);
    return $nb > 0;
  }
  
  /**
   * Vérifie si la page a un parent
   * 
   * @return boolean
   */            
  public function has_parent(){
    if ($this->parent_id == "" || $this->parent_id == "NULL")
      return false;
    else
      return true;
  }
  
  /**
   * Récupert les pages enfants
   * 
   * @param boolean $all Si true, récupert toutes les pages. Si false, ne récupert que les pages activées (state > 1)    
   * @param string $order Par quel champ les membres trouvés seront triés (name, state, date_creation, user_creation, template, position). Si vide, on tri selon l'id.
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)      
   * 
   * @return mixed Un tableau d'éléments de type Miki_page représentant les pages enfants trouvées         
   */
  public function get_children($order = "", $all = true, $type = "asc"){
    $sql = sprintf("SELECT id FROM miki_page WHERE parent_id = %d",
      mysql_real_escape_string($this->id));
      
    if (!$all)
      $sql .= " AND state > 0";
    
    // ordonne les pages
    if ($order == "name")
      $sql .= " ORDER BY name " .$type;
    elseif ($order == "state")
      $sql .= " ORDER BY state " .$type;
    elseif ($order == "date_creation")
      $sql .= " ORDER BY date_creation " .$type;
    elseif ($order == "user_creation")
      $sql .= " ORDER BY user_creation " .$type;
    elseif ($order == "template")
      $sql .= " ORDER BY template " .$type;
    elseif ($order == "position")
      $sql .= " ORDER BY position " .$type;
    else
      $sql .= " ORDER BY id " .$type;  
    
    $result = mysql_query($sql);
    
    $return = array();
    while($row = mysql_fetch_array($result)){
      $page = new Miki_page($row['id']);
      $return[] = $page;
     } 
    return $return;
  }
  
  /**
   * Récupert la position hiérarchique de la page   
   *
   * @return float   
   */
  public function get_hierarchical_position(){
    $pos = 0;
    $temp = $this;
    while($temp->has_parent()){
      $temp = new Miki_page($temp->parent_id);
      $pos++;
    }
    return $pos;
  }
  
  /**
   * Recherche la page précédente
   * 
   * @param boolean $check_parents Si TRUE et que la page possède une page parent, on recherche également dans les hiérarchies (pages) supérieures
   * 
   * @return mixed La page précédente (Miki_page) si une page précédente a été trouvée, false sinon      
   */
  public function get_previous($check_parents = true){
    // recherche la page précédente dans avec le même parent
    $sql = sprintf("SELECT id FROM miki_page WHERE position < %d AND parent_id = %d ORDER BY ID DESC LIMIT 1",
      mysql_real_escape_string($this->position),
      mysql_real_escape_string($this->parent_id));
    
    $result = mysql_query($sql);
    
    // si on n'a pas trouvé de page précédente avec le même parent
    if (mysql_num_rows($result) == 0){
      // si on doit rechercher dans les hiérarchies supérieures et que la page possède une page parent
      if ($check_parents && $this->has_parent()){
        // on prend la page parent
        $parent = new Miki_page($this->parent_id);
        // et on recherche la page précédente de la page parent (récursivement)
        return $parent->get_previous($check_parents);
      }
      // sinon, aucune page précédente
      else{
        return false;
      }
    }
    // s'il existe une page précédente directe
    else{
      // on renvoit la page précédente trouvée
      $row = mysql_fetch_array($result);
      return new Miki_page($row[0]);
    }
  }
  
  /**
   * Recherche la page suivante
   * 
   * @param boolean $check_parents Si TRUE et que la page possède une page parent, on recherche également dans les hiérarchies (pages) supérieures
   * 
   * @return mixed La page suivante (Miki_page) si une page suivante a été trouvée, false sinon      
   */
  public function get_next($check_parents = true){
    // recherche la page suivante dans avec le même parent
    $sql = sprintf("SELECT id FROM miki_page WHERE position > %d AND parent_id = %d ORDER BY ID ASC LIMIT 1",
      mysql_real_escape_string($this->position),
      mysql_real_escape_string($this->parent_id));
    
    $result = mysql_query($sql);
    
    // si on n'a pas trouvé de page suivante avec le même parent
    if (mysql_num_rows($result) == 0){
      // si on doit rechercher dans les hiérarchies supérieures et que la page possède une page parent
      if ($check_parents && $this->has_parent()){
        // on prend la page parent
        $parent = new Miki_page($this->parent_id);
        // et on recherche la page suivante de la page parent (récursivement)
        return $parent->get_next($check_parents);
      }
      // sinon, aucune page suivante
      else{
        return false;
      }
    }
    // s'il existe une page suivante directe
    else{
      // on renvoit la page suivante trouvée
      $row = mysql_fetch_array($result);
      return new Miki_page($row[0]);
    }
  }
  
  /**
   * Détermine si la page en cours correspond à la page donnée en paramètre ou est une de ses pages enfant
   * 
   * @param Miki_page $p La page à comparer
   * 
   * @return boolean
   */
  public function is_page($p){
  
    $result = false;
    
    // si la page en cours correspond à la page donnée --> true
    if ($this->name == $p->name){
      return true;
    }
    
    // si la page en cours a des enfants on vérifie chacun d'eux et les sous-enfants récursivement
    if ($this->has_children()){
      $children = $this->get_children();
      foreach($children as $child){
        if ($child->name == $p->name){
          return true;
        }
        elseif ($child->has_children()){
          if ($child->is_page($p))
            return true;
        }
      }
    }
    
    return $result;
  }
  
  /**
   * Ajoute un bloc de contenu global à la page
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @param Miki_global_content $global_content le bloc de contenu global
   * 
   * @return boolean      
   */   
  public function add_global_content(Miki_global_content $global_content){
    // Si la section y est déjà, ne fait rien (mot-clé "ignore")
    $sql = sprintf("insert ignore into miki_global_content_s_miki_page (page_id, global_content_id) VALUES(%d, %d)",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($global_content->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur pendant l'ajout du bloc de contenu global à la page : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime tous les blocs de contenu globaux liés à la page
   * 
   * Si une erreur survient, une exception est levée.      
   */      
  public function remove_global_contents(){
    $sql = sprintf("DELETE FROM miki_global_content_s_miki_page WHERE page_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur pendant la suppression des blocs de contenu globaux liés à la page : ") ."<br />" .mysql_error());
  }
  
  /**
   * Vérifie si la page est déjà liée avec le bloc de contenu global donné
   * 
   * @param Miki_global_content $global_content Le bloc de contenu global à vérifier
   * 
   * @return boolean      
   */   
  public function has_global_content(Miki_global_content $global_content){
    $sql = sprintf("SELECT count(*) FROM miki_global_content_s_miki_page WHERE page_id = %d AND  global_content_id = %d",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($global_content->id));
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    return $row[0] > 0;
  }
  
  /**
   * Détermine si la page doit être affichée dans le menu.
   * 
   * @return boolean      
   */     
  public function in_menu(){
    return $this->menu == 1 && $this->state > 0;
  }
  
  /**
   * Recherche le texte du menu de la page dans une langue donnée.
   * 
   * Si le texte du menu est vide, on renvoit le nom de la page.      
   * 
   * @param string $lang Le code du language dans lequel on veut récupérer le texte du menu
   * 
   * @return mixed Le texte du menu si la page existe dans la langue donnée, False sinon
   */
  public function get_menu_name($lang = ""){
    if ($lang == "")
      $lang = Miki_language::get_main_code();
      
    // récupert le contenu de la page dans la langue donnée
    $content = $this->get_contents($lang);
    
    // si aucune contenu n'est disponible dans cette langue, on retourne false
    if ($content === false)
      return false;
    
    // récupert le dernier contenu récupéré
    $content = current($content);
    
    // puis retourne le texte du menu configuré dans le Miki si non vide ou le nom de la page si vide
    if ($content->menu_text != "")
      return $content->menu_text;
    else
      return $this->name;
  }
  
  /**
   * Recherche l'URL de la page en fonction des alias et des catégories
   * 
   * @param string $lang Le code du language dans lequel on veut récupérer l'URL
   * 
   * @return string            
   */   
  public function get_url_simple($lang = ""){
    if ($lang == "")
      $lang = Miki_language::get_main_code();
    
    // ajoute les catégories
    $content = $this->get_contents($lang);
    
    if (!$content)
      return false;
      
    $content = current($content);
    
    // si il n'y a aucun alias, on retourne l'url simple
    if ($content->alias == "")
      $url = "index.php?pn=$this->name&amp;l=$lang";
    else
      $url = $content->alias;
      
    if (isset($content->category_id) && is_numeric($content->category_id)){
      $cat = new Miki_category($content->category_id);
      $url = $cat->name .'/' .$url;
      while(isset($cat->parent_id)){
        $cat = new Miki_category($cat->parent_id);
        $url = $cat->name .'/' .$url;
      }
    }
    
    $site_url = str_ireplace("http://", "", SITE_URL);
    $host = explode(".", $site_url);
    if (sizeof($host) == 3)
      $host = $lang ."." .$host[1] ."." .$host[2];
    elseif (sizeof($host) == 2)
      $host = $lang ."." .$host[0] ."." .$host[1];
    else
      $host = $site_url;

    // puis l'alias de la page
    $url = "http://" .$host .'/' .$url;
    return $url;
  }
  
  /**
   * Génère la ligne à destination du fichier .htaccess pour gérer l'URL Rewritting en fonction des alias et des catégories
   * 
   * @param string $l Le code du language dans lequel on veut récupérer l'URL
   * 
   * @return string            
   */
  public function get_url($l){
    $url = "";
    // ajoute les catégories
    $content = $this->get_contents($l);

    if (!$content)
      return false;

    $content = current($content);
    
    // si il n'y a aucun alias, on ne fait pas d'url rewriting
    if ($content->alias == "")
      return false;

    if (isset($content->category_id) && is_numeric($content->category_id)){
      $cat = new Miki_category($content->category_id);
      $url .= $cat->name .'/';
      while(isset($cat->parent_id)){
        $cat = new Miki_category($cat->parent_id);
        $url = $cat->name .'/' .$url;
      }
    }
    // puis l'alias de la page
    $url .= $content->alias .'$';// .'\.php$';
    
    // puis l'url original
    $url .= " index.php?pn=$this->name&l=" .strtolower($l) ." [QSA,L]";
    return "RewriteRule ^$url";
  }
  
  /**
   * Recherche la page par défaut
   * 
   * @return Miki_page La page par défaut ou False si aucune page n'est définie par défaut
   */
  public static function get_default(){
    $sql = "SELECT id FROM miki_page WHERE state = 2";
    
    $result = mysql_query($sql);
    
    if (mysql_num_rows($result) == 0)
      return false;
      
    $row = mysql_fetch_array($result);
    return new Miki_page($row['id']);
  }
  
  /**
   * Recherche toutes les pages dont le nom (nom du menu), ou le contenu correspondent au critère donné
   * 
   * @param string $search Critère de recherche pour le nom (nom du menu), ou le contenu de la page
   * @param boolean $l Langue dans laquelle le contenu des pages doit être recherché. Si vide, on recherche dans toutes les langues
   * @param string $order Par quel champ les pages trouvées seront triés (name, state, date_creation, user_creation, template, position). Si vide, on tri selon l'id.
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * @param int $nb nombre de pages à retourner par page. Si = "" on retourne toutes les pages
   * @param int $page numéro de la page à retourner   
   *    
   * @static
   * 
   * @return mixed Un tableau d'élément Miki_event représentant les événements trouvés
   */ 
  public static function search($search = "", $l = "", $order = "", $order_type = "asc", $nb = "", $page = 1){
    $return = array();
    
    // si une langue est donnée, on ne prend en compte que les contenus dans cette langue-là
    if ($l != ""){
      $sql = sprintf("SELECT DISTINCT(mp.id) 
                      FROM miki_page mp,
                           miki_page_content mc,
                           miki_language ml
                      WHERE mc.page_id = mp.id
                        AND ml.code = '%s' 
                        AND mc.language_id = ml.id
                        AND mc.date_modification = (SELECT max(date_modification) FROM miki_page_content WHERE page_id = mp.id AND language_id = ml.id)",
              mysql_real_escape_string($l));
    }
    // sinon on prend toutes les langues
    else{
      $sql = sprintf("SELECT DISTINCT(mp.id) 
                      FROM miki_page mp,
                           miki_page_content mc,
                           miki_language ml
                      WHERE mc.page_id = mp.id
                        AND mc.date_modification = (SELECT max(date_modification) FROM miki_page_content WHERE page_id = mp.id)");
    }
    
    // applique les critères de recherche
    if ($search != ""){
      $search = mb_strtolower($search, 'UTF-8');
      
      $sql .= sprintf(" AND (LOWER(mc.menu_text) LIKE '%%%s%%' OR 
                             LOWER(mc.content) LIKE '%%%s%%')",
                  mysql_real_escape_string($search),
                  mysql_real_escape_string($search));
    }
    
    // ordonne les pages
    if ($order == "name")
      $sql .= sprintf(" ORDER BY mp.name %s", $order_type);
    elseif ($order == "state")
      $sql .= sprintf(" ORDER BY mp.state %s", $order_type);
    elseif ($order == "date_creation")
      $sql .= sprintf(" ORDER BY mp.date_creation %s", $order_type);
    elseif ($order == "user_creation")
      $sql .= sprintf(" ORDER BY mp.user_creation %s", $order_type);
    elseif ($order == "template")
      $sql .= sprintf(" ORDER BY mp.template %s", $order_type);
    elseif ($order == "position")
      $sql .= sprintf(" ORDER BY mp.position %s", $order_type);
    else
      $sql .= sprintf(" ORDER BY mp.id %s", $order_type);

    // si demandé, ne prend que certains résultats
    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }
    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_page($row[0]);
    }
    return $return;
  }
  
  /**
   * Recherche toutes les pages
   *
   * @param boolean $all Si true, récupert toutes les pages. Si false, ne récupert que les pages activées (state > 1)    
   * @param string $order Par quel champ les membres trouvés seront triés (name, state, date_creation, user_creation, template, position). Si vide, on tri selon l'id.
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * 
   * @return mixed Un tableau d'éléments de type Miki_newsletter_group                  
   */
  public static function get_all_pages($order = "", $all = true, $type = "asc"){
    $sql = "SELECT * FROM miki_page";
    
    if (!$all)
      $sql .= " WHERE state > 0";
    
    // ordonne les pages
    if ($order == "name")
      $sql .= " ORDER BY name " .$type;
    elseif ($order == "state")
      $sql .= " ORDER BY state " .$type;
    elseif ($order == "date_creation")
      $sql .= " ORDER BY date_creation " .$type;
    elseif ($order == "user_creation")
      $sql .= " ORDER BY user_creation " .$type;
    elseif ($order == "template")
      $sql .= " ORDER BY template " .$type;
    elseif ($order == "position")
      $sql .= " ORDER BY position " .$type;
    else
      $sql .= " ORDER BY id " .$type;
      
    $return = array();
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $page = new Miki_page($row['id']);
      $return[] = $page;
     } 
    return $return;
  }
}
?>