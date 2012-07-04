<?php
if (!test_right(74)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}
?>

<script type="text/javascript" src="scripts/checkform.js"></script>

<script type="text/javascript">

var page_now = 1;
window.addEvent('domready', function() {
  var myCheckForm = new checkForm($('search_comments_form'),{
                                        useAjax: true,
                                        ajaxSuccessFct: function(txt){
                                          $('comments').set('html', txt);
                                          
                                          $$(".delete").each(function(el){
                                            el.addEvent('click',function(){
                                              if (!confirm('<?php echo _("Etes-vous sûr de vouloir supprimer cet élément ?"); ?>'))
                                                return false;
                                            });
                                          });
                                        }
                                      });
  search_comments(page_now, '', '', 'asc');
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
      document.location = 'comment_delete.php?id=' + ids;
  }
  else if ($('action').value == "approved"){
    if (confirm("<?php echo _('Etes-vous sûr de vouloir modifier les éléments sélectionnés ?'); ?>"))
      document.location = 'comment_change_state.php?state=1&id=' + ids;
  }
  else if ($('action').value == "pending"){
    if (confirm("<?php echo _('Etes-vous sûr de vouloir modifier les éléments sélectionnés ?'); ?>"))
      document.location = 'comment_change_state.php?state=2&id=' + ids;
  }
  else if ($('action').value == "spam"){
    if (confirm("<?php echo _('Etes-vous sûr de vouloir modifier les éléments sélectionnés ?'); ?>"))
      document.location = 'comment_change_state.php?state=3&id=' + ids;
  }
}

var order_type = 'asc';

function search_comments(page, search_data, order, order_t, state){
  page_now = page;
  
  if (state === undefined) { state = ''; }
  
  if (order_t == '' && order != ''){
    if (order_type == 'asc')
      order_type = 'desc';
    else
      order_type = 'asc';
  }
  else if(order_t != '')
    order_type = order_t;
    
  var req = new Request({url:'comment_search.php', 
		onSuccess: function(txt){
			$('comments').set('html', txt);

      $$(".delete").each(function(el){
        el.addEvent('click',function(){
          if (!confirm('<?php echo _("Etes-vous sûr de vouloir supprimer cet élément ?"); ?>'))
            return false;
        });
      });
      
      // coche la checkbox si clic sur la ligne
      $$("table#main_table tr").addEvent('click', function(e) {
        var checkbox = $(this).getElement("input[type=checkbox]");
        if (checkbox)
          checkbox.checked = !checkbox.checked;
      });
      
      // Evite un double cochage / décochage avec le clic sur la ligne
      $$("table#main_table input[type=checkbox]").addEvent('click', function(event) {
        event.stopPropagation();
      });
    }
  });
  req.send("search=" + search_data + "&state=" + state + "&order=" + order + "&order_type=" + order_type + "&page=" + page);
}

</script>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <?php echo _("Activités"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Liste des commentaires"); ?></h1>
  
  <form id="search_comments_form" action="comment_search.php" method="post" name="search_comments_form" enctype="application/x-www-form-urlencoded" style="margin:0 0 20px 0">
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
          <select name="state">
            <option value=""><?php echo _('Tous'); ?></option>
            <option value="1"><?php echo _('Approuvé'); ?></option>
            <option value="2"><?php echo _('En attente de validation'); ?></option>
            <option value="3"><?php echo _('Indésirable'); ?></option>
          </select>
          <select name="oclass">
            <option value=""><?php echo _('Tous'); ?></option>
            <option value="Miki_page"><?php echo _('Page'); ?></option>
            <option value="Miki_news"><?php echo _('Actualité'); ?></option>
            <option value="Miki_shop_article"><?php echo _('Produit'); ?></option>
            <option value="Miki_event"><?php echo _('Evénement'); ?></option>
            <option value="Miki_album"><?php echo _('Album photo'); ?></option>
            <option value="Miki_album_picture"><?php echo _('Photo'); ?></option>
          </select>
          <input type="submit" value="Envoyer" />
        </td>
      </tr>
    </table>
  </form>
  
  <div style="margin-bottom:10px">
  </div>
  
  <div id="comments"></div>
  
  <div style="margin-top:10px;float:left">
  </div>
    
    <div style="margin-top:10px;float:right">
      <?php echo _('Activités sélectionnées : '); ?>
      <select name="action" id="action">
        <option value="delete"><?php echo _('Supprimer'); ?></option>
        <option value="approved"><?php echo _('Approuvé'); ?></option>
        <option value="pending"><?php echo _('En attente de validation'); ?></option>
        <option value="spam"><?php echo _('Indésirable'); ?></option>
      </select>&nbsp;
      <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
    </div>
    <div style="clear:both"></div>
</div>

<?php
  // stock l'url actuel pour un retour après opérations
  $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
?>