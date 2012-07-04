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
    
  // recherche des objets
  $objects = Miki_object::search($search, "", "", "", "", true, $order, $order_type, $max_elements, $page, $nb_objects); 
?>

  <table id="main_table">
    <tr class="headers">
      <td style="width:20%"><?php echo _("Titre"); ?></td>
      <td style="width:12%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_objects(<?php echo $page; ?>, '<?php echo $search; ?>', 'city', '');"><?php echo _("Ville"); ?></td>
      <td style="width:12%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_objects(<?php echo $page; ?>, '<?php echo $search; ?>', 'region', '');"><?php echo _("Région"); ?></td>
      <td style="width:12%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_objects(<?php echo $page; ?>, '<?php echo $search; ?>', 'country', '');"><?php echo _("Pays"); ?></td>
      <td style="width:14%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_objects(<?php echo $page; ?>, '<?php echo $search; ?>', 'category', '');"><?php echo _("Catégorie"); ?></td>
      <td style="width:10%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_objects(<?php echo $page; ?>, '<?php echo $search; ?>', 'date_created', '');"><?php echo _("Date d'ajout"); ?></td>
      <td style="width:10%;text-align:center"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_objects(<?php echo $page; ?>, '<?php echo $search; ?>', 'state', '');"><?php echo _("Publié"); ?></td>
      <td style="width:10%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      if (sizeof($objects) == 0)
        echo "<tr><td colspan='7'>" ._("Aucun objet n'est présent dans la base de données") ."</td></tr>";
      
      $n = 0;
      
      foreach($objects as $object){
        $date = explode(" ", $object->date_created);
        $date = explode("-", $date[0]);
        $jour = $date[2];
        $mois = $date[1];
        $annee = $date[0];
        $date = "$jour/$mois/$annee";
        
        $title = $object->title[Miki_language::get_main_code()];
        
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        echo "
          <tr id='$object->id' class='pages' onmouseover=\"colorLine('$object->id');\" onmouseout=\"uncolorLine('$object->id');\">
            <td class='$class' style='height:2em'>
              <a href='index.php?pid=173&id=$object->id' title='" ._('Editer') ."'>$title</a>
            </td>
            <td class='$class' style='height:2em'>
              $object->city
            </td>
            <td class='$class' style='height:2em'>
              $object->region
            </td>
            <td class='$class' style='height:2em'>
              $object->country
            </td>
            <td class='$class' style='height:2em'>
              $object->category
            </td>
            <td class='$class' style='height:2em'>
              $date
            </td>
            <td class='$class' style='height:2em;text-align:center'>";
              
              if (test_right(56) && $object->state == 0)
                echo "<a href='object_change_state.php?id=$object->id&state=1' title='" ._('Publier') ."'><img src='pictures/false.gif' border='0' alt='" ._('Publier') ."' /></a>";
              elseif (test_right(56) && $object->state == 1)
                echo "<a href='object_change_state.php?id=$object->id&state=0' title='" ._('Dépublier') ."'><img src='pictures/true.gif' border='0' alt='" ._('Dépublier') ."' /></a>";
              elseif ($object->state == 0)
                echo "<img src='pictures/false.gif' border='0' alt='" ._('Non-publié') ."' />";
              elseif ($object->state == 1)
                echo "<img src='pictures/true.gif' border='0' alt='" ._('Publié') ."' />";
              
      echo "</td>
            <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>";
              if (test_right(56))
                echo "<span style='margin-right:10px'><a href='index.php?pid=173&id=$object->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
              if (test_right(57))
                echo "<span style='margin-right:10px'><a class='delete' href='object_delete.php?id=$object->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
              
              echo "<span><input type='checkbox' class='check_element' element_id='$object->id' /></span>
            </td>
          </tr>";
      }
      
      echo "<tr>
              <td colspan='5' style='height:30px;vertical-align:middle;text-align:left;padding-left:5px'>
                Nombre d'objets total : $nb_objects<br />
              </td>";
    
      // calcul le nombre de pages totales  
      $nb_pages = (int)($nb_objects / $max_elements);
      $reste = ($nb_objects % $max_elements);
      if ($reste != 0)
        $nb_pages++;
      
      if ($nb_pages > 1){
        $code = "<td colspan='5' style='height:30px;vertical-align:top;text-align:right;padding-right:10px'>";
        
        if ($page != 1)
          $code .= "<a href='#' title='première page' onclick=\"search_objects(1, '$search', '$order', '$order_type');\"><<</a>&nbsp;&nbsp;<a href='#' title='page précédente' onclick=\"search_objects(" .($page-1) .", '$search', '$order', '$order_type');\"><</a>&nbsp;&nbsp;";
        
        if ($nb_pages <= 12){
          for ($x=1; $x<=$nb_pages; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_objects($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == $nb_pages){
          for ($x=($page-12); $x<=$page; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_objects($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == 1){
          for ($x=$page; $x<=($page+12); $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_objects($x, '$search', '$order', '$order_type');\">$x</a> | ";             
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
              $code .= "<a href='#' onclick=\"search_objects($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        
        $code = mb_substr($code, 0, mb_strlen($code)-3);
        
        if ($page != $nb_pages)
          $code .= "&nbsp;&nbsp;<a href='#' title='page suivante' onclick=\"search_objects(" .($page+1) .", '$search', '$order', '$order_type');\">></a>&nbsp;&nbsp;<a href='#' title='dernière page' onclick=\"search_objects($nb_pages, '$search', '$order', '$order_type');\">>></a>";
        
        $code .= "</td></tr>";
        
        echo $code;
      }
  ?>
  </table>