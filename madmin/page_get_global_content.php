<?php
  session_start();
  require_once('../class/miki_page.php');
  require_once('../class/miki_template.php');
  require_once('../class/miki_template_part.php');
  require_once('../class/miki_global_content.php');
  require_once('../scripts/config.php');
  
  // si l'id du gabarit est donné, ça veut dire qu'on veut récupérer uniquement en-têtes des sections
  if (isset($_POST['template_id']) && is_numeric($_POST['template_id'])){
    $template_id = $_POST['template_id'];
    $tab = array();
    $template = new Miki_template($template_id);
    $parts = $template->get_parts(); 
    
    if (isset($_POST['page_id']) && is_numeric($_POST['page_id']))
      $page = new Miki_page($_POST['page_id']);
    else
      $page = false;
      
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
    foreach($parts as $part){
      $tab_gc = array();
      $title = _("Section '$part->name'");
      $content = "<div>"
                 ._("Sélectionner les blocs de contenus globaux que vous désirez intégrer à la page : ")
                 ."<br /><br /><ul class='sortable'>";
               
      // récupert les blocs de la section
      $global_contents = $part->get_global_contents();
      // parcourt chaque bloc 
      foreach($global_contents as $gc){
        // récupert les blocs 
        // si la page a été donnée et qu'elle contient le bloc en cours, on le check
        if ($page && $page->has_global_content($gc)){
          $position = $gc->get_position($page->id);
          $tab_gc[] = "$position&&<li id='$gc->id'><input type='checkbox' name='" .$part->name ."_" .$gc->name ."' value='1' checked='checked' /> $gc->name</li>";
        }
        else
          $tab_gc[] = "0&&<li id='$gc->id'><input type='checkbox' name='" .$part->name ."_" .$gc->name ."' value='1' /> $gc->name</li>";
      }
      
      // trie le tableau des blocs récupérés
      usort($tab_gc, "cmp");
      
      // ajoute chaque bloc au contenu html
      foreach($tab_gc as $gc){
        $temp = explode("&&", $gc);
        $content .= $temp[1];
      }
      
      $content .= "</ul></div>";
      $tab[] = array("title"=>$title, "content"=>$content);
    }
    
    
    $json = array("tabs"=>$tab);
    echo json_encode($json);
  }
  // sinon, si l'id de la partie est donné, on veut récupérer les bloc de contenu global
  elseif(isset($_GET['part_id']) && is_numeric($_GET['part_id'])){
    $part_id = $_GET['part_id'];
    $part = new Miki_template_part($part_id);
    
    $content = "<div id='part_$part->name'>"
                ._("Sélectionner les blocs de contenus globaux que vous désirez intégrer à la page : ")
                ."<br /><br />";
                
                $global_contents = $part->get_global_contents();
                foreach($global_contents as $gc){
                  if (isset($_GET['page_id']) && is_numeric($_GET['page_id'])){
                    $page = new Miki_page($_GET['page_id']);
                    if ($page->has_global_content($gc))
                      $content .= "<input type='checkbox' name='" .$part->name ."_" .$gc->name ."' value='1' checked='checked' /> $gc->name<br /><br />";
                    else
                      $content .= "<input type='checkbox' name='" .$part->name ."_" .$gc->name ."' value='1' /> $gc->name<br /><br />";
                  }
                  else  
                    $content .= "<input type='checkbox' name='" .$part->name ."_" .$gc->name ."' value='1' /> $gc->name<br /><br />";
                }
    $content .= "</div>";
    echo $content;
  }
  
?>
