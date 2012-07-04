<?php 
  require_once("include/headers.php");
  require_once("functions_pictures.php");
  
  if (!test_right(7)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['name']) && !isset($_POST['content'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // vérifie si l'utilisateur est membre du groupe SEO
  $user = new Miki_user($_SESSION['miki_admin_user_id']);
  $is_seo = $user->has_group(new Miki_group(2));
  
  // sauve la page
  try{
    $page = new Miki_page(); 
    $page->name = decode($_POST['name']);
    $page->parent_id = $_POST['parent'] == -1 ? 'NULL' : $_POST['parent'];
    $page->template_id = $_POST['template'] == "" ? 'NULL' : $_POST['template'];
    $page->login = $_POST['login'];
    $page->state = (isset($_POST['active']) && $_POST['active'] == 1) ? 1:0;
    $page->menu = (isset($_POST['menu']) && $_POST['menu'] == 1) ? 1:0;
    $page->analytics = 1;
    $page->save();
    
    $languages = Miki_language::get_all_languages(); 
    foreach ($languages as $l){
      $content = new Miki_page_content(); 
      $content->page_id = $page->id;
      $content->language_id = $l->id;
      $content->category_id = 'NULL';
      
      // ici on vérifie si les données ont été fournies car si on est pas membre du groupe SEO, ces données ne sont pas renseignées
      if ($is_seo){
        $content->title = $_POST["title_$l->code"];
        $content->description = $_POST["description_$l->code"];
        $content->keywords = $_POST["keywords_$l->code"];
        $content->metas = $_POST["metas_$l->code"];
        $content->alias = trim(decode($_POST["alias_$l->code"]));
        $content->category_id = $_POST["category_$l->code"] == "" ? 'NULL' : $_POST["category_$l->code"];
      }
        
      $content->menu_text = stripslashes(htmlspecialchars($_POST["menu_text_$l->code"]));
      $content->noembed = stripslashes($_POST["noembed_$l->code"]);
      $content->content_type = $_POST["content_type_$l->code"];
      
      if ($content->content_type == 'file'){
        $content->content = $_FILES["file_$l->code"]['name'];
      }
      elseif ($content->content_type == 'code'){
        $content->content = stripslashes($_POST["code_$l->code"]);
        $content->content = str_replace('</textarea>', htmlentities('</textarea>'), $content->content);
      }
      elseif ($content->content_type == 'url'){
        $content->content = stripslashes($_POST["url_$l->code"]);
      }
      $content->save();
    }
    
    // ajoute les blocs de contenu global sélectionnés à la page
    $template = new Miki_template($page->template_id);
    $parts = $template->get_parts(); 
    
    // récupert l'ordre des blocs de contenu global
    $sort = $_POST['global_content_sort'];
    $sort = explode("&", $sort);
    foreach($sort as $s){
      // si il n'y a qu'une seule liste (= 1 seule section)
      if (strpos($s, ",") === false && $s != ""){
        $temp = explode("=", $s);
        $ordre[$temp[0]] = $temp[1];
      }
      // s'il y a plusieurs listes (= plusieurs sections)
      else{
        $sort2 = explode(",", $s);
        foreach($sort2 as $s){
          if ($s != ""){
            $temp = explode("=", $s);
            $ordre[$temp[0]] = $temp[1];
          }
        }
      }
    }
    
    foreach($parts as $part){
      $global_contents = $part->get_global_contents();
      foreach($global_contents as $gc){
        if (isset($_POST[$part->name .'_' .$gc->name]) && $_POST[$part->name .'_' .$gc->name] == 1){
          $page->add_global_content($gc);
          $gc->set_position($page->id, $ordre[$gc->id]);
        }
      }
    }
    
    // réécrit le fichier .htaccess    
    $page->update_htaccess();
    
    // met à jour le fichier sitemap.xml
    $page->update_sitemap();
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("La page a été ajoutée avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=2";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=3";
    // sauve les élément postés
    $_SESSION['saved_name']     = $_POST['name'];
    $_SESSION['saved_template'] = $_POST['template'];
    $_SESSION['saved_parent']   = $_POST['parent'];
    $_SESSION['saved_login']    = $_POST['login'];
    $_SESSION['saved_active']   = $_POST['active'] == 1 ? true : false;
    $_SESSION['saved_menu']     = $_POST['menu'] == 1 ? true : false;
    
    $title = "";
    $description = "";
    $keywords = "";
    $metas = "";
    $alias = "";
    $menu_text = "";
    $noembed = "";
    $content_type = "";
    $content = "";
    $category = "";
    $languages = Miki_language::get_all_languages(); 
    foreach ($languages as $l){
      
      // ici on vérifie si les données ont été fournies car si on est pas membre du groupe SEO, ces données ne sont pas renseignées
      if (isset($_POST["title_$l->code"]))
        $title        .= ";;$l->code%%" .$_POST["title_$l->code"];
      if (isset($_POST["description_$l->code"]))
        $description  .= ";;$l->code%%" .$_POST["description_$l->code"];
      if (isset($_POST["keywords_$l->code"]))
        $keywords     .= ";;$l->code%%" .$_POST["keywords_$l->code"];
      if (isset($_POST["metas_$l->code"]))
        $metas        .= ";;$l->code%%" .$_POST["metas_$l->code"];
      if (isset($_POST["alias_$l->code"]))
        $alias        .= ";;$l->code%%" .trim($_POST["alias_$l->code"]);
      if (isset($_POST["category_$l->code"]))
        $category     .= ";;$l->code%%" .($_POST["category_$l->code"] == "" ? 'NULL' : $_POST["category_$l->code"]);
      
      $menu_text    .= ";;$l->code%%" .$_POST["menu_text_$l->code"];
      $noembed      .= ";;$l->code%%" .$_POST["noembed_$l->code"];
      $content_type .= ";;$l->code%%" .$_POST["content_type_$l->code"];
      
      
      if ($_POST["content_type_$l->code"] == 'code')
        $content .= ";$l->code%%" .$_POST["code_$l->code"];
    }
    $_SESSION['saved_title']        = $title;
    $_SESSION['saved_description']  = $description;
    $_SESSION['saved_keywords']     = $keywords;
    $_SESSION['saved_metas']        = $metas;
    $_SESSION['saved_alias']        = $alias;
    $_SESSION['saved_menu_text']    = $menu_text;
    $_SESSION['saved_noembed']      = $noembed;
    $_SESSION['saved_content_type'] = $content_type;
    $_SESSION['saved_content']      = $content;
    $_SESSION['saved_category']     = $category;
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>