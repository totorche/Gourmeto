<?php

if (!test_right(3) && !test_right(11) && !test_right(19))
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
      document.location = 'language_delete.php?id=' + ids;
  }
}

</script>

<div id="arianne">
  <a href="#"><?php echo _("Administration du site"); ?></a> > <?php echo _("Langues"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Langue"); ?></h1>
  
  <div style="margin-bottom:10px">
    <?php 
    if (test_right(3)){ ?>
      <a href="index.php?pid=42" title="<?php echo _("Ajouter une langue"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une langue"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter une langue"); ?></a>
    <?php } ?>    
  </div>
  
  <table id="main_table">
    <tr class="headers">
      <td style="width:10%"><?php echo _("Drapeau"); ?></td>
      <td style="width:30%"><?php echo _("Nom"); ?></td>
      <td style="width:10%"><?php echo _("Code"); ?></td>
      <td style="width:10%;text-align:center"><?php echo _("Langue principale"); ?></td>
      <td style="width:40%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      $elements = Miki_language::get_all_languages();
      $n = 0;
      
      if (sizeof($elements) == 0)
        echo "<tr><td colspan='8'>" ._("Aucune langue n'est présente dans la base de données") ."</td></tr>";
      
      foreach($elements as $el){
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        
        echo "
          <tr id='$el->id' class='pages' onmouseover=\"colorLine('$el->id');\" onmouseout=\"uncolorLine('$el->id');\">
            <td class='$class' style='height:2em'>";
            if ($el->picture != "")
              echo "<img src='pictures/flags/$el->picture' alt='$el->name' />";
            else
              echo _("Aucune image");
      echo "</td>
            <td class='$class' style='height:2em'>";
            if (test_right(11))
              echo "<a href='index.php?pid=43&id=$el->id' title='" ._('Editer') ."'>$el->name</a>";
            else
              echo $el->name;
            echo "</td>
            <td class='$class' style='height:2em'>$el->code</td>
            <td class='$class' style='text-align:center'>";
            
            if($el->is_main()) 
              echo "<img src='pictures/true.gif' border='0' alt='" ._('Langue principale') ."' title='" ._('Langue principale') ."' />";
            else
              echo "<img src='pictures/false.gif' border='0' alt='" ._('Langue secondaire') ."' title='" ._('Langue secondaire') ."' />";
            
      echo "</td>
            <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>";
              if (test_right(11))
                echo "<span style='margin-right:10px'><a href='index.php?pid=43&id=$el->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
              if (test_right(19))
                echo "<span style='margin-right:10px'><a class='delete' href='language_delete.php?id=$el->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
              echo "<span><input type='checkbox' class='check_element' page_id='$el->id' /></span>
            </td>
          </tr>";
      }   
    ?>
      
    </table>
    
    <div style="margin-top:10px;float:left">
      <?php 
      if (test_right(3)){ ?>
        <a href="index.php?pid=42" title="<?php echo _("Ajouter une langue"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une langue"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter une langue"); ?></a>
      <?php } ?>
    </div>
    <div style="margin-top:10px;float:right">
      <?php 
        if (test_right(19)){ 
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