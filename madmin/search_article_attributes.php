<?php
  require_once('include/headers.php');
  require_once('shop_article_attribute_type.php');
  
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
    
  // recherche les attributs
  $sql = "SELECT * FROM miki_shop_article_attribute WHERE (LOWER(name) LIKE '%$search%' OR LOWER(value) LIKE '%$search%')"; 
  
  if ($order == "")
    $sql .= " ORDER BY name $order_type";
  elseif ($order == "name")
    $sql .= " ORDER BY name $order_type";
  elseif($order == "type")
    $sql .= " ORDER BY type $order_type";
  elseif($order == "value")
    $sql .= " ORDER BY value $order_type";
          
  $result = mysql_query($sql) or die("Erreur SQL : $sql");

  $attributes = array();
  
  $x = 0;
  while($row = mysql_fetch_array($result)){
    $attributes[$x]['id'] = $row['id'];
    $attributes[$x]['name'] = $row['name'];
    $attributes[$x]['type'] = $shop_article_attribute_type[$row['type']];
    $attributes[$x]['value'] = $row['value'];
    $x++;
  }
?>

  <table id="main_table">
    <tr class="headers">
      <td style="width:28%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_attributes(<?php echo $page; ?>, '<?php echo $search; ?>', 'name', '');"><?php echo _("Nom"); ?></a></td>
      <td style="width:28%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_attributes(<?php echo $page; ?>, '<?php echo $search; ?>', 'state', '');"><?php echo _("Type"); ?></td>
      <td style="width:28%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_attributes(<?php echo $page; ?>, '<?php echo $search; ?>', 'value', '');"><?php echo _("Valeurs"); ?></td>
      <td style="width:16%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      if (sizeof($attributes) == 0)
        echo "<tr><td colspan='4'>" ._("Aucun attribut ne correspond aux critères") ."</td></tr>";
      
      $n = 0;
      $start = ($page-1) * $max_elements;
      for ($x=$start; $x < ($max_elements + $start) && $x < sizeof($attributes); $x++){
        $attribute = $attributes[$x];
        
        $id = $attribute['id'];
        $name = $attribute['name'];
        $type = $attribute['type'];
        $value = $attribute['value'];
        
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        echo "
          <tr id='$id' class='pages' onmouseover=\"colorLine('$id');\" onmouseout=\"uncolorLine('$id');\">
            <td class='$class' style='height:2em'>
              <a href='index.php?pid=155&id=$id' title='" ._('Editer') ."'>$name</a>
            </td>
            <td class='$class' style='height:2em'>
              $type
            </td>
            <td class='$class' style='height:2em'>
              $value
            </td>
            <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>";
              if (test_right(52)){
                echo "<span style='margin-right:10px'><a href='index.php?pid=155&id=$id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
              }
              if (test_right(53))
                echo "<span style='margin-right:10px'><a class='delete' href='article_attribute_delete.php?id=$id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
              
              echo "<span><input type='checkbox' class='check_element' element_id='$id' /></span>
            </td>
          </tr>";
      }
      
      echo "<tr>
              <td colspan='5' style='height:30px;vertical-align:middle;text-align:left;padding-left:5px'>
                Nombre d'attributs total : " .sizeof($attributes) ."<br />
              </td>";
    
      // calcul le nombre de pages totales  
      $nb_pages = (int)(sizeof($attributes) / $max_elements);
      $reste = (sizeof($attributes) % $max_elements);
      if ($reste != 0)
        $nb_pages++;
      
      if ($nb_pages > 1){
        $code = "<td colspan='5' style='height:30px;vertical-align:top;text-align:right;padding-right:10px'>";
        
        if ($page != 1)
          $code .= "<a href='#' title='première page' onclick=\"search_attributes(1, '$search', '$order', '$order_type');\"><<</a>&nbsp;&nbsp;<a href='#' title='page précédente' onclick=\"search_attributes(" .($page-1) .", '$search', '$order', '$order_type');\"><</a>&nbsp;&nbsp;";
        
        if ($nb_pages <= 12){
          for ($x=1; $x<=$nb_pages; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_attributes($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == $nb_pages){
          for ($x=($page-12); $x<=$page; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_attributes($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == 1){
          for ($x=$page; $x<=($page+12); $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_attributes($x, '$search', '$order', '$order_type');\">$x</a> | ";             
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
              $code .= "<a href='#' onclick=\"search_attributes($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        
        $code = mb_substr($code, 0, mb_strlen($code)-3);
        
        if ($page != $nb_pages)
          $code .= "&nbsp;&nbsp;<a href='#' title='page suivante' onclick=\"search_attributes(" .($page+1) .", '$search', '$order', '$order_type');\">></a>&nbsp;&nbsp;<a href='#' title='dernière page' onclick=\"search_attributes($nb_pages, '$search', '$order', '$order_type');\">>></a>";
        
        $code .= "</td></tr>";
        
        echo $code;
      }
  ?>
  </table>