<?php

if (!test_right(6) && !test_right(14) && !test_right(22))
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
      ids += ";" + el.get('page_id');
  });
  
  // si aucune page n'est cochée
  if (ids == "")
    return false;
  
  // enlève le 1er ';'
  ids = ids.substring(1);
  
  // effectue l'opération demandée
  if ($('action').value == "delete"){
    if (confirm("<?php echo _('Etes-vous sûr de vouloir supprimer les éléments sélectionnés ?'); ?>"))
      document.location = 'template_part_delete?id=' + ids;
  }
}

</script>

<div id="arianne">
  <a href="#"><?php echo _("Disposition"); ?></a> > <?php echo _("Section"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Sections"); ?></h1>
  
  <div style="margin-bottom:10px">
    <?php 
    if (test_right(6)){ ?>
      <a href="index.php?pid=32" title="<?php echo _("Ajouter une section"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une section"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter une section"); ?></a>
    <?php } ?>
  </div>
  
  <table id="main_table">
    <tr class="headers">
      <td style="width:35%"><?php echo _("Titre"); ?></td>
      <td style="width:35%"><?php echo _("Balise pour utiliser ce bloc"); ?></td>
      <td style="width:30%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      try{
        $elements = Miki_template_part::get_all_parts();
        $n = 0;
        foreach($elements as $el){
          // détecte la class
          if ($n === 1)
            $class = "line1";
          else
            $class = "line2";
          
          $n = ($n+1)%2;
          
          echo "
            <tr id='$el->id' class='pages' onmouseover=\"colorLine('$el->id');\" onmouseout=\"uncolorLine('$el->id');\">
              <td class='$class' style='width:35%;height:2em'>";
              if (test_right(14))
                echo "<a href='index.php?pid=33&id=$el->id' title='" ._('Editer') ."'>$el->name</a>";
              else
                echo "$el->name";
              echo "</td>
              <td class='$class' style='width:35%;height:2em'>$el->code</td>
              <td class='$class' style='width:30%;height:2em;height:20px;text-align:right;padding-right:10px'>";
                if (test_right(14))
                  echo "<span style='margin-right:10px'><a href='index.php?pid=33&id=$el->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
                if (test_right(22))
                  echo "<span style='margin-right:10px'><a class='delete' href='template_part_delete?id=$el->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
                echo "<span><input type='checkbox' class='check_element' page_id='$el->id' /></span>
              </td>
            </tr>";
        }
      }catch(Exception $e){
        echo "<tr><td colspan='8'>" ._("Aucune section n'est présente dans la base de données") ."</td></tr>";
        $elements = array();
      }    
    ?>
      
    </table>
    
    <div style="margin-top:10px;float:left">
      <?php 
      if (test_right(6)){ ?>
        <a href="index.php?pid=32" title="<?php echo _("Ajouter une section"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une section"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter une section"); ?></a>
      <?php } ?>
    </div>
    <div style="margin-top:10px;float:right">
      <?php 
        if (test_right(22)){ 
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