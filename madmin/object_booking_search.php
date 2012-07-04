<?php
  require_once("include/headers.php");
  
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
    
  // récupert le nombre d'objets existants
  $nb_bookings = Miki_object_booking::get_nb_object_booking();
    
  // recherche des objets
  $bookings = Miki_object_booking::search_object_booking($search, "", $order, $order_type, $max_elements, $page); 
?>

  <table id="main_table">
    <tr class="headers">
      <td style="width:25%"><?php echo _("Objet"); ?></td>
      <td style="width:10%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_object_booking(<?php echo $page; ?>, '<?php echo $search; ?>', 'firstname', '');"><?php echo _("Prénom"); ?></td>
      <td style="width:10%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_object_booking(<?php echo $page; ?>, '<?php echo $search; ?>', 'lastname', '');"><?php echo _("Nom"); ?></td>
      <td style="width:15%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_object_booking(<?php echo $page; ?>, '<?php echo $search; ?>', 'date_booking', '');"><?php echo _("Date de la réservation"); ?></td>
      <td style="width:15%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_object_booking(<?php echo $page; ?>, '<?php echo $search; ?>', 'date_start', '');"><?php echo _("Depuis le"); ?></td>
      <td style="width:15%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_object_booking(<?php echo $page; ?>, '<?php echo $search; ?>', 'date_stop', '');"><?php echo _("Jusqu'au"); ?></td>
      <td style="width:10%;text-align:right;padding-right:10px">&nbsp;</td>
    </tr>
    
    <?php
      if (sizeof($bookings) == 0)
        echo "<tr><td colspan='7'>" ._("Aucune réservation n'est présente dans la base de données") ."</td></tr>";
      
      $n = 0;
      
      foreach($bookings as $booking){
        $date_booking = date("d/m/Y", strtotime($booking->date_booking));
        $date_start = date("d/m/Y", strtotime($booking->date_start));
        $date_stop = date("d/m/Y", strtotime($booking->date_stop));
        
        // récupert l'objet réservé
        $object = new Miki_object($booking->id_object);
        
        // récupert la personne ayant passé la réservation
        $person = new Miki_person($booking->id_person);
        
        $title = $object->title[Miki_language::get_main_code()];
        
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        echo "
          <tr id='$booking->id' class='pages' onmouseover=\"colorLine('$booking->id');\" onmouseout=\"uncolorLine('$booking->id');\">
            <td class='$class' style='height:2em'>
              <a href='index.php?pid=175&id=$booking->id' title='" ._('Voir') ."'>$title</a>
            </td>
            <td class='$class' style='height:2em'>
              $person->firstname
            </td>
            <td class='$class' style='height:2em'>
              $person->lastname
            </td>
            <td class='$class' style='height:2em'>
              $date_booking
            </td>
            <td class='$class' style='height:2em'>
              $date_start
            </td>
            <td class='$class' style='height:2em'>
              $date_stop
            </td>
            <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>
              <a href='index.php?pid=175&id=$booking->id' title='" ._('Voir') ."'><img src='pictures/view.gif' border='0' alt='" ._('Voir') ."' /></a>
            </td>
          </tr>";
      }
      
      echo "<tr>
              <td colspan='5' style='height:30px;vertical-align:middle;text-align:left;padding-left:5px'>
                Nombre de réservations total : $nb_bookings<br />
              </td>";
    
      // calcul le nombre de pages totales  
      $nb_pages = (int)($nb_bookings / $max_elements);
      $reste = (sizeof($bookings) % $max_elements);
      if ($reste != 0)
        $nb_pages++;
      
      if ($nb_pages > 1){
        $code = "<td colspan='5' style='height:30px;vertical-align:top;text-align:right;padding-right:10px'>";
        
        if ($page != 1)
          $code .= "<a href='#' title='première page' onclick=\"search_object_booking(1, '$search', '$order', '$order_type');\"><<</a>&nbsp;&nbsp;<a href='#' title='page précédente' onclick=\"search_object_booking(" .($page-1) .", '$search', '$order', '$order_type');\"><</a>&nbsp;&nbsp;";
        
        if ($nb_pages <= 12){
          for ($x=1; $x<=$nb_pages; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_object_booking($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == $nb_pages){
          for ($x=($page-12); $x<=$page; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_object_booking($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == 1){
          for ($x=$page; $x<=($page+12); $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_object_booking($x, '$search', '$order', '$order_type');\">$x</a> | ";             
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
              $code .= "<a href='#' onclick=\"search_object_booking($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        
        $code = mb_substr($code, 0, mb_strlen($code)-3);
        
        if ($page != $nb_pages)
          $code .= "&nbsp;&nbsp;<a href='#' title='page suivante' onclick=\"search_object_booking(" .($page+1) .", '$search', '$order', '$order_type');\">></a>&nbsp;&nbsp;<a href='#' title='dernière page' onclick=\"search_object_booking($nb_pages, '$search', '$order', '$order_type');\">>></a>";
        
        $code .= "</td></tr>";
        
        echo $code;
      }
  ?>
  </table>