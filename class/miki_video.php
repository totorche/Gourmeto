<?php
/**
 * Classe Miki_video
 * @package Miki
 */ 


/**
 * Représentation d'une vidéo. Un video contient un fichier.
 * 
 * @package Miki  
 */ 
class Miki_video{

  /**
   * Id de la vidéo
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Type de la vidéo (youtube, vimeo, etc)
   * 
   * @var string
   * @access public         
   */     
   public $type;
   
   /**
   * Etat de la vidéo (0 = non-publié, 1 = publié)
   * 
   * @var int
   * @access public         
   */     
   public $state;
  
  /**
   * Titre de la vidéo. Tableau dont la clé représente le code de la langue
   *      
   * @var mixed
   * @access public   
   */
  public $title;
  
  /**
   * Description de la vidéo. Tableau dont la clé représente le code de la langue
   *      
   * @var mixed
   * @access public   
   */
  public $description;
  
  /**
   * Url de la vidéo
   *       
   * @var string
   * @access public   
   */
  public $video;
  
  /**
   * Catégorie de la vidéo
   *       
   * @var string
   * @access public   
   */
  public $category;
  
  /**
   * Date de création de la vidéo (format yyyy-mm-dd hh:mm:ss)
   *      
   * @var string
   * @access public   
   */
  public $date_created;
  
  /**
   * Position de la vidéo
   *      
   * @var int
   * @access public   
   */
  public $position;
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge la vidéo dont l'id a été donné
   * 
   * @param int $id Id de la vidéo à charger (optionnel)
   */
  function __construct($id = ""){
    // charge la vidéo si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge une vidéo depuis un id
   *    
   * Si la vidéo n'existe pas, une exception est levée.
   *    
   * @param int $id id de la vidéo à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT mv.*, temp.title title, temp.description description, temp.language_code code
                    FROM miki_video mv
     
                    LEFT OUTER JOIN (SELECT mvt1.title, mvd1.description, mvt1.language_code, mvt1.id_video
                    FROM miki_video_title mvt1,
                         miki_video_description mvd1
                    WHERE mvt1.id_video = mvd1.id_video
                      AND mvt1.language_code = mvd1.language_code) temp ON temp.id_video = mv.id
     
                    WHERE mv.id = %d",
      mysql_real_escape_string($id));

    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql : <br /><br />$sql<br /><br />");
    
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("la vidéo demandée n'existe pas"));
    while ($row = mysql_fetch_array($result)){
      $this->id                               = $row['id'];
      $this->type                             = $row['type'];
      $this->state                            = $row['state'];
      $this->title[$row['code']]              = stripslashes($row['title']);
      $this->description[$row['code']]        = stripslashes($row['description']);
      $this->category                         = stripslashes($row['category']);
      $this->date_created                     = $row['date_created'];
      $this->video                            = $row['video'];
      $this->position                         = $row['position'];
    }
    
    return true;
  }
  
  /**
   * Sauvegarde la vidéo dans la base de données.
   *    
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout
   * 
   * Si une erreur survient, une exception est levée
   *    
   * @return boolean   
   */
  public function save(){
    // si l'id de la vidéo existe, c'est que la vidéo existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    // débute la transaction
    mysql_query("START TRANSACTION");

    $sql = sprintf("INSERT INTO miki_video (type, state, video, category, date_created, position) VALUES('%s', %d, '%s', %d, NOW(), %d)",
      mysql_real_escape_string($this->type),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->video),
      mysql_real_escape_string($this->category),
      mysql_real_escape_string($this->position));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'ajout de la vidéo dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    
    // ajoute le titre de la vidéo dans chaque langue
    if (is_array($this->title)){
      foreach($this->title as $key => $t){
        $sql = sprintf("INSERT INTO miki_video_title (id_video, language_code, title) VALUES(%d, '%s', '%s')",
          mysql_real_escape_string($this->id),
          mysql_real_escape_string($key),
          mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
        $result = mysql_query($sql);
        
        if (!$result){
          // annule les modifications
          mysql_query("ROLLBACK");

          throw new Exception(_("Erreur lors de l'ajout de la vidéo dans la base de données :") ."<br />" .mysql_error());
        }
      }
    }
    
    // ajoute la description de la vidéo dans chaque langue
    if (is_array($this->description)){
      foreach($this->description as $key => $t){
        $sql = sprintf("INSERT INTO miki_video_description (id_video, language_code, description) VALUES(%d, '%s', '%s')",
          mysql_real_escape_string($this->id),
          mysql_real_escape_string($key),
          mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
        $result = mysql_query($sql);
        if (!$result){
          // annule les modifications
          mysql_query("ROLLBACK");

          throw new Exception(_("Erreur lors de l'ajout de la vidéo dans la base de données :") ."<br />" .mysql_error());
        }
      }
    }
    
    // applique les modifications
    mysql_query("COMMIT");
    
    // recharge la vidéo
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour la vidéo dans la base de données.
   * 
   * Si l'id n'existe pas encore, une sauvegarde (save) est effectuée à la place de la mise à jour
   *    
   * Si une erreur survient, une exception est levée   
   *    
   * @return boolean   
   */  
  public function update(){
    // si aucun id existe, c'est que la vidéo n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // débute la transaction
    mysql_query("START TRANSACTION");
    
    $sql = sprintf("UPDATE miki_video SET type = '%s', state = %d, video = '%s', category = %d, position = %d WHERE id = %d",
      mysql_real_escape_string($this->type),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->video),
      mysql_real_escape_string($this->category),
      mysql_real_escape_string($this->position),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de la vidéo dans la base de données : ") ."<br />" .mysql_error());
    }
      
      
    // supprime le titre de la vidéo dans chaque langue
    $sql = sprintf("DELETE FROM miki_video_title WHERE id_video = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de la vidéo dans la base de données : ") ."<br />" .mysql_error());
    }
    
    // ajoute le titre de la vidéo dans chaque langue
    foreach($this->title as $key => $t){
      $sql = sprintf("INSERT INTO miki_video_title (id_video, language_code, title) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
      $result = mysql_query($sql);
      if (!$result){
        // annule les modifications
        mysql_query("ROLLBACK");
      
        throw new Exception(_("Erreur lors de la mise à jour de la vidéo dans la base de données : ") ."<br />" .mysql_error());
      }
    }
    
    
    // supprime la description de la vidéo dans chaque langue
    $sql = sprintf("DELETE FROM miki_video_description WHERE id_video = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de la vidéo dans la base de données : ") ."<br />" .mysql_error());
    }
    
    // ajoute la description de la vidéo dans chaque langue
    foreach($this->description as $key => $t){
      $sql = sprintf("INSERT INTO miki_video_description (id_video, language_code, description) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string(htmlspecialchars($t, ENT_NOQUOTES, "UTF-8", false)));
      $result = mysql_query($sql);
      if (!$result){
        // annule les modifications
        mysql_query("ROLLBACK");
      
        throw new Exception(_("Erreur lors de la mise à jour de la vidéo dans la base de données : ") ."<br />" .mysql_error());
      }
    }
    
    // applique les modifications
    mysql_query("COMMIT");
    
    return true;
  }
  
  /**
   * Supprime la vidéo
   * 
   * Si une erreur survient, une exception est levée       
   * 
   * @return boolean
   */         
  public function delete(){
    // supprime la vidéo de la base de données
    $sql = sprintf("DELETE FROM miki_video WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la vidéo : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Change l'état de la vidéo. Si elle était publiée, on la dépublie et inversément.
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
      
    // puis met à jour la vidéo
    return $this->update();
  }
  
  public function get_link(){
    if ($this->type == 'youtube'){
//      return "http://youtube.com/$this->video";
      return "http://www.youtube.com/embed/" .$this->video ."?autoplay=1";
    }
    elseif($this->type == 'vimeo'){
//      return "http://vimeo.com/$this->video";
      return "http://player.vimeo.com/video/" .$this->video;
    }
    else
      return false;
  }
  
  /**
   * Affiche la vidéo
   * 
   * @param int $width La largeur de la vidéo. Si aucune valeur n'est fournie, la largeur sera de 400px
   * @param int $height La hauteur de la vidéo. Si aucune valeur n'est fournie, la hauteur sera de 400px
   * @param string $styles Le style CSS qui sera mis pour l'iframe contenant la vidéo. Vide par défaut. Ex : "border: solid 1px #000000; float: left;"   
   *      
   * @return boolean
   */     
  public function print_video($width = "", $height = "", $styles = ""){
    
    if ($styles != "" && substr($styles, -1) != ';')
      $styles .= ';';
    
    if ($this->type == 'youtube'){
      $site_url = Miki_configuration::get('site_url');
      echo '<iframe class="miki_video" src="http://www.youtube.com/embed/' .$this->video .'?enablejsapi=1&origin=' .$site_url .'&autohide=1&showinfo=0&modestbranding=1" id="player" type="text/html" width="' .$width .'" height="' .$height .'" frameborder="0" style="' .$styles .'"></iframe>';
      
      /*echo '<object width="425" height="355">
              <param name="movie" value="http://www.youtube.com/v/' .$this->video .'?version=3&autohide=1&showinfo=0&modestbranding=1"></param>
              <param name="allowScriptAccess" value="always"></param>
              <embed src="http://www.youtube.com/v/' .$this->video .'?version=3&autohide=1&showinfo=0&modestbranding=1" type="application/x-shockwave-flash" allowscriptaccess="always" width="425" height="355"></embed>
            </object>';*/
    }
    elseif($this->type == 'vimeo'){
      return '<iframe class="miki_video" src="http://player.vimeo.com/video/' .$this->video .'" width="' .$width .'" height="' .$height .'" frameborder="0" style="' .$styles .'"></iframe>';
    }
    else
      return false;
  }
  
  /**
   * Affiche la vignette de la vidéo
   * 
   * @param int $width La largeur de l'image à afficher. Si aucune valeur n'est fournie, la largeur d'origine sera prise
   * @param int $height La hauteur de la vidéo. Si aucune valeur n'est fournie, la hauteur d'origine sera prise 
   * @param string $id L'id qui sera mis pour l'image miniature. Pas d'id par défaut.
   * @param string $styles Le style CSS qui sera mis pour l'image miniature. Vide par défaut. Ex : "border: solid 1px #000000; float: left;"
   * @param string $target La cible du lien. Vide par défaut. Exemple : _self, _blank, _parent, _top          
   *      
   * @return boolean
   */     
  public function print_thumb($width = "", $height = "", $id = "", $styles = "", $target = ""){
    
    if ($this->type == 'youtube'){
      //$pic = "http://img.youtube.com/vi/$this->video/default.jpg"; // photo par défaut
      $pic = "http://img.youtube.com/vi/$this->video/0.jpg"; // grande photo par défaut
      /*return "http://img.youtube.com/vi/$this->video/1.jpg"; // photo 1
      return "http://img.youtube.com/vi/$this->video/2.jpg"; // photo 2
      return "http://img.youtube.com/vi/$this->video/3.jpg"; // photo 3*/
      
//      $link = "http://youtu.be/$this->video";
//        $link = "http://www.youtube.com/watch?v=".$this->video;
        $link = "http://www.youtube.com/embed/" .$this->video ."?autoplay=1";
    }
    elseif($this->type == 'vimeo'){
      $url = "http://vimeo.com/api/v2/video/$this->video.xml";
      
      // récupert les informations depuis Vimeo
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_TIMEOUT, 30);
      $return = curl_exec($curl);
      curl_close($curl);
      
      $datas = simplexml_load_string($return);
      $pic = $datas->video->thumbnail_large;
      
//      $link = "http://vimeo.com/$this->video";
      $link = "http://player.vimeo.com/video/" .$this->video;
    }
    else
      return false;
    
    if (isset($_SESSION['lang']))
      $lang = $_SESSION['lang'];
    else
      $lang = Miki_language::get_main_code();
      
    if ($styles != "" && substr($styles, -1) != ';')
      $styles .= ';';
    
    // vérifie si la cible du lien est valide
    if (!in_array(strtolower($target), array("_self", "_blank", "_parent", "_top")))
      $target = "";
    
    $site_url = Miki_configuration::get('site_url');
    
    $pic = $site_url ."/scripts/watermark.php?src=$pic&w=$width&h=$height";
    echo "<a class='miki_video'" .(($target != "") ? " target=$target" : "") ." href='$link' title=\"" .$this->title[$lang] ."\"><img" .(($id != "") ? " id=$id" : "") ." src='$pic' alt=\"" .$this->title[$lang] ."\" style='" .$styles ."border: 0; height: " .$height ."px; width: " .$width ."px' /></a>";
    return true;
  }
  
  /**
   * Recherche le nombre de vidéos total
   * 
   * @static   
   * @return int      
   */     
  public static function get_nb_videos(){
    $sql = "SELECT COUNT(*) FROM miki_video";
    
    $result = mysql_query($sql);
    
    $row = mysql_fetch_array($result);
    return $row[0];
  }

  /**
   * Recherche toutes les vidéos selon certains critères
   * 
   * @param string $search Critère de recherche qui sera appliqué sur le nom (dans toutes les langues)
   * @param int $category Recherche seulement les vidéos de la catégorie donnée      
   * @param boolean $all si true, on prend toutes les vidéos, si false on ne prend que les vidéos publiées (state = 1)
   * @param string $order Par quel champ les vidéos trouvées seront triés (category, date_created, state). Si vide, on tri selon la date de création (date_created).
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * @param int $nb nombre de vidéos à retourner par page. Si = "" on retourne toutes les vidéos
   * @param int $page numéro de la page à retourner     
   * 
   * @static
   * @return mixed un tableau d'élément miki_video représentant toutes les vidéos trouvées   
   */             
  public static function search($search = "", $category = "", $all = true, $order = "", $order_type = "asc", $nb = "", $page = 1){
    $return = array();
    $sql = "SELECT DISTINCT(mv.id) 
            FROM miki_video mv, miki_video_title mvt
            WHERE mvt.id_video = mv.id";
    
    // Applique les critères de recherche
    if ($search !== ""){
      $search = mb_strtolower($search, 'UTF-8');
      $sql .= sprintf(" AND LOWER(mvt.title) LIKE '%%%s%%'",
                mysql_real_escape_string($search));
    }
    
    // Recherche selon la catégorie
    if ($category !== ""){
      $category = mb_strtolower($category, 'UTF-8');
      $sql .= sprintf(" AND mv.category = %s",
                mysql_real_escape_string($category));
    }
    
    // ne prend que les vidéos dans l'état 'publiées'
    if (!$all){
      $sql .= " AND state = 1";
    }
    
    if ($order == "category")
      $sql .= " ORDER BY mv.category " .$order_type;
    elseif ($order == "date_created")
      $sql .= " ORDER BY mv.date_created " .$order_type;
    elseif ($order == "state")
      $sql .= " ORDER BY mv.state " .$order_type;
    elseif ($order == "position")
      $sql .= " ORDER BY mv.position " .$order_type;
    else
      $sql .= " ORDER BY mv.position " .$order_type;

    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }

    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $return[] = new miki_video($row['id']);
    }
    return $return;
  }
  
  /**
   * Recherche toutes les vidéos
   * 
   * @param boolean $all si true, on prend toutes les vidéos, si false on ne prend que les vidéos publiées (state = 1)
   * @param string $order Par quel champ les vidéos trouvées seront triées (category, date_created, state). Si vide, on tri selon la date de création (date_created).
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * @param int $nb Nombre de vidéos à retourner par page. Si = "" on retourne toutes les vidéos
   * @param int $page Numéro de la page à retourner     
   * 
   * @static
   * @return mixed un tableau d'élément miki_video représentant toutes les vidéos trouvées   
   */             
  public static function get_all_videos($all = true, $order = "", $order_type = "asc", $nb = "", $page = 1){
    $return = array();
    $sql = "SELECT * FROM miki_video";
    
    // ne prend que les vidéos dans l'état 'publiées'
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
      $return[] = new miki_video($row['id']);
    }
    return $return;
  }
}
?>