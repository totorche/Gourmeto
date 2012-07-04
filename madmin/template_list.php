<?php

if (!test_right(5) && !test_right(13) && !test_right(21))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

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

// coche ou décoche tous les éléments de la liste
function check_element(item){
  $$('.check_element').each(function(el){
    el.checked = item.checked;
  });
}

function action_send(){
  var ids = "";
  // récupert les pages cochées
  $$('.check_element').each(function(el){
    if (el.checked)
      ids += ";" + el.get('element_id');
  });
  
  // si aucune page n'est cochée
  if (ids == "")
    return false;
  
  // enlève le 1er ';'
  ids = ids.substring(1);
  
  // effectue l'opération demandée
  if ($('action').value == "delete"){
    if (confirm("<?php echo _('Etes-vous sûr de vouloir supprimer les éléments sélectionnés ?'); ?>"))
      document.location = 'template_delete.php?id=' + ids;
  }
  else if ($('action').value == "active")
    document.location = 'template_change_state.php?a=1&id=' + ids;
  else if ($('action').value == "inactive")
    document.location = 'template_change_state.php?a=0&id=' + ids;
}

</script>

<div id="arianne">
  <a href="#"><?php echo _("Disposition"); ?></a> > <?php echo _("Gabarits"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Gabarits actuels"); ?></h1>
  
  <div style="margin-bottom:10px">
    <?php 
    if (test_right(5)){ ?>
      <a href="index.php?pid=22" title="<?php echo _("Ajouter un gabarit"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un gabarit"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un gabarit"); ?></a>
    <?php } ?>
  </div>
  
  <table id="main_table">
    <tr class="headers">
      <td style="width:50%"><?php echo _("Gabarit"); ?></td>
      <td style="width:8%;text-align:center"><?php echo _("Actif"); ?></td>
      <td style="width:8%;text-align:center"><?php echo _("Défaut"); ?></td>
      <td style="width:14%;text-align:center">&nbsp;</td>
      <td style="width:20%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
    
      $templates = Miki_template::get_all_templates();
      if (sizeof($templates) > 0){
        $n = 0;
        foreach($templates as $template){
          // détecte la class
          if ($n === 1)
            $class = "line1";
          else
            $class = "line2";
          
          $n = ($n+1)%2;
          echo "
            <tr id='$template->id' class='pages' onmouseover=\"colorLine('$template->id');\" onmouseout=\"uncolorLine('$template->id');\">
              <td class='$class' style='height:2em'>";
                if (test_right(13))
                  echo "<a href='index.php?pid=23&id=$template->id' title='" ._('Editer') ."'>$template->name</a>";
                else
                  echo $template->name;                  
              echo "</td>
              <td class='$class' style='height:2em;text-align:center'>";
              if($template->state == 2) 
                echo "<img src='pictures/true.gif' border='0' alt='" ._('Actif') ."' />"; 
              elseif($template->state == 1)
                echo "<a href='template_change_state.php?id=$template->id' title='" ._('Définir comme inactif') ."'><img src='pictures/true.gif' border='0' alt='" ._('Définir comme inactif') ."' /></a>";
              else
                echo "<a href='template_change_state.php?id=$template->id' title='" ._('Définir comme actif') ."'><img src='pictures/false.gif' border='0' title='" ._('Définir comme actif') ."' /></a>";
        echo "</td>
              <td class='$class' style='height:2em;text-align:center'>";
              if($template->state == 2) 
                echo "<img src='pictures/true.gif' border='0' alt='" ._('Gabarit par défaut') ."' />"; 
              else 
                echo "<a href='template_set_default.php?id=$template->id' title='" ._('Définir comme gabarit par défaut') ."'><img src='pictures/false.gif' border='0' alt='" ._('Définir comme gabarit par défaut') ."' /></a>"; 
        echo "</td>
              <td class='$class' style='height:2em;height:20px;'><a href='template_set_for_all.php'>" ._("Choisir pour toutes les pages") ."</a></td>
              <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>";
                if (test_right(13))
                  echo "<span style='margin-right:10px'><a href='index.php?pid=23&id=$template->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
                if (test_right(21))
                  echo "<span style='margin-right:10px'><a class='delete' href='template_delete.php?id=$template->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
                echo "<span><input type='checkbox' class='check_element' element_id='$template->id' /></span>
              </td>
            </tr>";
        }
      }else{
        echo "<tr><td colspan='8'>" ._("Aucun gabarit n'est présent dans la base de données") ."</td></tr>";
      }
    ?>
      
    </table>
    
    <div style="margin-top:10px;float:left">
      <?php 
      if (test_right(5)){ ?>
        <a href="index.php?pid=22" title="<?php echo _("Ajouter un gabarit"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un gabarit"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un gabarit"); ?></a>
      <?php } ?>
    </div>
    <div style="margin-top:10px;float:right">
      <?php echo _('Objets sélectionnés : '); ?>
      <select name="action" id="action">
        <?php 
          if (test_right(21)){ 
        ?>
        <option value="delete"><?php echo _('Supprimer'); ?></option>
        <?php } ?>
        <option value="active"><?php echo _('Activer'); ?></option>
        <option value="inactive"><?php echo _('Désactiver'); ?></option>
      </select>&nbsp;
      <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
    </div>
    <div style="clear:both"></div>
</div>