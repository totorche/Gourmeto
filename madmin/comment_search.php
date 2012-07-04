<?php
  require_once("include/headers.php");
  require_once("scripts/functions.php");
  
  // définit le nombre max de'éléments à afficher par page
  $max_elements = 50;
  
  if (isset($_POST['page']) && is_numeric($_POST['page']))
    $page = $_POST['page'];
  else
    $page = 1;
    
  if (isset($_POST['state']) && is_numeric($_POST['state']))
    $state = $_POST['state'];
  else
    $state = "";
    
  if (isset($_POST['oclass']))
    $object_class = $_POST['oclass'];
  else
    $object_class = "";
    
  if (isset($_POST['search']))
    $search = mb_strtolower($_POST['search'], 'UTF-8');
  else
    $search = "";
    
  if (isset($_POST['order']))
    $order = $_POST['order'];
  else
    $order = "";
    
  if (isset($_POST['order_type']) && $_POST['order_type'] !== "")
    $order_type = $_POST['order_type'];
  else
    $order_type = "asc";
    
  // pour stocker le nombre de commentaires existants
  $nb_comments = 0;
    
  // recherche des commentaires
  $comments = Miki_comment::search($search, "", "", $state, $object_class, $order, $order_type, $max_elements, $page, $nb_comments); 
  
?>
  
  <table id="main_table">
    <tr class="headers">
      <td style="width:20%"><?php echo _("Auteur"); ?></td>
      <td style="width:35%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_comments(<?php echo $page; ?>, '<?php echo $search; ?>', 'author', '');"><?php echo _("Commentaire"); ?></td>
      <td style="width:10%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_comments(<?php echo $page; ?>, '<?php echo $search; ?>', 'state', '');"><?php echo _("Status"); ?></td>
      <td style="width:10%; text-align: center;"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_comments(<?php echo $page; ?>, '<?php echo $search; ?>', 'rating', '');"><?php echo _("Note"); ?></td>
      <td style="width:15%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_comments(<?php echo $page; ?>, '<?php echo $search; ?>', 'source', '');"><?php echo _("En réponse à"); ?></td>
      <td style="width:10%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      if (sizeof($comments) == 0)
        echo "<tr><td colspan='6'>" ._("Aucun commentaire n'est présent dans la base de données") ."</td></tr>";
      
      $n = 0;
      
      foreach($comments as $comment){
        $person = $comment->get_person();
        $date = date("d/m/Y à H:i", strtotime($comment->date));
        $text = truncate_text($comment->comment, 100, true, true);
        
        if ($comment->rating > 0)
          $rating = $comment->rating;
        else
          $rating = "";
        
        switch($comment->state){
          case 1:
            $state = _("Approuvé");
            break;
          case 2:
            $state = _("En attente de validation");
            break;
          case 3:
            $state = _("Indésirable");
            break;
          default:
            $state = _("Indéfini");
            break;
        }
        
        try{
          // vérifie le type d'objet lié au commentaire
          if ($comment->object_class == "Miki_page"){
              $source_type = _("Page");
              $object = new Miki_page($this->id_object);
              $link = $object->get_url_simple();
              $title = $object->get_menu_name();
          }
          elseif ($comment->object_class == "Miki_news"){
              $source_type = _("Actualité");
              $object = new Miki_news($comment->id_object);
              $link = $object->get_url_simple();
              $title = truncate_text($object->title, 50, true, true);
          }
          elseif ($comment->object_class == "Miki_shop_article"){
              $source_type = _("Produit");
              $object = new Miki_shop_article($comment->id_object);
              $link = $object->get_url_simple();
              $title = truncate_text($object->name['fr'], 50, true, true);
          }
          elseif ($comment->object_class == "Miki_event"){
              $source_type = _("Evénement");
              $object = new Miki_event($comment->id_object);
              $link = $object->get_url_simple();
              $title = truncate_text($object->title['fr'], 50, true, true);
          }
          elseif ($comment->object_class == "Miki_album"){
              $source_type = _("Album photo");
              $object = new Miki_album($comment->id_object);
              $link = $object->get_url_simple();
              $title = truncate_text($object->name, 50, true, true);
          }
          elseif ($comment->object_class == "Miki_album_picture"){
              $source_type = _("Photo");
              return new Miki_album_picture($comment->id_object);
          }
        }
        catch(Exception $e){
          return false;
        }
        
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        echo "
          <tr id='$comment->id' class='pages' onmouseover=\"colorLine('$comment->id');\" onmouseout=\"uncolorLine('$comment->id');\">
            <td class='$class' style='padding: 5px;'>
              $person->firstname $person->lastname";
              
              if ($person->email1 != "")
                echo "<br /><a href='mailto:$person->email1' title='" ._("Ecrire à cette personne") ."'>$person->email1</a>";
              if ($person->web != "")
                echo "<br /><a href='$person->web' target='_blank' title='" ._("Visiter le site web") ."'>$person->web</a>";
                
      echo "</td>
            <td class='$class' style='padding: 5px;'>
              <div style='color: #999999; margin-bottom: 10px;'>" ._("Envoyé le ") ."$date</div>
              <div>$text</div>
            </td>
            <td class='$class' style='padding: 5px;'>$state</td>
            <td class='$class' style='padding: 5px; text-align: center;'>$rating</td>
            <td class='$class' style='padding: 5px;'>
              <div style='margin-bottom: 10px;'><a href='$link' target='_blank' title='" ._("Voir cet objet") ."'>$source_type</a></div>
              <div>$title</div>
            </td>
            <td class='$class' style='padding: 5px; text-align:right; padding-right:10px;'>
              <span style='margin-right:10px'><a href='index.php?pid=242&id=$comment->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>
              <span style='margin-right:10px'><a class='delete' href='comment_delete.php?id=$comment->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>
              <span><input type='checkbox' class='check_element' element_id='$comment->id' /></span>
            </td>
          </tr>";
      }
      
      echo "</table>";
      
      echo "<div style='width: 100%; overflow: hidden; margin-top: 20px;'>
              <div style='float: left; height: 30px; text-align: left; padding-left: 5px;'>
                Nombre de commentaires total : $nb_comments
              </div>";
    
      // calcul le nombre de pages totales  
      $nb_pages = (int)($nb_comments / $max_elements);
      $reste = ($nb_comments % $max_elements);
      if ($reste != 0)
        $nb_pages++;
      
      if ($nb_pages > 1){
        $code = "<div style='float: right; height: 30px; text-align: right; padding-right: 5px;'>";
        
        if ($page != 1)
          $code .= "<a href='#' title='première page' onclick=\"search_comments(1, '$search', '$order', '$order_type');\"><<</a>&nbsp;&nbsp;<a href='#' title='page précédente' onclick=\"search_comments(" .($page-1) .", '$search', '$order', '$order_type');\"><</a>&nbsp;&nbsp;";
        
        if ($nb_pages <= 12){
          for ($x=1; $x<=$nb_pages; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_comments($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == $nb_pages){
          for ($x=($page-12); $x<=$page; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_comments($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == 1){
          for ($x=$page; $x<=($page+12); $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_comments($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page != 1){
          $start = $page - 6;
          if ($start < 1)
            $start = 1;
          $stop = $start + 12;
          for ($x=$start; $x<=$stop; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_comments($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        
        $code = mb_substr($code, 0, mb_strlen($code)-3);
        
        if ($page != $nb_pages)
          $code .= "&nbsp;&nbsp;<a href='#' title='page suivante' onclick=\"search_comments(" .($page+1) .", '$search', '$order', '$order_type');\">></a>&nbsp;&nbsp;<a href='#' title='dernière page' onclick=\"search_comments($nb_pages, '$search', '$order', '$order_type');\">>></a>";
        
        $code .= "</div>";
        
        echo $code;
      }
      
      echo "</div>";
  ?>