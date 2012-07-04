<?php

// si pas d'id donné, retour
if (!isset($_GET['id']) || !is_numeric($_GET['id']))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
if (!test_right(15)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}
  
$id = $_GET['id'];
$page = "";
$contents = "";

try{
  // recherche les informations l'élément donnée en paramètre
  $page = new Miki_page($id); 
  $contents = $page->get_contents();
}catch(Exception $e){
  // si aucun enregistrement n'a été trouvé -> retour
  //echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

// vérifie si l'utilisateur est membre du groupe SEO
$user = new Miki_user($_SESSION['miki_admin_user_id']);
$is_seo = $user->has_group(new Miki_group(2));

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];



/**
 * Récupert les pages enfants d'une page donnée
 * 
 * @param Miki_page $p La page dont on veut récupérer les pages enfants
 * @param string $pos La position de la page dont on veut récupérer les pages enfants
 * @param int $depth La pronfondeur de la page dont on veut récupérer les pages enfants (pour indentation)
 * 
 * @return mixed Un tableau contenant les pages enfants (position + page)
 */     
function get_children($p, $pos, $depth = 0){
  // récupert la variable de la page en cours d'édition
  global $page;
  
  // récupert les pages enfants de la page donnée
  $children = $p->get_children("position");
  
  // le tableau dans lequel seront stockées les pages enfants avant d'être retournées
  $temp = array();
  
  // variable utilisée pour l'indentation des pages
  $white = "";
  
  // définit l'indentation
  for($x=0; $x<$depth; $x++){
    $white .= "&nbsp;&nbsp;";
  }
  
  // parcourt chaque page enfant trouvée
  foreach($children as $child){
    // si l'enfant en cours n'est pas la page en cours d'édition, on le conserve
    if ($child->id != $page->id){
      $temp[] = array($white ."$pos.$child->position" => $child);
      $temp = array_merge($temp, get_children($child, "$pos.$child->position", $depth + 1));
    }
  }
  
  // retourne les résultats trouvés
  return $temp;
}

?>

<script type="text/javascript" src="scripts/checkform.js"></script>
<script type="text/javascript" src="scripts/mootools_more.js"></script>
<script type="text/javascript" src="scripts/SimpleTabs.js"></script>
<script type="text/javascript" src="scripts/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>

<link rel="stylesheet" type="text/css" href="scripts/SimpleTabs.css" />

<script type="text/javascript">
  
  // feuille de style CSS du gabarit sélectionné
  var tinymce_css = "";

  window.addEvent('domready', function() {
    
    // récupert la feuille de style liée au gabarit sélectionné
    get_stylesheet($('template').value);
  
    var myCheckForm = new checkForm($('formulaire'),{
                                          useAjax: false,
                                          errorPlace: 'bottom',
                                          divErrorCss: {
                                            'margin':'5px 0 0 0px'
                                          }
                                        });
    
    tabs = new SimpleTabs($('tab_content'),{
      selector:'.tab_selector',
      onSelect: function(toggle, container, index) {
  			toggle.addClass('tab-selected');
  			container.setStyle('display', '');
  			
  			// supprime tous les éditeurs TinyMCE
  			var tcss = "";
  			for (edId in tinyMCE.editors){
          // si c'est un éditeur d'une langue
          if (edId.contains("code_")){
            tinyMCE.editors[edId].remove();
          }
        }
  			
  			if (container.getElement("textarea.tinymce")){
    			tinyMCE.init({
            // General options
            mode : "exact",
            elements : container.getElement("textarea.tinymce").id,
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
            content_css : tinymce_css,
            document_base_url : "../../../../../",
            plugin_preview_width : "980",
        	  plugin_preview_height : "600",
            convert_urls : false,
            entity_encoding : "raw"
          });
        }
  		}
    });
    
    // ajout les tabs pour les sections du gabarit sélectionné
    get_global_content($('template').value, <?php echo $page->id; ?>);
    
    // affiche l'éditeur de code ou la sélection du fichier selon le type de contenu de chaque langue
    $$('.lang_content').each(function(el){
      var select = $('content_' + el.get('lang')).getElement('select[name=content_type_' + el.get('lang') + ']');
      change_content_type(select.value, el.get('lang'));
    });
    
    // avant d'envoyer les modifications de la page...
    $('formulaire').addEvent('submit', function(){
      
      // si il y a des blocs de contenu global
      if (sort){
        // on récupert leur ordre
        var ordre = sort.serialize(function(element, index){
                      return element.getProperty('id') + '=' + index;
                    }).join('&');
        $('global_content_sort').set('value', ordre);
      }
    });
  });
  
  // lorsque l'on change de type de contenu
  function change_content_type(value, langue){
    if (value == 'file'){
      $('editor_' + langue).setStyle('display','none');
      $('url_' + langue).setStyle('display','none');
      $('file_' + langue).setStyle('display','block');
    }
    else if (value == 'code'){
      $('file_' + langue).setStyle('display','none');
      $('url_' + langue).setStyle('display','none');
      $('editor_' + langue).setStyle('display','block');
    }
    else if (value == 'url'){
      $('file_' + langue).setStyle('display','none');
      $('editor_' + langue).setStyle('display','none');
      $('url_' + langue).setStyle('display','block');      
    }
  }
  
  var nbTabsAdded = 0;
  var sort = false;
  
  // récupert les blocs de contenu globaux
  function get_global_content(template_id, page_id){
    for (x=0; x<nbTabsAdded; x++){
      tabs.removeLastTab();
    }
    nbTabsAdded = 0;
    var req = new Request.JSON({
      url: 'page_get_global_content.php',
      onComplete: function(json){
        if (json != null){
          json.tabs.each(function(el){
            eltemp = new Element('div');
            eltemp.set('html',el.content)
            tabs.addTab(el.title, el.title, eltemp);
            //tabs.select(nbTabsAdded);
            nbTabsAdded++;
            
            // Initialise la liste triable
          	sort = new Sortables('.sortable', {
          		//constrain: true,
          		clone: false
          	});
          });
        }
      }
    }).send('template_id=' + template_id + '&page_id=' + page_id);
  }
  
  // récupert la feuille de style CSS liée au contenu
  function get_stylesheet(template_id){
    var req = new Request({
      url: 'get_css.php',
      onComplete: function(text){
        tinymce_css = "admin/css/" + text;
        //tinymce.DOM.loadCSS(text);
      }
    }).send('template_id=' + template_id);
  }
  
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
        
        // met à jour le select
        var select1 = $('content_' + lang).getElement('select[name=content_type_' + lang + ']');
        var select2 = el.getElement('select[name=content_type_' + el.get('lang') + ']');
        select2.value = select1.value;
        change_content_type(select2.value, el.get('lang'));
        
        // met à jour les éditeur WYSIWYG        
        $('code_' + el.get('lang')).set('html', $('code_' + lang).get('html')); 
        $('noembed_' + el.get('lang')).set('html', $('noembed_' + lang).get('html'));
      }
    });
  }

</script>

<div id="arianne">
  <a href="index.php?pid=1"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=2"><?php echo _("Pages"); ?></a> > <?php echo _("Modifier une page"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modifier la page ") ."'$page->name'"; ?></h1>
  <form id="formulaire" action="page_test_edit.php" method="post" name="formulaire" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="global_content_sort" id="global_content_sort" value="">
    
    <table id="main_table_form">
      <tr>
        <td colspan="2">
          <div id="tab_content">
            <!-- onglet "page" -->
            <span class="tab_selector"><?php echo _("Page"); ?></span>
            <div id="page">
              <table>
                <tr>
                  <td class="form_text"><?php echo _("Nom : "); ?> <span style="color:#ff0000">*</span></td>
                  <td class="form_box"><input type="text" name="name" style="width:200px" class="required" value="<?php echo $page->name; ?>"></td>
                </tr>
                <tr>
                  <td class="form_text"><?php echo _("Parent : "); ?></td>
                  <td class="form_box">
                    <select name="parent">
                      <option value="-1"><?php echo _("Aucun"); ?></option>
                      <?php
                      
                        // récupert toutes les pages
                        $elements = Miki_page::get_all_pages("position", false);
                        
                        // recherche les pages de premier niveau (pas de parent)
                        $temp = array();
                        foreach($elements as $el){
                          // si c'est une page de premier niveau (pas de parent) on la prend en compte
                          if (!$el->has_parent()){
                            $temp[] = $el;
                          }
                        }
                        $elements = $temp;
                        
                        
                        $parents = array();
                        // parcourt toutes les pages de premier niveau (pas de parent)
                        foreach ($elements as $el){
                          // créé l'élément représentant la page et sa position
                          $pages_list[] = array($el->position => $el);
                          // récupert de manière récursive les enfants de cette page
                          $pages_list = array_merge($pages_list, get_children($el, $el->position, 1));
                        }
                        
                        // parcourt toutes les pages trouvées
                        foreach($pages_list as $pages){
                          // récupert la page
                          $p = current($pages);
                          // sa position
                          $pos = key($pages);
                          
                          // puis l'ajoute au select
                          echo "<option value='$p->id' ";
                          if ($p->id == $page->parent_id) 
                            echo "selected='selected'"; 
                          echo ">$pos - $p->name</option>";
                        }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="form_text"><?php echo _("Gabarit : "); ?></td>
                  <td class="form_box">
                    <select name="template" id="template" onchange='javascript:get_global_content(this.value, <?php echo $page->id; ?>); get_stylesheet(this.value);'>
                      <?php 
                        $elements = Miki_template::get_all_templates(false);
                        foreach ($elements as $el){
                          echo "<option value='$el->id' "; if ($el->id == $page->template_id) echo "selected='selected'"; echo ">$el->name</option>";
                        }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="form_text"><?php echo _("Type de login : "); ?></td>
                  <td class="form_box">
                    <select name="login">
                      <?php 
                        for($x=0;$x<5;$x++){
                          echo "<option value='$x' "; if ($x == $page->login) echo "selected='selected'"; echo ">$x</option>";
                        }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="form_text"><?php echo _("Actif : "); ?></td>
                  <td class="form_box"><input type="checkbox" name="active" <?php if ($page->state == 1) echo "checked='checked'"; ?> value="1" /></td>
                </tr>
                <tr>
                  <td class="form_text"><?php echo _("Afficher dans le menu : "); ?></td>
                  <td class="form_box"><input type="checkbox" name="menu" <?php if ($page->in_menu()) echo "checked='checked'"; ?> value="1" /></td>
                </tr>
              </table>
            </div>
            
            <!-- onglets "contenus" -->
            <?php
              $languages = Miki_language::get_all_languages();
              
              foreach ($languages as $l){
                $content = $contents[strtolower($l->code)];
                echo "<span class='tab_selector'><span style='float:left;margin:0 5px'>" ._("Contenu") ."</span><img src='pictures/flags/$l->picture' alt='$l->name' title='$l->name' style='margin:4px;vertical-align:middle;border:0' /></span>";
                echo "
                <div id='content_$l->code' class='lang_content' lang='$l->code'>
                  <table>
                    <tr>
                      <td colspan='2' style='text-align:center'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td class='form_text' style='width: 105px; '>" ._("Langue : ") ."</td>
                      <td class='form_box'>$l->name</td>
                    </tr>
                    <tr>
                      <td colspan='2' style='text-align:center'>&nbsp;</td>
                    </tr>";
                    
                  if ($is_seo){
                    echo "
                    <tr>
                      <td class='form_text'>" ._("Titre : ") ."</td>
                      <td class='form_box'><input type='text' name='title_$l->code' value=\"" .stripslashes($content->title) ."\" style='width:800px' /></td>
                    </tr>
                    <tr>
                      <td class='form_text'>" ._("Description : ") ."</td>
                      <td class='form_box'><input type='text' name='description_$l->code' value=\"" .stripslashes($content->description) ."\" style='width:800px' /></td>
                    </tr>
                    <tr>
                      <td class='form_text'>" ._("Mots-clés : ") ."</td>
                      <td class='form_box'><input type='text' name='keywords_$l->code' value=\"" .stripslashes($content->keywords) ."\" style='width:800px' /></td>
                    </tr>
                    <tr>
                      <td class='form_text'>" ._("Métas : ") ."</td>
                      <td class='form_box'><textarea name='metas_$l->code' style='width:800px;height:200px'>" .stripslashes($content->metas) ."</textarea></td>
                    </tr>
                    <tr>
                      <td class='form_text'>" ._("Alias (url) : ") ."</td>
                      <td class='form_box'><input type='text' name='alias_$l->code' value=\"" .$content->alias ."\" style='width:200px' /></td>
                    </tr>
                    <tr>
                      <td class='form_text'>" ._("Catégorie : ") ."</td>
                      <td class='form_box'>
                        <select name='category_$l->code' id='category'>
                          <option value=''>" ._("Aucune catégorie") ."</option>";
               
                            $elements = Miki_category::get_all_categories(false);
                            foreach ($elements as $el){
                              echo "<option value='$el->id' "; if ($el->id == $content->category_id) echo "selected='selected'"; echo ">$el->name</option>";
                            }
                     
                  echo "</select>
                      </td>
                    </tr>";
                  }
                  
              echo "<tr>
                      <td class='form_text'>" ._("Texte du menu : ") ."</td>
                      <td class='form_box'><input type='text' name='menu_text_$l->code' value=\"" .stripslashes(htmlspecialchars_decode($content->menu_text)) ."\" style='width:200px' /></td>
                    </tr>
                    <tr>
                      <td class='form_text'>" ._("Type de contenu : ") ."</td>
                      <td class='form_box'>
                        <select name='content_type_$l->code' onchange='javacript:change_content_type(this.value, \"$l->code\");'>
                          <option value='file' "; if ($content->content_type == 'file') echo "selected='selected'"; echo ">" ._("Fichier") ."</option>
                          <option value='code' "; if ($content->content_type == 'code') echo "selected='selected'"; echo ">" ._("Code") ."</option>
                          <option value='url' "; if ($content->content_type == 'url') echo "selected='selected'"; echo ">" ._("Url") ."</option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td colspan='2'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td class='form_text' style='text-align:left'>" ._("Contenu : ") ."</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan='2' class='form_box' id='content_value_$l->code'>
                        <div id='editor_$l->code' "; if ($content->content_type != 'code') echo "style='display:none'"; echo ">
                          <textarea class='tinymce' id='code_$l->code' name='code_$l->code' style='width:980px;height:600px'>"; if (isset($content->content) && $content->content_type == 'code') echo $content->content; echo"</textarea>
                        </div>
                        <div id='file_$l->code' "; if ($content->content_type != 'file') echo "style='display:none'"; echo ">
                          <input type='file' name='file_$l->code' />&nbsp;&nbsp;"; if (isset($content->content) && $content->content_type == 'file') echo _("Actuellement : ") .$content->content;
                  echo "</div>
                        <div id='url_$l->code' "; if ($content->content_type != 'url') echo "style='display:none'"; echo ">
                          <input type='text' name='url_$l->code' style='width: 400px' value=\""; if (isset($content->content) && $content->content_type == 'url') echo $content->content; echo "\" />
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan='2'>&nbsp;</td>
                    </tr>
                    <tr style='display:none'>
                      <td class='form_text' style='text-align:left'>" ._("Balise Noembed : ") ."</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr style='display:none'>
                      <td colspan='2' class='form_box' id='noembed_value_$l->code'>
                        <div id='editor_$l->code'>
                          <textarea class='tinymce' id='noembed_$l->code' name='noembed_$l->code' style='width:980px;height:600px'>" .$content->noembed ."</textarea>
                        </div>
                        <div id='file_$l->code' style='display:none'>
                          <input type='file' name='noembed_file_$l->code' />
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan='2'><a href='#' onclick='javascript:copy_content(\"$l->code\");'>" ._("Copier le contenu de cette langue dans les autres langues") ."</a></td>
                    </tr>
                  </table>
                </div>";
              }
            ?>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=2'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" onclick="javascript:tabs.select(0)" />          
        </td>
      </tr>
    </table>      
  </form>
</div>