<?php
  require_once("include/headers.php");
  
  // définit le nombre max de'éléments à afficher par page
  $max_elements = 50;
  
  if (isset($_POST['page']) && is_numeric($_POST['page']))
    $page = $_POST['page'];
  else
    $page = 1;
    
  if (isset($_POST['search']))
    $search = mb_strtolower($_POST['search'], 'utf-8');
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

  // recherche les groupes    
  $groups = Miki_newsletter_group::search($search, $order, $order_type, $max_elements, $page);
  
  // recherche le nombre de groupes total
  $nb_groups = Miki_newsletter_group::get_nb_groups();
?>

  <table id="main_table">
    <tr class="headers">
      <td style="width:40%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_groups(<?php echo $page; ?>, '<?php echo $search; ?>', 'name', '');"><?php echo _("Nom"); ?></a></td>
      <td style="width:30%"><?php echo _("Membres"); ?></td>
      <td style="width:30%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      if (sizeof($groups) == 0)
        echo "<tr><td colspan='3'>" ._("Il n'y a aucun groupe d'abonnés aux newsletter") ."</td></tr>";
      
      $n = 0;
      // parcourt les groupes
      foreach($groups as $group){

        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        echo "
          <tr id='$group->id' class='pages' onmouseover=\"colorLine('$group->id');\" onmouseout=\"uncolorLine('$group->id');\">
            <td class='$class' style='height:2em'>
              $group->name
            </td>
            <td class='$class' style='height:2em'>"
              .$group->get_nb_members()
          ."</td>
            <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>";
              
              if (test_right(31) || test_right(32) || test_right(33)){
                echo "<span style='margin-right:10px'><a href='index.php?pid=1195&id=$group->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
                echo "<span style='margin-right:10px'><a class='delete' href='newsletter_group_delete.php?id=$group->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
              }

        echo "<span><input type='checkbox' class='check_element' element_id='$group->id' /></span>
            </td>
          </tr>";
        
        $x++;
      }
  
      
      echo "<tr>
              <td colspan='3' style='height:30px;vertical-align:middle;text-align:left;padding-left:5px'>
                Nombre de groupes total : $nb_groups<br />
              </td>
            </tr>";
    
      // calcul le nombre de pages totales  
      $nb_pages = (int)($nb_groups / $max_elements);
      $reste = ($nb_groups % $max_elements);
      $code = "";
      
      if ($reste != 0)
        $nb_pages++;
      
      if ($nb_pages > 1){
        $code .= "<tr><td colspan='3' style='height:30px;vertical-align:top;text-align:right;padding-right:10px'>";
        
        if ($page != 1)
          $code .= "<a href='#' title='première page' onclick=\"search_groups(1, '$search', '$order', '$order_type');\"><<</a>&nbsp;&nbsp;<a href='#' title='page précédente' onclick=\"search_groups(" .($page-1) .", '$search', '$order', '$order_type');\"><</a>&nbsp;&nbsp;";
        
        if ($nb_pages <= 12){
          for ($x=1; $x<=$nb_pages; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_groups($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == $nb_pages){
          for ($x=($page-12); $x<=$page; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_groups($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == 1){
          for ($x=$page; $x<=($page+12); $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_groups($x, '$search', '$order', '$order_type');\">$x</a> | ";             
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
              $code .= "<a href='#' onclick=\"search_groups($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        
        $code = mb_substr($code, 0, mb_strlen($code)-3);
        
        if ($page != $nb_pages)
          $code .= "&nbsp;&nbsp;<a href='#' title='page suivante' onclick=\"search_groups(" .($page+1) .", '$search', '$order', '$order_type');\">></a>&nbsp;&nbsp;<a href='#' title='dernière page' onclick=\"search_groups($nb_pages, '$search', '$order', '$order_type');\">>></a>";
        
        $code .= "</td></tr>";
        
        echo $code;
      }
  ?>
  </table>