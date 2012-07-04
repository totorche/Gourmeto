<?php

if (!test_right(28) && !test_right(29) && !test_right(30))
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
      document.location = 'menu_delete.php?id=' + ids;
  }
}

</script>

<div id="arianne">
  <a href="#"><?php echo _("Disposition"); ?></a> > <?php echo _("Menus"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Menus actuels"); ?></h1>
  
  <div style="margin-bottom:10px">
    <?php 
    if (test_right(28)){ ?>
      <a href="index.php?pid=92" title="<?php echo _("Ajouter un menu"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un menu"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un menu"); ?></a>
    <?php } ?>
  </div>
  
  <table id="main_table">
    <tr class="headers">
      <td style="width:50%"><?php echo _("Menu"); ?></td>
      <td style="width:20%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
    
      $templates = Miki_menu::get_all_menus();
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
                if (test_right(29))
                  echo "<a href='index.php?pid=93&id=$template->id' title='" ._('Editer') ."'>$template->name</a>";
                else
                  echo $template->name;                  
              echo "</td>
              <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>";
                if (test_right(29))
                  echo "<span style='margin-right:10px'><a href='index.php?pid=93&id=$template->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
                if (test_right(30))
                  echo "<span style='margin-right:10px'><a class='delete' href='menu_delete.php?id=$template->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
                echo "<span><input type='checkbox' class='check_element' element_id='$template->id' /></span>
              </td>
            </tr>";
        }
      }else{
        echo "<tr><td colspan='8'>" ._("Aucun menu n'est présent dans la base de données") ."</td></tr>";
      }
    ?>
      
    </table>
    
    <div style="margin-top:10px;float:left">
      <?php 
      if (test_right(28)){ ?>
        <a href="index.php?pid=92" title="<?php echo _("Ajouter un menu"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un menu"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un menu"); ?></a>
      <?php } ?>
    </div>
    <div style="margin-top:10px;float:right">
      <?php echo _('Objets sélectionnés : '); ?>
      <select name="action" id="action">
        <?php 
          if (test_right(30)){ 
        ?>
        <option value="delete"><?php echo _('Supprimer'); ?></option>
        <?php } ?>
      </select>&nbsp;
      <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
    </div>
    <div style="clear:both"></div>
</div>