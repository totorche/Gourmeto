<?php

if (!test_right(48) && !test_right(49) && !test_right(50))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

?>

<script type="text/javascript" src="scripts/checkform.js"></script>

<script type="text/javascript">

var page_now = 1;
var month_now = '';

window.addEvent('domready', function() {
  var myCheckForm = new checkForm($('search_orders_form'),{
                                        useAjax: true,
                                        ajaxSuccessFct: function(txt){
                                          $('orders').set('html', txt);
                                          
                                          $$(".delete").each(function(el){
                                            el.addEvent('click',function(){
                                              if (!confirm('<?php echo _("Etes-vous sûr de vouloir supprimer cet élément ?"); ?>'))
                                                return false;
                                            });
                                          });
                                        }
                                      });
  search_order(page_now, '', '', '', '');
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
      document.location = 'order_delete.php?id=' + ids;
  }
}

var order_type = 'asc';

function search_order(page, search_data, month, order, order_t){
  page_now = page;
  month_now = month;
  
  if (order_t == '' && order != ''){
    if (order_type == 'asc')
      order_type = 'desc';
    else
      order_type = 'asc';
  }
  else if(order_t != '')
    order_type = order_t;
    
  var req = new Request({url:'search_orders.php', 
		onSuccess: function(txt){
			$('orders').set('html', txt);
			
			$$(".delete").each(function(el){
        el.addEvent('click',function(){
          if (!confirm('<?php echo _("Etes-vous sûr de vouloir supprimer cet élément ?"); ?>'))
            return false;
        });
      });
    }
  });
  req.send("search=" + search_data + "&month=" + month + "&order=" + order + "&order_type=" + order_type + "&page=" + page);
}

</script>

<div id="arianne">
  <a href="#"><?php echo _("Shops"); ?></a> > <?php echo _("Liste commandes"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Liste des commandes"); ?></h1>
  
  <div style="float:left;margin-right:30px">
    <form id="search_orders_form" action="search_orders.php" method="post" name="search_orders_form" enctype="application/x-www-form-urlencoded" style="margin:0 0 20px 0">
      <table>
        <tr>
          <td><span style="font-size:16px;font-weight:bold">Recherche</span></td>
          <td style="padding-left: 5px;">
            <input type="text" name="search" style="width:200px;padding: 5px 5px 6px" />
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td style="padding-left: 5px;">
            <?php echo _("Mois :"); ?> 
            <select name="month">
              <option value="" selected="selected">Tous</option>
              <option value="1">Janvier</option>
              <option value="2">Février</option>
              <option value="3">Mars</option>
              <option value="4">Avril</option>
              <option value="5">Mai</option>
              <option value="6">Juin</option>
              <option value="7">Juillet</option>
              <option value="8">Août</option>
              <option value="9">Septembre</option>
              <option value="10">Octobre</option>
              <option value="11">Novembre</option>
              <option value="12">Décembre</option>
            </select>
            &nbsp;&nbsp;
            <?php echo _("Type de commande :"); ?> 
            <select name="type">
              <option value=""><?php echo _("Tous"); ?></option>
              <option value="1"><?php echo _("Commandes traditionnelles"); ?></option>
              <option value="2"><?php echo _("Deals"); ?></option>
            </select>
            <input type="submit" value="Envoyer" />
          </td>
        </tr>
      </table>
    </form>
  </div>
  
  <div id="orders" style="clear:both"></div>
  
  <div style="margin-top:10px;float:right">
    <?php echo _('Objets sélectionnés : '); ?>
    <select name="action" id="action">
      <?php 
        if (test_right(50)){
      ?>
      <option value="delete"><?php echo _('Supprimer'); ?></option>
      <?php } ?>
    </select>&nbsp;
    <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
  </div>
  <div style="clear:both"></div>
</div>