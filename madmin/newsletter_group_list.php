<?php

if (!test_right(31) && !test_right(32) && !test_right(33)){
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
  var myCheckForm = new checkForm($('search_groups_form'),{
                                        useAjax: true,
                                        ajaxSuccessFct: function(txt){
                                          $('groups').set('html', txt);
                                          
                                          $$(".delete").each(function(el){
                                            el.addEvent('click',function(){
                                              if (!confirm('<?php echo _("Etes-vous sûr de vouloir supprimer ce groupe ?"); ?>'))
                                                return false;
                                            });
                                          });
                                        }
                                      });
  search_groups(page_now, '', 'name', 'asc');
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
  if ($('action').value == "unsubscribe"){
    if (confirm("<?php echo _('Etes-vous sûr de vouloir supprimer les groupes sélectionnés ?'); ?>"))
      document.location = 'newsletter_group_delete.php?id=' + ids;
  }
}

var order_type = 'asc';

function search_groups(page, search_data, order, order_t){
  page_now = page;
  
  if (order_t == '' && order != ''){
    if (order_type == 'asc')
      order_type = 'desc';
    else
      order_type = 'asc';
  }
  else if(order_t != '')
    order_type = order_t;
    
  var req = new Request({url:'search_newsletter_groups.php', 
		onSuccess: function(txt){
			$('groups').set('html', txt);

      $$(".delete").each(function(el){
        el.addEvent('click',function(){
          if (!confirm('<?php echo _("Etes-vous sûr de vouloir supprimer ce groupe ?"); ?>'))
            return false;
        });
      });
    }
  });
  req.send("search=" + search_data + "&order=" + order + "&order_type=" + order_type + "&page=" + page);
}

</script>

<div id="arianne">
  <a href="index.php?pid=114"><?php echo _("Newsletter"); ?></a> > <?php echo _("Liste des groupes d'abonnés"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Liste des groupes d'abonnés aux newsletters"); ?></h1>
  
  <div style="float:left;margin-right:30px">
    <form id="search_groups_form" action="search_newsletter_groups.php" method="post" name="search_membres_form" enctype="application/x-www-form-urlencoded" style="margin:0 0 20px 0">
      <span style="font-size:16px;font-weight:bold">Recherche</span>
      &nbsp;&nbsp;<input type="text" name="search" id="search_input" style="width:200px;padding: 5px 5px 6px" />
      &nbsp;&nbsp;<input type="submit" value="Envoyer" />
    </form>
  </div>
  
  <div style="margin-bottom:10px;clear:both">
    <?php 
    if (test_right(31) || test_right(32) || test_right(33)){ ?>
      <a href="index.php?pid=1194" title="<?php echo _("Ajouter un groupe"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un groupe"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un groupe"); ?></a>
    <?php } ?>
  </div>
  
  <div id="groups"></div>
  
  <div style="margin-top:10px;float:left">
    <?php 
    if (test_right(31) || test_right(32) || test_right(33)){ ?>
      <a href="index.php?pid=1194" title="<?php echo _("Ajouter un groupe"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un groupe"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un groupe"); ?></a>
    <?php } ?>
  </div>
    
  <div style="margin-top:10px;float:right">
    <?php echo _('Groupes sélectionnés : '); ?>
    <select name="action" id="action">
      <option value="unsubscribe"><?php echo _('Supprimer'); ?></option>
    </select>&nbsp;
    <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
  </div>
  <div style="clear:both"></div>
</div>