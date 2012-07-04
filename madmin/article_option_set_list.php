<?php
if (!test_right(51) && !test_right(52) && !test_right(53)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}
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
      document.location = 'article_option_set_delete?id=' + ids;
  }
}

</script>

<div id="arianne">
  <a href="#"><?php echo _("Shop"); ?></a> > <?php echo _("Liste des sets d'options"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Set d'options"); ?></h1>
  
  <div style="margin-bottom:10px">
    <a href="index.php?pid=292" title="<?php echo _("Ajouter un set"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un set"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un set"); ?></a>
  </div>
  
  <table id="main_table">
    <tr class="headers">
      <td style="width:70%"><?php echo _("Nom"); ?></td>
      <td style="width:30%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      try{
        $elements = Miki_shop_article_option_set::get_all_sets();
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
              <td class='$class' style='width:35%;height:2em'>
                <a href='index.php?pid=293&id=$el->id' title='" ._('Editer') ."'>$el->name</a>
              </td>
              <td class='$class' style='width:30%;height:2em;height:20px;text-align:right;padding-right:10px'>
                <span style='margin-right:10px'><a href='index.php?pid=293&id=$el->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>
                <span style='margin-right:10px'><a class='delete' href='article_option_set_delete.php?id=$el->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>
                <span><input type='checkbox' class='check_element' page_id='$el->id' /></span>
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
      <a href="index.php?pid=292" title="<?php echo _("Ajouter un set"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un set"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un set"); ?></a>
    </div>
    <div style="margin-top:10px;float:right">
      <?php echo _('Objets sélectionnés : '); ?>
      <select name="action" id="action">
        <option value="delete"><?php echo _('Supprimer'); ?></option>
      </select>&nbsp;
      <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
    </div>
    <div style="clear:both"></div>
</div>

<?php
  // stock l'url actuel pour un retour après opérations
  $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
?>