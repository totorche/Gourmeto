<?php
  require_once('include/headers.php');
  require_once('shop_article_state.php');
  
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
    
  // recherche les options
  $options = Miki_shop_article_option::search("", $search, Miki_language::get_main_code(), false, false, $order, $order_type);
?>

  <table id="main_table">
    <tr class="headers">
      <td style="width:10%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_options(<?php echo $page; ?>, '<?php echo $search; ?>', 'ref', '');"><?php echo _("Réf"); ?></a></td>
      <td style="width:35%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_options(<?php echo $page; ?>, '<?php echo $search; ?>', 'name', '');"><?php echo _("Nom"); ?></a></td>
      <td style="width:10%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_options(<?php echo $page; ?>, '<?php echo $search; ?>', 'state', '');"><?php echo _("Etat"); ?></a></td>
      <td style="width:10%; text-align: center;"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_options(<?php echo $page; ?>, '<?php echo $search; ?>', 'stock', '');"><?php echo _("Stock"); ?></a></td>
      <td style="width:15%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_options(<?php echo $page; ?>, '<?php echo $search; ?>', 'price', '');"><?php echo _("Suppl. Prix"); ?></a></td>
      <td style="width:20%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      if (sizeof($options) == 0)
        echo "<tr><td colspan='6'>" ._("Aucune option ne correspond aux critères") ."</td></tr>";
      
      $n = 0;
      $start = ($page-1) * $max_elements;
      for ($x=$start; $x < ($max_elements + $start) && $x < sizeof($options); $x++){
        $option = $options[$x];
        
        $name = $option->get_name(Miki_language::get_main_code());
        $state = $shop_article_state[$option->state];
        
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        echo "
          <tr id='$option->id' class='pages' onmouseover=\"colorLine('$option->id');\" onmouseout=\"uncolorLine('$option->id');\">
            <td class='$class' style='height:2em'>
              <a href='index.php?pid=283&id=$option->id' title='" ._('Editer') ."'>$option->ref</a>
            </td>
            <td class='$class' style='height:2em'>
              <a href='index.php?pid=283&id=$option->id' title='" ._('Editer') ."'>$name</a>
            </td>
            <td class='$class' style='height:2em'>
              $state
            </td>
            <td class='$class' style='height:2em; text-align: center;'>
              $option->quantity
            </td>
            <td class='$class' style='height:2em'>"
              .number_format($option->price,2,'.',"'") ." CHF
            </td>
            <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>
                <span style='margin-right:10px'><a href='index.php?pid=283&id=$option->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>
                <span style='margin-right:10px'><a class='delete' href='article_option_delete.php?id=$option->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>
              
                <span><input type='checkbox' class='check_element' element_id='$option->id' /></span>
            </td>
          </tr>";
      }
      
      echo "<tr>
              <td colspan='2' style='height:30px;vertical-align:middle;text-align:left;padding-left:5px'>
                Nombre d'options total : " .Miki_shop_article_option::get_nb_options() ."<br />
              </td>";
    
      // calcul le nombre de pages totales  
      $nb_pages = (int)(sizeof($options) / $max_elements);
      $reste = (sizeof($options) % $max_elements);
      if ($reste != 0)
        $nb_pages++;
      
      if ($nb_pages > 1){
        $code = "<td colspan='4' style='height:30px;vertical-align:top;text-align:right;padding-right:10px'>";
        
        if ($page != 1)
          $code .= "<a href='#' title='première page' onclick=\"search_options(1, '$search', '$order', '$order_type');\"><<</a>&nbsp;&nbsp;<a href='#' title='page précédente' onclick=\"search_options(" .($page-1) .", '$search', '$order', '$order_type');\"><</a>&nbsp;&nbsp;";
        
        if ($nb_pages <= 12){
          for ($x=1; $x<=$nb_pages; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_options($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == $nb_pages){
          for ($x=($page-12); $x<=$page; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_options($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == 1){
          for ($x=$page; $x<=($page+12); $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_options($x, '$search', '$order', '$order_type');\">$x</a> | ";             
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
              $code .= "<a href='#' onclick=\"search_options($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        
        $code = mb_substr($code, 0, mb_strlen($code)-3);
        
        if ($page != $nb_pages)
          $code .= "&nbsp;&nbsp;<a href='#' title='page suivante' onclick=\"search_options(" .($page+1) .", '$search', '$order', '$order_type');\">></a>&nbsp;&nbsp;<a href='#' title='dernière page' onclick=\"search_options($nb_pages, '$search', '$order', '$order_type');\">>></a>";
        
        $code .= "</td></tr>";
        
        echo $code;
      }
      else{
        echo "<td colspan='4'>&nbsp;</td></tr>";
      }
  ?>
  </table>