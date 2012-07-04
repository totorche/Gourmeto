<?php 
  require_once("include/headers.php");
  require_once("functions_pictures.php");  
  
  if (!test_right(15))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_POST['name']) || !isset($_POST['id']) || !is_numeric($_POST['id']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // vérifie si l'utilisateur est membre du groupe SEO
  $user = new Miki_user($_SESSION['miki_admin_user_id']);
  $is_seo = $user->has_group(new Miki_group(2));
  
  // sauve la page
  try{
    $page = new Miki_page($_POST['id']); 
    $page->name = decode($_POST['name']);
    $page->template_id = $_POST['template'] == "" ? 'NULL' : $_POST['template'];
    $page->login = $_POST['login'];
    $page->state = (isset($_POST['active']) && $_POST['active'] == 1) ? 1:0;
    $page->menu = (isset($_POST['menu']) && $_POST['menu'] == 1) ? 1:0;
    
    if ($_POST['parent'] != -1 && $page->parent_id != $_POST['parent']){
      $page->get_position($_POST['parent']);
    }
    
    $page->parent_id = $_POST['parent'] == -1 ? 'NULL' : $_POST['parent'];
    
    $page->update();
    
    // supprime tous les liens avec les sections
    $contents = $page->get_contents();
    
    // puis ajoute les contenus qui n'existaient pas encore
    $languages = Miki_language::get_all_languages(); 
    foreach ($languages as $l){
      // si un contenu dans la langue en cours existe déjà, on fait un update
      if (isset($contents[strtolower($l->code)])){
        $content = $contents[strtolower($l->code)];
        $content->page_id = $page->id;
        $content->category_id = 'NULL';
        
        if ($is_seo){
          $content->title = stripslashes($_POST["title_$l->code"]);
          $content->description = stripslashes($_POST["description_$l->code"]);
          $content->keywords = stripslashes($_POST["keywords_$l->code"]);
          $content->metas = stripslashes($_POST["metas_$l->code"]);
          $content->alias = trim(decode($_POST["alias_$l->code"]));
          $content->category_id = $_POST["category_$l->code"] == "" ? 'NULL' : $_POST["category_$l->code"];
        }
        
        $content->menu_text = stripslashes(htmlspecialchars($_POST["menu_text_$l->code"]));
        $content->noembed = stripslashes($_POST["noembed_$l->code"]);
        $content->content_type = $_POST["content_type_$l->code"];
        if ($content->content_type == 'file' && $_FILES["file_$l->code"]['name'] != ""){
          $content->content = $_FILES["file_$l->code"]['name'];
        }
        elseif ($content->content_type == 'code'){
          $content->content = stripslashes($_POST["code_$l->code"]);
          $content->content = str_replace('</textarea>', htmlentities('</textarea>'), $content->content);      
        }
        elseif ($content->content_type == 'url'){
          $content->content = stripslashes($_POST["url_$l->code"]);
        }
        $content->update();
      }
      // sinon on l'ajoute
      else{
        $content = new Miki_page_content(); 
        $content->page_id = $page->id;
        $content->language_id = $l->id;
        $content->title = $_POST["title_$l->code"];
        $content->description = $_POST["description_$l->code"];
        $content->keywords = $_POST["keywords_$l->code"];
        $content->metas = $_POST["metas_$l->code"];
        $content->alias = trim($_POST["alias_$l->code"]);
        $content->menu_text = $_POST["menu_text_$l->code"];
        $content->category_id = $_POST["category_$l->code"] == "" ? 'NULL' : $_POST["category_$l->code"];
        $content->noembed = stripslashes($_POST["noembed_$l->code"]);
        $content->content_type = $_POST["content_type_$l->code"];
        if ($content->content_type == 'file' && $_FILES["file_$l->code"]['name'] != ""){
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
    }
    
    // ajoute les blocs de contenu global sélectionnés à la page
    $page->remove_global_contents();
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
    $_SESSION['msg'] = _("La page a été modifiée avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=2";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    echo $e;
    $referer = $_SESSION['url_back'];
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>