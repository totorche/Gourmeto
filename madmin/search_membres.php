<?php
  require_once("include/headers.php");
  
  // définit le nombre max de'éléments à afficher par page
  $max_elements = 50;
  
  if (isset($_POST['type']))
    $type = $_POST['type'];
  else
    $type = "all";
    
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
    
  // recherche des personnes
  $persons = array();
  $sql = "select mp.*, ma.username, ma.type
          from miki_person mp, miki_account ma
          where ma.state = 1 and
                ma.person_id = mp.id and (
                LOWER(mp.firstname) like '%$search%' or 
                LOWER(mp.lastname) like '%$search%' or 
                LOWER(mp.npa) like '%$search%' or 
                LOWER(mp.city) like '%$search%' or 
                LOWER(mp.dept) like '%$search%' or 
                LOWER(mp.country) like '%$search%' or 
                LOWER(mp.email1) like '%$search%' or 
                LOWER(ma.username) like '%$search%')";
  
  if ($order == "")
    $sql .= " order by ma.type, ma.username asc";
  elseif($order == "username")
    $sql .= " order by ma.username $order_type";
  elseif($order == "firstname")
    $sql .= " order by mp.firstname $order_type";
  elseif($order == "lastname")
    $sql .= " order by mp.lastname $order_type";
  elseif($order == "country")
    $sql .= " order by mp.country $order_type";  
  elseif($order == "city")
    $sql .= " order by mp.city $order_type";  
  elseif($order == "date_created")
    $sql .= " order by ma.date_created $order_type";
  elseif($order == "account_type")
    $sql .= " order by ma.type $order_type";
  elseif($order == "email")
    $sql .= " order by mp.email1 $order_type";
          
  $result = mysql_query($sql);

  while($row = mysql_fetch_array($result)){
    $persons[] = new Miki_person($row[0]);
  }
?>

  <table id="main_table">
    <tr class="headers">
      <td style="width:15%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_membres(<?php echo $page; ?>, '<?php echo $search; ?>', 'username', '');"><?php echo _("Pseudo"); ?></a></td>
      <td style="width:10%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_membres(<?php echo $page; ?>, '<?php echo $search; ?>', 'firstname', '');"><?php echo _("Nom"); ?></td>
      <td style="width:10%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_membres(<?php echo $page; ?>, '<?php echo $search; ?>', 'lastname', '');"><?php echo _("Prénom"); ?></td>
      <td style="width:10%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_membres(<?php echo $page; ?>, '<?php echo $search; ?>', 'cit<', '');"><?php echo _("Ville"); ?></td>
      <td style="width:10%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_membres(<?php echo $page; ?>, '<?php echo $search; ?>', 'country', '');"><?php echo _("Pays"); ?></td>
      <td style="width:10%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_membres(<?php echo $page; ?>, '<?php echo $search; ?>', 'email', '');"><?php echo _("Email"); ?></td>
      <td style="width:10%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_membres(<?php echo $page; ?>, '<?php echo $search; ?>', 'date_created', '');"><?php echo _("Date inscription"); ?></td>
      <td style="width:10%"><a href='#' style="color:#ffffff;text-decoration:none;font-weight:bold" onclick="search_membres(<?php echo $page; ?>, '<?php echo $search; ?>', 'account_type', '');"><?php echo _("Type de compte"); ?></td>
      <td style="width:15%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      if (sizeof($persons) == 0)
        echo "<tr><td colspan='10'>" ._("Aucun membre n'est présent dans la base de données") ."</td></tr>";
      
      $n = 0;
      $start = ($page-1) * $max_elements;
      for ($x=$start; $x < ($max_elements + $start) && $x < sizeof($persons); $x++){
        $person = $persons[$x];
        
        $account = new Miki_account();
        $account->load_from_person($person->id);
        $account_type = $account->type;
        //$company = new Miki_company($person->company_id);
        
        $username = $account->username;
        $firstname = $person->firstname;
        $lastname = $person->lastname;
        $email = $person->email1;
        $country = $person->country;
        $city = $person->city;
        
        $date = explode(" ", $account->date_created);
        $date = explode("-", $date[0]);
        $jour = $date[2];
        $mois = $date[1];
        $annee = $date[0];
        $date = "$jour/$mois/$annee";
        
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        echo "
          <tr id='$account->id' class='pages' onmouseover=\"colorLine('$account->id');\" onmouseout=\"uncolorLine('$account->id');\">
            <td class='$class' style='height:2em'>
              <a href='index.php?pid=102&id=$person->id' title='" ._('Editer') ."'>$username</a>
            </td>
            <td class='$class' style='height:2em'>
              $lastname
            </td>
            <td class='$class' style='height:2em'>
              $firstname
            </td>
            <td class='$class' style='height:2em'>
              $city
            </td>
            <td class='$class' style='height:2em'>
              $country
            </td>
            <td class='$class' style='height:2em'>
              $email
            </td>
            <td class='$class' style='height:2em'>
              $date
            </td>
            <td class='$class' style='height:2em'>
              $account_type
            </td>
            <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>
              <span style='margin-right:10px'><a href='index.php?pid=102&id=$person->id' title='" ._('Voir') ."'><img src='pictures/view.gif' border='0' alt='" ._('Voir') ."' /></a></span>";
              if (test_right(35))
                echo "<span style='margin-right:10px'><a href='index.php?pid=103&id=$person->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
              if (test_right(36))
                echo "<span style='margin-right:10px'><a class='delete' href='person_delete.php?id=$person->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
              
              echo "<span style='margin:20px 0'><a href='inscriptions/$person->id.pdf' title='" ._('Imprimer la lettre') ."' target='_blank'><img src='pictures/print.gif' border='0' alt='" ._('Imprimer') ."' /></a></span>";
              
              echo "<span><input type='checkbox' class='check_element' element_id='$person->id' /></span>
            </td>
          </tr>";
      }
      
      echo "<tr>
              <td colspan='4' style='height:30px;vertical-align:middle;text-align:left;padding-left:5px'>
                Nombre de membres total : " .sizeof($persons) ."<br />
              </td>";
    
      // calcul le nombre de pages totales  
      $nb_pages = (int)(sizeof($persons) / $max_elements);
      $reste = (sizeof($persons) % $max_elements);
      if ($reste != 0)
        $nb_pages++;
      
      if ($nb_pages > 1){
        $code .= "<td colspan='5' style='height:30px;vertical-align:top;text-align:right;padding-right:10px'>";
        
        if ($page != 1)
          $code .= "<a href='#' title='première page' onclick=\"search_membres(1, '$search', '$order', '$order_type');\"><<</a>&nbsp;&nbsp;<a href='#' title='page précédente' onclick=\"search_membres(" .($page-1) .", '$search', '$order', '$order_type');\"><</a>&nbsp;&nbsp;";
        
        if ($nb_pages <= 12){
          for ($x=1; $x<=$nb_pages; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_membres($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == $nb_pages){
          for ($x=($page-12); $x<=$page; $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_membres($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        elseif ($page == 1){
          for ($x=$page; $x<=($page+12); $x++){
            if ($x == $page)
              $code .= "<span style='font-weight:bold'>$x</span> | ";
            else
              $code .= "<a href='#' onclick=\"search_membres($x, '$search', '$order', '$order_type');\">$x</a> | ";             
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
              $code .= "<a href='#' onclick=\"search_membres($x, '$search', '$order', '$order_type');\">$x</a> | ";             
          }
        }
        
        $code = mb_substr($code, 0, mb_strlen($code)-3);
        
        if ($page != $nb_pages)
          $code .= "&nbsp;&nbsp;<a href='#' title='page suivante' onclick=\"search_membres(" .($page+1) .", '$search', '$order', '$order_type');\">></a>&nbsp;&nbsp;<a href='#' title='dernière page' onclick=\"search_membres($nb_pages, '$search', '$order', '$order_type');\">>></a>";
        
        $code .= "</td></tr>";
        
        echo $code;
      }
  ?>
  </table>