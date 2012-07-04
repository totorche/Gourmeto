<?php

if (!test_right(34) && !test_right(35) && !test_right(36)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

?>

<script type="text/javascript" src="scripts/checkform.js"></script>

<script type="text/javascript">

var page_now = 1;
window.addEvent('domready', function() {
  var myCheckForm = new checkForm($('search_membres_form'),{
                                        useAjax: true,
                                        ajaxSuccessFct: function(txt){
                                          $('membres').set('html', txt);
                                          
                                          $$(".delete").each(function(el){
                                            el.addEvent('click',function(){
                                              if (!confirm('<?php echo _("Etes-vous sûr de vouloir supprimer cet élément ?"); ?>'))
                                                return false;
                                            });
                                          });
                                        }
                                      });
  search_membres(page_now, '', 'username', 'asc');
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
      document.location = 'person_delete.php?id=' + ids;
  }
  else if ($('action').value == "active")
    document.location = 'person_change_state.php?state=1&id=' + ids;
  else if ($('action').value == "inactive")
    document.location = 'person_change_state.php?state=0&id=' + ids;
  else if ($('action').value == "paye")
    document.location = 'person_change_state.php?state=2&id=' + ids;
  else if ($('action').value == "non_paye")
    document.location = 'person_change_state.php?state=1&id=' + ids;
}

var order_type = 'asc';

function search_membres(page, search_data, order, order_t){
  page_now = page;
  
  if (order_t == '' && order != ''){
    if (order_type == 'asc')
      order_type = 'desc';
    else
      order_type = 'asc';
  }
  else if(order_t != '')
    order_type = order_t;
    
  var req = new Request({url:'search_membres.php', 
		onSuccess: function(txt){
			$('membres').set('html', txt);

      $$(".delete").each(function(el){
        el.addEvent('click',function(){
          if (!confirm('<?php echo _("Etes-vous sûr de vouloir supprimer cet élément ?"); ?>'))
            return false;
        });
      });
    }
  });
  req.send("search=" + search_data + "&order=" + order + "&order_type=" + order_type + "&page=" + page + "&lang=<?php echo $_SESSION['lang']; ?>");
}

</script>

<div id="arianne">
  <a href="#"><?php echo _("Utilisateurs/Groupes"); ?></a> > <?php echo _("Liste membres"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Liste des membres"); ?></h1>
  
  <form id="search_membres_form" action="search_membres.php" method="post" name="search_membres_form" enctype="application/x-www-form-urlencoded" style="margin:0 0 20px 0">
    <span style="font-size:16px;font-weight:bold">Recherche</span>
    &nbsp;&nbsp;<input type="text" name="search" style="width:200px;padding: 5px 5px 6px" />
    &nbsp;&nbsp;<input type="submit" value="Envoyer" />
  </form>
  
  <div style="overflow: hidden;">
    <div style="margin-bottom:10px;float:left">
      <?php 
      if (test_right(34)){ ?>
        <a href="index.php?pid=107" title="<?php echo _("Ajouter un membre"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un membre"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un membre"); ?></a>
      <?php } ?>
    </div>
    
    <div style="margin-bottom:10px;float:right">
      <a href="person_list_excel.php" title="<?php echo _("Télécharger la liste des membres"); ?>" target="_blank"><img src="pictures/excel.gif" border="0" alt="<?php echo _("Télécharger la liste des membres"); ?>" style="vertical-align:middle; margin-right: 5px;" /><?php echo _("Télécharger la liste des membres"); ?></a>
    </div>
  </div>
  
  <div id="membres"></div>
  
  <div style="margin-top:10px;float:left">
    <?php 
    if (test_right(34)){ ?>
      <a href="index.php?pid=107" title="<?php echo _("Ajouter un membre"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un membre"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un membre"); ?></a>
    <?php } ?>
  </div>
    
    <div style="margin-top:10px;float:right">
      <?php echo _('Objets sélectionnés : '); ?>
      <select name="action" id="action">
        <?php 
          if (test_right(36)){ 
        ?>
        <option value="delete"><?php echo _('Supprimer'); ?></option>
        <?php } ?>
        <option value="active"><?php echo _('Activer'); ?></option>
        <option value="inactive"><?php echo _('Désactiver'); ?></option>
        <option value="paye"><?php echo _('Payé'); ?></option>
        <option value="non_paye"><?php echo _('Non payé'); ?></option>
      </select>&nbsp;
      <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
    </div>
    <div style="clear:both"></div>
</div>