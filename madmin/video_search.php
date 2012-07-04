<?php
  require_once("include/headers.php");
  require_once("../config/video_category.php");
  
  // définit le nombre max d'éléments à afficher par page
  $max_elements = 999999999;
  
  if (isset($_POST['page']) && is_numeric($_POST['page']))
    $page = $_POST['page'];
  else
    $page = 1;
    
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
    
  $order = "position";
  
  // récupert le nombre de vidéos existantes
  $nb_objects = Miki_video::get_nb_videos();
    
  // recherche des vidéos
  $objects = Miki_video::search($search, "", true, $order, $order_type, $max_elements, $page);
?>
  
  <ul class="table" class="sortable">
    <li class="headers non-sortable">
      <div style="4%; padding-left: 1%">&nbsp;</div>
      <div style="width:30%"><?php echo _("Titre"); ?></div>
      <div style="width:25%;"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_videos(<?php echo $page; ?>, '<?php echo $search; ?>', 'category', '');"><?php echo _("Catégorie"); ?></a></div>
      <div style="width:20%;"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_videos(<?php echo $page; ?>, '<?php echo $search; ?>', 'date_created', '');"><?php echo _("Date d'ajout"); ?></a></div>
      <div style="width:10%; text-align: center"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_videos(<?php echo $page; ?>, '<?php echo $search; ?>', 'state', '');"><?php echo _("Publié"); ?></a></div>
      <div style="width:9%; text-align: right; padding-right: 1%"><input type="checkbox" onclick="check_element(this)" /></div>
    </li>
    
    
    
    <?php
      if (sizeof($objects) == 0)
        echo "<li><div>" ._("Aucune vidéo n'est présent dans la base de données") ."</div></li>";
      else
        //echo "<div id='main_table' style='width: 100%; overflow: hidden' class='sortable'>";
      
      $n = 0;
      
      foreach($objects as $object){
        $date = date("d/m/Y", strtotime($object->date_created));
      
        $title = $object->title[Miki_language::get_main_code()];
        
        $n = ($n+1)%2;
        echo "
          <li class='videos' id='$object->id' onmouseover=\"colorLine('$object->id');\" onmouseout=\"uncolorLine('$object->id');\">
            <div style='width:5%; text-align: center'>
              <img src='pictures/move.gif' style='vertical-align: middle; cursor: pointer' alt='+' class='enable_sortable' />
            </div>
            <div style='width:30%'>";
            
              if (test_right(72))
                echo "<a href='index.php?pid=233&id=$object->id' title='" ._('Editer') ."'>$title</a>";
              else
                echo "$title";
            
      echo "</div>
            <div style='width:25%;'>
              " .$video_category[$object->category] ."
            </div>
            <div style='width:20%;'>
              $date
            </div>
            <div style='text-align:center; width:10%;'>";
              
              if (test_right(72) && $object->state == 0)
                echo "<a href='video_change_state.php?id=$object->id&state=1' title='" ._('Publier') ."'><img src='pictures/false.gif' border='0' alt='" ._('Publier') ."' /></a>";
              elseif (test_right(72) && $object->state == 1)
                echo "<a href='video_change_state.php?id=$object->id&state=0' title='" ._('Dépublier') ."'><img src='pictures/true.gif' border='0' alt='" ._('Dépublier') ."' /></a>";
              elseif ($object->state == 0)
                echo "<img src='pictures/false.gif' border='0' alt='" ._('Non-publié') ."' />";
              elseif ($object->state == 1)
                echo "<img src='pictures/true.gif' border='0' alt='" ._('Publié') ."' />";
              
      echo "</div>
            <div style='text-align:right; width:9%; padding-right: 1%;'>";
              if (test_right(72))
                echo "<span style='margin-right:10px'><a href='index.php?pid=233&id=$object->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
              if (test_right(73))
                echo "<span style='margin-right:10px'><a class='delete' href='video_delete.php?id=$object->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
              
              echo "<span><input type='checkbox' class='check_element' element_id='$object->id' /></span>
            </div>
          </li>";
      }
      
      echo "</ul>";
      
      echo "<div style='float: left; width: 30%; height: 30px; vertical-align: middle; padding-left:5px'>
              Nombre de vidéos total : $nb_objects<br />
            </div>";
    
      // calcul le nombre de pages totales  
      $nb_pages = (int)($nb_objects / $max_elements);
      $reste = (sizeof($objects) % $max_elements);
      if ($reste != 0)
        $nb_pages++;
      
      if ($nb_pages > 1){
        $code = "<div style='float: right; height: 30px; vertical-align: top; text-align: right; padding-right:10px'>";
        
        if ($page != 1)
          $code .= "<a href='#' title='première page' onclick=\"search_videos(1, '$search', '$order', '$order_type');\"><<</a>&nbsp;&nbsp;<a href='#' title='page précédente' onclick=\"search_videos(" .($page-1) .", '$search', '$order', '$order_type');\"><</a>&nbsp;&nbsp;";
        
        if ($nb_pages <= 12){
          for ($x=1; $x<=$nb_pages; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_videos($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == $nb_pages){
          for ($x=($page-12); $x<=$page; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_videos($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == 1){
          for ($x=$page; $x<=($page+12); $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_videos($x, '$search', '$order', '$order_type');\">$x</a> | ";             
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
              $code .= "<a href='#' onclick=\"search_videos($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        
        $code = mb_substr($code, 0, mb_strlen($code)-3);
        
        if ($page != $nb_pages)
          $code .= "&nbsp;&nbsp;<a href='#' title='page suivante' onclick=\"search_videos(" .($page+1) .", '$search', '$order', '$order_type');\">></a>&nbsp;&nbsp;<a href='#' title='dernière page' onclick=\"search_videos($nb_pages, '$search', '$order', '$order_type');\">>></a>";
        
        $code .= "</div>";
        
        echo $code;
      }
  ?>