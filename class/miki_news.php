<?php
/**
 * Classe Miki_news
 * @package Miki
 */ 

/**
 * Inclut les fonctions d'envoi d'e-mail
 */ 
require_once("class.phpmailer.php");

/**
 * Inclut les fonctions d'importation et de traitement des images
 */ 
require_once("functions_pictures.php");

/**
 * Représentation d'une actualité
 * 
 * @package Miki  
 */ 
class Miki_news extends Miki_comment_object{

  /**
   * Id de l'actualité
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Titre de l'actualité
   *      
   * @var string
   * @access public   
   */
  public $title;
  
  /**
   * Texte de l'actualité
   *      
   * @var string
   * @access public   
   */
  public $text;
  
  /**
   * Date de l'actualité
   *      
   * @var string
   * @access public   
   */
  public $date;
  
  /**
   * Id de l'utilisateur (Miki_user) ayant posté l'actualité
   *      
   * @var int
   * @see Miki_user   
   * @access public   
   */
  public $poster;
  
  /**
   * Image de l'actualité
   *      
   * @var string
   * @access public   
   */
  public $picture;
  
  /**
   * Code du language de l'actualité
   *      
   * @var string
   * @access public   
   */
  public $language;
  
  /**
   * Chemin relatif pour l'upload de l'image de l'actualité
   *      
   * @var string
   * @access private   
   */
  private $picture_path = "../pictures/news/";
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge l'actualité dont l'id a été donné
   * 
   * @param int $id Id de l'actualité à charger (optionnel)
   */
  function __construct($id = ""){
    // charge la news si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge une actualité depuis un id
   *    
   * Si l'actualité n'existe pas, une exception est levée.
   *    
   * @param int $id id de l'actualité à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_news WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La news demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id       = $row['id'];
    $this->title    = $row['title'];
    $this->text     = $row['text'];
    $this->date     = $row['date'];
    $this->poster   = $row['poster'];
    $this->picture  = $row['picture'];
    $this->language = $row['language'];
    return true;
  }
  
  /**
   * Sauvegarde l'actualité dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){      
    // si un l'id de la news existe, c'est que la news existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    $sql = sprintf("INSERT INTO miki_news (title, text, date, poster, picture, language) VALUES('%s', '%s', NOW(), %s, '%s', '%s')",
      mysql_real_escape_string($this->title),
      mysql_real_escape_string($this->text),
      mysql_real_escape_string($this->poster),
      mysql_real_escape_string($this->picture),
      mysql_real_escape_string($this->language));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion de la news dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge le message
    $this->load($this->id);    
    return true;
  }
  
  /**
   * Met à jour l'actualité dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){      
    // si aucun id existe, c'est que la news n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
    
    $sql = sprintf("UPDATE miki_news SET title = '%s', text = '%s', picture = '%s', language = '%s', date = NOW() WHERE id = %d",
      mysql_real_escape_string($this->title),
      mysql_real_escape_string($this->text),
      mysql_real_escape_string($this->picture),
      mysql_real_escape_string($this->language),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour de la news dans la base de données : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime l'actualité
   * 
   * Supprime également le fichier de l'image de l'actualité       
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */  
  public function delete(){
    $sql = sprintf("DELETE FROM miki_news WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la news : ") ."<br />" .mysql_error());
    
    // supprime l'image de la news
    $this->delete_picture();
    
    return true;
  }
  
  /**
   * Retourne une partie du titre de l'actualité
   * 
   * Si le titre de l'actualité est plus court que la partie demandée, le titre de l'actualité est retourné en entier.      
   * 
   * @param int $nb_char Nombre de caractères désirés
   * @param boolean $full_word Si true, le texte est coupé par mots entiers. Si false, les mots peuvent être coupé en plein milieu      
   * 
   * @return string
   */         
  public function get_title($nb_char, $full_word = true){
    if ($nb_char < mb_strlen($this->title)){
      if ($full_word === false)
        $stop = $nb_char;
      else{
        $stop = mb_strpos($this->title, ' ', $nb_char);
        if ($stop === false){
          $stop = mb_strlen($this->title);
        }
      }
      return mb_substr($this->title, 0, $stop);
    }
    else
      return $this->title;
  }
  
  /**
   * Retourne une partie du texte de l'actualité
   * 
   * Si le texte de l'actualité est plus court que la partie demandée, le texte de l'actualité est retourné en entier.      
   * 
   * @param int $nb_char Nombre de caractères désirés
   * @param boolean $full_word Si true, le texte est coupé par mots entiers. Si false, les mots peuvent être coupé en plein milieu
   * @param boolean $tags Si true, le texte est retourné avec les balises HTML. Si false, les balises HTML sont enlevées         
   * 
   * @return string
   */    
  public function get_text($nb_char, $full_word = true, $tags = true){
    // supprime les balises HTML si demandé
    if ($tags)
      $text = $this->text;
    else
      $text = strip_tags($this->text);
      
    if ($nb_char < mb_strlen($text)){
      if ($full_word === false)
        $stop = $nb_char;
      else{
        $stop = mb_strpos($text, ' ', $nb_char);
        if ($stop === false){
          $stop = mb_strlen($text);
        }
      }
      return mb_substr($text, 0, $stop);
    }
    else
      return $text;
  }
  
  /*********************************************************
   * teste l'image à uploader, la redimensionne puis l'upload
   * 
   *   fichier : fichier envoyé (FILE['nom_de_l_element'])
   *   chemin_destination : ou sera uploadée l'image
   *   
   *********************************************************/
   
  /**
   * Ajoute une image à l'actualité
   * 
   * Ajoute une image à l'actualité, l'upload et créé une vignette de maximum 200px de large ou de long
   * 
   * Si une erreur survient, une exception est levée.      
   * 
   * @param mixed $fichier Fichier envoyé (FILE['nom_de_l_element'])
   * @param string $nom_destination Nom de destination de l'image finale
   * @return boolean
   */     
  public function upload_picture($fichier, $nom_destination){ 
    $nom_destination = $nom_destination;
    
    // ajoute l'extension
    $system = explode('.',strtolower($fichier['name']));
    $ext = $system[sizeof($system)-1];
    $nom_destination = $nom_destination ."." .$ext;  

    // le fichier doit être au format jpg, gif ou png
  	if (!preg_match('/png/i',$ext) && !preg_match('/gif/i',$ext) && !preg_match('/jpg|jpeg/i',$ext)){
      throw new Exception(_("Le type de l'image n'est pas supporté. Les types supportés sont : JPEG, GIF et PNG : $nom_destination - " .$fichier['name']));
    }

    // teste s'il y a eu une erreur
  	if ($fichier['error']) {
  		switch ($fichier['error']){
  			case 1: // UPLOAD_ERR_INI_SIZE
  				throw new Exception(_("Le fichier dépasse la limite autorisée (5Mo)"));
  				break;
  			case 2: // UPLOAD_ERR_FORM_SIZE
  				throw new Exception(_("Le fichier dépasse la limite autorisée (5Mo)"));
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
  	
  	$size = "";
  	$file = $fichier['tmp_name'];
  	if (!is_uploaded_file($file) || $fichier['size'] > 5 * 1024 * 1024)
  		throw new Exception(_("Veuillez uploader des images plus petites que 5Mb"));
  	if (!($size = @getimagesize($file)))
  		throw new Exception(_("Veuillez n'uploader que des images. Les autres fichiers ne sont pas supportés"));
  	if (!in_array($size[2], array(1, 2, 3) ) )
  		throw new Exception(_("Le type de l'image n'est pas supporté. Les types supportés sont : JPEG, GIF et PNG"));

  	// pas d'erreur --> upload
  	if ((isset($fichier['tmp_name']))&&($fichier['error'] == UPLOAD_ERR_OK)) {
  		if (!move_uploaded_file($fichier['tmp_name'], $this->picture_path .$nom_destination))
        exit();
  	}

  	// redimensionne l'image
  	createthumb($this->picture_path .$nom_destination, $this->picture_path ."thumb/" .$nom_destination, 200, 200, false);

  	$this->picture = $nom_destination;
  	return true;
  }
  
  /**
   * Supprime l'image de l'actualité
   * 
   * Supprime l'image et la vignette de l'actualité ainsi que le fichier de ces images.
   *       
   * Si le fichier représentant l'image n'existe pas, une exception est levée
   * 
   * @return boolean
   */ 
  public function delete_picture(){
    if ($this->picture == "")
      return;
      
    // pour l'image
    $path = $this->picture_path .$this->picture;
    if (file_exists($path)){
      if (!unlink($path)){
        throw new Exception("Erreur pendant la suppression de l'image");
      }
    }
    
    // pour la miniature
    $path = $this->picture_path ."thumb/" .$this->picture;
    if (file_exists($path)){
      if (!unlink($path)){
        throw new Exception("Erreur pendant la suppression de l'image");
      }
    }
  }
  
  /**
   * Envoi un mail au blog du site Internet pour mettre l'actualité sur le blog
   * 
   * Publie l'actualité sur le blog lors de l'ajout ou de la modification de l'actualité via le Miki (console d'administration).
   * Publie uniquement si "Publier les actualités sur le blog" est coché dans la partie "Administration -> Configurer le site Internet" du Miki.
   * L'adresse e-mail de publication doit également être renseignée dans la partie "Administration -> Configurer le site Internet" du Miki.
   */   
  public function send_to_blog(){
    // création du mail
    $mail = new phpmailer();
    if (isset($_SESSION['lang']))
      $mail->SetLanguage($_SESSION['lang']);
    else
      $mail->SetLanguage('fr');
    
    $sitename = Miki_configuration::get('sitename');
    $email_answer = Miki_configuration::get('email_answer');
    $site_url = Miki_configuration::get('site_url');
    $publish_email_address = Miki_configuration::get('publish_email_address');
    
    if ($this->picture != "")
      $size = get_image_size($this->picture_path ."thumb/$this->picture", 100, 100);
      
    $mail->CharSet	=	"UTF-8";
    //$mail->From     = $email_answer;
    $mail->From = "auto_blog@fbw-one.com";
    $mail->Sender = "auto_blog@fbw-one.com";
    $mail->FromName = "";
    //$mail->FromName = $sitename;
    //$mail->IsMail();
    $mail->IsSMTP();
    $mail->Host = "ns0.ovh.net";
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Password = "courrendlin1";
    $mail->Username = "blog1%fbw-one.com";
    $mail->PluginDir = "../scripts/";
    $mail->isHTML(true);
    
    /*******************************************************************
     *
     * Post l'article sur le blog Bleu Blog via envoi par e-mail
     * 
     *******************************************************************/
                   
    $subject = stripslashes($this->title);
        
    // contenu html
    $body = "<a href='$site_url/" .stripslashes($this->get_url_simple()) ."' title='" .stripslashes($this->get_url_simple()) ."'>" .stripslashes($this->title) ."</a><br /><br />";
            
            if ($this->picture != "")
              $body .= "<img src='$site_url/pictures/news/thumb/$this->picture' alt=\"image de l'actualité\" title=\"$this->title\" style='border:0;width:" .$size[0] ."px;height:" .$size[1] ."px;float:left;vertical-align:top;margin:0 10px 10px 0' />";
              
    $body .= stripslashes($this->text);    
    
    $mail->Subject = $subject;
    $mail->Body = $body;

    $mail->AddAddress($publish_email_address);
    //$mail->AddAddress("herve@fbw-one.com");
    
    if(!$mail->Send())
      throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);

    $mail->ClearAddresses();
  }
  
  /**
   * Retourne l'url de l'actualité en fonction de son titre.
   * 
   * Cette fonction DOIT être utilisée pour afficher un lien vers l'actualité dans une page web dans le cadre du référencement.
   * Le lien vers la page réelle est gérer via l'Url Rewriting    
   * 
   * @return string L'url de l'actualité en fonction de son titre   
   */   
  public function get_url_simple(){
    $url = "";
    
    //return "index.php?pn=actualite_details&nid=$this->id";
    if ($_SESSION['lang'] == 'fr')
      $url .= 'actualite/' .decode($this->title) .'-' .$this->id;
    elseif ($_SESSION['lang'] == 'de')
      $url .= 'aktuelle/' .decode($this->title) .'-' .$this->id;
    elseif ($_SESSION['lang'] == 'en')
      $url .= 'news/' .decode($this->title) .'-' .$this->id;
      
    return $url;
  }
  
  /**
   * Récupert le nombre total d'actualités
   * 
   * @param int $poster Si défini, ne recherche que les actualités postée par l'id de l'utilisateur (Miki_user) donné
   *    
   * @static
   * @see Miki_user
   * @return int
   */            
  public static function get_nb_news($poster = ""){
    $sql = "SELECT count(*) FROM miki_news";
    
    if ($poster !== "")
      $sql .= " WHERE poster = $poster";
    
    $result = mysql_query($sql);

    $row = mysql_fetch_array($result);
    return $row[0];
  }
  
  /**
   * Recherche toutes les actualités selon certains critères
   * 
   * @param string $search Critère de recherche qui sera appliqué sur les actualités
   * @param string $language Code de la langue dans laquelle on veut rechercher les actualités. Si = "", on recherche dans toutes les langues   
   * @param string $order Par quel champ les actualités trouvées seront triés (title, date, language). Si vide, on tri selon la date.
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)
   * @param int $nb nombre d'actualités à retourner par page. Si = "" on retourne toutes les actualités
   * @param int $page numéro de la page à retourner     
   * 
   * @static
   * @return mixed un tableau d'élément miki_news représentant toutes les actualités trouvées   
   */             
  public static function search($search = "", $language = "", $order = "", $order_type = "asc", $nb = "", $page = 1){
    $return = array();
    $sql = "SELECT DISTINCT(id) 
            FROM miki_news
            WHERE 1";
    
    // Applique les critères de recherche
    if ($search !== ""){
      $search = mb_strtolower($search, 'UTF-8');
      $sql .= sprintf(" AND (LOWER(title) LIKE '%%%s%%'
                          OR LOWER(text) LIKE '%%%s%%')",
                mysql_real_escape_string($search),
                mysql_real_escape_string($search));
    }
    
    // Recherche selon la langue
    if ($language !== ""){
      $language = mb_strtolower($language, 'UTF-8');
      $sql .= sprintf(" AND language = '%s'",
                mysql_real_escape_string($language));
    }
    
    if ($order == "title")
      $sql .= " ORDER BY title " .$order_type;
    elseif ($order == "date")
      $sql .= " ORDER BY date " .$order_type;
    elseif ($order == "language")
      $sql .= " ORDER BY language " .$order_type;
    else
      $sql .= " ORDER BY date " .$order_type;

    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }

    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $return[] = new miki_news($row['id']);
    }
    return $return;
  }

  /**
   * Récupert toutes les news
   * 
   * @param string $language Si défini, ne recherche que les actualités dans le langage dont le code correspondant au code donné
   * @param int $poster Si défini, ne recherche que les actualités postée par l'id de l'utilisateur (Miki_user) donné
   * @param int $nb Si défini, ne récupert que $nb actualités
   * @param int $page Si $nb est défini, une page d'actualités contient $nb actualités et on récupert uniquement la page n° $page
   * 
   * @static
   * @return mixed Un tableau d'éléments Miki_news correspondant aux actualités récupérées selon les critères en paramètre               
   */      
  public static function get_all_news($language = "", $poster = "", $nb = "", $page = 1){
    $return = array();
    $sql = "SELECT * FROM miki_news";
    
    $condition = "";
    
    if ($language !== "")
      $condition = " WHERE language = '$language'";
      
    if ($poster !== ""){
      if ($condition == "")
        $condition = " WHERE poster = $poster";
      else
        $condition .= " AND poster = $poster";
    }
    
    $sql .= $condition;
    
    $sql .= " ORDER BY date DESC";
    
    if ($nb !== "" && is_numeric($nb) && is_numeric($page) && $page > 0){
      $start = ($page - 1) * $nb;
      $sql .= " LIMIT $start, $nb";
    }
    
    $result = mysql_query($sql);

    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_news($row['id']);
    }
    
    return $return;
  }
  
  /**
   * Affiche les détails de l'actualité
   * 
   * Les actualités possèdent un système de Template. Deux templates doivent être défini :  
   *   - Un pour l'affichage des détails de l'actualité
   *   - Un pour l'affichage de la liste des actualités
   *      
   * Les templates sont dans le répertoire "template/news/" du site web.       
   * 
   * @param string $template_name Le nom du template à utiliser (sans l'extension)
   */
  public function print_details($template_name){
    
    // vérifie que le templat demandé existe bien
    if (!file_exists("template/news/" .$template_name .".php")){
      echo "Le template '$template_name' n'existe pas.";
      return "";
    }
    
    $element = $this;
    
    // appel le template donné
    require("template/news/" .$template_name .".php");
  }
   
  /**
   * Affiche la liste des actualités
   * 
   * Les actualités possèdent un système de Template. Deux templates doivent être défini :  
   *   - Un pour l'affichage des détails de l'actualité
   *   - Un pour l'affichage de la liste des actualités
   *      
   * Les templates sont dans le répertoire "template/news/" du site web.       
   * 
   * @param string $page_name Le nom de la page (Miki_page) où sera affichée la liste des actualités
   * @param int $nb_element_by_page Le nombre d'actualités à afficher par page
   * @param string $lang La langue dans laquelle on veut récupérer les actualités   
   * @param int $page_no Le numéro de la page à afficher actuellement (débutant à 1)
   * @param string $template_name Le nom du template à utiliser (sans l'extension)   
   * 
   * @see Miki_page   
   * @static         
   */
  public static function print_all($page_name, $nb_element_by_page, $lang, $page_no, $template_name){
    
    // vérifie que le templat demandé existe bien
    if (!file_exists("template/news/" .$template_name .".php")){
      echo "Le template '$template_name' n'existe pas.";
      return "";
    }
    
    try{
      // récupert toutes les news
      $news = Miki_news::get_all_news($lang);
    }
    catch(Exception $e){
      return false;
    }
    
    if (sizeof($news) == 0){
      echo _("Aucune actualité pour le moment.");
    }
    
    $start = ($page_no-1) * $nb_element_by_page;
    // parcourt les news à afficher
    for ($x=$start; $x < ($nb_element_by_page + $start) && $x < sizeof($news); $x++){
      $element = $news[$x];
      
      // appel le template donné
      require("template/news/" .$template_name .".php");
    }

    // calcul le nombre de pages totales
    $nb_pages = (int)(sizeof($news) / $nb_element_by_page);
    $reste = (sizeof($news) % $nb_element_by_page);
    if ($reste != 0)
      $nb_pages++;
    
    // puis affiche la pagination
    if ($nb_pages > 1){
      echo "<div style='height:30px;text-align:right;padding-right:10px'>";
      
      if ($page_no != 1)
        echo "<a href='[miki_page='$page_name' params='p=1']' title='première page'><<</a>&nbsp;&nbsp;<a href='[miki_page='$page_name' params='p=" .($page_no-1) ."']' title='page précédente'><</a>&nbsp;&nbsp;";
      
      if ($nb_pages <= 12){
        for ($x=1; $x<=$nb_pages; $x++){
          if ($x == $page_no)
            echo "<span style='font-weight:bold'>$x</span> | ";
          else
            echo "<a href='[miki_page='$page_name' params='p=$x']'>$x</a> | ";             
        }
      }
      elseif ($page_no == $nb_pages){
        for ($x=($page_no-12); $x<=$page_no; $x++){
          if ($x == $page_no)
            echo "<span style='font-weight:bold'>$x</span> | ";
          else
            echo "<a href='[miki_page='$page_name' params='p=$x']'>$x</a> | ";             
        }
      }
      elseif ($page_no == 1){
        for ($x=$page_no; $x<=($page_no+12); $x++){
          if ($x == $page_no)
            echo "<span style='font-weight:bold'>$x</span> | ";
          else
            echo "<a href='[miki_page='$page_name' params='p=$x']'>$x</a> | ";             
        }
      }
      elseif ($page_no != 1){
        $start = $page_no - 6;
        if ($start < 1)
        $start = 1;
        $stop = $start + 12;
        for ($x=$start; $x<=$stop; $x++){
          if ($x == $page_no)
            echo "<span style='font-weight:bold'>$x</span> | ";
          else
            echo "<a href='[miki_page='$page_name' params='p=$x']'>$x</a> | ";             
        }
      }
      
      if ($page_no != $nb_pages)
        echo "&nbsp;&nbsp;<a href='[miki_page='$page_name' params='p=" .($page_no+1) ."']' title='page suivante'>></a>&nbsp;&nbsp;<a href='[miki_page='$page_name' params='p=$nb_pages']' title='dernière page'>>></a>";
      
      echo "</div>";
    }
  }
}
?>