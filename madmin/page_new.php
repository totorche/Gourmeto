<?php

if (!test_right(7)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

// vérifie si l'utilisateur est membre du groupe SEO
$user = new Miki_user($_SESSION['miki_admin_user_id']);
$is_seo = $user->has_group(new Miki_group(2));

// récupert les données sauvegardées
if (isset($_SESSION['saved_name'])) 
  $name = $_SESSION['saved_name'];
else
  $name = "";
  
if (isset($_SESSION['saved_parent'])) 
  $parent = $_SESSION['saved_parent'];
else
  $parent = -1;

if (isset($_SESSION['saved_template'])) 
  $template = $_SESSION['saved_template'];
else
  $template = -1;
  
if (isset($_SESSION['saved_login'])) 
  $login = $_SESSION['saved_login'];
else
  $login = -1;
  
if (isset($_SESSION['saved_active'])) 
  $active = $_SESSION['saved_active'];
else
  $active = true;
  
if (isset($_SESSION['saved_menu'])) 
  $menu = $_SESSION['saved_menu'];
else
  $menu = true;
  
if (isset($_SESSION['saved_category'])) {
  $tab = explode(";;",$_SESSION['saved_category']);
  foreach($tab as $c){
    if ($c != ""){
      $tab2 = explode("%%",$c);
      $category[$tab2[0]] = $tab2[1];
    }
  }
}

if (isset($_SESSION['saved_title'])) {
  $tab = explode(";;",$_SESSION['saved_title']);
  foreach($tab as $c){
    if ($c != ""){
      $tab2 = explode("%%",$c);
      $title[$tab2[0]] = $tab2[1];
    }
  }
}

if (isset($_SESSION['saved_description'])) {
  $tab = explode(";;",$_SESSION['saved_description']);
  foreach($tab as $c){
    if ($c != ""){
      $tab2 = explode("%%",$c);
      $description[$tab2[0]] = $tab2[1];
    }
  }
}

if (isset($_SESSION['saved_keywords'])) {
  $tab = explode(";;",$_SESSION['saved_keywords']);
  foreach($tab as $c){
    if ($c != ""){
      $tab2 = explode("%%",$c);
      $keywords[$tab2[0]] = $tab2[1];
    }
  }
}

if (isset($_SESSION['saved_metas'])) {
  $tab = explode(";;",$_SESSION['saved_metas']);
  foreach($tab as $c){
    if ($c != ""){
      $tab2 = explode("%%",$c);
      $metas[$tab2[0]] = $tab2[1];
    }
  }
}

if (isset($_SESSION['saved_alias'])) {
  $tab = explode(";;",$_SESSION['saved_alias']);
  foreach($tab as $c){
    if ($c != ""){
      $tab2 = explode("%%",$c);
      $alias[$tab2[0]] = $tab2[1];
    }
  }
}

if (isset($_SESSION['saved_menu_text'])) {
  $tab = explode(";;",$_SESSION['saved_menu_text']);
  foreach($tab as $c){
    if ($c != ""){
      $tab2 = explode("%%",$c);
      $menu_text[$tab2[0]] = $tab2[1];
    }
  }
}

if (isset($_SESSION['saved_content_type'])) {
  $tab = explode(";;",$_SESSION['saved_content_type']);
  foreach($tab as $c){
    if ($c != ""){
      $tab2 = explode("%%",$c);
      $content_type[$tab2[0]] = $tab2[1];
    }
  }
}

if (isset($_SESSION['saved_content'])) {
  $tab = explode(";;",$_SESSION['saved_content']);
  foreach($tab as $c){
    if ($c != ""){
      $tab2 = explode("%%",$c);
      $content[$tab2[0]] = $tab2[1];
    }
  }
}
 
if (isset($_SESSION['saved_noembed'])) {
  $tab = explode(";;",$_SESSION['saved_noembed']);
  foreach($tab as $c){
    if ($c != ""){
      $tab2 = explode("%%",$c);
      $noembed[$tab2[0]] = $tab2[1];
    }
  }
} 
  
unset($_SESSION['saved_name']);
unset($_SESSION['saved_parent']); 
unset($_SESSION['saved_template']); 
unset($_SESSION['saved_active']); 
unset($_SESSION['saved_title']); 
unset($_SESSION['saved_description']); 
unset($_SESSION['saved_keywords']); 
unset($_SESSION['saved_metas']); 
unset($_SESSION['saved_alias']); 
unset($_SESSION['saved_menu_text']); 
unset($_SESSION['saved_content_type']); 
unset($_SESSION['saved_content']); 
unset($_SESSION['saved_noembed']); 


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
    $temp[] = array($white ."$pos.$child->position" => $child);
    $temp = array_merge($temp, get_children($child, "$pos.$child->position", $depth + 1));
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
  			
  			
  		},
    });
    
    // ajout les tabs pour les sections du gabarit sélectionné
    get_global_content($('template').value);
    
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
  function get_global_content(template_id){
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
            nbTabsAdded++;
            
            // Initialise la liste triable
          	sort = new Sortables('.sortable', {
          		//constrain: true,
          		clone: false
          	});
          });
        }
      }
    }).send('template_id=' + template_id);
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
  <a href="index.php?pid=1"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=2"><?php echo _("Pages"); ?></a> > <?php echo _("Ajouter une page"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajouter une page"); ?></h1>
  <form id="formulaire" action="page_test_new.php" method="post" name="formulaire" enctype="multipart/form-data">
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
                  <td class="form_box"><input type="text" name="name" style="width:200px" class="required" value="<?php echo $name; ?>"></td>
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
                          echo "<option value='$p->id'>$pos - $p->name</option>";
                        }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="form_text"><?php echo _("Gabarit : "); ?></td>
                  <td class="form_box">
                    <select name="template" id="template" onchange='javascript:get_global_content(this.value); get_stylesheet(this.value);'>
                      <?php 
                        $elements = Miki_template::get_all_templates(false);
                        foreach ($elements as $el){
                          echo "<option value='$el->id' "; if ($el->id == $template) echo "selected='selected'"; echo ">$el->name</option>";
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
                          echo "<option value='$x' "; if ($x == $login) echo "selected='selected'"; echo ">$x</option>";
                        }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="form_text"><?php echo _("Actif : "); ?></td>
                  <td class="form_box"><input type="checkbox" name="active" <?php if ($active) echo "checked='checked'"; ?> value="1" /></td>
                </tr>
                <tr>
                  <td class="form_text"><?php echo _("Afficher dans le menu : "); ?></td>
                  <td class="form_box"><input type="checkbox" name="menu" <?php if ($menu) echo "checked='checked'"; ?> value="1" /></td>
                </tr>
              </table>
            </div>
            
            <!-- onglets "contenus" -->
            <?php
              $languages = Miki_language::get_all_languages();
              
              foreach ($languages as $l){
                echo "<span class='tab_selector'><span style='float:left;margin:0 5px'>" ._("Contenu") ."</span><img src='pictures/flags/$l->picture' alt='$l->name' title='$l->name' style='margin:4px;vertical-align:middle;border:0' /></span>";
                echo "
                <div id='content_$l->code' class='lang_content' lang='$l->code'>
                  <table>
                    <tr>
                      <td colspan='2' style='text-align:center'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td class='form_text' style='width: 105px;'>" ._("Langue : ") ."</td>
                      <td class='form_box'>$l->name</td>
                    </tr>
                    <tr>
                      <td colspan='2' style='text-align:center'>&nbsp;</td>
                    </tr>
                    <tr>";
                    
                  if ($is_seo){
                    echo "
                      <td class='form_text'>" ._("Titre : ") ."</td>
                      <td class='form_box'><input type='text' name='title_$l->code' value='" .((isset($title)) ? $title[$l->code] : "") ."' style='width:800px' /></td>
                    </tr>
                    <tr>
                      <td class='form_text'>" ._("Description : ") ."</td>
                      <td class='form_box'><input type='text' name='description_$l->code' value='" .((isset($description)) ? $description[$l->code] : "") ."' style='width:800px' /></td>
                    </tr>
                    <tr>
                      <td class='form_text'>" ._("Mots-clés : ") ."</td>
                      <td class='form_box'><input type='text' name='keywords_$l->code' value='" .((isset($keywords)) ? $keywords[$l->code] : "") ."' style='width:800px' /></td>
                    </tr>
                    <tr>
                      <td class='form_text'>" ._("Métas : ") ."</td>
                      <td class='form_box'><textarea name='metas_$l->code' value='" .((isset($title)) ? $title : "") ."' style='width:800px;height:200px'>" .((isset($metas)) ? $metas[$l->code] : "") ."</textarea></td>
                    </tr>
                    <tr>
                      <td class='form_text'>" ._("Alias : ") ."</td>
                      <td class='form_box'><input type='text' name='alias_$l->code' value='" .((isset($alias)) ? $alias[$l->code] : "") ."' style='width:200px' /></td>
                    </tr>
                    <tr>
                      <td class='form_text'>" ._("Catégorie : ") ."</td>
                      <td class='form_box'>
                        <select name='category_$l->code' id='category'>
                          <option value=''>" ._("Aucune catégorie") ."</option>";
               
                            $elements = Miki_category::get_all_categories(false);
                            foreach ($elements as $el){
                              echo "<option value='$el->id' "; if (isset($category[$l->code]) && $el->id == $category[$l->code]) echo "selected='selected'"; echo ">$el->name</option>";
                            }
                     
                  echo "</select>
                      </td>
                    </tr>";
                  }
                  
              echo "<tr>
                      <td class='form_text'>" ._("Texte du menu : ") ."</td>
                      <td class='form_box'><input type='text' name='menu_text_$l->code' value='" .((isset($menu_text)) ? $menu_text[$l->code] : "") ."' style='width:200px' /></td>
                    </tr>
                    <tr>
                      <td class='form_text'>" ._("Type de contenu : ") ."</td>
                      <td class='form_box'>
                        <select name='content_type_$l->code' onchange='javacript:change_content_type(this.value, \"$l->code\");'>
                          <option value='file' "; if (isset($content_type[$l->code]) && $content_type[$l->code] == 'file') echo "selected='selected'"; echo ">" ._("Fichier") ."</option>
                          <option value='code' "; if ((isset($content_type[$l->code]) && $content_type[$l->code] == 'code') || !isset($content_type[$l->code])) echo "selected='selected'"; echo ">" ._("Code") ."</option>
                          <option value='url' "; if (isset($content_type[$l->code]) && $content_type[$l->code] == 'url') echo "selected='selected'"; echo ">" ._("Url") ."</option>
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
                        <div id='editor_$l->code' "; if (isset($content_type[$l->code]) && $content_type[$l->code] != 'code') echo "style='display:none'"; echo ">
                          <textarea class='tinymce' id='code_$l->code' name='code_$l->code' style='width:980px;height:600px'>"; if (isset($content[$l->code]) && $content_type[$l->code] == 'code') echo $content[$l->code]; echo"</textarea>
                        </div>
                        <div id='file_$l->code' "; if (!isset($content_type[$l->code]) || $content_type[$l->code] != 'file') echo "style='display:none'"; echo ">
                          <input type='file' name='file_$l->code' />
                        </div>
                        <div id='url_$l->code' "; if (!isset($content_type[$l->code]) || $content_type[$l->code] != 'url') echo "style='display:none'"; echo ">
                          <input type='text' name='url_$l->code' style='width: 400px' value=\""; if (isset($content[$l->code]) && $content_type[$l->code] == 'url') echo $content[$l->code]; echo"\"/>
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
                          
                          <textarea class='tinymce' id='noembed_$l->code' name='noembed_$l->code' style='width:980px;height:600px'>"; if (isset($noembed[$l->code])) echo $noembed[$l->code]; echo"</textarea>
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
              
              include("page_get_global_content.php");
            ?>
          </div>
          <?php //include("page_get_global_content.php"); ?>
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