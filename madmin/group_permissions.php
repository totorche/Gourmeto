<?php

if (!test_right(10))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

// récupert les groupes à afficher
$all_groups = Miki_group::get_all_groups(false);
$groups = array();
if (isset($_GET['group_id']) && is_numeric($_GET['group_id']) && $_GET['group_id'] != -1){
  try{
    $groups[] = new Miki_group($_GET['group_id']);
  }catch(Exception $e){
    // si l'id du groupe n'existe pas 
    $groups = Miki_group::get_all_groups(false);
  }
}
else
  $groups = Miki_group::get_all_groups(false);

// détermine la taille de chaque colonne
$width = (int)(70/sizeof($groups));

?>

<script type="text/javascript">

// colorie une ligne d'un tableau
function colorLine(lineId){
  var td = $(lineId).getElements('td');
  td.each(function(item){
    item.addClass('mouseOver');
  });
}

// remet une ligne dans sa couleur normale
function uncolorLine(lineId){
  var td = $(lineId).getElements('td');
  td.each(function(item){
    item.removeClass('mouseOver');
  });
}

// n'affiche qu'un seul groupe
function select_group(group_id){
  document.location = 'index.php?pid=74&group_id=' + group_id;
}

</script>

<div id="arianne">
  <a href="#"><?php echo _("Utilisateurs/Groupes"); ?></a> > <?php echo _("Gestion des permissions"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Gestion des permissions"); ?></h1>
  <form id="formulaire" action="group_test_group_permissions.php" method="post" name="formulaire" enctype="application/x-www-form-urlencoded">
  
  <div style="margin-bottom:10px">
    <?php echo _("Sélection du groupe : "); ?>
    <select name="groups" onchange="javascript:select_group(this.value);">
      <option value="-1"><?php echo _("Tous les groupes"); ?></option>
      <?php
        foreach($all_groups as $group){
          echo "<option value='$group->id' "; if (isset($_GET['group_id']) && $_GET['group_id'] == $group->id) echo "selected='selected'"; echo ">$group->name</option>";
        }
      ?>
    </select>
  </div>
  
  <table id="main_table">
    <tr class="headers">
      <td style="width:30%">&nbsp;</td>
      <?php
        foreach($groups as $group)
          echo "<td style='width:$width;text-align:center'>$group->name</td>";
      ?>
    </tr>
    
    <?php
    
      $actions = Miki_action::get_all_actions();
      $titre = "";
      $n = 0;
      
      if (sizeof($actions) == 0)
        echo "<tr><td colspan='" .(sizeof($groups) + 1) ."'>" ._("Aucune action n'est présente dans la base de données") ."</td></tr>";
      
      // 1 ligne par action
      foreach($actions as $action){
        // si on a un nouveau titre, on l'affiche
        if ($titre != $action->title){
          echo "<tr>
                  <td style='padding:30px 0 10px 5px;font-weight:bold' class='pages' colspan='" .(sizeof($groups) + 1) ."'>$action->title</td>
                </tr>";
                
          $titre = $action->title;
          $n = 1;
        }
        
        // puis on affiche l'action
        
        // on détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        echo "
          <tr id='$action->id' class='pages' onmouseover=\"colorLine('$action->id');\" onmouseout=\"uncolorLine('$action->id');\">
            <td class='$class' style='height:2em'>$action->name</td>";
            
            // 1 colonne par groupe
            foreach($groups as $group){
              echo "<td class='$class' style='height:2em;text-align:center'><input type='checkbox' name='" .$action->id ."_" ."$group->id' "; if ($group->can($action->id)) echo "checked='checked'"; echo "/></td>";
            }
            
        echo "
          </tr>";
      }
      
    ?>
      
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=71'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
      </tr>
    </table>
    </form>
    <div style="clear:both"></div>
</div>