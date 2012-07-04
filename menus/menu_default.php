<?php

  function print_menu($parent = ""){
    global $page; 
    
    // si on ne donne pas de parent, on part avec les pages les plus haut placées
    if ($parent == ""){
      // on récupert donc uniquement les pages qui n'ont pas de parent
      $temp = array();
      $pages = Miki_page::get_all_pages("position", false);
      foreach($pages as $p){
        if (!$p->has_parent())
          $temp[] = $p;
      }
      $pages = $temp;
    }
    // sinon on récupert les pages enfants de la page donnée
    else{
      $pages = $parent->get_children();
    }
    
    
    // on débute l'affichage de cette partie du menu
    if (sizeof($pages) > 0){
      echo "<ul>";
    }
    
    // on parcourt chaque page récupérée
    foreach($pages as $p){
      // si la page doit être affichée dans le menu, on l'affiche
      if ($p->in_menu()){
        $text_menu = $p->get_menu_name($_SESSION['lang']);
        $url_page = $p->get_url_simple($_SESSION['lang']);
        
        // vérifie si la page que l'on traite est la page affichée actuellement et lui donne une classe en rapport
        if ($p->is_page($page))
          $class = "active";
        else
          $class = "inactive";
        
        echo "<li>
                <a href='$url_page' class='$class'>$text_menu</a>";
        
        // si la page a des pages enfants, on affiche les descendants
        if ($p->has_children()){
          print_menu($p);
        }
        
        echo "</li>";
      }
    }
    
    
    if (sizeof($pages) > 0){
      echo "</ul>";
    }
    
  }
  
  
  // affiche le menu
  print_menu();
    
?>