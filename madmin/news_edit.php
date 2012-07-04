<?php

if (!test_right(38)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

// si pas d'id donné, retour
if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

$id = $_GET['id'];
$user = "";

try{
  $news = new Miki_news($id);
  //$news->text = preg_replace("/<br( \/)?\>/i", "", $news->text);  
}catch(Exception $e){
  // si aucun enregistrement n'a été trouvé -> retour
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}
  
// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

?>

<script type="text/javascript" src="scripts/checkform.js"></script>
<script type="text/javascript" src="scripts/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>

<script type="text/javascript">

  window.addEvent('domready', function() {
    var myCheckForm = new checkForm($('formulaire'),{
                                          useAjax: false,
                                          errorPlace: 'bottom',
                                          divErrorCss: {
                                            'margin':'5px 0 0 0px'
                                          }
                                        });
                                        
    tinyMCE.init({
      // General options
      mode : "specific_textareas",
      editor_selector : "tinymce",
      theme : "advanced",
      language: "fr",
    plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",
      // Theme options
      theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,cut,copy,paste,pastetext,pasteword",
      theme_advanced_buttons2 : ",undo,redo,|,link,unlink,image,cleanup,help,code,|,preview,|,table,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,media,|,fullscreen,|,spellchecker,|,nonbreaking,blockquote",
      theme_advanced_buttons3 : "",
      theme_advanced_buttons4 : "",
      theme_advanced_toolbar_location : "top",
      theme_advanced_toolbar_align : "left",
      theme_advanced_statusbar_location : "bottom",
      theme_advanced_resizing : true,
      // Example content CSS (should be your site CSS)
    content_css : "css/default.css",
    document_base_url : "../../../../../",
      plugin_preview_width : "800",
  	  plugin_preview_height : "600",
      convert_urls : false,
      entity_encoding : "raw"
    });
  });

</script>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=121"><?php echo _("Actualités"); ?></a> > <?php echo _("Modifier une Actualité"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modifier une actualité"); ?></h1>
    
  <form id="formulaire" action="news_test_edit.php" method="post" style="width:100%;margin-top:20px" name="formulaire" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <table>
      <tr>
        <td><?php echo _("Titre : "); ?> <span style="color:#ff0000">*</span></td>
        <td><input type="text" name="title" class="required" style="width:400px" value="<?php echo $news->title; ?>" /></td>
      </tr>
      <tr>
        <td style="vertical-align:top"><?php echo _("Texte : "); ?></td>
        <td><textarea name="text" class="tinymce" style="width:400px;height:300px"><?php echo $news->text; ?></textarea></td>
      </tr>
      <tr>
        <td><?php echo _("Langue : "); ?></td>
        <td>
          <select name="language">
            <?php
              $langs = Miki_language::get_all_languages();
              foreach($langs as $lang){
                if ($news->language == $lang->code)
                  echo "<option value='$lang->code' selected='selected'>$lang->name</option>\n";
                else
                  echo "<option value='$lang->code'>$lang->name</option>\n";
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td><?php echo _("Image : "); ?></td>
        <td><input type="file" name="picture" /></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=121'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
      </tr>
    </table>
  </form>
    
</div>