<?php
  require_once("include/headers.php");
  require_once("event_category.php");
  
  // définit le nombre max de'éléments à afficher par page
  $max_elements = 50;
  
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
    
  // recherche des événements
  $events = Miki_event::search($search, true, $order, $order_type, $max_elements, $page, $nb_events); 
?>

  <table id="main_table">
    <tr class="headers">
      <td style="width:20%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_events(<?php echo $page; ?>, '<?php echo $search; ?>', 'title', '');"><?php echo _("Titre"); ?></td>
      <td style="width:13%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_events(<?php echo $page; ?>, '<?php echo $search; ?>', 'category', '');"><?php echo _("Catégorie"); ?></td>
      <td style="width:13%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_events(<?php echo $page; ?>, '<?php echo $search; ?>', 'city', '');"><?php echo _("Ville"); ?></td>
      <td style="width:12%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_events(<?php echo $page; ?>, '<?php echo $search; ?>', 'country', '');"><?php echo _("Pays"); ?></td>
      <td style="width:7%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_events(<?php echo $page; ?>, '<?php echo $search; ?>', 'date_start', '');"><?php echo _("Depuis le"); ?></td>
      <td style="width:7%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_events(<?php echo $page; ?>, '<?php echo $search; ?>', 'date_stop', '');"><?php echo _("Jusqu'au"); ?></td>
      <td style="width:11%;text-align:center"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_events(<?php echo $page; ?>, '<?php echo $search; ?>', 'entrance_type', '');"><?php echo _("Payant"); ?></td>
      <td style="width:7%;text-align:center"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold"><?php echo _("Inscrits"); ?></td>
      <td style="width:10%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      if (sizeof($events) == 0)
        echo "<tr><td colspan='8'>" ._("Aucun événement n'est présent dans la base de données") ."</td></tr>";
      
      $n = 0;
      
      foreach($events as $event){
        $date_start = explode(" ", $event->date_start);
        $date_start = explode("-", $date_start[0]);
        $jour = $date_start[2];
        $mois = $date_start[1];
        $annee = $date_start[0];
        $date_start = "$jour/$mois/$annee";
        
        $date_stop = explode(" ", $event->date_stop);
        $date_stop = explode("-", $date_stop[0]);
        $jour = $date_stop[2];
        $mois = $date_stop[1];
        $annee = $date_stop[0];
        $date_stop = "$jour/$mois/$annee";
        
        $title = $event->title[Miki_language::get_main_code()];
        
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        echo "
          <tr id='$event->id' class='pages' onmouseover=\"colorLine('$event->id');\" onmouseout=\"uncolorLine('$event->id');\">
            <td class='$class' style='height:2em'>
              <a href='index.php?pid=193&id=$event->id' title='" ._('Editer') ."'>$title</a>
            </td>
            <td class='$class' style='height:2em'>
              " .$categories[$event->category] ."
            </td>
            <td class='$class' style='height:2em'>
              $event->city
            </td>
            <td class='$class' style='height:2em'>
              $event->country
            </td>
            <td class='$class' style='height:2em'>
              $date_start
            </td>
            <td class='$class' style='height:2em'>
              $date_stop
            </td>
            <td class='$class' style='height:2em;text-align:center'>";
              
              if ($event->entrance_type == 0)
                echo _("non");
              elseif ($event->entrance_type == 1)
                echo _("oui");
              
      echo "</td>
            <td class='$class' style='height:2em;text-align:center'>
              <a href='event_participants_list.php?id=$event->id' title='" ._('Imprimer') ."' target='_blank'>";
                echo $event->get_nb_participants();
        echo "</a>
            </td>
            <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>";
              
              echo "<span style='margin-right:10px'><a href='event_participants_list.php?id=$event->id' title='" ._('Imprimer') ."' target='_blank'><img src='pictures/print.gif' border='0' alt='" ._('Imprimer') ."' /></a></span>";
              
              if (test_right(63))
                echo "<span style='margin-right:10px'><a href='index.php?pid=193&id=$event->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
              if (test_right(64))
                echo "<span style='margin-right:10px'><a class='delete' href='event_delete.php?id=$event->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
              
              echo "<span><input type='checkbox' class='check_element' element_id='$event->id' /></span>
            </td>
          </tr>";
      }
      
      echo "<tr>
              <td colspan='2' style='height:30px;vertical-align:middle;text-align:left;padding-left:5px'>
                Nombre d'événements total : $nb_events<br />
              </td>";
    
      // calcul le nombre de pages totales  
      $nb_pages = (int)($nb_events / $max_elements);
      $reste = ((int)$nb_events % $max_elements);
      if ($reste != 0)
        $nb_pages++;
        
      if ($nb_pages > 1){
        $code = "<td colspan='7' style='height:30px;vertical-align:top;text-align:right;padding-right:10px'>";
        
        if ($page != 1)
          $code .= "<a href='#' title='première page' onclick=\"search_events(1, '$search', '$order', '$order_type');\"><<</a>&nbsp;&nbsp;<a href='#' title='page précédente' onclick=\"search_events(" .($page-1) .", '$search', '$order', '$order_type');\"><</a>&nbsp;&nbsp;";
        
        if ($nb_pages <= 12){
          for ($x=1; $x<=$nb_pages; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_events($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == $nb_pages){
          for ($x=($page-12); $x<=$page; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_events($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == 1){
          for ($x=$page; $x<=($page+12); $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_events($x, '$search', '$order', '$order_type');\">$x</a> | ";             
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
              $code .= "<a href='#' onclick=\"search_events($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        
        $code = mb_substr($code, 0, mb_strlen($code)-3);
        
        if ($page != $nb_pages)
          $code .= "&nbsp;&nbsp;<a href='#' title='page suivante' onclick=\"search_events(" .($page+1) .", '$search', '$order', '$order_type');\">></a>&nbsp;&nbsp;<a href='#' title='dernière page' onclick=\"search_events($nb_pages, '$search', '$order', '$order_type');\">>></a>";
        
        $code .= "</td></tr>";
        
        echo $code;
      }
      else{
        echo "<td colspan='7'>&nbsp;</td></tr>";
      }
  ?>
  </table>