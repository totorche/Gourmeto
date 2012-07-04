<?php

if (!test_right(10))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

// si pas d'id donné, retour
if (!isset($_GET['id']) || !is_numeric($_GET['id']))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

$id = $_GET['id'];
$user = "";

try{
  $group = new Miki_group($id);
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
  <a href="#"><?php echo _("Utilisateurs/Groupes"); ?></a> > <a href="index.php?pid=61"><?php echo _("Groupes"); ?></a> > <?php echo _("Modifier un groupe"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modifier un groupe"); ?></h1>
  <form id="formulaire" action="group_test_edit.php" method="post" name="formulaire" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <table id="main_table_form">
      <tr>
        <td class="form_text"><?php echo _("Nom : "); ?> <span style="color:#ff0000">*</span></td>
        <td class="form_box"><input type="text" name="name" style="width:200px" class="required" value="<?php echo $group->name; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Actif : "); ?></td>
        <td class="form_box"><input type="checkbox" name="active" <?php if ($group->state == 1) echo "checked='checked'"; ?> value="1" /></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=71'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
      </tr>
    </table>
  </form>
</div>