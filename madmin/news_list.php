<?php

if (!test_right(37) && !test_right(38) && !test_right(39))
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
      document.location = 'news_delete.php?id=' + ids;
  }
}

</script>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <?php echo _("Actualités"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Actualités actuelles"); ?></h1>
  
  <div style="margin-bottom:10px">
    <?php 
    if (test_right(37)){ ?>
      <a href="index.php?pid=122" title="<?php echo _("Ajouter une actualité"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une actualité"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter une actualité"); ?></a>
    <?php } ?>
  </div>
  
  <table id="main_table">
    <tr class="headers">
      <td style="width:70%"><?php echo _("Titre"); ?></td>
      <td style="width:8%;text-align:center"><?php echo _("Langue"); ?></td>
      <td style="width:8%;text-align:center"><?php echo _("Date"); ?></td>
      <td style="width:14%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
    
      $news = Miki_news::get_all_news();
      $n = 0;
      
      if (sizeof($news) == 0)
        echo "<tr><td colspan='8'>" ._("Aucune actualité n'est présente dans la base de données") ."</td></tr>";
      
      foreach($news as $new){
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $language = new Miki_language();
        $language->load_from_code($new->language);
          
        $date = explode(" ", $new->date);
        $date = explode("-", $date[0]);
        $date = $date[2] .'/' .$date[1] .'/' .$date[0];
        
        $n = ($n+1)%2;
        echo "
          <tr id='$new->id' class='pages' onmouseover=\"colorLine('$new->id');\" onmouseout=\"uncolorLine('$new->id');\">
            <td class='$class' style='height:2em'>";
              if (test_right(38))
                echo "<a href='index.php?pid=123&id=$new->id' title='" ._('Editer') ."'>$new->title</a>";
              else
                echo $new->title;
      echo "</td>
            <td class='$class' style='text-align:center'>
              <img src='pictures/flags/$language->picture' alt='$language->name' />
            </td>
            <td class='$class' style='text-align:center'>
              $date
            </td>
            <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>";
              if (test_right(38))
                echo "<span style='margin-right:10px'><a href='index.php?pid=123&id=$new->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
              if (test_right(39))
                echo "<span style='margin-right:10px'><a class='delete' href='news_delete.php?id=$new->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
              echo "<span><input type='checkbox' class='check_element' element_id='$new->id' /></span>
            </td>
          </tr>";
      }
    ?>
      
    </table>
    
    <div style="margin-top:10px;float:left">
      <?php 
      if (test_right(37)){ ?>
        <a href="index.php?pid=122" title="<?php echo _("Ajouter une actualité"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une actualité"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter une actualité"); ?></a>
      <?php } ?>
    </div>
    <div style="margin-top:10px;float:right">
      <?php echo _('Objets sélectionnés : '); ?>
      <select name="action" id="action">
        <?php 
          if (test_right(39)){ 
        ?>
        <option value="delete"><?php echo _('Supprimer'); ?></option>
        <?php } ?>
      </select>&nbsp;
      <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
    </div>
    <div style="clear:both"></div>
</div>