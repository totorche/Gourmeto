<?php

if (!test_right(68) && !test_right(69) && !test_right(70)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

?>

<style type="text/css">
  
  #places{
    width: 100%;
    overflow: hidden;
  }
  
  ul.table{
    width: 100%;
    display: table;
  }
  
  ul.table ul{
    width: 100%;
    display: table;
  }
  
  li.headers{
    width: 100%;
    overflow: hidden;
    display: table-row;
    padding: 10px 0;
  }
  
  li.headers div{
    display: table-cell;
    vertical-align: middle;
    height: 39px;
  }
  
  li.places{
    width: 100%;
    overflow: hidden;
    display: table-row;
  }
  
  li.places div{
    padding: 0.2em 0;
    display: table-cell;
    vertical-align: middle;
  }
  
  li.places:nth-child(odd){
    background-color: #f0f0f0;
  }
  
  li.places:nth-child(even){
    background-color: #ffffff;
  }

</style>

<script type="text/javascript" src="scripts/checkform.js"></script>
<script type="text/javascript" src="scripts/mootools_more.js"></script>

<script type="text/javascript">

var page_now = 1;
var sort = null;
var busy = false;
  
window.addEvent('domready', function() {
  var myCheckForm = new checkForm($('search_places_form'),{
                                        useAjax: true,
                                        ajaxSuccessFct: function(txt){
                                          $('places').set('html', txt);
                                          
                                          updateSort();
                                          
                                          $$(".delete").each(function(el){
                                            el.addEvent('click',function(){
                                              if (!confirm('<?php echo _("Etes-vous sûr de vouloir supprimer cet élément ?"); ?>'))
                                                return false;
                                            });
                                          });
                                        }
                                      });
  search_places(page_now, '', 'date_created', 'asc');
});

// Initialise la liste triable
function updateSort(){
  
  //sort = new Sortables($$(".places"), {
  sort = new Sortables($$("ul.table"), {
        	 clone: false,
        	 opacity: 0.5,
        	 revert: true,
        	 onComplete: function(){
             sort.detach();
             busy = true;
             
             // on récupert leur ordre
              var ordre = sort.serialize(function(element, index){
                          return element.id + '=' + index;
                        }).join('|');
                        
              //var ordre = sort.serialize();
            
              var req = new Request({url:'place_repos.php', 
            		onSuccess: function(txt){
            		  busy = false;
                }
              });
              req.send("pos=" + ordre);
           }
         });
         
  sort.detach();

  Array.each($$('.enable_sortable'), function(el){
    el.addEvent('mousedown', function(){
      if (!busy){
        sort.attach();
      }
    });
    
    el.addEvent('mouseup', function(){
      //sort.detach();
    });
  });
}

// colorie une ligne d'un tableau
function colorLine(lineId){ 
  var div = $(lineId).getElements('div');
  $(lineId).addClass('mouseOver');
  div.each(function(item){
    item.addClass('mouseOver');
  });
}

// remet une ligne dans sa couleur normale
function uncolorLine(lineId){
  var div = $(lineId).getElements('div');
  $(lineId).removeClass('mouseOver');
  div.each(function(item){
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
      document.location = 'place_delete.php?id=' + ids;
  }
  else if ($('action').value == "active")
    document.location = 'place_change_state.php?state=1&id=' + ids;
  else if ($('action').value == "inactive")
    document.location = 'place_change_state.php?state=0&id=' + ids;
}

var order_type = 'asc';

function search_places(page, search_data, order, order_t){
  page_now = page;
  
  if (order_t == '' && order != ''){
    if (order_type == 'asc')
      order_type = 'desc';
    else
      order_type = 'asc';
  }
  else if(order_t != '')
    order_type = order_t;
    
  var req = new Request({url:'place_search.php', 
		onSuccess: function(txt){
			$('places').set('html', txt);
			
			updateSort();

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
  <a href="#"><?php echo _("Contenu"); ?></a> > <?php echo _("Bonnes adresses"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Liste des bonnes adresses"); ?></h1>
  
  <form id="search_places_form" action="place_search.php" method="post" name="search_places_form" enctype="application/x-www-form-urlencoded" style="margin:0 0 20px 0">
    <span style="font-size:16px;font-weight:bold">Recherche</span>
    &nbsp;&nbsp;<input type="text" name="search" style="width:200px;padding: 5px 5px 6px" />
    &nbsp;&nbsp;<input type="submit" value="Envoyer" />
  </form>
  
  <div style="margin-bottom:10px">
    <?php 
    if (test_right(68)){ ?>
      <a href="index.php?pid=212" title="<?php echo _("Ajouter une bonne adresse"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une bonne adresse"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter une bonne adresse"); ?></a>
    <?php } ?>
  </div>
  
  <div id="places"></div>
  
  <div style="margin-top:10px;float:left">
    <?php 
    if (test_right(68)){ ?>
      <a href="index.php?pid=212" title="<?php echo _("Ajouter une bonne adresse"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une bonne adresse"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter une bonne adresse"); ?></a>
    <?php } ?>
  </div>
    
    <div style="margin-top:10px;float:right">
      <?php echo _('Bonnes adresses sélectionnées : '); ?>
      <select name="action" id="action">
        <?php 
          if (test_right(70)){ 
        ?>
        <option value="delete"><?php echo _('Supprimer'); ?></option>
        <?php } ?>
        <option value="active"><?php echo _('Publier'); ?></option>
        <option value="inactive"><?php echo _('Dépublier'); ?></option>
      </select>&nbsp;
      <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
    </div>
    <div style="clear:both"></div>
</div>