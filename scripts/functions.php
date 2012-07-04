<?php
  
  /**
   * Fonction de redirection.
   * 
   * Redirige l'utilisateur vers la page donnée de façon correcte.
   * 
   * @param string $page La page vers laquelle on veut rediriger l'utilisateur    
   */ 
  function miki_redirect($page){
    if (!headers_sent()){
      header("Location: $page");
      exit();
    }
    else{
      echo "<script type='text/javascript'>document.location='$page';</script>";
      exit();
    }
  }
  
  /**
   * Vérifie si l'utilisateur est connecté (via la session ou un cookie)
   * 
   * @return TRUE si connecé, FALSE sinon      
   */   
  function is_connected(){
    if (isset($_SESSION['miki_user_id']) && is_numeric($_SESSION['miki_user_id'])){
      try{
        $person = new Miki_person($_SESSION['miki_user_id']);
        return $person;
      }
      catch(Exception $e){
        return false;
      }
    }
    elseif (isset($_COOKIE['miki_user_id']) && is_numeric($_COOKIE['miki_user_id'])){
      try{
        $person = new Miki_person($_COOKIE['miki_user_id']);
        $_SESSION['miki_user_id'] = $_COOKIE['miki_user_id'];
        return $person;
      }
      catch(Exception $e){
        return false;
      }
    }
    else{
      return false;
    }
  }
  
  /**
   * Teste si la page donnée (Miki_page) peut être affichée en fonction des droits spécifiés
   * 
   * @see Miki_page
   * @param Miki_page $page La page pour laquelle on veut savoir si elle peut être affichée ou pas en fonction des droits du visiteur
   * @return TRUE si la page peut être affichée, FALSE sinon.         
   */   
  function test_right_pages($page){
    
    // utilisateur connecté
    if ($page->login == 1){
      
      // vérifier si l'utilisateur est connecté
      $person = is_connected();
      if ($person === false){
        return false;
      }
      
      // teste si le compte est activé
      $account = new Miki_account();
      $account->load_from_person($person->id);
      
      if ($account->state == 1)
        return true;
      else
        return false;
    }
    else{
      return true;
    }
  }
  
  /**
   * Retourne une chaîne tronquée selon les paramètres donnés
   * 
   * @param string $text Le texte à tronquer
   * @param int $nb_char Nombre de caractères désirés
   * @param boolean $full_word Si TRUE, le texte est coupé par mots entiers. Si FALSE, les mots peuvent être coupé en plein milieu. True par défaut.
   * @param boolean $strip_tags Si TRUE, supprime au préalable tous les tags HTML. Si False, conserve tout. True par défaut. 
   *   
   * @return string Le texte tronqué
   */               
  function truncate_text($text, $nb_char, $full_word = true, $strip_tags = true){
    
    if ($strip_tags)
      $text = strip_tags($text);
      
    if ($nb_char < mb_strlen($text)){
      if ($full_word === false)
        $stop = $nb_char;
      else{
        $stop = mb_strpos($text, ' ', $nb_char);
        if ($stop === false){
          $stop = mb_strlen($text);
        }
      }
      return mb_substr($text, 0, $stop) ."...";
    }
    else
      return $text;
  }
  
  /**
   * Récupert la lattitude et la longitude d'après une adresse
   * 
   * @param string $address L'adresse dont on veut récupérer la lattitude et la longitude
   * 
   * @return mixed Un tableau contenant la latitude et la longitude ou FALSE si un erreur suvient
   */     
  function get_coordonate_from_address($address){
    if (in_array('curl', get_loaded_extensions())){
      try{
        $address = urlencode($address);
        $url = "http://maps.googleapis.com/maps/api/geocode/json?sensor=true&address=$address";
        
        //$postdata='url=http://' .$_SERVER['HTTP_HOST'] .$_SERVER['REQUEST_URI'];
        $ch1 = curl_init($url);
        curl_setopt ($ch1, CURLOPT_VERBOSE, 2);
        curl_setopt ($ch1, CURLOPT_ENCODING, 0);
        curl_setopt ($ch1, CURLOPT_USERAGENT, 'Mozilla/5.0');
        curl_setopt ($ch1, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch1, CURLOPT_FAILONERROR, 1);
        curl_setopt ($ch1, CURLOPT_HEADER, 0);
        curl_setopt ($ch1, CURLINFO_HEADER_OUT, 0);
        curl_setopt ($ch1, CURLOPT_CONNECTTIMEOUT, 30);
        $r=curl_exec ($ch1);
        $ch1_info=curl_getinfo($ch1);
        if (curl_errno($ch1)) return false;
        else curl_close($ch1);
  
        $r = json_decode($r, true);
        $return = array();
        $return['latitude'] = $r['results'][0]['geometry']['location']['lat'];
        $return['longitude'] = $r['results'][0]['geometry']['location']['lng'];
        return $return;
      }
      catch(Exception $e){
        return false;
      }
    }
    else{
      return false;
    }
  }
  
  /**
   * Envoie un message de résultat
   * 
   * @param boolean $success Si le message est un succès (TRUE) ou une erreur (FALSE)
   * @param string $message Le message à envoyer
   * @param boolean $ajax Si on utilise Ajax ou non (FALSE par défaut)
   * @param string $url La page à laquelle on doit communiquer le message. Si vide (valeur par défaut), envoie à la page précédente ($_SESSION['url_back'])
   * @param boolean $stop Si on arrête le traitement de la page une fois le message envoyé (TRUE par défaut)   
   * 
   * @return boolean TRUE si le message a été transmis, FALSE sinon                     
   */
  function send_result($success, $message, $ajax = false, $url = "", $stop = true){
    
    // vérifie le type de message
    if ($success)
      $result = 1;
     else
      $result = 0;
      
    // vérifie l'url de retour
    if ($url == "")
      $url = $_SESSION['url_back'];
    
    // Si on utilise Ajax, on imprime simplement le message
    if ($ajax){
      echo "<div>
              <div id='result'>$result</div>
              <div id='msg'>$message</div>
            </div>";
      
      // arrête le traiement si demandé
      if ($stop)
        exit();
      else
        return true;
    }
    // Si on utilise pas Ajax, on appelle la page de destination avec le message en paramètre
    else{
      // dissèque l'url de destination
      $url_tab = explode("?", $url);
      
      // vérifie les paramètres et supprime les paramètres "result" et "msg" si ils sont déjà présents
      $param_tab = explode("&", $url_tab[1]);
      foreach($param_tab as $index => $p){
        $p_tab = explode("=", $p);
        if ($p_tab[0] == "result" || $p_tab[0] == "msg")
          unset($param_tab[$index]);
      }
      $url_tab[1] = implode("&", $param_tab);
      
      // reforme l'url en incorporant le résultat et le message
      if (sizeof($url_tab) == 1 || 
          (sizeof($url_tab) == 2 && empty($url_tab[1]))){
        
        $url = $url_tab[0] ."?result=$result&msg=" .urlencode($message);
        
        // redirige vers l'url demandée
        miki_redirect($url);
      }
      elseif (sizeof($url_tab) == 2){
        $url = $url_tab[0] .'?' .$url_tab[1] ."&result=$result&msg=" .urlencode($message);
        // redirige vers l'url demandée
        miki_redirect($url);
      }
      else{
        // arrête le traiement si demandé
        if ($stop)
          exit();
        else
          return false;
      }
    }    
  }
  
  /**
   * Affiche le message de résultat
   */     
  function print_results(){
    if (isset($_GET['result']) && $_GET['result'] == 1)
      echo "<div id='form_results_success' class='box_result_success'><p>" .urldecode(stripslashes($_GET['msg'])) ."</p></div>";
    else
      echo "<div id='form_results_success' class='box_result_success' style='display: none;'><p></p></div>";
      
    if (isset($_GET['result']) && $_GET['result'] == 0)
      echo "<div id='form_results_error' class='box_result_error'><p>" .urldecode(stripslashes($_GET['msg'])) ."</p></div>";
    else
      echo "<div id='form_results_error' class='box_result_error' style='display: none;'><p></p></div>";
  }
  
  /**
   * Retourne l'url actuel avec tous les paramètres
   */     
  function get_actual_url(){
    // Pour Unix
    if (isset($_SERVER["SCRIPT_URI"])){
      $url = $_SERVER["SCRIPT_URI"];
      
      if (isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] != "")
        $url .= '?' .$_SERVER["QUERY_STRING"];
    }
    // Pour Windows
    else{
      $url = "http://" .$_SERVER["HTTP_HOST"] .$_SERVER["REQUEST_URI"];
    }
    
    return $url;
  }
  
  /**
   * Génère un fil d'arianne à partir d'une catégorie donnée
   * 
   * @param int $cat_id Id de la catégorie à partir de laquelle on doit générer le fil d'arianne
   * @param string $sep Le séparateur des éléments du fil d'arianne (' - ' par défaut)   
   * 
   * @return string Le fil d'arianne généré
   */     
  function print_breadcrumb_from_category($cat_id, $sep = " - "){
    $breadcrumb = "";
    
    if (strpos($sep, '.')){
      $ext = pathinfo($sep, PATHINFO_EXTENSION);
      if (in_array($ext, array('jpg', 'jpeg', 'gif', 'png', 'bmp')))
        $sep = "<img src='pictures/$sep' alt=' - ' style='margin: 0 10px; vertical-align: middle;' />";
    }
    
    if ($cat_id != ""){
      try{
        $cat = new Miki_shop_article_category($cat_id);
        $parents = $cat->get_parents();
        $breadcrumb = "<span class='breadcrumb_last'>" .$cat->name[$_SESSION['lang']] ."</span>";
        foreach($parents as $p){
          $breadcrumb = "<a href='[miki_page='shop_articles_list' params='cat=$p->id']' class='breadcrumb_link' title=\"" ._("Retourner sur la catégorie") ." " .$p->name[$_SESSION['lang']] ."\">" .$p->name[$_SESSION['lang']] ."</a>" .$sep .$breadcrumb;
        }
      }
      catch(Exception $e){
        return "";
      }
    
      $breadcrumb = "<div class='breadcrumb'>
                       <a href='" .SITE_URL ."' title=\"" ._("Retourner à la page d'accueil") ."\"><img src='pictures/breadcrumb-home.png' alt='Home' style='height: 30px; vertical-align: middle;' /></a>"
                       .$sep
                     ."<a href='[miki_page='shop_articles_list']' class='breadcrumb_link' title=\"" ._("Toutes les catégories") ."\">" ._("Toutes les catégories") ."</a>" 
                       .$sep 
                       .$breadcrumb
                    ."</div>";
    }
    else{
      $breadcrumb = "<div class='breadcrumb'>
                       <a href='" .SITE_URL ."' title=\"" ._("Retourner à la page d'accueil") ."\"><img src='pictures/breadcrumb-home.png' alt='Home' style='height: 30px; vertical-align: middle;' /></a>"
                       .$sep
                     ."<span class='breadcrumb_last'>" ._("Toutes les catégories") ."</span>" 
                    ."</div>";
    }
    
    return $breadcrumb;
  }
  
  /**
   * Génère un fil d'arianne à partir d'un article donné
   * 
   * @param int $article_id Id de l'article à partir duquel on doit générer le fil d'arianne
   * @param string $sep Le séparateur des éléments du fil d'arianne (' - ' par défaut). Si un fichier est donné, une image sera affichée (emplacement : pictures/)   
   * 
   * @return string Le fil d'arianne généré
   */     
  function print_breadcrumb_from_article($article_id, $sep = " - "){
  
    $breadcrumb = "";
    
    if (strpos($sep, '.')){
      $ext = pathinfo($sep, PATHINFO_EXTENSION);
      if (in_array($ext, array('jpg', 'jpeg', 'gif', 'png', 'bmp')))
        $sep = "<img src='pictures/$sep' alt=' - ' style='margin: 0 10px; vertical-align: middle;' />";
    }
    
    try{
      $article = new Miki_shop_article($article_id);
      $cat = new Miki_shop_article_category($article->id_category);
      $parents = $cat->get_parents();
      //$breadcrumb = "<span class='breadcrumb_last'>" .$cat->name[$_SESSION['lang']] ."</span>";
      $breadcrumb = "<a href='[miki_page='shop_articles_list' params='cat=$cat->id']' class='breadcrumb_link' title=\"" ._("Retourner sur la catégorie") ." " .$cat->name[$_SESSION['lang']] ."\">" .$cat->name[$_SESSION['lang']] ."</a>" 
                     .$sep 
                     .$breadcrumb
                   ."<span class='breadcrumb_last'>" .$article->get_name($_SESSION['lang']) ."</span>";
      foreach($parents as $p){
        $breadcrumb = "<a href='[miki_page='shop_articles_list' params='cat=$p->id']' class='breadcrumb_link' title=\"" ._("Retourner sur la catégorie") ." " .$p->name[$_SESSION['lang']] ."\">" .$p->name[$_SESSION['lang']] ."</a>" .$sep .$breadcrumb;
      }
    }
    catch(Exception $e){
      return "";
    }
  
    $breadcrumb = "<div class='breadcrumb'>
                     <a href='" .SITE_URL ."' title=\"" ._("Retourner à la page d'accueil") ."\"><img src='pictures/breadcrumb-home.png' alt='Home' style='height: 30px; vertical-align: middle;' /></a>"
                     .$sep
                   ."<a href='[miki_page='shop_articles_list']' class='breadcrumb_link' title=\"" ._("Toutes les catégories") ."\">" ._("Toutes les catégories") ."</a>" 
                     .$sep 
                     .$breadcrumb
                  ."</div>";
    
    return $breadcrumb;
  
    /*$breadcrumb = "";
    try{
      $article = new Miki_shop_article($article_id);
      $cat = new Miki_shop_article_category($article->id_category);
      $parents = $cat->get_parents();
      
      if (strpos($sep, '.')){
        $ext = pathinfo($sep, PATHINFO_EXTENSION);
        if (in_array($ext, array('jpg', 'jpeg', 'gif', 'png', 'bmp')))
          $sep = "<img src='pictures/$sep' alt=' - ' />";
      }
      
      $breadcrumb = "<span class='breadcrumb_last'>" .$article->get_name($_SESSION['lang']) ."</span>";
      foreach($parents as $p){
        $breadcrumb = "<a href='[miki_page='shop_articles_list' params='cat=$p->id']' class='breadcrumb_link' title=\"" ._("Retourner sur la catégorie") ." " .$p->name[$_SESSION['lang']] ."\">" .$p->name[$_SESSION['lang']] ."</a>" .$sep .$breadcrumb;
      }
    }
    catch(Exception $e){
      return "";
    }
    
    return "<div class='breadcrumb'>" ._("Vous êtes ici") ." : " ."<a href='" .SITE_URL ."' title=\"" ._("Retourner à la page d'accueil") ."\"><img src='pictures/breadcrumb-home.png' alt='Home' /></a>" .$sep .$breadcrumb ."</div>";*/
  }
?>