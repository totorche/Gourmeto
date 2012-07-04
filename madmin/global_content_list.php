<?php

if (!test_right(8) && !test_right(16) && !test_right(24))
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
      document.location = 'global_content_delete.php?id=' + ids;
  }
}

</script>

<div id="arianne">
  <a href="#"><?php echo _("Disposition"); ?></a> > <?php echo _("Blocs de contenu globaux"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Blocs de contenu global actuels"); ?></h1>
  
  <div style="margin-bottom:10px">
    <?php 
    if (test_right(8)){ ?>
      <a href="index.php?pid=52" title="<?php echo _("Ajouter un bloc de contenu global"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un bloc de contenu global"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un bloc de contenu global"); ?></a>
    <?php } ?>
  </div>
  
  <table id="main_table">
    <tr class="headers">
      <td style="width:35%"><?php echo _("Blocs de contenu global"); ?></td>
      <td style="width:35%"><?php echo _("Balise pour utiliser ce bloc de contenu"); ?></td>
      <td style="width:20%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
    
      try{
        $elements = Miki_global_content::get_all_global_contents();
        $n = 0;
        foreach($elements as $element){
          // détecte la class
          if ($n === 1)
            $class = "line1";
          else
            $class = "line2";
          
          $n = ($n+1)%2;
          echo "
            <tr id='$element->id' class='pages' onmouseover=\"colorLine('$element->id');\" onmouseout=\"uncolorLine('$element->id');\">
              <td class='$class' style='height:2em'>";
                if (test_right(16))
                  echo "<a href='index.php?pid=53&id=$element->id' title='" ._('Editer') ."'>$element->name</a>";
                else
                  echo $element->name;
              echo "</td>
              <td class='$class' style='width:35%;height:2em'>[miki_gc='$element->name']</td>
              <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>";
                if (test_right(16))
                  echo "<span style='margin-right:10px'><a href='index.php?pid=53&id=$element->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
                if (test_right(24))
                  echo "<span style='margin-right:10px'><a class='delete' href='global_content_delete.php?id=$element->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
                echo "<span><input type='checkbox' class='check_element' element_id='$element->id' /></span>
              </td>
            </tr>";
        }
      }catch(Exception $e){
        echo "<tr><td colspan='8'>" ._("Aucun bloc de contenu global n'est présent dans la base de données") ."</td></tr>";
      }
    ?>
      
  </table>
  
  <div style="margin-top:10px;float:left">
    <?php 
    if (test_right(8)){ ?>
      <a href="index.php?pid=52" title="<?php echo _("Ajouter un bloc de contenu global"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un bloc de contenu global"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un bloc de contenu global"); ?></a>
    <?php } ?>
  </div>
  <div style="margin-top:10px;float:right">
    <?php 
      if (test_right(24)){ 
        echo _('Objets sélectionnés : '); 
    ?>
    <select name="action" id="action">
      <option value="delete"><?php echo _('Supprimer'); ?></option>
    </select>&nbsp;
    <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
    <?php } ?>
  </div>
  <div style="clear:both"></div>
</div>