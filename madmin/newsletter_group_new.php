<?php

if (!test_right(31) && !test_right(32) && !test_right(33)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}
    
// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

if (isset($_SESSION['saved_group'])){
  $group = $_SESSION['saved_group'];
  unset($_SESSION['saved_group']);
}

$groups = Miki_newsletter_group::get_all_groups();

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
  <a href="#"><?php echo _("Newsletter"); ?></a> > <a href="index.php?pid=1193"><?php echo _("Liste des groupes d'abonnés"); ?></a> > <?php echo _("Ajouter un groupe d'abonnés"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajouter un groupe d'abonnés"); ?></h1>
  <form id="formulaire" action="newsletter_group_test_new.php" method="post" name="formulaire" enctype="multipart/form-data">
    <table id="main_table_form">
      <tr>
        <td class="form_text"><?php echo _("Nom : "); ?></td>
        <td class="form_box"><input type="text" name="name" style="width:200px" value="<?php if (isset($group)) echo $group->name; ?>"></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td class="form_text">&nbsp;</td>
        <td class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=1193'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
      </tr>
    </table>
  </form>
</div>