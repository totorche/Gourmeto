<?php

if (!test_right(43) && !test_right(44) && !test_right(45)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

?>

<script type="text/javascript">

// pour savoir qu'est-ce qu'on est en train de faire : 
// 0 = rien
// 1 = ajout
// 2 = modification
// 3 = déplacement (étape 1)
// 4 = déplacement (étape 2)
// 5 = suppression 
job = 0;


window.addEvent('domready', function() {
  $('form_category').addEvent('submit', function(){
    if (job == 0){
      alert('Aucune action sélectionnée');
      return false;
    }
    else if (job == 3){
      alert('Aucune catégorie source n\'a encore été sélectionnée.');
      return false;
    }
  });
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
      document.location = 'category_delete.php?id=' + ids;
  }
}

// recharge toutes les catégories
function get_categories(cat, type){

  // si la catégorie == -1, on vide le select
  if(cat == -1){
    $('categorie' + type).set('html', '');
    $('categorie' + type).removeClass('select_category');
    
    // et on vide également les catégories suivantes
  	if (type <= 3)
  	 get_categories(-1, type + 1);
  	 
    return;
  }
  
  // sinon on le recharge
  var cat_id = cat.split("&&")[0];
  
  // effectue les opérations désirées selon le job en cours
  get_info_category(cat_id);
  
  if (type <= 4){
    var req = new Request({url:'search_categories.php', 
  		onSuccess: function(txt){
  		  if (txt != "-1"){ 
  		    $('categorie' + type).set('html', '');
  		    
  		    var tab = txt.split("%%");
  		    
  		    tab.each(function(el){
  		      if (el != ""){
              var opt = el.split("&&");
              var option = new Element('option');
              option.set('value', el);
              option.set('html', opt[1] + ' (' + opt[0] + ')');
              option.inject($('categorie' + type));
            }
          });
  		    
          // donne la catégorie au complet
          var categorie = "";
        	for (x=1; x<=4; x++){
            var temp = $('categorie' + x).get('value');
            if (temp !== ""){
              temp = temp.split("&&")[1];
              categorie += temp + ' - ';
            }
          }
        	
        	// recharge les catégories suivantes
        	if (type <= 3)
        	 get_categories(-1, type + 1);
  			}
      }
	  });
	  req.send("cat_id=" + cat_id);
	}
	else{
	 // donne la catégorie au complet
  	var categorie = "";
  	for (x=1; x<=4; x++){
      var temp = $('categorie' + x).get('value');
      if (temp !== ""){
        temp = temp.split("&&")[1];
        categorie += temp + ' - ';
      }
    }
  	
  	//$('categorie_finale').set('html', categorie.substr(0, categorie.length - 3));
  }
}

function get_info_category(cat_id){
  // pour l'ajout d'une catégorie
  if (job == 1){
    // place l'id de la catégorie dans l'input[hidden]
    $('cat_ref_id').set('value', cat_id);
    
    // puis recherche le nom de la catégorie
    var req = new Request({url:'search_category.php', 
  		onSuccess: function(txt){
  		  if (txt != "-1"){
  		    $$('.cat_name').set('value', '');
  		    
  		    var tab = txt.split("%%");
  		    
  		    tab.each(function(el){
  		      if (el != ""){
              var val = el.split("&&");
              var lang = val[0];
              var name = val[1];
              if (lang == 'fr')
                $('cat_ref_add').set('html', name);
            }
          });
  			}
      }
    });
    req.send("cat_id=" + cat_id);
  }
  // pour la modification d'une catégorie
  else if (job == 2){
    // place l'id de la catégorie dans l'input[hidden]
    $('cat_ref_id').set('value', cat_id);
    
    // recherche le nom de la catégorie
    var req = new Request({url:'search_category.php', 
  		onSuccess: function(txt){
  		  if (txt != "-1"){
  		    $$('.cat_name').set('value', '');
  		    
  		    var tab = txt.split("%%");
  		    
  		    tab.each(function(el){
  		      if (el != ""){
              var val = el.split("&&");
              var lang = val[0];
              var name = val[1];
              $('name_' + lang).set('value', name);
            }
          });
  			}
      }
    });
    req.send("cat_id=" + cat_id);
  }
  // pour le déplacement d'une catégorie (étape 1 = sélection de la catégorie à déplacer)
  else if (job == 3){
    // place l'id de la catégorie à déplacer dans l'input[hidden]
    $('cat_to_move_id').set('value', cat_id);
    
    // puis recherche le nom de la catégorie
    var req = new Request({url:'search_category.php', 
  		onSuccess: function(txt){
  		  if (txt != "-1"){
  		    var tab = txt.split("%%");
  		    
  		    tab.each(function(el){
  		      if (el != ""){
              var val = el.split("&&");
              var lang = val[0];
              var name = val[1];
              if (lang == 'fr')
                $('cat_to_move').set('html', name);
            }
          });
  			}
      }
    });
    req.send("cat_id=" + cat_id);
  }
  // pour le déplacement d'une catégorie (étape 2 = sélection du nouvel emplacement de la catégorie)
  else if (job == 4){
    // test que l'on ne sélectionne pas les mêmes catégories source et destination
    if ($('cat_to_move_id').get('value') == cat_id){
      alert('Les catégories source et destination ne peuvent pas être les mêmes.');
      return false;
    }
    
    // place l'id de la catégorie référence pour l'emplacement dans l'input[hidden]
    $('cat_ref_id').set('value', cat_id);
    
    // puis recherche le nom de la catégorie
    var req = new Request({url:'search_category.php', 
  		onSuccess: function(txt){
  		  if (txt != "-1"){
  		    var tab = txt.split("%%");
  		    
  		    tab.each(function(el){
  		      if (el != ""){
              var val = el.split("&&");
              var lang = val[0];
              var name = val[1];
              if (lang == 'fr')
                $('cat_ref_move').set('html', name);
            }
          });
  			}
      }
    });
    req.send("cat_id=" + cat_id);
  }
  // pour la suppression d'une catégorie
  else if (job == 5){
    
    // place l'id de la catégorie à supprimer dans l'input[hidden]
    $('cat_ref_id').set('value', cat_id);
    
    // recherche le nom de la catégorie
    var req = new Request({url:'search_category.php', 
  		onSuccess: function(txt){
  		  if (txt != "-1"){
  		    var tab = txt.split("%%");
  		    
  		    tab.each(function(el){
  		      if (el != ""){
              var val = el.split("&&");
              var lang = val[0];
              var name = val[1];
              if (lang == 'fr')
                $('cat_ref_delete').set('html', name);
            }
          });
  			}
      }
    });
    req.send("cat_id=" + cat_id);
  }
}

// préparation pour l'ajout d'une catégorie
function ajouter_category(){

  $('quoi_faire').set('html', 'Veuillez choisir où vous désirez placer votre nouvelle catégorie.');
  
  $('selection_categorie').setStyle('display','block');
  $('info_categorie').setStyle('display','block');
  $$('.texte_plusieurs_categories').setStyle('display','block');
  $('envoyer').setStyle('display','block');
  
  $('category_reference_add').setStyle('display','block');
  $('category_reference_move').setStyle('display','none');
  $('category_reference_delete').setStyle('display','none');
  $('div_bouton_destination').setStyle('display','none');
  
  $$('.cat_name').each(function(item){
    item.set('value', '');
  });
  
  $('job').set('value', '1');
  job = 1;
}

// préparation pour la modification d'une catégorie
function modifier_category(){

  $('quoi_faire').set('html', 'Veuillez choisir la catégorie que vous désirez modifier.');
  
  $('selection_categorie').setStyle('display','block');
  $('info_categorie').setStyle('display','block');
  $('envoyer').setStyle('display','block');
  
  $('category_reference_add').setStyle('display','none');
  $('category_reference_move').setStyle('display','none');
  $('category_reference_delete').setStyle('display','none');
  $('div_bouton_destination').setStyle('display','none');
  $$('.texte_plusieurs_categories').setStyle('display','none');
  
  $('job').set('value', '2');
  job = 2;
}

// préparation pour le déplacement d'une catégorie (étape 1)
function deplacer_category1(){

  // change le texte "quoi faire"
  var myFx = new Fx.Tween($('quoi_faire'), {duration: 'short'});
  myFx.start('opacity', '1', '0').chain(
    function(){
      $('quoi_faire').set('html', 'Veuillez choisir la catégorie que vous désirez déplacer.');
      myFx.start('opacity','0','1');
    }
  );
  
  $('selection_categorie').setStyle('display','block');
  $('envoyer').setStyle('display','block');
  $('div_bouton_destination').setStyle('display','block');
  $('category_reference_move').setStyle('display','block');
  
  $('category_reference_add').setStyle('display','none');
  $('category_reference_delete').setStyle('display','none');
  $('info_categorie').setStyle('display','none');
  $$('.texte_plusieurs_categories').setStyle('display','none');
  
  $('cat_to_move_id').set('value', '');
  $('cat_to_move').set('html', '');
  
  $('bouton_destination').set('value','Sélectionner la destination');
  $('bouton_destination').set('onclick','deplacer_category2();');
  
  $('job').set('value', '3');
  job = 3;
}

// préparation pour le déplacement d'une catégorie (étape 2)
function deplacer_category2(){
  if ($('cat_to_move_id').get('value') == ''){
    alert('Aucune catégorie source n\'a encore été sélectionnée.');
    return false;
  }
  
  // change le texte "quoi faire"
  var myFx = new Fx.Tween($('quoi_faire'), {duration: 'short'});
  myFx.start('opacity', '1', '0').chain(
    function(){
      $('quoi_faire').set('html', 'Veuillez choisir le nouvel emplacement de votre catégorie.');
      myFx.start('opacity','0','1');
    }
  );
  
  $('bouton_destination').set('value','Sélectionner la catégorie à déplacer');
  $('bouton_destination').set('onclick','deplacer_category1();');
  
  $('job').set('value', '4');
  job = 4;
}

// préparation pour la suppression d'une catégorie
function supprimer_category(){

  $('quoi_faire').set('html', 'Veuillez choisir la catégorie que vous désirez supprimer.');
  
  $('selection_categorie').setStyle('display','block');
  $('envoyer').setStyle('display','block');
  $('category_reference_delete').setStyle('display','block');
  
  $('info_categorie').setStyle('display','none');
  $$('.texte_plusieurs_categories').setStyle('display','none');
  $('category_reference_add').setStyle('display','none');
  $('category_reference_move').setStyle('display','none');
  $('div_bouton_destination').setStyle('display','none');
  $('info_categorie').setStyle('display','none');
  $$('.texte_plusieurs_categories').setStyle('display','none');
  
  $('job').set('value', '5');
  job = 5;
}


// annule l'opération en cours
function annuler(){
  $('quoi_faire').set('html', '');
  $('selection_categorie').setStyle('display','none');
  $('envoyer').setStyle('display','none');
  $('category_reference_move').setStyle('display','none');
  $('category_reference_add').setStyle('display','none');
  $('category_reference_delete').setStyle('display','none');
  $('div_bouton_destination').setStyle('display','none');
  $('info_categorie').setStyle('display','none');
  $$('.texte_plusieurs_categories').setStyle('display','none');
  
  $('job').set('value', '0');
  job = 0;
}

</script>

<div id="arianne">
  <a href="index.php?pid=1"><?php echo _("Contenu"); ?></a> > <?php echo _("Catégories des articles"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Catégories des articles"); ?></h1>
  
  <p style="float:left;margin-right:20px;font-weight:bold">
    Opération : 
  </p>
  
  <table style="margin-bottom:10px">
    <?php 
    if (test_right(43)){
      echo "<tr>
              <td style='text-align:center;height:40px'>
                <a href='#quoi_faire' onclick='javascript:ajouter_category();' title='" ._("Ajouter une catégorie") ."'><img src='pictures/newobject_small.gif' border='0' alt='" ._("Ajouter une catégorie") ."' style='vertical-align:middle' /></a>
              </td>
              <td>
                <a href='#quoi_faire' onclick='javascript:ajouter_category();' title='" ._("Ajouter une catégorie") ."'>" ._("Ajouter une catégorie") ."</a>
              </td>
            </tr>";
    }
    if (test_right(44)){
      echo "<tr>
              <td style='text-align:center;height:40px'>
                <a href='#quoi_faire' onclick='javascript:modifier_category();' title='" ._("Modifier une catégorie") ."'><img src='pictures/edit.gif' border='0' alt='" ._("Modifier une catégorie") ."' style='vertical-align:middle' /></a>
              </td>
              <td>
                <a href='#quoi_faire' onclick='javascript:modifier_category();' title='" ._("Modifier une catégorie") ."'>" ._("Modifier une catégorie") ."</a>
              </td>
            </tr>";
    }
    if (test_right(44)){
      echo "<tr>
              <td style='text-align:center;height:40px'>
                <a href='#quoi_faire' onclick='javascript:deplacer_category1();' title='" ._("Déplacer une catégorie") ."'><img src='pictures/reorder_small.gif' border='0' alt='" ._("Déplacer une catégorie") ."' style='vertical-align:middle' /></a>
              </td>
              <td>
                <a href='#quoi_faire' onclick='javascript:deplacer_category1();' title='" ._("Déplacer une catégorie") ."'>" ._("Déplacer une catégorie") ."</a>
              </td>
            </tr>";
    }
    if (test_right(45)){
      echo "<tr>
              <td style='text-align:center;height:40px'>
                <a href='#quoi_faire' onclick='javascript:supprimer_category();' title='" ._("Supprimer une catégorie") ."'><img src='pictures/delete.gif' border='0' alt='" ._("Supprimer une catégorie") ."' style='vertical-align:middle' /></a>
              </td>
              <td>
                <a href='#quoi_faire' onclick='javascript:supprimer_category();' title='" ._("Supprimer une catégorie") ."'>" ._("Supprimer une catégorie") ."</a>
              </td>
            </tr>";
    }
    ?>
  </table>
  
  <div id="selection_categorie" class="title" style="display:none;margin:10px auto">  
    <a name="quoi_faire"></a>
    <p id="quoi_faire" style="font-weight:bold"></p>
    
    <div style="float:left;margin-right:20px">
      <select id="categorie1" class="select_category" size="10" style="width:250px" onclick="get_categories(this.value, 2);">
        <?php
          $categories = Miki_shop_article_category::get_all_categories("lft", "asc", false, true);
          foreach($categories as $c){
              echo "<option value='$c->id&&" .$c->name[$_SESSION['lang']] ."'>" .$c->name[$_SESSION['lang']] ." ($c->id)</option>\n";
          }
        ?>
      </select>
    </div>
    
    <div>
      <select id="categorie2" size="10" style="width:250px" onclick="get_categories(this.value, 3);">
      </select>
    </div>
    
    <div style="clear:left;margin-top:20px;float:left;margin-right:20px">
      <select id="categorie3" size="10" style="width:250px" onclick="get_categories(this.value, 4);">
      </select>
    </div>
    
    <div style="margin-top:20px">
      <select id="categorie4" size="10" style="width:250px" onclick="get_categories(this.value, 5);">
      </select>
    </div>
    
    <div style="display:none;clear:left;text-align:right;width:520px;margin-top:20px" id="div_bouton_destination">
      <input type="button" value="Sélectionner la destination" id="bouton_destination" onclick="deplacer_category2();" />
    </div>
  </div>
  
  <form id="form_category" action="shop_article_category_apply.php" method="post" name="form_category" enctype="application/x-www-form-urlencoded" style="display:inline">
    
    <input type="hidden" name="job" id="job" value="0" />
    <input type="hidden" name="cat_ref_id" id="cat_ref_id" value="" />
    <input type="hidden" name="cat_to_move_id" id="cat_to_move_id" value="" />
    
    <!-- Pour l'ajout ou la modification d'une catégorie -->
    <div id="info_categorie" style="display:none">
      <p style="margin-right:20px;font-weight:bold">
        Informations de la catégorie
      </p>
      
      <?php
        // ajoute un champ par langue
        $langs = Miki_language::get_all_languages();
        foreach($langs as $lang){
          echo "<div style='clear:left;width:100px;margin-right:10px;float:left'>$lang->name :</div><input type='text' id='name_$lang->code' class='cat_name' name='name_$lang->code' style='margin:0 0 10px 10px;width:200px;float:left' /><div class='texte_plusieurs_categories'>&nbsp;&nbsp;(pour insérer plusieurs catégories, séparez-les par une virgule)</div><br />";
        }
      ?>
    </div>
    
    <!-- Pour l'ajout d'une catégorie -->
    <div id="category_reference_add" style="display:none">
      <p style="margin-right:20px;font-weight:bold">
        Emplacement
      </p>
      placer cette catégorie
      <select name="add_place">
        <option value="after">après</option>
        <option value="in">à l'intérieur de</option>
      </select>
      la catégorie <span id="cat_ref_add" style="font-weight:bold"></span>
    </div>
    
    
    <!-- Pour le déplacement d'une catégorie -->
    <div id="category_reference_move" style="display:none">
      <p style="margin-right:20px;font-weight:bold">
        Déplacement
      </p>
      déplacer la catégorie <span id="cat_to_move" style="font-weight:bold"></span>
      <select name="move_place">
        <option value="after">après</option>
        <option value="in">à l'intérieur de</option>
      </select>
      la catégorie <span id="cat_ref_move" style="font-weight:bold"></span>
    </div>
    
    <!-- Pour la suppression d'une catégorie -->
    <div id="category_reference_delete" style="display:none">
      <p style="margin-right:20px;font-weight:bold">
        Suppression
      </p>
      supprimer la catégorie <span id="cat_ref_delete" style="font-weight:bold"></span>
    </div>
    
    <div id="envoyer" style="display:none;margin-top:20px;clear:left">
      <input type="button" value="Annuler" onclick="annuler();" />&nbsp;&nbsp;
      <input type="submit" value="Envoyer" />
    </div>
  </form>  
  
  
</div>