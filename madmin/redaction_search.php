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
    
  // recherche des rédactions
  $redactions = Miki_redaction::search($search, "", "", $order, $order_type, $max_elements, $page);
  
  // récupert le nombre de rédactions existantes
  $nb_redactions = sizeof(Miki_redaction::search($search, "", "", $order, $order_type, "", ""));
?>

  <table id="main_table">
    <tr class="headers">
      <td style="width:30%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_redactions(<?php echo $page; ?>, '<?php echo $search; ?>', 'title', '');"><?php echo _("Titre"); ?></a></td>
      <td style="width:20%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_redactions(<?php echo $page; ?>, '<?php echo $search; ?>', 'person', '');"><?php echo _("Client"); ?></td>
      <td style="width:10%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_redactions(<?php echo $page; ?>, '<?php echo $search; ?>', 'nb_words', '');"><?php echo _("Nb de mots"); ?></td>
      <td style="width:15%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_redactions(<?php echo $page; ?>, '<?php echo $search; ?>', 'date_created', '');"><?php echo _("Date de commande"); ?></td>
      <td style="width:15%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_redactions(<?php echo $page; ?>, '<?php echo $search; ?>', 'date_published', '');"><?php echo _("Date de publication"); ?></td>
      <td style="width:10%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      if (sizeof($redactions) == 0)
        echo "<tr><td colspan='6'>" ._("Aucune rédaction n'est présent dans la base de données") ."</td></tr>";
      
      $n = 0;
      
      foreach($redactions as $redac){
      
        $person = new Miki_person($redac->id_person);
        
        $date_created = date("d/m/Y à H\hi", strtotime($redac->date_created));
        
        if ($redac->date_published == '0000-00-00 00:00:00')
          $date_published = "Non-publié";
        else
          $date_published = date("d/m/Y à H\hs", strtotime($redac->date_published));
        
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        echo "
          <tr id='$redac->id' class='pages' onmouseover=\"colorLine('$redac->id');\" onmouseout=\"uncolorLine('$redac->id');\">
            <td class='$class' style='height:2em'>";
              if (test_right(72))
                echo "<a href='index.php?pid=223&id=$redac->id' title='" ._('Editer') ."'>$redac->title</a>";
              else
                echo $redac->title;
      echo "</td>
            <td class='$class' style='height:2em'>
              $person->lastname $person->firstname
            </td>
            <td class='$class' style='height:2em'>
              $redac->nb_words
            </td>
            <td class='$class' style='height:2em'>
              $date_created
            </td>
            <td class='$class' style='height:2em'>
              $date_published
            </td>
            <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>";
              if (test_right(72))
                echo "<span style='margin-right:10px'><a href='index.php?pid=223&id=$redac->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
              if (test_right(73))
                echo "<span style='margin-right:10px'><a class='delete' href='redaction_delete.php?id=$redac->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
              
              echo "<span><input type='checkbox' class='check_element' element_id='$redac->id' /></span>
            </td>
          </tr>";
      }
      
      echo "<tr>
              <td style='height:30px;vertical-align:middle;text-align:left;padding-left:5px'>
                Nombre de rédactions total : $nb_redactions<br />
              </td>";
    
      // calcul le nombre de pages totales  
      $nb_pages = (int)($nb_redactions / $max_elements);
      $reste = ($nb_redactions % $max_elements);
      if ($reste != 0)
        $nb_pages++;
      
      if ($nb_pages > 1){
        $code = "<td colspan='4' style='height:30px;vertical-align:top;text-align:right;padding-right:10px'>";
        
        if ($page != 1)
          $code .= "<a href='#' title='première page' onclick=\"search_redactions(1, '$search', '$order', '$order_type');\"><<</a>&nbsp;&nbsp;<a href='#' title='page précédente' onclick=\"search_redactions(" .($page-1) .", '$search', '$order', '$order_type');\"><</a>&nbsp;&nbsp;";
        
        if ($nb_pages <= 12){
          for ($x=1; $x<=$nb_pages; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_redactions($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == $nb_pages){
          for ($x=($page-12); $x<=$page; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_redactions($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == 1){
          for ($x=$page; $x<=($page+12); $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_redactions($x, '$search', '$order', '$order_type');\">$x</a> | ";             
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
              $code .= "<a href='#' onclick=\"search_redactions($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        
        $code = mb_substr($code, 0, mb_strlen($code)-3);
        
        if ($page != $nb_pages)
          $code .= "&nbsp;&nbsp;<a href='#' title='page suivante' onclick=\"search_redactions(" .($page+1) .", '$search', '$order', '$order_type');\">></a>&nbsp;&nbsp;<a href='#' title='dernière page' onclick=\"search_redactions($nb_pages, '$search', '$order', '$order_type');\">>></a>";
        
        $code .= "</td></tr>";
        
        echo $code;
      }
  ?>
  </table>