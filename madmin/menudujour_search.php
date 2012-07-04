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
    
  // recherche des événements
  $elements = miki_menudujour::search($search, "", $order, $order_type, $max_elements, $page, $nb_elements); 
?>

  <table id="main_table">
    <tr class="headers">
      <td style="width:20%;"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_elements(<?php echo $page; ?>, '<?php echo $search; ?>', 'date', '');"><?php echo _("Date"); ?></td>
      <td style="width:40%;"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_elements(<?php echo $page; ?>, '<?php echo $search; ?>', 'name', '');"><?php echo _("Nom"); ?></td>
      <td style="width:30%; text-align: center;"><?php echo _("Nb de plats"); ?></td>
      <td style="width:10%; text-align: right; padding-right: 10px;"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      if (sizeof($elements) == 0)
        echo "<tr><td colspan='4'>" ._("Aucun menu du jour n'est présent dans la base de données") ."</td></tr>";
      
      $n = 0;
      
      foreach($elements as $element){
        $date = date("d/m/Y", strtotime($element->date));
        
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        echo "
          <tr id='$element->id' class='pages' onmouseover=\"colorLine('$element->id');\" onmouseout=\"uncolorLine('$element->id');\">
            <td class='$class' style='height:2em'>
              <a href='index.php?pid=303&id=$element->id' title='" ._('Editer') ."'>$date</a>
            </td>
            <td class='$class' style='height:2em'>
              <a href='index.php?pid=303&id=$element->id' title='" ._('Editer') ."'>$element->name</a>
            </td>
            <td class='$class' style='height:2em; text-align: center;'>"
              .$element->get_nb_plats() 
          ."</td>
            <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>";
              
              if (test_right(80))
                echo "<span style='margin-right:10px'><a href='index.php?pid=303&id=$element->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
              if (test_right(81))
                echo "<span style='margin-right:10px'><a class='delete' href='menudujour_delete.php?id=$element->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
              
              echo "<span><input type='checkbox' class='check_element' element_id='$element->id' /></span>
            </td>
          </tr>";
      }
      
      echo "<tr>
              <td style='height:30px;vertical-align:middle;text-align:left;padding-left:5px'>
                Nombre de menus du jour total : $nb_elements<br />
              </td>";
    
      // calcul le nombre de pages totales  
      $nb_pages = (int)($nb_elements / $max_elements);
      $reste = ((int)$nb_elements % $max_elements);
      if ($reste != 0)
        $nb_pages++;
        
      if ($nb_pages > 1){
        $code = "<td colspan='3' style='height:30px;vertical-align:top;text-align:right;padding-right:10px'>";
        
        if ($page != 1)
          $code .= "<a href='#' title='première page' onclick=\"search_elements(1, '$search', '$order', '$order_type');\"><<</a>&nbsp;&nbsp;<a href='#' title='page précédente' onclick=\"search_elements(" .($page-1) .", '$search', '$order', '$order_type');\"><</a>&nbsp;&nbsp;";
        
        if ($nb_pages <= 12){
          for ($x=1; $x<=$nb_pages; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_elements($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == $nb_pages){
          for ($x=($page-12); $x<=$page; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_elements($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == 1){
          for ($x=$page; $x<=($page+12); $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_elements($x, '$search', '$order', '$order_type');\">$x</a> | ";             
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
              $code .= "<a href='#' onclick=\"search_elements($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        
        $code = mb_substr($code, 0, mb_strlen($code)-3);
        
        if ($page != $nb_pages)
          $code .= "&nbsp;&nbsp;<a href='#' title='page suivante' onclick=\"search_elements(" .($page+1) .", '$search', '$order', '$order_type');\">></a>&nbsp;&nbsp;<a href='#' title='dernière page' onclick=\"search_elements($nb_pages, '$search', '$order', '$order_type');\">>></a>";
        
        $code .= "</td></tr>";
        
        echo $code;
      }
      else{
        echo "<td colspan='3'>&nbsp;</td></tr>";
      }
  ?>
  </table>