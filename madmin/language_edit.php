<?php

if (!test_right(11))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

// si pas d'id donné, retour
if (!isset($_GET['id']) || !is_numeric($_GET['id']))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

$id = $_GET['id'];
$language = "";
try{
  $language = new Miki_language($id);
}catch(Exception $e){
  // si aucun enregistrement n'a été trouvé -> retour
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}
  
// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

?>

<script type="text/javascript" src="scripts/checkform.js"></script>

<script type="text/javascript">

  window.addEvent('domready', function() {
    var myCheckForm = new checkForm($('formulaire'),{
                                          useAjax: false,
                                          errorPlace: 'bottom',
                                          divErrorCss: {
                                            'margin':'5px 0 0 0px'
                                          }
                                        });
  });

</script>

<div id="arianne">
  <a href="#"><?php echo _("Administration du site"); ?></a> > <a href="index.php?pid=41"><?php echo _("Langues"); ?></a> > <?php echo _("Modifier une langue"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modifier une langue"); ?></h1>
  <form id="formulaire" action="language_test_edit.php" method="post" name="formulaire" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <table id="main_table_form">
      <tr>
        <td class="form_text"><?php echo _("Nom : "); ?> <span style="color:#ff0000">*</span></td>
        <td class="form_box"><input type="text" name="name" style="width:200px" class="required" value="<?php echo $language->name; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Code : "); ?> <span style="color:#ff0000">*</span></td>
        <td class="form_box"><input type="text" name="code" style="width:200px" class="required" value="<?php echo $language->code; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Image : "); ?></td>
        <td class="form_box"><input type="file" name="picture" /></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Langue principale ? : "); ?></td>
        <td class="form_box">
          <select name="mainLanguage">
            <option value="0" <?php if (!$language->is_main()) echo "selected='selected'"; ?>><?php echo _("Non"); ?></option>
            <option value="1" <?php if ($language->is_main()) echo "selected='selected'"; ?>><?php echo _("Oui"); ?></option>
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=41'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
      </tr>
    </table>
  </form>
</div>