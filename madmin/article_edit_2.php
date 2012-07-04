<head>
  <?php
    require_once ('shop_article_state.php');
    require_once("functions_pictures.php");
  
    if (!test_right(52))
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      
    // si pas d'id d'article spécifié, on retourne à la page précédente
    if (!isset($_REQUEST['id']) || !is_numeric($_REQUEST['id'])){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    else
      $id = $_REQUEST['id'];
      
    try{
      $article = new Miki_shop_article($id);
    }
    catch(Exception $e){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    
    // test que l'on aie bien un shop de configuré
    $shops = Miki_shop::get_all_shops();
    if (sizeof($shops) == 0){
      $shop = false;
    }
    else
      $shop = array_shift($shops);
      
    if (!$shop){
      echo "<div>
              Vous n'avez aucune shop de configuré pour le moment.<br /><br />
              <input type='button' value='Créer un shop' onclick=\"document.location='index.php?pid=142'\" />
            </div>";
      exit();
    }
    
    // si le type d'article a été donné, on l'affect à l'article
    if (isset($_REQUEST['article_type']) && is_numeric($_REQUEST['article_type'])){
      $article->type = $_REQUEST['article_type'];
      $article->update();
    }
    
    // si on est en train de modifier un produit configurable et si les sets d'options ont été transmis, on les ajoute à l'article
    if ($article->type == 2 && isset($_REQUEST['article_option_sets'])){
      foreach($_REQUEST['article_option_sets'] as $option_set){
        try{
          $article->add_set($option_set);
        }
        catch(Exception $e){}
      }
    }
    
    // vérifie si on utilise la gestion des stock ou pas
    $use_stock = Miki_configuration::get('miki_shop_gestion_stock');
  ?>
  
  <script type="text/javascript" src="scripts/SimpleTabs.js"></script>
  <link rel="stylesheet" type="text/css" href="scripts/SimpleTabs.css" />
  <script type="text/javascript" src="scripts/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>
  
  <script type="text/javascript" src="scripts/mootools_more.js"></script>
  
  <script type="text/javascript">
  
  var tabs;
  var sort = null;
  var busy = false;
  
  // copie le contenu de la langue en cours dans les autres langues
  function copy_content(lang){
    // récupert les éléments de la langue actuelle
    var inputs1 = $('content_' + lang).getElements('input');
    var textareas1 = $('content_' + lang).getElements('textarea');
    
    // parcourt toutes les langues
    $$('.lang_content').each(function(el){
      // si la langue n'est pas la langue actuelle, on la met à jour
      if (el.id != "content_" + lang){
        // récupert les élément de la langue à mettre à jour
        var inputs2 = el.getElements('input');
        var textareas2 = el.getElements('textarea');
        
        // met à jour les inputs
        for (x=0;x<inputs1.length;x++){
          inputs2[x].value = inputs1[x].value;
        }
        
        // met à jour les textareas
        for (x=0;x<textareas1.length;x++){
          textareas2[x].value = textareas1[x].value;
        }
        
        // met à jour les éditeur WYSIWYG     
        tinyMCE.get('description_' + el.get('lang')).setContent(tinyMCE.get('description_' + lang).getContent());
      }
    });
  }
  
  window.addEvent('domready', function() {
    <?php require_once("scripts/checkform.js"); ?>
    
    var myCheckForm = new checkForm($('form_new_article'),{
                                      useAjax: false,
                                      errorPlace: 'bottom'
                                    });
    
    tabs = new SimpleTabs($('tab_content'),{selector:'.tab_selector'});
             
    tinyMCE.init({
      // General options
      mode : "specific_textareas",
      editor_selector : "tinymce",
      theme : "advanced",
      language: "fr",
      plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager,miki_preview,miki_link,codeprotect,miki_video",
      // Theme options
      theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
      theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,miki_link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,miki_preview,|,miki_video",
      theme_advanced_buttons3 : "table,|,cite,|,nonbreaking,blockquote,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
      theme_advanced_buttons4 : "",
      theme_advanced_toolbar_location : "top",
      theme_advanced_toolbar_align : "left",
      theme_advanced_statusbar_location : "bottom",
      theme_advanced_resizing : true,
      force_br_newlines : true,
  	  force_p_newlines : false,
  	  extended_valid_elements : "iframe[src|width|height|name|align|style|frameborder],script[language|type|src],style[type]",
      // Example content CSS (should be your site CSS)
      content_css : "css/default.css",
      document_base_url : "../../../../../",
      plugin_preview_width : "980",
  	  plugin_preview_height : "600",
      convert_urls : false,
      entity_encoding : "raw"
    });
    
    // Initialise les listes triables
    var sort = new Array();
    var x = 0;
    $$("ul.table").each(function(list_sortable){
      sort[x] = new Sortables($(list_sortable), {
          	 clone: false,
          	 opacity: 0.5,
          	 revert: true,
          	 onComplete: function(el){
          	   // récupert l'id de l'objet "sortable" en cours
          	   var sort_id = $(el).getElement('img.enable_sortable').get('rel');
          	   
          	   // détache l'objet "sortable" en cours
               sort[sort_id].detach();
               
               // récupert l'ordre
               var ordre = sort[sort_id].serialize(function(element, index){
                              if (element.id != '')
                                return element.id;
                            }).join('|');
               
               // et l'affecte au formulaire
               var ul = el.getParent('ul.option_set');
               ul.getNext('input[name=options_pos[]]').set('value', ordre);
             }
           });
      
      // affecte l'id de l'objet "sortable" en cours aux images permettant de trier
      $(list_sortable).getElements('.enable_sortable').set('rel', x);
      
      // détache l'objet "sortable" en cours
      sort[x].detach();
      
      Array.each($(list_sortable).getElements('.enable_sortable'), function(el){
        el.addEvent('mousedown', function(){
          if (!busy){
            sort[$(this).get('rel')].attach();
          }
        });
      });
      
      x++;
    });
    
    // coche la checkbox si clic sur la ligne
    //$$("table.option_set tr").addEvent('click', function(event) {
    $$("li.elements").addEvent('click', function(event) {
      var checkbox = $(this).getElement("input[type=checkbox]");
      if (checkbox)
        checkbox.checked = !checkbox.checked;
    });
    
    // Evite un double cochage / décochage avec le clic sur la ligne
    //$$("table.option_set input[type=checkbox]").addEvent('click', function(event) {
    $$("li.elements input[type=checkbox]").addEvent('click', function(event) {
      event.stopPropagation();
    });
    
    // pour la suppression d'un set d'options
    $$('.remove_option_set').addEvent('click',function(){
      if (!confirm('<?php echo _("Etes-vous sûr de vouloir supprimer cet élément ?"); ?>'))
          return false;
    });
  });
  
  var nb_pictures = <?php echo sizeof($article->pictures); ?>;
  
  // ajouter un logo supplémentaire
  function add_picture(){
    nb_pictures++;
    
    var br = new Element('br');
    br.inject($('ajouter_image'), 'before');
    
    var el2 = new Element('input', {type: 'file', name: 'picture' + nb_pictures});
    el2.inject($('ajouter_image'), 'before');
    
    /*var br = new Element('br');
    br.inject($('ajouter_image'), 'before');*/
    
    $('nb_pictures').set('value',nb_pictures);
  }
  
  // colorie une ligne d'un tableau
  function colorLine(el){
    $(el).getElements('div').addClass('mouseOver');
  }
  
  // remet une ligne dans sa couleur normale
  function uncolorLine(el){
    $(el).getElements('div').removeClass('mouseOver');
  }
  
  </script>
  
  <style type="text/css">
    #form_new_article table td{
    }
    
    #form_new_article input[type=file]{
      margin-bottom: 10px;
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
      background: #3b65be;
    }
    
    li.elements{
      width: 100%;
      overflow: hidden;
      display: table-row;
    }
    
    li.elements div{
      padding: 0.2em 0;
      display: table-cell;
      vertical-align: middle;
    }
    
    li.elements:nth-child(odd){
      background-color: #f0f0f0;
    }
    
    li.elements:nth-child(even){
      background-color: #ffffff;
    }
  </style>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Shop"); ?></a> > <a href="index.php?pid=143"><?php echo _("Liste articles"); ?></a> > Modifier un article
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modification d'un article"); ?></h1>  
  
  <?php
    // si l'article est un article configurable, on permet d'ajouter des sets d'options qui ne font pas encore partie de l'article
    if ($article->type == 2){
      $sets_disponibles = array();
      
      // récupert tous les sets d'options
      $option_sets = Miki_shop_article_option_set::get_all_sets($shop->id);
      
      // parcourt les sets récupérés
      foreach($option_sets as $option_set){
        // si l'article ne possède pas encore le set, on prend le set en compte
        if (!$article->has_set($option_set->id)){
          $sets_disponibles[] = $option_set;
        }
      }
      
      // créé un select avec tous les sets pris en compte
      if (sizeof($sets_disponibles) > 0){
        echo "<form action='article_option_set_update.php' method='post' style='margin: 20px 0;' enctype='multipart/form-data'>
                <input type='hidden' name='aid' value='$article->id' />
                <input type='hidden' name='action' value='add' />"
                ._("Ajouter un set d'options") ." :  
                <select name='sid'>";
                  foreach($sets_disponibles as $set){
                    echo "<option value='$set->id'>$set->name</option>";
                  }
          echo "</select>
                <input type='submit' value='" ._("Ajouter") ."' />
              </form>";
      }
    }
  ?>
  
  <form id="form_new_article" action="article_test_edit.php" method="post" name="form_new_article" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $article->id; ?>" />
    <input type="hidden" name="nb_pictures" id="nb_pictures" value="<?php echo sizeof($article->pictures); ?>" />
    
    <div id="tab_content">
      <!-- Onglet "Général" -->
      <span class='tab_selector'><?php echo _("Général"); ?></span>
      <div>
        <table>
          <tr>
            <td colspan='2'>&nbsp;</td>
          </tr>
          <tr>
            <td style="vertical-align:top"><?php echo _("Type d'article"); ?></td>
            <td>
              <?php
                if ($article->type == 1)
                  echo _("Article simple");
                elseif ($article->type == 2)
                  echo _("Article configurable");
              ?>
              <span style="padding-left: 20px;"><a href="index.php?pid=1451&id=<?php echo $article->id; ?>" title="<?php echo _("Modifier le type de l'article"); ?>"><?php echo _("Modifier"); ?></a></span>
            </td>
          </tr>
          <tr>
            <td colspan='2'>&nbsp;</td>
          </tr>
          <tr>
            <td style="vertical-align:top; width: 200px;">Catégorie <span style="color:#ff0000">*</span></td> 
            <td>
              <select name="category">
                <?php
                  $categories = Miki_shop_article_category::get_all_categories("name");
                  foreach($categories as $cat){
                    echo "<option value=\"$cat->id\"";
                    if ($article->id_category == $cat->id) echo " selected='selected'";
                    echo ">" .$cat->name['fr'] ."</option>"; 
                  }
                ?>
              </select>
            </td>
          </tr>
          <tr>
            <td style="vertical-align:top">Référence</td>
            <td><input type="text" class="" name="ref" style="width:200px" value="<?php echo $article->ref; ?>" /></td>
          </tr>
          <tr>
            <td style="vertical-align:top">Etat <span style="color:#ff0000">*</span></td> 
            <td>
              <select name="state">
                <?php
                  foreach($shop_article_state as $key=>$value){
                    echo "<option value=\"$key\""; 
                      if ($article->state == $key) echo " selected='selected'";
                    echo ">$value</option>";
                  }
                ?>
              </select>
            </td>
          </tr>
          <tr>
            <td style="vertical-align:top">Poids</td>
            <td><input type="text" class="currency" name="weight" style="width:200px" value="<?php echo number_format($article->weight,2,'.',"'"); ?>" /> Kg</td>
          </tr>
          <tr>
            <td style="vertical-align:top">Prix <span style="color:#ff0000">*</span></td>
            <td>
              <?php
                if (test_right(81))
                  echo "<input type='text' class='required currency' name='price' style='width:200px' value=\"$article->price\" /> CHF";
                else
                  echo "$article->price CHF";
              ?>
          </tr>
          <tr>
            <td style="vertical-align:top">Quantité disponible <?php echo ($use_stock) ? "<span style='color:#ff0000'>*</span>" : ""; ?></td> 
            <td>
              <input type="text" name="quantity" class="<?php echo ($use_stock) ? "required" : ""; ?> numeric" style="width:200px" value="<?php echo $article->quantity; ?>" />
            </td>
          </tr>
          <tr>
            <td style="vertical-align:top">Image</td>
            <td>
              <input type='hidden' name='MAX_FILE_SIZE' value='5000000' />
              
              <?php
                $x = 1;
                foreach($article->pictures as $pic){
                  $size = get_image_size("../pictures/shop_articles/thumb/$pic", 50, 50);
                  echo "<div style='width: 100%; overflow: hidden; margin-bottom: 20px;'>
                          <input type='file' name='picture$x' style='float: left;' />
                          <img src='../pictures/shop_articles/thumb/$pic' alt=\"image de l'article\" style='float: left; margin-left: 10px; width: " .$size[0] ."px; height: " .$size[1] ."px; vertical-align: top;' />
                          <div style='float: left; margin-left: 10px;'>
                            (Laissez vide pour conserver l'image actuelle)<br />
                            <a href='article_supprimer_photo.php?aid=$article->id&pic=$pic' title='Supprimer cette photo'>Supprimer cette photo</a>
                          </div>
                        </div>";
                  $x++;
                }
              ?>
              
              <span id="ajouter_image">
                <a href="#logos" onclick="add_picture();" title="Ajouter une image" style="margin:0 5px">
                  <img src='pictures/add.png' alt="Ajouter une image" style="border:0; vertical-align:middle;" />
                <a>
                <a href="#logos" onclick="add_picture();" title="Ajouter une image">Ajouter une image</a>
              </span>
            </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr> 
          <tr>
            <td colspan="2" style="font-weight:bold">Attributs de l'article</td>
          </tr>
          <tr>
            <td style="vertical-align:top">Sélectionnez les attributs désirés</td>
            <td>
              <?php
                $attributes = Miki_shop_article::get_all_attributes();
                
                if (sizeof($attributes) == 0){
                  echo "Aucun attribut n'est disponible.";
                }
                
                foreach($attributes as $attribute){
                  echo "<input type='checkbox' name='attributes[]' value='" .$attribute['id'] ."' style='border:0'";
                   if ($article->has_attribute($attribute['id'])) echo " checked='checked'";
                  echo " /> " .$attribute['name'] ."<br />";
                }
              ?>
            </td>
          </tr>
        </table>
      </div>
    
      <?php
        // onglets "titre et description" dans toutes les langues
        $languages = Miki_language::get_all_languages();
        $x = 1;
        
        foreach ($languages as $l){
          echo "<span class='tab_selector'><span style='float:left;margin:0 5px'>" ._("Contenu") ."</span><img src='pictures/flags/$l->picture' alt='$l->name' title='$l->name' style='margin:4px;vertical-align:middle;border:0' /></span>";
          echo "
          <div id='content_$l->code' class='lang_content' lang='$l->code'>
            <table>
              <tr>
                <td colspan='2' style='text-align:center'>&nbsp;</td>
              </tr>
              <tr>
                <td>" ._("Langue : ") ."</td>
                <td>$l->name</td>
              </tr>
              <tr>
                <td colspan='2' style='text-align:center'>&nbsp;</td>
              </tr>
              <tr>
                <td style='vertical-align: top;'>" ._("Titre : ") ."</td>
                <td><input type='text' id='name_$l->code' name='name[$l->code]' value='" .$article->get_name($l->code) ."' class='required' style='width:400px' /></td>
              </tr>
              <tr>
                <td style='vertical-align: top;'>" ._("Description : ") ."</td>
                <td><textarea name='description[$l->code]' class='tinymce' id='description_$l->code' class='required' style='width: 600px; height: 400px;'>" .$article->get_description($l->code) ."</textarea></td>
              </tr>
              <tr>
                <td colspan='2'><a href='#' onclick='javascript:copy_content(\"$l->code\");'>" ._("Copier le contenu de cette langue dans les autres langues") ."</a></td>
              </tr>
            </table>
          </div>";
          
          $x++;
        }
        
        // onglets des sets d'options
        if ($article->type == 2){
          // récupert tous les sets liés à l'article
          $option_sets = $article->get_sets();
          
          $lang = Miki_language::get_main_code();
          
          // parcourt tous les sets
          foreach($option_sets as $option_set){
            // récupert toutes les options du set
            $options = $option_set->get_options();
            
            // récupert les options du set utilisées par l'article
            $options_article = $article->get_options($option_set->id, false);

            echo "
            <span class='tab_selector'>$option_set->name</span>
            <div>";
              
        echo "<div style='margin: 10px 0;'>
                <a class='remove_option_set' href='article_option_set_update.php?aid=$article->id&sid=$option_set->id&action=remove' title=\"" ._("Supprimer ce set de l'article") ."\">" ._("Supprimer ce set de l'article") ."</a>
              </div>
              
              <ul class='table option_set sortable' id='option_set_$option_set->id'>
                <li class='headers non-sortable'>
                  <div style='width: 4%; padding-left: 1%'>&nbsp;</div>
                  <div style='width: 5%;'>&nbsp;</div>
                  <div style='width: 15%;'>" ._('Réf') ."</div>
                  <div style='width: 40%;'>" ._('Nom') ."</div>
                  <div style='width: 15%; text-align: center;'>" ._('Stock') ."</div>
                  <div style='width: 15%;'>" ._("Suppl. Prix") ."</div>
                </li>";
                
                $n = 0;
                
                // pour la gestion des positions des options
                $positions = array();
                
                // affiche en premier lieu toutes les options du set déjà utilisées par l'article
                foreach($options_article as $option){
                
                  // détecte la class
                  if ($n === 1)
                    $class = "line1";
                  else
                    $class = "line2";
                    
                  $n = ($n+1)%2;
                  
                  // ajoute l'option dans la gestion des positions
                  $positions[] = $option->id;
        
                  $name = $option->get_name(Miki_language::get_main_code());
                  $state = $shop_article_state[$option->state];
                  echo "
                    <li class='elements' id='$option->id' onmouseover=\"colorLine('$option->id');\" onmouseout=\"uncolorLine('$option->id');\">
                      <div style='width: 5%; text-align: center'>
                        <img src='pictures/move.gif' style='vertical-align: middle; cursor: pointer' alt='+' class='enable_sortable' />
                      </div>
                      <div style='width: 5%;'>
                        <span><input type='checkbox' name='article_option[$option_set->id][]' value='$option->id' checked='checked' /></span>
                      </div>
                      <div style='width: 15%;'>
                        <a href='index.php?pid=283&id=$option->id' title='" ._('Editer') ."'>$option->ref</a>
                      </div>
                      <div style='width: 40%;'>
                        <a href='index.php?pid=283&id=$option->id' title='" ._('Editer') ."'>$name</a>
                      </div>
                      <div style='width: 15%; text-align: center;'>
                        $option->quantity
                      </div>
                      <div style='width: 15%'>"
                        .number_format($option->price,2,'.',"'") ." CHF
                      </div>
                    </li>";
                }
                
                // puis le reste des options
                foreach($options as $option){
                
                  if (!$article->has_option($option->id)){
                    // détecte la class
                    if ($n === 1)
                      $class = "line1";
                    else
                      $class = "line2";
                      
                    $n = ($n+1)%2;
                    
                    // ajoute l'option dans la gestion des positions
                    $positions[] = $option->id;
          
                    $name = $option->get_name(Miki_language::get_main_code());
                    $state = $shop_article_state[$option->state];
                    echo "
                      <li class='elements' id='$option->id' onmouseover=\"colorLine('$option->id');\" onmouseout=\"uncolorLine('$option->id');\">
                        <div style='width: 5%; text-align: center'>
                          <img src='pictures/move.gif' style='vertical-align: middle; cursor: pointer' alt='+' class='enable_sortable' />
                        </div>
                        <div style='width: 5%;'>
                          <span><input type='checkbox' name='article_option[$option_set->id][]' value='$option->id' /></span>
                        </div>
                        <div style='width: 15%;'>
                          <a href='index.php?pid=283&id=$option->id' title='" ._('Editer') ."'>$option->ref</a>
                        </div>
                        <div style='width: 40%;'>
                          <a href='index.php?pid=283&id=$option->id' title='" ._('Editer') ."'>$name</a>
                        </div>
                        <div style='width: 15%; text-align: center;'>
                          $option->quantity
                        </div>
                        <div style='width: 15%'>"
                          .number_format($option->price,2,'.',"'") ." CHF
                        </div>
                      </li>";
                  }
                }
        echo "</ul>
        
              <input type='hidden' name='options_pos[]' value='" .implode('|', $positions) ."' />
            </div>";
          }
        }
      ?>
    </div>
      
    <div style="font-weight:bold; margin: 20px 0;">Les champs munis d'une <span style="color:#ff0000">*</span> sont obligatoires</div>

    <div class="form_box" style="margin: 20px 0;">
      <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=143'" />
      &nbsp;&nbsp;
      <input type="submit" value="<?php echo _("Envoyer"); ?>" />
    </div>
  </form>
</div>

<?php
  // stock l'url actuel pour un retour après opérations
  $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
?>