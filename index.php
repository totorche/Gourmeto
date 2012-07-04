<?php
  header('Content-type: text/html; charset=UTF-8'); 
    
  // récupert et insère les menus et leur feuille de style dans le template
  function get_menus(&$code, &$stylesheet){
    // recherche les balises de menu
    $start = mb_strpos($code,"[miki_menu='");
    while ($start){
      // récupert la balise complète
      $stop = mb_strpos($code, "'", $start + 12);
      $data = mb_substr($code, $start, $stop - ($start-2));
      // puis uniquement le nom du menu
      $start_menu = mb_strpos($data, "'") + 1;
      $stop_menu = mb_strpos($data, "'", $start_menu);
      $name_menu = mb_substr($data, $start_menu, $stop_menu - $start_menu);
      
      try{
        // récupert le contenu du menu
        $menu = new Miki_menu();
        $menu->load_from_name($name_menu);
        $code = str_replace($data, $menu->content, $code);
        // ajoute la feuille de style du menu à la feuille de style générale
        if (!empty($menu->stylesheet_id)){
          $sh = new Miki_stylesheet($menu->stylesheet_id);
          $stylesheet .= "\n/* Stylesheet menu '$menu->name' */\n$sh->content\n";
        }
      }
      catch(Exception $e){}
      $start = mb_strpos($code,"[miki_menu='");
    }
  }
  
  // récupert et insère les blocs de contenu globaux dans le template
  function get_global_contents(&$code, $lang){
    // recherche les balises de menu
    $start = mb_strpos($code,"[miki_gc='");
    while ($start){
      // récupert la balise complète
      $stop = mb_strpos($code, "'", $start + 10);
      $data = mb_substr($code, $start, $stop - ($start-2));
      // puis uniquement le nom du bloc de contenu global
      $start_gc = strpos($data, "'") + 1;
      $stop_gc = mb_strpos($data, "'", $start_gc);
      $name_gc = mb_substr($data, $start_gc, $stop_gc - $start_gc);

      try{
        // récupert le contenu du menu
        $gc = new Miki_global_content();
        $gc->load_from_name($name_gc);
        $content = $gc->get_contents($lang);
        $content = current($content);
        $code_content = "";
        // récupert le contenu
        if ($content && $content->content_type == "code"){
          $code_content = $content->content;
        }
        elseif ($content && $content->content_type == "file"){
          if (file_exists("box/" .$content->content)){
            $code_content = file_get_contents("box/" .$content->content);
          }
          else{
            $code_content = "Le fichier '$content->content' n'existe pas";
          }
        }
        
        // exécute le code PHP s'il y en a
        ob_start();
        eval("?>" .$code_content); 
        $code_content = ob_get_contents();
        ob_end_clean();
      }
      catch(Exception $e){
        $code_content = _("Le contenu global $name_gc n'existe pas");
      }
      $code = str_replace($data, $code_content, $code);
      $start = mb_strpos($code,"[miki_gc='");
    }
  }
  
  // récupert et insère les sections et leurs blocs de contenu dans le contenu du gabarit
  function get_sections($page, $template, $lang){
    $parts = array();
    // récupert les sections
    $template_parts = $template->get_parts();
    
    // fonction pour le tri du tableau
    function cmp($a, $b)
    {
      // récupert la position des blocs
      $val_a = explode("&&", $a);
      $val_b = explode("&&", $b);
      $val_a = $val_a[0];
      $val_b = $val_b[0];
      
      if ($val_a == $val_b) {
        return 0;
      }
      return ($val_a < $val_b) ? -1 : 1;
    }
    
    // parcourt chaque section
    foreach($template_parts as $part){
      // récupert les blocs de contenu globaux de la section en cours
      $tab_gc = array();
      $code_content = "";
      $gcs = $part->get_global_contents();
      foreach($gcs as $gc){
        // test si la page affiche ce bloc de contenu
        if ($page->has_global_content($gc)){
          $position = $gc->get_position($page->id);
          $tab_gc[] = "$position&&$gc->id";
        }
      }
      
      // trie le tableau des blocs récupérés
      usort($tab_gc, "cmp");
      
      foreach($tab_gc as $el){
        // récupert le contenu du bloc
        $gc = explode("&&", $el);
        $gc = new Miki_global_content($gc[1]);
        
        $content = $gc->get_contents($lang);
        $content = current($content);
        // récupert le contenu et l'ajoute au contenu général de la section
        if ($content && $content->content_type == "code"){
          $code_content .= $content->content;
        }
        elseif ($content && $content->content_type == "file"){
          if (file_exists("box/" .$content->content)){
            $code_content .= file_get_contents("box/" .$content->content);
          }
          else{
            $code_content .= "Le fichier '$content->content' n'existe pas";
          }
        }
      }
      
      // intègre les blocs de contenu à la section en cours
      $code_part = $part->content;
      $code_part = str_replace("[miki_part_content]", $code_content, $code_part);
      $parts[$part->name] = $code_part;
    }
      
    // ajoute les sections dans le gabarit    
    foreach($parts as $name => $content){
      $search = "[miki_part='$name']";
      $template->content = str_replace($search, $content, $template->content);
    }
  }
  
  // inclus la fonction pour la traduction des liens 'miki' en liens réels
  require_once("include/headers.php");
  require_once("set_links.php");
  
  // récupert le contenu du gabarit soit depuis le fichier soit directement depuis le code
  function get_template_content(&$template){
    if ($template->content_type == "code"){
      $code = $template->content;
    }
    elseif (file_exists("template/$template->content")){
      $code = file_get_contents("template/$template->content");
    }
    else{
      $code = "Le fichier '$template->content' n'existe pas";
    }
    
    $template->content = $code;
  }
  
  // récupert le contenu de la page soit depuis le fichier soit directement depuis le code
  function get_page_content(&$content){
    if ($content->content_type == "code"){
      $code = $content->content;
    }
    elseif ($content->content_type == "file" && file_exists($content->content)){
      $code = file_get_contents($content->content);
    }
    elseif ($content->content_type == "url"){
      miki_redirect($content->content);
    }
    else{
      $code = "Le fichier '$content->content' n'existe pas";
    }
    
    $content->content = $code;
  }
  
  // récupert et retourne les en-têtes de la page et les enlève de la page
  function get_headers_from_page(&$content){
  
    $pattern = "@<head>(.*)</head>@is";
    $results = "";
  
    if (preg_match($pattern, $content, $results)){
      $content = preg_replace($pattern, "", $content);
      return $results[1];
    }
    
    return "";
  }
  
  // récupert et retourne le titre de la page et les enlève de la page
  function get_title_from_page($code){
    $pattern1 = "@<title2>(.*)</title2>@is";
    $pattern2 = '@<title>(.*)</title>@i';
    $results = "";
  
    if (preg_match($pattern1, $code, $results)){
      $new = '<title>' .$results[1] .'</title>';
      $code = preg_replace($pattern1, "", $code);
      $code = preg_replace($pattern2, $new, $code);
    }
    
    return $code;
  }
  
  // récupert et retourne la description de la page et les enlève de la page
  function get_description_from_page($code){
    $pattern1 = "@<description>(.*)</description>@is";
    $pattern2 = '@<meta name="Description" lang="fr" content="(.*)" />@i';
    $results = "";
  
    if (preg_match($pattern1, $code, $results)){
      $new = '<meta name="Description" lang="fr" content="' .$results[1] .'" />';
      $code = preg_replace($pattern1, "", $code);
      $code = preg_replace($pattern2, $new, $code);
    }
    return $code;
  }
  
  
  // functions nécessaires à la fonction "get_style_from_page" ci-dessous
  function replace_style1_function($matches){
    global $style_page;
    
    $val = $matches[1];
    $style_page .= $matches[1];
    return "";
  }
  
  function replace_style2_function($matches){
    global $style2_page;

    $val = $matches[1];
    $style2_page[] = $matches[1];
    return "";
  }
  
  // récupert et retourne les en-têtes de la page et les enlève de la page
  function get_style_from_page(&$code){
    // recherche les styles dans la page
    $pattern = "/(?:<style.*?>)((\n|\r|.)*?)(?:<\/style>)/";
    $code = preg_replace_callback($pattern, "replace_style1_function", $code);
    
    // recherche les inclusions de styles dans la page du genre : <link rel="stylesheet" href="css/objects.css" />
    $pattern = "/(?:<link rel=[',\"]stylesheet[',\"] href=[',\"])(.*)([',\"].*\/>)/";
    $code = preg_replace_callback($pattern, "replace_style2_function", $code);
  }
  
  if (isset($_GET['preview']) && $_GET['preview'] == 1 && isset($_GET['template']) && is_numeric($_GET['template'])){
    $preview = true;
    
    $page = new Miki_page();
    $page->template_id = $_GET['template'];
    
    $page_content = new Miki_page_content();
    $page_content->content_type = 'code';
    $page_content->content = "<div id='miki_preview_content'></div>";
  }
  else{
    $preview = false;
    
    // si aucune page n'est donnée en paramètre
    if (!isset($_GET['pn']) || $_GET['pn'] == ""){
      // Recherche la page par défaut
      $page = Miki_page::get_default();
  
      if ($page === false)
        exit(_("Aucune page n'a été spécifiée"));
    }
    else{
      // récupert la page donnée en paramètres
      try{
        $page = new Miki_page();
        $page->load_from_name($_GET['pn']);
      }catch(Exception $e){
        $page = Miki_page::get_default();
        
        if ($page === false)
          exit(_("Aucune page n'a été spécifiée"));
      }
    }
  }
  
  
  // récupert les langues préférées du visiteur
  $pref_langs = get_pref_language_array();
  $lang = "";
  
  if (isset($_GET['l'])){
    if (Miki_language::exist($_GET['l'])){
      $lang = $_GET['l'];
      // stock la langue dans la session
      $_SESSION['lang'] = $lang;
    }
  }
  elseif (isset($_SESSION['lang']) && Miki_language::exist($_SESSION['lang'])){
    $lang = $_SESSION['lang'];
  }
  else{
    // recherche la langue à afficher (d'après les langues préférées)  
    $x = 0;
    while (!Miki_language::exist($lang) && $x < sizeof($pref_langs)){
      $lang = strtolower($pref_langs[$x]);
      $x++;
    }

    // si aucune langue n'a été trouvée, on prend la langue principale
    if (!Miki_language::exist($lang)){
      $lang = Miki_language::get_main_code();
    }
    
    // stock la langue dans la session
    $_SESSION['lang'] = $lang;
  }

  // gère les traductions  
  bindtextdomain("messages", URL_BASE ."locales");
  bind_textdomain_codeset("messages", "UTF-8");
  textdomain("messages");
  
  if ($_SESSION['lang'] == 'fr'){
    $_SESSION['lang_comp'] = 'fr_FR';
    setlocale(LC_ALL, 'fr_FR');
  }
  elseif ($_SESSION['lang'] == 'de'){
    $_SESSION['lang_comp'] = 'de_DE';
    setlocale(LC_ALL, 'de_DE');
  }
  elseif ($_SESSION['lang'] == 'en'){
    $_SESSION['lang_comp'] = 'en_US';
    setlocale(LC_ALL, 'en_US');
  }
  else{
    $_SESSION['lang_comp'] = 'fr_FR';
    setlocale(LC_ALL, 'fr_FR');
  }
  
  // teste si la page a le droit d'être affichée
  if (!test_right_pages($page)){
    $_SESSION['last_url'] = $_SERVER["REQUEST_URI"];
    
    // sinon, on affiche de login
    $page->load_from_name('login');
    miki_redirect($page->get_url_simple($lang));
  }
  
  if ($preview){
    $content = $page_content;
  }
  else{
    // récupert le contenu de la page dans la langue désirée
    if ($page->get_contents($lang)){
      $content = $page->get_contents($lang);
      $content = current($content);
    }
    else{
      // si la langue demandée n'existe pas, on prend la première langue disponible
      $contents = $page->get_contents();
      $content = $contents[0];
    }
  }

  $template = new Miki_template($page->template_id);
  $sh = new Miki_stylesheet($template->stylesheet_id);

  // récupert la feuille de style  
  $stylesheet = $sh->content ."\n";
  // évalue le code du template (exécute le php s'il y en a)
  ob_start();
  eval("?>" .$template->content); 
  $template->content = ob_get_contents();
  ob_end_clean();
  get_template_content($template);
  

  // récupert le contenu de la page soit depuis le fichier soit directement depuis le code
  get_page_content($content);

  // récupert les en-têtes éventuels dans le template
  $headers = get_headers_from_page($template->content);
  
  // récupert les en-têtes éventuels dans le contenu de la page
  $headers .= get_headers_from_page($content->content);
  
  // récupert les menus
  get_menus($template->content, $stylesheet);
  
  // récupert les sections et leurs blocs de contenu globaux
  get_sections($page, $template, $lang);
  
  // récupert les différent style CSS de la page pour les mettre dans les en-têtes
  $style_page = "";
  $style2_page = array();
  get_style_from_page($template->content);
  get_style_from_page($content->content);

  $var_temp = "";
  foreach($style2_page as $style2){
    $var_temp .= "<link rel='stylesheet' href='$style2' />\r\n";
  }
  $style2_page = $var_temp;
  

  // ajoute le code Google Analytics si la page le demande
  if ($page->analytics == 1){
    $id_account_analytics = Miki_configuration::get('analytics');
    $domain = Miki_configuration::get('site_url');
    if (mb_substr($domain, 0, 7) == 'http://')
      $domain = mb_substr($domain, 7);
    elseif (mb_substr($domain, 0, 8) == 'https://')
      $domain = mb_substr($domain, 8);
  
    $template->content = "\n\n
        <!-- Début du code Google Analytics -->
        <script type=\"text/javascript\">
          
          // à modifier
          var id_account_analytics = '$id_account_analytics';
          
          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', id_account_analytics]);
          _gaq.push(['_setDomainName', '$domain']);
          _gaq.push(['_trackPageview']);
        
          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();
        
        </script>
        <!-- Fin du code Google Analytics -->" .$template->content;
  }
  
  // préparation de la page
  $code = "
    <!DOCTYPE html>
    <html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"$lang\" lang=\"$lang\">
      <head>
        <meta http-equiv=\"content-Type\" content=\"text/html; charset=utf-8\" />
        <title>" .htmlspecialchars($content->title) ."</title>
        <meta name=\"Description\" lang=\"$lang\" content=\"$content->description\" />
        <meta name=\"Keywords\" lang=\"$lang\" content=\"$content->keywords\" />
        <meta name=\"verify-v1\" content=\"k+OEULs2fxfQu/n2BYpOOU9lVqhWCzEYcdvfYZhhP3M=\" />";
        
        $site_url = SITE_URL;
        if (mb_substr(SITE_URL, -1) != '/')
          $site_url = SITE_URL .'/';
        
        $code .= "\n\n<base href='$site_url' />
        
        <link rel=\"SHORTCUT ICON\" href=\"favicon.ico\" />
        <link rel=\"icon\" type=\"image/png\" href=\"favicon.png\" />
        <!--<link rel=\"icon\" href=\"favicon.ico\" />-->";
        
        // ajoute les liens vers les versions de la page dans les autres langues pour la gestion du multilingue (et surtout le SEO)
        $languages = Miki_language::get_all_languages();
        foreach($languages as $language){
          if ($language->code != $_SESSION['lang'])
            $code .= "\n\n<link rel='alternate' hreflang='$language->code' href='" .$page->get_url_simple($language->code) ."' />\n";
        }
        
        // inclusion des javascript nécessaire pour la preview depuis le Miki
        if ($preview){
          $code .= "<script type=\"text/javascript\" src=\"../../tiny_mce_popup.js\"></script>
          <script type=\"text/javascript\" src=\"jscripts/embed.js\"></script>";
	      }
        
        $code .= "
        <script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js\" type=\"text/javascript\"></script>
        <script src=\"scripts/jquery.defaultvalue.js\" type=\"text/javascript\"></script>
        
        <!--<script type=\"text/javascript\" src=\"" .URL_BASE ."scripts/adapt.min.js\"></script>-->
        
        <!-- Inclusion du système 960 Grid -->
        <link rel=\"stylesheet\" href=\"css/reset.css\" />
        <link rel=\"stylesheet\" href=\"css/text.css\" />
        <link rel=\"stylesheet\" href=\"css/960.css\" />
        
        $content->metas
        
        <style type=\"text/css\">
          $stylesheet
          
          $style_page
        </style>
        
        $style2_page
        
        <!--[if IE]>
        <script type=\"text/javascript\">
          (function(){
            var html5elmeents = \"address|article|aside|audio|canvas|command|datalist|details|dialog|figure|figcaption|footer|header|hgroup|keygen|mark|meter|menu|nav|progress|ruby|section|time|video\".split(\"|\");
      
            for(var i = 0; i < html5elmeents.length; i++){
              document.createElement(html5elmeents[i]);
            }
      		})();
        </script>
        <![endif]-->
        
        <script type=\"text/javascript\">
      		// détection d'un navigateur ancien
          if($.browser.msie && ($.browser.version==7.0)) {";
            
        		if (!isset($_COOKIE['old_browser']) || $_COOKIE['old_browser'] != 1){
    $code .= "alert(\"Attention : \\r\\n\\r\\nCe site Internet est optimisé pour des navigateurs récents. Votre navigateur Internet n'est pas à jour. Un navigateur ancien est également une porte ouverte aux virus.\\r\\n\\r\\nVous pouvez télécharger la dernière version d'Internet Explorer en vous rendant sur cette page : http://windows.microsoft.com/fr-CH/internet-explorer/products/ie/home\ \\r\\n\\r\\nConseils et astuces : \\r\\nNous vous recommandons d'utiliser un navigateur compatible, rapide et sécurisé tel que Firefox ou Google Chrome\");
      		    document.cookie = \"old_browser=1\";"; 
      		  }
$code .= "}
        </script>
        
        $headers

      </head>
      <body>
      
        $template->content
        
        ";
        if ($content->noembed != ""){
          $code .= "<noembed>
                      $content->noembed
                    </noembed>";
        }
        
        $code .= "
      </body>
    </html>";
  
  // ajoute le contenu dans la page
  $code = str_replace("[miki_content]", $content->content, $code);  
  
  // traduction des liens 'miki'
  set_links($code, $lang); 
  
  // évalue le code final (exécute le php s'il y en a)
  ob_start();
  eval("?>" .$code); 
  $code = ob_get_contents();
  ob_end_clean();
  
  
  get_global_contents($code, $lang);
  
  
  // traduction des liens 'miki'
  set_links($code, $lang); 
  
  // récupert le titre éventuel dans le contenu de la page
  $code = get_title_from_page($code);
  
  // récupert la description éventuel dans le contenu de la page
  $code = get_description_from_page($code);
  
  $code = str_replace("&lt;/textarea&gt;","</textarea>",$code);
  
  // affiche la page
  echo $code;  
  
  
  // stock l'url actuel pour un retour après opérations
  if ($page->name != 404)
    $_SESSION['url_back'] = get_actual_url();
?>