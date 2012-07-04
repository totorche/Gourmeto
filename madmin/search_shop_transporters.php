<?php
  require_once('include/headers.php');
  
  // test que l'on aie bien un shop de configuré
  $shops = Miki_shop::get_all_shops();
  if (sizeof($shops) == 0){
    exit("<div>
            Vous n'avez aucune shop de configuré pour le moment.<br /><br />
            <input type='button' value='Créer un shop' onclick=\"document.location='index.php?pid=142'\" />
          </div>");
  }
  else
    $shop = array_shift($shops);
  
  // définit le nombre max de'éléments à afficher par page
  $max_elements = 50;
  
  if (isset($_POST['page']) && is_numeric($_POST['page']))
    $page = $_POST['page'];
  else
    $page = 1;
    
  if (isset($_POST['search']))
    $search = mb_strtolower(addcslashes($_POST['search'], "'"), 'UTF-8');
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
    
  // recherche les transporteurs
  $elements = Miki_shop_transporter::search($search, false, $order, $order_type);
?>

  <table id="main_table">
    <tr class="headers">
      <td style="width:25%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_elements(<?php echo $page; ?>, '<?php echo $search; ?>', 'name', '');"><?php echo _('Name'); ?></a></td>
      <td style="width:10%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_elements(<?php echo $page; ?>, '<?php echo $search; ?>', 'state', '');"><?php echo _("Etat"); ?></a></td>
      <td style="width:30%;"><?php echo _("Délai de livraison"); ?></td>
      <td style="width:20%"><?php echo _("Logo"); ?></td>
      <td style="width:15%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      if (sizeof($elements) == 0)
        echo "<tr><td colspan='5'>" ._("Aucun transporteur ne correspond aux critères") ."</td></tr>";
      
      $n = 0;
      $start = ($page-1) * $max_elements;
      for ($x=$start; $x < ($max_elements + $start) && $x < sizeof($elements); $x++){
        $element = $elements[$x];
        
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        echo "
          <tr id='$element->id' class='pages' onmouseover=\"colorLine('$element->id');\" onmouseout=\"uncolorLine('$element->id');\">
            <td class='$class' style='height:2em'>
              <a href='index.php?pid=253&amp;id=$element->id' title='" ._('Editer') ."'>$element->name</a>
            </td>
            <td class='$class' style='height:2em'>";
              if ($element->state == 0)
                echo "<a href='shop_transporter_change_state.php?id=$element->id&state=1' title='" ._("Activer") ."'><img src='pictures/false.gif' alt='Désactivé' /></a>";
              elseif ($element->state == 1)
                echo "<a href='shop_transporter_change_state.php?id=$element->id&state=0' title='" ._("Désactiver") ."'><img src='pictures/true.gif' alt='Activé' /></a>";
      echo "</td>
            <td class='$class' style='height:2em;'>
              $element->shipping_delay
            </td>
            <td class='$class' style='height:2em'>";
              if (!empty($element->logo) && $element->logo != "NULL")
                echo "<img src='../pictures/shop_transporter/" .$element->logo ."' style='width: 50px; margin: 5px;' alt=\"$element->name\" />";
      echo "</td>
            <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>
              <span style='margin-right:10px'><a href='index.php?pid=254&amp;id=$element->id' title='" ._('Voir') ."'><img src='pictures/view.gif' border='0' alt='" ._('Voir') ."' /></a></span>";
              if (test_right(76))
                echo "<span style='margin-right:10px'><a href='index.php?pid=253&amp;id=$element->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
              if (test_right(77))
                echo "<span style='margin-right:10px'><a class='delete' href='shop_transporter_delete.php?id=$element->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
              
              echo "<span><input type='checkbox' class='check_element' element_id='$element->id' /></span>
            </td>
          </tr>";
      }
      
      echo "<tr>
              <td colspan='2' style='height:30px;vertical-align:middle;text-align:left;padding-left:5px'>
                Nombre de transporteurs total : " .Miki_shop_transporter::get_nb_transporters() ."<br />
              </td>";
    
      // calcul le nombre de pages totales  
      $nb_pages = (int)(sizeof($elements) / $max_elements);
      $reste = (sizeof($elements) % $max_elements);
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