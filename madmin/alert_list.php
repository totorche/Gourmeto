<?php

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

?>

<script type="text/javascript" src="scripts/mootools_more.js"></script>

<script type="text/javascript">

// met à jour la liste des alertes
var maj_liste = new Request({
  url: 'alert_search_list.php',
  onRequest: function(){
    $('alerts').set('html', "<img src='pictures/ajax-loader.gif' alt='Veuillez patienter...' />");
    $('alerts').setStyle('text-align', 'center'); 
  },
  onSuccess: function(html){
    $('alerts').setStyle('text-align', 'left');
    $('alerts').set('html', html);
  }
});


/**
 * Affiche le message de succès ou d'erreur après ajout d'une alerte
 * 
 * msg : élément html représentant le message à traiter
 * state : si 1, on l'affiche. Si 0, on le masque
 */    
var print_message = function (msg, state){
  // si state != 0 ou 1, on arrête là
  if (state != 0 && state != 1){
    return false;
  }
  
  msg.setStyle('display', 'block');
  msg.set('tween', {duration: 'long'});
  
  // si on doit l'afficher, on l'affiche
  if (state == 1){
    msg.tween('opacity', state);
  }
  // sinon on le masque puis on l'efface
  else if (state == 0){
    var myFx = new Fx.Tween(msg, {property: 'opacity'});
    myFx.start(1,0).chain(
      function(){msg.setStyle('display', 'none');}
    );
  }
 
  // si on affiche le message, on le masque dans 5 secondes
  if (state == 1){
    print_message.delay(5000, this, new Array(msg, 0));
  }
}

window.addEvent('domready', function() {
  $('form_add_alert').addEvent('submit', function(){
    var req = new Request({
      url: this.get('action'),
      onSuccess: function(txt){
        if (txt == 1){
          maj_liste.send();
          $('alert_input').value = '';
          print_message($('successmsg'), 1);
        }
        else{
          print_message($('errormsg'), 1);
        }
      },
      onFailure: function(){
        print_message($('errormsg'), 1);
      } 
    }); 
    
    req.send($('form_add_alert').toQueryString());
    return false;
  });
  
  // affiche la liste des alertes
  maj_liste.send();
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

// supprime un élément
function delete_element(ids){
  if (confirm("<?php echo _('Etes-vous sûr de vouloir supprimer les éléments sélectionnés ?'); ?>")){
    var req = new Request({
      url: 'alert_delete.php',
      onSuccess: function(txt){
        if (txt == 1){
          maj_liste.send();
          print_message($('successmsg_delete'), 1);
        }
        else{
          print_message($('errormsg_delete'), 1);
        }
      },
      onFailure: function(){
        print_message($('errormsg_delete'), 1);
      } 
    }); 
    
    req.send('id=' + ids);
  }
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
    delete_element(ids);
  }
}

</script>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <?php echo _("Alertes"); ?>
</div>

<div class="errormsg" id="errormsg" style="display:none;opacity:0;-moz-opacity:0;-ms-filter:'alpha(opacity=0)';filter:alpha(opacity=0);">
  Une erreur est survenue lors de la création de l'alerte !
</div>

<div class="successmsg" id="successmsg" style="display:none;opacity:0;-moz-opacity:0;-ms-filter:'alpha(opacity=0)';filter:alpha(opacity=0);">
  L'alerte a été ajoutée avec succès !
</div>

<div class="errormsg" id="errormsg_delete" style="display:none;opacity:0;-moz-opacity:0;-ms-filter:'alpha(opacity=0)';filter:alpha(opacity=0);">
  Une erreur est survenue lors de la suppression des alertes sélectionnées !
</div>

<div class="successmsg" id="successmsg_delete" style="display:none;opacity:0;-moz-opacity:0;-ms-filter:'alpha(opacity=0)';filter:alpha(opacity=0);">
  Les alertes sélectionnées ont été supprimés avec succès !
</div>


<div id="first_contener">
  <h1><?php echo _("Alertes actuelles"); ?></h1>
  
  <div style="margin-bottom:10px;text-align:right">
    <form id="form_add_alert" action="alert_test_new.php" method="post" name="form_add_alert" enctype="application/x-www-form-urlencoded">
      Nouvelle alerte : <input type="text" name="alert" id="alert_input" style="width:200px" />
      &nbsp;&nbsp;
      <input type="submit" value="Ajouter" />
    </form>
  </div>
  
  <div id="alerts"></div>
  
    
    <div style="margin-top:10px;float:right">
      Objets sélectionnés : 
      <select name="action" id="action">
        <option value="delete"><?php echo _('Supprimer'); ?></option>
      </select>&nbsp;
      <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
    </div>
    <div style="clear:both"></div>
</div>