<head>
  <?php
    require_once ('shop_article_state.php');
    require_once("functions_pictures.php");
  
    if (!test_right(51) && !test_right(52) && !test_right(53)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
      
    // si pas d'id d'article spécifié, on retourne à la page précédente
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    else
      $id = $_GET['id'];
      
    try{
      $option = new Miki_shop_article_option($id);
    }
    catch(Exception $e){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
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
    
    // vérifie si on utilise la gestion des stock ou pas
    $use_stock = Miki_configuration::get('miki_shop_gestion_stock');
  ?>
  
  <script type="text/javascript" src="scripts/SimpleTabs.js"></script>
  <link rel="stylesheet" type="text/css" href="scripts/SimpleTabs.css" />
  <script type="text/javascript" src="scripts/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>
  
  <script type="text/javascript">
    
    var myCheckForm = null;
    
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
      
      myCheckForm = new checkForm($('form_new_article_option'),{
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
    });
    
    var nb_pictures = <?php echo sizeof($option->pictures); ?>;
    
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
    
    // lors d'un changement de gestion du stock
    function change_stock(el){
      if (el.value == 1){
        $$("input[name=quantity]").set('disabled', false);
        $$("input[name=quantity]").addClass('required');
      }
      else{
        $$("input[name=quantity]").set('disabled', true);
        $$("input[name=quantity]").removeClass('required');
      }
      myCheckForm.ResetItem($$("input[name=quantity]"));
    }
    
  </script>
  
  <style type="text/css">
    #form_new_article_option table td{
      padding-bottom: 10px;
    }
    
    #form_new_article_option input[type=file]{
      margin-bottom: 10px;
    }
  </style>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Shop"); ?></a> > <a href="index.php?pid=281"><?php echo _("Liste des options d'article"); ?></a> > Modifier une option d'article
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modification d'une option d'article"); ?></h1>  

  <form id="form_new_article_option" action="article_option_test_edit.php" method="post" name="form_new_article_option" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $option->id; ?>" />
    <input type="hidden" name="nb_pictures" id="nb_pictures" value="<?php echo sizeof($option->pictures); ?>" />
    
    <table>
      <tr>
        <td colspan="2" style="font-weight:bold">Information sur l'option</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>        
      <tr>
        <td style="vertical-align:top">Référence</td>
        <td><input type="text" class="" name="ref" style="width:200px" value="<?php echo $option->ref; ?>" /></td>
      </tr>
      <tr>
        <td style="vertical-align:top">Etat <span style="color:#ff0000">*</span></td> 
        <td>
          <select name="state">
            <?php
              foreach($shop_article_state as $key=>$value){
                echo "<option value=\"$key\""; 
                  if ($option->state == $key) echo " selected='selected'";
                echo ">$value</option>";
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <div id="tab_content">
            <!-- onglets "titre et description" dans toutes les langues -->
            <?php
              $languages = Miki_language::get_all_languages();
              
              $x = 1;
              
              foreach ($languages as $l){
                echo "<span class='tab_selector'><span style='float:left;margin:0 5px'>" ._("Contenu") ."</span><img src='pictures/flags/$l->picture' alt='$l->name' title='$l->name' style='margin:4px;vertical-align:middle;border:0' /></span>";
                echo "
                <div id='content_$l->code' class='lang_content' lang='$l->code'>
                  <table>
                    <tr>
                      <td colspan='2' style='text-align:center;'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td>" ._("Langue : ") ."</td>
                      <td>$l->name</td>
                    </tr>
                    <tr>
                      <td colspan='2'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td style='vertical-align: top;'>" ._("Titre : ") ."</td>
                      <td><input type='text' id='name_$l->code' name='name[$l->code]' value='" .$option->get_name($l->code) ."' class='required' style='width:400px' /></td>
                    </tr>
                    <tr>
                      <td style='vertical-align: top;'>" ._("Description : ") ."</td>
                      <td><textarea name='description[$l->code]' class='tinymce' id='description_$l->code' class='required' style='width: 600px; height: 400px;'>" .$option->get_description($l->code) ."</textarea></td>
                    </tr>
                    <tr>
                      <td colspan='2'><a href='#' onclick='javascript:copy_content(\"$l->code\");'>" ._("Copier le contenu de cette langue dans les autres langues") ."</a></td>
                    </tr>
                  </table>
                </div>";
                
                $x++;
              }
            ?>
          </div>
        </td>
      </tr>
      <tr>
        <td style="vertical-align:top">Supplément de prix <span style="color:#ff0000">*</span></td>
        <td><input type="text" class="required currency" name="price" style="width:200px" value="<?php echo $option->price; ?>" /> CHF</td>
      </tr>
      <tr>
        <td style="vertical-align:top">Gestion du stock <span style='color:#ff0000'>*</span></td> 
        <td>
          <select name="use_stock" onchange="change_stock(this);">
            <option value="1" <?php if ($option->use_stock == 1) echo "selected='selected'"; ?>><?php echo _("Oui"); ?></option>
            <option value="0" <?php if ($option->use_stock == 0) echo "selected='selected'"; ?>><?php echo _("Non"); ?></option>
          </select>
        </td>
      </tr>
      <tr>
        <td style="vertical-align:top">Quantité disponible <?php echo ($use_stock) ? "<span style='color:#ff0000'>*</span>" : ""; ?></td> 
        <td>
          <input type="text" name="quantity" class="<?php echo ($use_stock) ? "required" : ""; ?> numeric" style="width:200px" value="<?php echo $option->quantity; ?>" />
        </td>
      </tr>
      <tr>
        <td style="vertical-align:top">Images</td>
        <td>
          <input type='hidden' name='MAX_FILE_SIZE' value='5000000' />
          
          <?php
            $x = 1;
            foreach($option->pictures as $pic){
              $size = get_image_size("../pictures/shop_articles_options/thumb/$pic", 50, 50);
              echo "<div style='width: 100%; overflow: hidden; margin-bottom: 20px;'>
                      <input type='file' name='picture$x' style='float: left;' />
                      <img src='../pictures/shop_articles_options/thumb/$pic' alt=\"image de l'article\" style='float: left; margin-left: 10px; width: " .$size[0] ."px; height: " .$size[1] ."px; vertical-align: top;' />
                      <div style='float: left; margin-left: 10px;'>
                        (Laissez vide pour conserver l'image actuelle)<br />
                        <a href='article_option_supprimer_photo.php?eid=$option->id&pic=$pic' title='Supprimer cette photo'>Supprimer cette photo</a>
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
        <td colspan="2" style="font-weight:bold">Les champs munis d'une <span style="color:#ff0000">*</span> sont obligatoires</td>
      </tr> 
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=143'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
      </tr> 
    </table>
  </form>
</div>

<?php
  // stock l'url actuel pour un retour après opérations
  $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
?>