<?php
  require_once("include/headers.php");
  
  // définit le nombre max de'éléments à afficher par page
  $max_elements = 100;
  
  if (isset($_POST['page']) && is_numeric($_POST['page']))
    $page = $_POST['page'];
  else
    $page = 1;
    
  if (isset($_POST['search']))
    $search = $_POST['search'];
  else
    $search = "";
    
  if (isset($_POST['month']) && is_numeric($_POST['month'])){
    $month = $_POST['month'];
    $date_start = date("Y-" .($month) ."-1");
    $date_stop = date("Y-" .($month + 1) ."-1"); 
  }
  else
    $month = "";
    
  if (isset($_POST['type']))
    $type = $_POST['type'];
  else
    $type = "";
    
  if (isset($_POST['order']))
    $order_sql = $_POST['order'];
  else
    $order_sql = "";
    
  if (isset($_POST['order_type']) && $_POST['order_type'] !== "")
    $order_type = $_POST['order_type'];
  else
    $order_type = "asc";
    
  // recherche des personnes
  $persons = array();
  
  $sql = "SELECT mo.id
          FROM miki_person mp, miki_order mo
          WHERE mp.id = mo.id_person AND (
                mo.no_order like '%$search%' OR
                mp.firstname like '%$search%' OR 
                mp.lastname like '%$search%' OR 
                mp.npa like '%$search%' OR 
                mp.city like '%$search%' OR 
                mp.dept like '%$search%' OR 
                mp.country like '%$search%' OR 
                mp.email1 like '%$search%')";
                
  if ($month != "")
    $sql .= " and mo.date_created between '$date_start' and '$date_stop'";
    
  if ($type == 1)
    $sql .= " and mo.type != 2";
  elseif ($type == 2)
    $sql .= " and mo.type = 2";

  if ($order_sql == "")
    $sql .= " order by mo.date_created desc";
  elseif($order_sql == "date_created")
    $sql .= " order by mo.date_created $order_type";
  elseif($order_sql == "date_completed")
    $sql .= " order by mo.date_completed $order_type";
  elseif($order_sql == "date_payed")
    $sql .= " order by mo.date_payed $order_type";
  elseif($order_sql == "no_order")
    $sql .= " order by mo.no_order $order_type";
  elseif($order_sql == "type")
    $sql .= " order by mo.type $order_type";
  elseif($order_sql == "state")
    $sql .= " order by mo.state $order_type";
  elseif($order_sql == "price")
    $sql .= " order by mo.price_total $order_type";
  elseif($order_sql == "firstname")
    $sql .= " order by mp.firstname $order_type";
  elseif($order_sql == "lastname")
    $sql .= " order by mp.lastname $order_type";
  elseif($order_sql == "country")
    $sql .= " order by mp.country $order_type";  
  elseif($order_sql == "email")
    $sql .= " order by mp.email1 $order_type";
          
  $result = mysql_query($sql) or die("sql : <br />$sql");

  $orders = array();
  while($row = mysql_fetch_array($result)){
    $item = new Miki_order($row[0]);
    $orders[] = $item;
  }
?>

  <table id="main_table">
    <tr class="headers">
      <td style="width:15%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_order(<?php echo $page; ?>, '<?php echo $search; ?>', '<?php echo $month; ?>', 'no_order', '');"><?php echo _("No"); ?></a></td>
      <td style="width:15%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_order(<?php echo $page; ?>, '<?php echo $search; ?>', '<?php echo $month; ?>', 'lastname', '');"><?php echo _("Client"); ?></a></td>
      <!--<td style="width:15%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_order(<?php echo $page; ?>, '<?php echo $search; ?>', '<?php echo $month; ?>', 'member', '');"><?php echo _("Membre"); ?></a></td>-->
      <td style="width:20%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_order(<?php echo $page; ?>, '<?php echo $search; ?>', '<?php echo $month; ?>', 'date_completed', '');"><?php echo _("Date de la commande"); ?></a></td>
      <!--<td style="width:15%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_order(<?php echo $page; ?>, '<?php echo $search; ?>', '<?php echo $month; ?>', 'date_payed', '');"><?php echo _("Date de paiement"); ?></a></td>-->
      <td style="width:15%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_order(<?php echo $page; ?>, '<?php echo $search; ?>', '<?php echo $month; ?>', 'price', '');"><?php echo _("Montant"); ?></a></td>
      <td style="width:15%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_order(<?php echo $page; ?>, '<?php echo $search; ?>', '<?php echo $month; ?>', 'state', '');"><?php echo _("Etat"); ?></a></td>
      <!--<td style="width:5%;text-align:center"><?php echo _("Payé"); ?></td>-->
      <td style="width:20%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      if (sizeof($orders) == 0)
        echo "<tr><td colspan='6'>" ._("Aucune commande ne correspond aux critères") ."</td></tr>";
      
      $n = 0;
      $start = ($page-1) * $max_elements;
      for ($x=$start; $x < ($max_elements + $start) && $x < sizeof($orders); $x++){
        $order = $orders[$x];
        
        $person = new Miki_person($order->id_person);
        
        $date_created = explode(" ", $order->date_created);
        $heure = $date_created[1];
        $heure = explode(":", $heure);
        $heure = $heure[0] .':' .$heure[1];
        $date_created = explode("-", $date_created[0]);
        $jour = $date_created[2];
        $mois = $date_created[1];
        $annee = $date_created[0];
        //$date_created = "$jour/$mois/$annee $heure";
        
        if ($order->date_completed == "")
          $date_completed = "Commande non terminée";
        else{
          $date_completed = explode(" ", $order->date_completed);
          $heure = $date_completed[1];
          $heure = explode(":", $heure);
          $heure = $heure[0] .':' .$heure[1];
          $date_completed = explode("-", $date_completed[0]);
          $jour = $date_completed[2];
          $mois = $date_completed[1];
          $annee = $date_completed[0];
          $date_completed = "$jour/$mois/$annee $heure";
        }
        
        if ($order->date_payed == "")
          $date_payed = "Commande non payée";
        else{
          $date_payed = explode(" ", $order->date_payed);
          $heure = $date_payed[1];
          $heure = explode(":", $heure);
          $heure = $heure[0] .':' .$heure[1];
          $date_payed = explode("-", $date_payed[0]);
          $jour = $date_payed[2];
          $mois = $date_payed[1];
          $annee = $date_payed[0];
          $date_payed = "$jour/$mois/$annee $heure";
        }
        
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        echo "
          <tr id='$order->id' class='pages' onmouseover=\"colorLine('$order->id');\" onmouseout=\"uncolorLine('$order->id');\">
            <td class='$class' style='height:2em'>
              <a href='index.php?pid=148&id=$order->id' title='" ._('Voir les détails') ."'>$order->no_order</a>
            </td>
            <td class='$class' style='height:2em'>
              <a href='index.php?pid=102&id=$person->id' title='Voir les détails du membre'>$person->lastname $person->firstname</a>
            </td>
            <td class='$class' style='height:2em'>
              $date_completed
            </td>
            <!--<td class='$class' style='height:2em'>
              $date_payed
            </td>-->
            <td class='$class' style='height:2em;'>"
              .number_format($order->price_total,2,'.',"'") ." CHF"
          ."</td>
            <td class='$class' style='height:2em'>";
              
              if ($order->state == 0)
                echo _("Non finalisée");
              elseif ($order->state == 1)
                echo _("En attente de paiement");
              elseif ($order->state == 2)
                echo _("Payée");
              elseif ($order->state == 3)
                echo _("Annulée");
              
      echo "</td>
            <!--<td class='$class' style='height:2em;text-align:center'>";
              if ($order->state == 1)
                echo "<a href='order_change_state.php?id=$order->id&state=2' title=\"" ._('Définir la facture comme ayant été payée') ."\"><img src='pictures/false.gif' border='0' alt='" ._('Définir la facture comme ayant été payée') ."' /></a>";
              elseif ($order->state == 2)
                echo "<a href='order_change_state.php?id=$order->id&state=1' title=\"" ._('Définir la facture comme n\'ayant pas été payée') ."\"><img src='pictures/true.gif' border='0' alt=\"" ._('Définir la facture comme n\'ayant pas été payée') ."\" /></a>";
              else
                echo "<img src='pictures/false.gif' border='0' alt=\"" ._("La commande n'est pas terminée") ."\" title=\"" ._("La commande n'est pas terminée") ."\" />";
      echo "</td>-->
            <td class='$class' style='height:2em;text-align:right;padding-right:10px'>
              <span style='margin-right:10px'><a href='index.php?pid=148&id=$order->id' title='" ._('Voir les détails') ."'><img src='pictures/view.gif' border='0' alt='" ._('Voir') ."' /></a></span>";
              
              if (test_right(50))
                echo "<span style='margin-right:10px'><a class='delete' href='order_delete.php?id=$order->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
                
              echo "<span><input type='checkbox' class='check_element' element_id='$order->id' /></span>
            </td>
          </tr>";
      }
      
      echo "<tr>
              <td colspan='4' style='height:30px;vertical-align:middle;text-align:left;padding-left:5px'>
                Nombre de commandes total : " .Miki_order::get_nb_orders() ."<br />
              </td>";
    
      // calcul le nombre de pages totales  
      $nb_pages = (int)(sizeof($orders) / $max_elements);
      $reste = (sizeof($orders) % $max_elements);
      if ($reste != 0)
        $nb_pages++;
      
      if ($nb_pages > 1){
        $code = "<td colspan='4' style='height:30px;vertical-align:top;text-align:right;padding-right:10px'>";
        
        if ($page != 1)
          $code .= "<a href='#' title='première page' onclick=\"search_order(1, '$search', '$month', '$order_sql', '$order_type');\"><<</a>&nbsp;&nbsp;<a href='#' title='page précédente' onclick=\"search_order(" .($page-1) .", '$search', '$month', '$order_sql', '$order_type');\"><</a>&nbsp;&nbsp;";
        
        if ($nb_pages <= 12){
          for ($x=1; $x<=$nb_pages; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_order($x, '$search', '$month', '$order_sql', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == $nb_pages){
          for ($x=($page-12); $x<=$page; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_order($x, '$search', '$month', '$order_sql', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == 1){
          for ($x=$page; $x<=($page+12); $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_order($x, '$search', '$month', '$order_sql', '$order_type');\">$x</a> | ";             
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
              $code .= "<a href='#' onclick=\"search_order($x, '$search', '$month', '$order_sql', '$order_type');\">$x</a> | ";             
          }
        }
        
        $code = mb_substr($code, 0, mb_strlen($code)-3);
        
        if ($page != $nb_pages)
          $code .= "&nbsp;&nbsp;<a href='#' title='page suivante' onclick=\"search_order(" .($page+1) .", '$search', '$month', '$order_sql', '$order_type');\">></a>&nbsp;&nbsp;<a href='#' title='dernière page' onclick=\"search_order($nb_pages, '$search', '$month', '$order_sql', '$order_type');\">>></a>";
        
        $code .= "</td></tr>";
        
        echo $code;
      }
  ?>
  </table>