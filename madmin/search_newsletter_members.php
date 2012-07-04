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
    
  if (isset($_POST['group_id']) && is_numeric($_POST['group_id']))
    $group_id = $_POST['group_id'];
  else
    $group_id = "";
    
  // recherche des abonnés aux newsletter
  $members = Miki_newsletter_member::search($search, $group_id, $order, $order_type, $max_elements, $page);
  
  // recherche le nombre de membres trouvés au total selon les critères de recherche
  $nb_members = sizeof(Miki_newsletter_member::search($search, $group_id, $order, $order_type));
  
  // recherche le nombre total de membres
  $nb_members_total = Miki_newsletter_member::get_nb_members()
?>

  <table id="main_table">
    <tr class="headers">
      <td style="width:25%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_membres(<?php echo $page; ?>, '<?php echo $group_id; ?>', '<?php echo $search; ?>', 'firstname', '');"><?php echo _("Prénom"); ?></a></td>
      <td style="width:25%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_membres(<?php echo $page; ?>, '<?php echo $group_id; ?>', '<?php echo $search; ?>', 'lastname', '');"><?php echo _("Nom"); ?></td>
      <td style="width:25%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_membres(<?php echo $page; ?>, '<?php echo $group_id; ?>', '<?php echo $search; ?>', 'email', '');"><?php echo _("Email"); ?></td>
      <td style="width:25%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      if (sizeof($members) == 0)
        echo "<tr><td colspan='4'>" ._("Il n'y a aucun abonné aux newsletter") ."</td></tr>";
      
      $n = 0;
      foreach ($members as $member){
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        echo "
          <tr id='$member->id' class='pages' onmouseover=\"colorLine('$member->id');\" onmouseout=\"uncolorLine('$member->id');\">
            <td class='$class' style='height:2em'>
              $member->firstname
            </td>
            <td class='$class' style='height:2em'>
              $member->lastname
            </td>
            <td class='$class' style='height:2em'>
              $member->email
            </td>
            <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>";
              
              if (test_right(31) || test_right(32) || test_right(33)){
                echo "<span style='margin-right:10px'><a href='index.php?pid=1192&id=$member->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
                echo "<span style='margin-right:10px'><a class='delete' href='newsletter_unsubscribe.php?id=$member->id&gid=$group_id' title='" ._('Désinscrire') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Désinscrire') ."' /></a></span>";
              }

        echo "<span><input type='checkbox' class='check_element' element_id='$member->id' /></span>
            </td>
          </tr>";
      }
      
      echo "<tr>
              <td colspan='4' style='height:30px;vertical-align:middle;text-align:left;padding-left:5px'>
                Nombre d'abonnés total : $nb_members_total<br />
              </td>
            </tr>";
    
      // calcul le nombre de pages totales  
      $nb_pages = (int)($nb_members / $max_elements);
      $reste = ($nb_members % $max_elements);
      $code = "";
      
      if ($reste != 0)
        $nb_pages++;
      
      if ($nb_pages > 1){
        $code .= "<tr><td colspan='4' style='height:30px;vertical-align:top;text-align:right;padding-right:10px'>";
        
        if ($page != 1)
          $code .= "<a href='#' title='première page' onclick=\"search_membres(1, '$group_id', '$search', '$order', '$order_type');\"><<</a>&nbsp;&nbsp;<a href='#' title='page précédente' onclick=\"search_membres(" .($page-1) .", $group_id, '$search', '$order', '$order_type');\"><</a>&nbsp;&nbsp;";
        
        if ($nb_pages <= 12){
          for ($x=1; $x<=$nb_pages; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_membres($x, '$group_id', '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == $nb_pages){
          for ($x=($page-12); $x<=$page; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_membres($x, '$group_id', '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == 1){
          for ($x=$page; $x<=($page+12); $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_membres($x, '$group_id', '$search', '$order', '$order_type');\">$x</a> | ";             
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
              $code .= "<a href='#' onclick=\"search_membres($x, '$group_id', '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        
        $code = mb_substr($code, 0, mb_strlen($code)-3);
        
        if ($page != $nb_pages)
          $code .= "&nbsp;&nbsp;<a href='#' title='page suivante' onclick=\"search_membres(" .($page+1) .", '$group_id', '$search', '$order', '$order_type');\">></a>&nbsp;&nbsp;<a href='#' title='dernière page' onclick=\"search_membres($nb_pages, $group_id, '$search', '$order', '$order_type');\">>></a>";
        
        $code .= "</td></tr>";
        
        echo $code;
      }
  ?>
  </table>