<?php

if (!test_right(71) && !test_right(72) && !test_right(73)){
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
  var myCheckForm = new checkForm($('search_redactions_form'),{
                                        useAjax: true,
                                        ajaxSuccessFct: function(txt){
                                          $('redactions').set('html', txt);
                                          
                                          $$(".delete").each(function(el){
                                            el.addEvent('click',function(){
                                              if (!confirm('<?php echo _("Etes-vous sûr de vouloir supprimer cet élément ?"); ?>'))
                                                return false;
                                            });
                                          });
                                        }
                                      });
  search_redactions(page_now, '', '', 'asc');
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
      document.location = 'redaction_delete.php?id=' + ids;
  }
}

var order_type = 'asc';

function search_redactions(page, search_data, order, order_t){
  page_now = page;
  
  if (order_t == '' && order != ''){
    if (order_type == 'asc')
      order_type = 'desc';
    else
      order_type = 'asc';
  }
  else if(order_t != '')
    order_type = order_t;
    
  var req = new Request({url:'redaction_search.php', 
		onSuccess: function(txt){
			$('redactions').set('html', txt);

      $$(".delete").each(function(el){
        el.addEvent('click',function(){
          if (!confirm('<?php echo _("Etes-vous sûr de vouloir supprimer cet élément ?"); ?>'))
            return false;
        });
      });
    }
  });
  req.send("search=" + search_data + "&order=" + order + "&order_type=" + order_type + "&page=" + page);
}

</script>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <?php echo _("Rédactions"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Liste des rédactions"); ?></h1>
  
  <form id="search_redactions_form" action="redaction_search.php" method="post" name="search_redactions_form" enctype="application/x-www-form-urlencoded" style="margin:0 0 20px 0">
    <span style="font-size:16px;font-weight:bold">Recherche</span>
    &nbsp;&nbsp;<input type="text" name="search" style="width:200px;padding: 5px 5px 6px" />
    &nbsp;&nbsp;<input type="submit" value="Envoyer" />
  </form>
  
  <div style="margin-bottom:10px">
    <?php 
    if (test_right(59)){ ?>
      <a href="index.php?pid=222" title="<?php echo _("Ajouter une rédaction"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une rédaction"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter une rédaction"); ?></a>
    <?php } ?>
  </div>
  
  <div id="redactions"></div>
  
  <div style="margin-top:10px;float:left">
    <?php 
    if (test_right(59)){ ?>
      <a href="index.php?pid=222" title="<?php echo _("Ajouter une rédaction"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une rédaction"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter une rédaction"); ?></a>
    <?php } ?>
  </div>
    
    <div style="margin-top:10px;float:right">
      <?php echo _('Rédactions sélectionnées : '); ?>
      <select name="action" id="action">
        <?php 
          if (test_right(61)){ 
        ?>
        <option value="delete"><?php echo _('Supprimer'); ?></option>
        <?php } ?>
      </select>&nbsp;
      <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
    </div>
    <div style="clear:both"></div>
</div>