<?php

if (!test_right(40) && !test_right(41) && !test_right(42)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

?>

<style type="text/css">

  #main_table td{
    vertical-align: middle;
  }

</style>

<script type="text/javascript">

window.addEvent('domready', function() {
  $$(".delete").each(function(el){
    el.addEvent('click',function(){
      if (!confirm('<?php echo _("Etes-vous sûr de vouloir supprimer cet élément ?"); ?>'))
        return false;
    });
  });
});

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
      document.location = 'album_delete.php?id=' + ids;
  }
  if ($('action').value == "enabled"){
      document.location = 'album_set_published.php?id=' + ids + '&state=1';
  }
  if ($('action').value == "disabled"){
      document.location = 'album_set_published.php?id=' + ids + '&state=0';
  }
}

</script>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <?php echo _("Albums photos"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Albums photos"); ?></h1>
  
  <div style="margin-bottom:10px">
    <?php 
    if (test_right(40)){ ?>
      <a href="index.php?pid=132" title="<?php echo _("Ajouter un album photos"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un album photos"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un album photos"); ?></a>
    <?php } ?>
  </div>
  
  <table id="main_table">
    <tr class="headers">
      <td style="width:10%"><?php echo _("Couverture"); ?></td>
      <td style="width:20%"><?php echo _("Nom"); ?></td>
      <td style="width:25%"><?php echo _("Titre"); ?></td>
      <td style="width:10%;text-align:center"><?php echo _("Nb de photos"); ?></td>
      <td style="width:10%;text-align:center"><?php echo _("Publié"); ?></td>
      <td style="width:10%"><?php echo _("Date de création"); ?></td>
      <td style="width:15%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
    
      $albums = Miki_album::get_all_albums();
      $n = 0;
      
      if (sizeof($albums) == 0)
        echo "<tr><td colspan='8'>" ._("Aucun album photos n'est présent dans la base de données") ."</td></tr>";
      
      foreach($albums as $album){
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
          
        $date = explode(" ", $album->date_creation);
        $date = explode("-", $date[0]);
        $date = $date[2] .'/' .$date[1] .'/' .$date[0];
        
        $n = ($n+1)%2;
        echo "
          <tr id='$album->id' class='pages' onmouseover=\"colorLine('$album->id');\" onmouseout=\"uncolorLine('$album->id');\">
            <td class='$class'>";
              if ($album->cover_picture != "NULL" && $album->cover_picture != ""){
                try{
                  $cover = new Miki_album_picture($album->cover_picture); 
                  echo "<img src='" .URL_BASE ."$album->folder/thumb/$cover->filename' alt='Couverture' />";
                }
                catch(Exception $e){
                  echo "non-définie";
                }
              }
              else
                echo "non-définie";
                
      echo "</td>
            <td class='$class'>";
              if (test_right(41))
                echo "<a href='index.php?pid=132&id=$album->id' title='" ._('Editer') ."'>" .$album->name ."</a>";
              else
                echo $album->name;
      echo "</td>
            <td class='$class'>";
              if (test_right(41))
                echo "<a href='index.php?pid=132&id=$album->id' title='" ._('Editer') ."'>" .$album->title[$_SESSION['lang']] ."</a>";
              else
                echo $album->title[$_SESSION['lang']];
      echo "</td>
            <td class='$class' style='text-align:center'>" .$album->get_nb_pictures() ."<br /><a href='index.php?pid=135&id=$album->id' title='" ._("Voir les photos") ."'>Voir les photos</a></td>
            <td class='$class' style='text-align:center'>";
              if(test_right(41) && $album->state == 0) 
                echo "<a href='album_set_published.php?id=$album->id&state=1' title='" ._('Publier l\'album') ."'><img src='pictures/false.gif' border='0' alt='" ._('Publier l\'album') ."' /></a>";
              elseif($album->state == 0)
                echo "<img src='pictures/false.gif' border='0' alt='" ._('Non publié') ."' title='" ._('Non publié') ."' />";
              elseif(test_right(41) && $album->state == 1) 
                echo "<a href='album_set_published.php?id=$album->id&state=0' title='" ._('Dépublier l\'album') ."'><img src='pictures/true.gif' border='0' alt='" ._('Dépublier l\'album') ."' /></a>";
              elseif($album->state == 1)
                echo "<img src='pictures/true.gif' border='0' alt='" ._('Publié') ."' title='" ._('Publié') ."' />";
      echo "</td>
            <td class='$class'>
              $date
            </td>
            <td class='$class' style='text-align:right;padding-right:10px'>";
              if (test_right(41)){
                echo "<span style='margin-right:10px'><a href='index.php?pid=133&id=$album->id' title='" ._('Ajouter des photos') ."'><img src='pictures/add.png' border='0' alt='" ._('Ajouter des photos') ."' /></a></span>";
                echo "<span style='margin-right:10px'><a href='index.php?pid=132&id=$album->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
              }
              if (test_right(42))
                echo "<span style='margin-right:10px'><a class='delete' href='album_delete.php?id=$album->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
              echo "<span><input type='checkbox' class='check_element' element_id='$album->id' /></span>
            </td>
          </tr>";
      }
    ?>
      
    </table>
    
    <div style="margin-top:10px;float:left">
      <?php 
      if (test_right(40)){ ?>
        <a href="index.php?pid=132" title="<?php echo _("Ajouter un album photos"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un album photos"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un album photos"); ?></a>
      <?php } ?>
    </div>
    <div style="margin-top:10px;float:right">
      <?php echo _('Objets sélectionnés : '); ?>
      <select name="action" id="action">
        <?php 
          if (test_right(41)){ 
        ?>
        <option value="enabled"><?php echo _('Publier'); ?></option>
        <option value="disabled"><?php echo _('Dépublier'); ?></option>
        <?php } ?>
        <?php 
          if (test_right(42)){ 
        ?>
        <option value="delete"><?php echo _('Supprimer'); ?></option>
        <?php } ?>
      </select>&nbsp;
      <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
    </div>
    <div style="clear:both"></div>
</div>