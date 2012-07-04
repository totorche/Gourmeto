<?php

if (!test_right(31))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

// récupert les données sauvegardées
if (isset($_SESSION['saved_name'])) 
  $name = $_SESSION['saved_name'];
else
  $name = "";

if (isset($_SESSION['saved_template'])) 
  $template = $_SESSION['saved_template'];
else
  $template = -1;
  
if (isset($_SESSION['saved_subject'])) 
  $subject = $_SESSION['saved_subject'];
else
  $subject = "";
  
if (isset($_SESSION['saved_content_type']))
  $content_type = $_SESSION['saved_content_type'];
else
  $content_type = "code";

if (isset($_SESSION['saved_content']))
  $content = $_SESSION['saved_content'];
else
  $content = "";
  
if (isset($_SESSION['saved_active'])) 
  $active = $_SESSION['saved_active'];
else
  $active = true;
  
unset($_SESSION['saved_name']);
unset($_SESSION['saved_template']); 
unset($_SESSION['saved_subject']); 
unset($_SESSION['saved_content_type']); 
unset($_SESSION['saved_content']);
unset($_SESSION['saved_active']);  

?>

<script type="text/javascript" src="scripts/checkform.js"></script>
<script type="text/javascript" src="scripts/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>

<link rel="stylesheet" type="text/css" href="scripts/SimpleTabs.css" />

<script type="text/javascript">

  window.addEvent('domready', function() {
    var myCheckForm = new checkForm($('formulaire'),{
                                          useAjax: false,
                                          errorPlace: 'bottom',
                                          divErrorCss: {
                                            'margin':'5px 0 0 0px'
                                          }
                                        });
    
    // affiche l'éditeur de code ou la sélection du fichier selon le type de contenu de chaque langue
    $$('.lang_content').each(function(el){
      var select = $('content_' + el.get('lang')).getElement('select[name=content_type_' + el.get('lang') + ']');
      change_content_type(select.value, el.get('lang'));
    });
  });
  
  // lorsque l'on change de type de contenu
  function change_content_type(value){
    if (value == 'file'){
      $('editor').setStyle('display','none');
      $('file').setStyle('display','block');
    }
    else if (value == 'code'){
      $('file').setStyle('display','none');
      $('editor').setStyle('display','block');      
    }
  }
  
  tinyMCE.init({
    // General options
    mode : "specific_textareas",
    editor_selector : "tinymce",
    theme : "advanced",
    language: "fr",
    plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager,miki_newsletter_firstname,miki_newsletter_lastname,miki_newsletter_email",
    // Theme options
    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
    theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,preview",
    theme_advanced_buttons3 : "hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,advhr,|,print,|,ltr,rtl,|,fullscreen,|,miki_newsletter_firstname,miki_newsletter_lastname,miki_newsletter_email",
    theme_advanced_buttons4 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing : true,
    // Example content CSS (should be your site CSS)
    content_css : "style.css",
    document_base_url : "../../../../../",
    plugin_preview_width : "800",
	  plugin_preview_height : "600",
    convert_urls : false,
    /*entity_encoding : "raw",*/
    force_br_newlines : true,
	  force_p_newlines : false,
	  extended_valid_elements : "v:shape[stroked|style],v:imagedata[src],style[type]",
    forced_root_block : ''
  });

</script>

<div id="arianne">
  <a href="#"><?php echo _("Newsletter"); ?></a> > <a href="index.php?pid=114"><?php echo _("Newsletters"); ?></a> > <?php echo _("Ajouter une newsletter"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajouter une newsletter"); ?></h1>
  <form id="formulaire" action="newsletter_test_new.php" method="post" name="formulaire" enctype="multipart/form-data">
    <table id="main_table_form">
      <tr>
        <td colspan="2">
          <div id="page">
            <table>
              <tr>
                <td class="form_text" style="width:30%"><?php echo _("Nom : "); ?> <span style="color:#ff0000">*</span></td>
                <td class="form_box" style="width:70%"><input type="text" name="name" style="width:200px" class="required" value="<?php echo $name; ?>"></td>
              </tr>
              <tr>
                <td class="form_text"><?php echo _("Gabarit : "); ?></td>
                <td class="form_box">
                  <select name="template" id="template">
                    <?php 
                      $elements = Miki_newsletter_template::get_all_templates(false);
                      foreach ($elements as $el){
                        echo "<option value='$el->id' "; if ($el->id == $template) echo "selected='selected'"; echo ">$el->name</option>";
                      }
                    ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td class="form_text" style="width:30%"><?php echo _("Sujet : "); ?></td>
                <td class="form_box" style="width:70%"><input type="text" name="subject" style="width:200px" value="<?php echo $subject; ?>"></td>
              </tr>
              <tr>
                <td class='form_text'><?php echo _("Type de contenu : "); ?></td>
                <td class='form_box'>
                  <select name='content_type' onchange='javacript:change_content_type(this.value);'>
                    <option value='file' <?php if (isset($content_type) && $content_type == 'file') echo "selected='selected'"; echo ">" ._("Fichier"); ?></option>
                    <option value='code' <?php if (!isset($content_type) || $content_type == 'code') echo "selected='selected'"; echo ">" ._("Code"); ?></option>
                  </select>
                </td>
              </tr>
              <tr>
                <td class='form_text'><?php echo _("Contenu : "); ?></td>
                <td class='form_box' id='content_value'>
                  <div id='editor' <?php if (isset($content_type) && $content_type == 'file') echo "style='display:none'";?>>
                    <textarea class='tinymce' id='code' name='code' style='width:800px;height:600px'><?php if (isset($content)) echo $content; ?></textarea>
                  </div>
                  <div id='file' <?php if (!isset($content_type) || $content_type == 'code') echo "style='display:none'"; ?>>
                    <input type='file' name='file_content' />
                  </div>
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>
                  <br />
                  Mémo :<br /><br />
                  Insertion du prénom de la personne : [miki_newsletter_firstname]<br />
                  Insertion du nom de la personne : [miki_newsletter_lastname]<br />
                  Insertion de l'e-mail de la personne : [miki_newsletter_email]<br />
                </td>
              </tr>
            </table>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=114'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Ajouter"); ?>" onclick="javascript:tabs.select(0)" />
        </td>
      </tr>
    </table>      
  </form>
</div>