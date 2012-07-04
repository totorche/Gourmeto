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
var group_now = '';

window.addEvent('domready', function() {
  var myCheckForm = new checkForm($('search_membres_form'),{
                                        useAjax: true,
                                        ajaxSuccessFct: function(txt){
                                          $('membres').set('html', txt);
                                          
                                          $$(".delete").each(function(el){
                                            el.addEvent('click',function(){
                                              if (!confirm('<?php echo _("Etes-vous sûr de vouloir annuler cet abonnement ?"); ?>'))
                                                return false;
                                            });
                                          });
                                        }
                                      });
  search_membres(page_now, group_now, '', 'username', 'asc');
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
    if (confirm("<?php echo _('Etes-vous sûr de vouloir annuler les abonnements sélectionnés ?'); ?>"))
      document.location = 'newsletter_unsubscribe.php?id=' + ids + "&gid=" + $('members_groups').value;
  }
}

function change_group(val){
  $('group_input').set('value', val);
  $('search_input').set('value', '');
  search_membres(page_now, val, '', '', '');
}

var order_type = 'asc';

function search_membres(page, group_id, search_data, order, order_t){
  page_now = page;
  group_now = group_id;
  
  if (order_t == '' && order != ''){
    if (order_type == 'asc')
      order_type = 'desc';
    else
      order_type = 'asc';
  }
  else if(order_t != '')
    order_type = order_t;
    
  var req = new Request({url:'search_newsletter_members.php', 
		onSuccess: function(txt){
			$('membres').set('html', txt);

      $$(".delete").each(function(el){
        el.addEvent('click',function(){
          if (!confirm('<?php echo _("Etes-vous sûr de vouloir annuler cet abonnement ?"); ?>'))
            return false;
        });
      });
    }
  });
  req.send("search=" + search_data + "&order=" + order + "&order_type=" + order_type + "&page=" + page + "&group_id=" + group_id);
}

</script>

<div id="arianne">
  <a href="index.php?pid=114"><?php echo _("Newsletter"); ?></a> > <?php echo _("Liste des abonnés"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Liste des abonnés aux newsletters"); ?></h1>
  
  <div style="float:left;margin-right:30px">
    <form id="search_membres_form" action="search_newsletter_members.php" method="post" name="search_membres_form" enctype="application/x-www-form-urlencoded" style="margin:0 0 20px 0">
      <input type="hidden" name="group_id" id="group_input" value="" />
      
      <span style="font-size:16px;font-weight:bold">Recherche</span>
      &nbsp;&nbsp;<input type="text" name="search" id="search_input" style="width:200px;padding: 5px 5px 6px" />
      &nbsp;&nbsp;<input type="submit" value="Envoyer" />
    </form>
  </div>
  <div>
    Choisissez le groupe d'abonnés que vous désirez afficher : 
    <select id="members_groups" onchange="javascript:change_group(this.value);">
      <option value="" selected="selected">Tous</option>
      
      <?php
        $groups = Miki_newsletter_group::get_all_groups();
        
        foreach($groups as $group){
          echo "<option value='$group->id'>$group->name</option>\n";
        }
      ?>
    </select>
  </div>
  
  <div style="margin-bottom:10px;clear:both">
    <?php 
    if (test_right(31) || test_right(32) || test_right(33)){ ?>
      <a href="index.php?pid=1191" title="<?php echo _("Ajouter un abonné"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un abonné"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un abonné"); ?></a>
    <?php } ?>
  </div>
  
  <div id="membres"></div>
  
  <div style="margin-top:10px;float:left">
    <?php 
    if (test_right(31) || test_right(32) || test_right(33)){ ?>
      <a href="index.php?pid=1191" title="<?php echo _("Ajouter un abonné"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un abonné"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un abonné"); ?></a>
    <?php } ?>
  </div>
    
  <div style="margin-top:10px;float:right">
    <?php echo _('Membres sélectionnés : '); ?>
    <select name="action" id="action">
      <option value="unsubscribe"><?php echo _('Désinscrire'); ?></option>
    </select>&nbsp;
    <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
  </div>
  <div style="clear:both"></div>
</div>