<?php

if (!test_right(31) && !test_right(32) && !test_right(33)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

// si pas d'id donné, retour
if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

// récupert le membre
try{
  $member = new Miki_newsletter_member($_GET['id']);
}catch(Exception $e){
  // si aucun enregistrement n'a été trouvé -> retour
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}
  
// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

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
  <a href="#"><?php echo _("Newsletter"); ?></a> > <a href="index.php?pid=119"><?php echo _("Liste des abonnés"); ?></a> > <?php echo _("Modifier un abonné"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modifier un abonné"); ?></h1>
  <form id="formulaire" action="newsletter_member_test_edit.php" method="post" name="formulaire" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $member->id; ?>">
    <table id="main_table_form">
      <tr>
        <td class="form_text"><?php echo _("Prénom : "); ?></td>
        <td class="form_box"><input type="text" name="firstname" style="width:200px" value="<?php echo $member->firstname; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Nom : "); ?></td>
        <td class="form_box"><input type="text" name="lastname" style="width:200px" value="<?php echo $member->lastname; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Adresse e-mail : "); ?> <span style="color:#ff0000">*</span></td>
        <td class="form_box"><input type="text" name="email" style="width:200px" class="required email" value="<?php echo $member->email; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Groupe d'abonnés : "); ?></td>
        <td class="form_box" style="width:650px">
          <?php
            if (sizeof($groups) == 0){
              echo "Aucun groupe d'abonné n'est disponible.<br /><br />
                    Veuillez créer un groupe d'abonné en vous rendant sur <a href='index.php?pid=1194' title=\"Ajouter un groupe d'abonnés\">cette page</a>";
            }
            else{
              foreach($groups as $group){
                echo "<input type='checkbox' name='groups[]' value='$group->id'";
                
                if ($member->is_in_group($group->id)){
                  echo " checked='checked'";
                }
                
                echo " />&nbsp;$group->name<br />";
              }
            }
          ?>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td class="form_text">&nbsp;</td>
        <td class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=119'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
      </tr>
    </table>
  </form>
</div>