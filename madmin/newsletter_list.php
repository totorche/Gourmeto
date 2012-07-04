<?php

if (!test_right(31) && !test_right(32) && !test_right(33))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

?>

<script type="text/javascript">

window.addEvent('domready', function() {
  /*$$(".envoi").each(function(el){
    el.addEvent('click',function(){
      if (!confirm('<?php echo _("Etes-vous sûr de vouloir envoyer cette newsletter ?"); ?>'))
        return false;
    });
  });*/
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
      document.location = 'newsletter_delete.php?id=' + ids;
  }
}

</script>

<div id="arianne">
  <a href="#"><?php echo _("Newsletter"); ?></a> > <?php echo _("Newsletters"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Newsletters actuelles"); ?></h1>
  
  <div style="margin-bottom:10px">
    <?php 
    if (test_right(31)){ ?>
      <a href="index.php?pid=115" title="<?php echo _("Ajouter une newsletter"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une newsletter"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter une newsletter"); ?></a>
    <?php } ?>
  </div>
  
  <table id="main_table">
    <tr class="headers">
      <td style="width:25%"><?php echo _("Newsletter"); ?></td>
      <td style="width:15%"><?php echo _("Gabarit"); ?></td>
      <td style="width:7%;text-align:center"><?php if(test_right(32)) echo _("Etat"); ?></td>
      <td style="width:10%;text-align:center"><?php if(test_right(32)) echo _("Ouverte"); ?></td>
      <td style="width:23%">&nbsp;</td>
      <td style="width:10%">&nbsp;</td>
      <td style="width:10%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      $newsletters = Miki_newsletter::get_all_newsletter("position");
      $n = 0;
      
      if (sizeof($newsletters) == 0)
        echo "<tr><td colspan='8'>" ._("Aucune newsletter n'est présente dans la base de données") ."</td></tr>";
        
      foreach($newsletters as $newsletter){
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        $gabarit = new Miki_newsletter_template($newsletter->template_id);
        echo "
          <tr id='$newsletter->id' class='pages' onmouseover=\"colorLine('$newsletter->id');\" onmouseout=\"uncolorLine('$newsletter->id');\">
            <td class='$class' style='height:2em'>";
              if (test_right(32))
                echo "<a href='index.php?pid=116&id=$newsletter->id' title='" ._('Editer') ."'>" .stripslashes($newsletter->name) ."</a>";
              else
                echo $newsletter->name; 
              echo "</td>
            <td class='$class' style='height:2em'>$gabarit->name</td>
            <td class='$class' style='height:2em;text-align:center'>";
            if($newsletter->state == 0)
              echo "<img src='pictures/false.gif' border='0' alt='" ._('Non envoyée')  ."' title='" ._('Non envoyée') ."' />";
            elseif($newsletter->state == 1)
              echo "<img src='pictures/warning.gif' border='0' title='" ._('Partiellement envoyée') ."' title='" ._('Partiellement envoyée') ."' />";
            elseif($newsletter->state == 2)
              echo "<img src='pictures/true.gif' border='0' title='" ._('Complètement envoyée') ."' title='" ._('Complètement envoyée') ."' />";
      echo "</td>
            <td class='$class' style='height:2em;text-align:center'>";
              echo $newsletter->get_opened() ."/" .$newsletter->nb_send;  
      echo "</td>
            <td class='$class' style='height:2em'>
              <a href='index.php?pid=1197&id=$newsletter->id&init=1' class='envoi' title='" ._('Envoyer la newsletter') ."'>" ._("Envoyer") ."</a>
              &nbsp;-&nbsp;&nbsp;<a href='index.php?pid=1196&id=$newsletter->id' title='" ._('Tester la newsletter') ."'>" ._("Tester") ."</a>
              
              <form id='fractal-$newsletter->id' action='http://getfractal.com/validate' method='post' target='_blank'>
                <textarea name='html' style='display: none;'>$newsletter->content</textarea>
                <a href=\"javascript:$('fractal-$newsletter->id').submit();\">vérif</a>
              </form>
              
              ";
              
              if ($newsletter->state == 1)
                echo "&nbsp;&nbsp;-&nbsp;&nbsp;<a href='index.php?pid=1198&id=$newsletter->id' class='envoi' title=\"" ._("Continuer l'envoi") ."\">" ._("Continuer l'envoi") ."</a>";
              
      echo "</td>
            <td class='$class' style='height:2em'>";
              if (test_right(32))
                echo "<a href='newsletter_duplicate.php?id=$newsletter->id' title='" ._('Dupliquer la newsletter') ."'>" ._("Dupliquer") ."</a>";
              else
                echo "&nbsp;";
      echo "</td>
            <td class='$class' style='width:15%;height:2em;height:20px;text-align:right;padding-right:10px'>
              <span style='margin-right:10px'><a href='newsletter_view.php?id=$newsletter->id' title='" ._('Voir') ."'><img src='pictures/view.gif' border='0' alt='" ._('Voir') ."' /></a></span>";
              
              if (test_right(32))
                echo "<span style='margin-right:10px'><a href='index.php?pid=116&id=$newsletter->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
              if (test_right(33))
                echo "<span style='margin-right:10px'><a class='delete' href='newsletter_delete.php?id=$newsletter->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
                
              echo "<span><input type='checkbox' class='check_element' page_id='$newsletter->id' /></span>
            </td>
          </tr>";
        }
    ?>
      
    </table>
    
    <div style="margin-top:10px;float:left">
      <?php 
      if (test_right(31)){ ?>
        <a href="index.php?pid=115" title="<?php echo _("Ajouter une newsletter"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une newsletter"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter une newsletter"); ?></a>
      <?php } ?>
    </div>
    <div style="margin-top:10px;float:right">
      <?php echo _('Objets sélectionnés : '); ?>
      <select name="action" id="action">
        <?php 
          if (test_right(23)){ 
        ?>
        <option value="delete"><?php echo _('Supprimer'); ?></option>
        <?php } ?>
      </select>&nbsp;
      <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
    </div>
    <div style="clear:both"></div>
</div>