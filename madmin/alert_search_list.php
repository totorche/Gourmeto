<?php
  require_once("include/headers.php");
?>

<table id="main_table">
  <tr class="headers">
    <td style="width:80%"><?php echo _("Alerte sur ces termes"); ?></td>
    <td style="width:20%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
  </tr>
  
  <?php
    $alerts = Miki_alert::get_all_alerts(0);
    
    $n = 0;
    
    if (sizeof($alerts) == 0)
      echo "<tr><td colspan='2'>" ._("Aucune alerte n'a encore été entrée") ."</td></tr>";
      
    foreach($alerts as $alert){
      // détecte la class
      if ($n === 1)
        $class = "line1";
      else
        $class = "line2";
        
      $n = ($n+1)%2;
      
      echo "
        <tr id='$alert->id' class='pages' onmouseover=\"colorLine('$alert->id');\" onmouseout=\"uncolorLine('$alert->id');\">
          <td class='$class' style='height:2em'>
            $alert->sentence
          </td>
          <td class='$class' style='height:2em;text-align:right;padding-right:10px'>
            <span style='margin-right:10px'><a class='delete' href='javascript:delete_element($alert->id);' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>
            <span><input type='checkbox' class='check_element' page_id='$alert->id' /></span>
          </td>
        </tr>";
    }
  ?>
    
</table>