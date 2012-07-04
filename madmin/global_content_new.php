<?php

if (!test_right(8))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

if (isset($_SESSION['saved_name'])) 
  $name = $_SESSION['saved_name'];
else
  $name = "";

if (isset($_SESSION['saved_content_type'])) {
  $tab = explode(";;",$_SESSION['saved_content_type']);
  foreach($tab as $c){
    $tab2 = explode("%%",$c);
    $content_type[$tab2[0]] = $tab2[1];
  }
}

if (isset($_SESSION['saved_content'])) {
  $tab = explode(";;",$_SESSION['saved_content']);
  foreach($tab as $c){
    $tab2 = explode("%%",$c);
    $content[$tab2[0]] = $tab2[1];
  }
}
  
unset($_SESSION['saved_name']);
unset($_SESSION['saved_content_type']); 
unset($_SESSION['saved_content']); 

?>

<script type="text/javascript" src="scripts/checkform.js"></script>
<script type="text/javascript" src="scripts/SimpleTabs.js"></script>
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
                                        
    new SimpleTabs($('tab_content'),{selector:'.tab_selector'});
  });
  
  // lorsque l'on change de type de contenu
  function change_content_type(value, langue){
    if (value == 'file'){
      $('editor_' + langue).setStyle('display','none');
      $('file_' + langue).setStyle('display','block');
    }
    else if (value == 'code'){
      $('file_' + langue).setStyle('display','none');
      $('editor_' + langue).setStyle('display','block');      
    }
  }
  
  tinyMCE.init({
    // General options
    mode : "specific_textareas",
    editor_selector : "tinymce",
    theme : "advanced",
    language: "fr",
    plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager,miki_link",
    // Theme options
    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
    theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,miki_link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview",
    theme_advanced_buttons3 : "table,|,cite,|,nonbreaking,blockquote,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
    theme_advanced_buttons4 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing : true,
    force_br_newlines : true,
	  force_p_newlines : false,
    // Example content CSS (should be your site CSS)
    content_css : "css/default.css",
    document_base_url : "../../../../../",
    plugin_preview_width : "980",
	  plugin_preview_height : "600",
    convert_urls : false,
    entity_encoding : "raw"
  });

</script>

<div id="arianne">
  <a href="#"><?php echo _("Disposition"); ?></a> > <a href="index.php?pid=51"><?php echo _("Blocs de contenu globaux"); ?></a> > <?php echo _("Modifier un bloc de contenu global"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajouter un bloc de contenu global"); ?></h1>
  <form id="formulaire" action="global_content_test_new.php" method="post" name="formulaire" enctype="multipart/form-data">
    <table id="main_table_form">
      <tr>
        <td colspan="2"><?php echo _("Nom : "); ?> <span style="color:#ff0000">*</span>&nbsp;&nbsp;&nbsp;<input type="text" name="name" style="width:200px" class="required" value="<?php echo $name; ?>"></td>
      </tr>
      <tr>
        <td colspan="2" style="padding-top:30px" >
          <div id="tab_content">
              <?php
              
              $languages = Miki_language::get_all_languages();
              
              foreach ($languages as $l){
                echo "<span class='tab_selector'>" ._("Contenu ") ." <img src='pictures/flags/$l->picture' alt='$l->name' style='vertical-align:middle' border='0' /></span>";
                echo "
                <div id='content_$l->code'>
                  <table>
                    <tr>
                      <td colspan='2' style='text-align:center'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td class='form_text'>" ._("Langue : ") ."</td>
                      <td class='form_box'>$l->name</td>
                    </tr>
                    <tr>
                      <td class='form_text'>" ._("Type de contenu : ") ."</td>
                      <td class='form_box'>
                        <select name='content_type_$l->code' onchange='javacript:change_content_type(this.value, \"$l->code\");'>
                          <option value='file' "; if (isset($content_type[$l->code]) && $content_type[$l->code] == 'file') echo "selected='selected'"; echo ">" ._("Fichier") ."</option>
                          <option value='code' "; if (!isset($content_type[$l->code]) || $content_type[$l->code] == 'code') echo "selected='selected'"; echo ">" ._("Code") ."</option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class='form_text'>" ._("Contenu : ") ."</td>
                      <td class='form_box' id='content_value_$l->code'>
                        <div id='editor_$l->code' "; if (isset($content_type[$l->code]) && $content_type[$l->code] == 'file') echo "style='display:none'"; echo ">
                          <textarea class='tinymce' name='code_$l->code' style='width:800px;height:600px'>"; if (isset($content[$l->code])) echo $content[$l->code]; echo"</textarea>
                        </div>
                        <div id='file_$l->code' "; if (!isset($content_type[$l->code]) || $content_type[$l->code] == 'code') echo "style='display:none'"; echo ">
                          <input type='file' name='file_$l->code' />
                        </div>
                      </td>
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
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=51'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
    </table>
  </form>
</div>