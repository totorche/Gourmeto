<?php

// si pas d'id donné, retour
if (!isset($_GET['id']) || !is_numeric($_GET['id']))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
if (!test_right(9))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

$id = $_GET['id'];
$user = "";

try{
  $user = new Miki_user($id);
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
  <a href="#"><?php echo _("Utilisateurs/Groupes"); ?></a> > <a href="index.php?pid=61"><?php echo _("Utilisateurs"); ?></a> > <?php echo _("Modifier un utilisateur"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modifier un utilisateur"); ?></h1>
  <form id="formulaire" action="user_test_edit.php" method="post" name="formulaire" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <table id="main_table_form">
      <tr>
        <td class="form_text"><?php echo _("Nom : "); ?> <span style="color:#ff0000">*</span></td>
        <td class="form_box"><input type="text" name="name" style="width:200px" class="required" value="<?php echo $user->name; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Mot de passe : "); ?></td>
        <td class="form_box">
          <input type="password" name="password" style="width:200px">
          <?php echo _("Laisser ce champ vide pour conserver le mot de passe actuel"); ?>
        </td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Mot de passe (vérification) : "); ?></td>
        <td class="form_box">
          <input type="password" name="password2" style="width:200px">
          <?php echo _("Laisser ce champ vide pour conserver le mot de passe actuel"); ?>
        </td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Nom : "); ?></td>
        <td class="form_box"><input type="text" name="firstname" style="width:200px" value="<?php echo $user->firstname; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Prénom : "); ?></td>
        <td class="form_box"><input type="text" name="lastname" style="width:200px" value="<?php echo $user->lastname; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Adresse e-mail : "); ?></td>
        <td class="form_box"><input type="text" name="email" style="width:200px" class="email" value="<?php echo $user->email; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Page de démarrage : "); ?></td>
        <td class="form_box"><input type="text" name="default_page" style="width:200px" value="<?php echo $user->default_page; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Actif : "); ?></td>
        <td class="form_box"><input type="checkbox" name="active" <?php if ($user->state == 1) echo "checked='checked'"; ?> value="1" /></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Groupes : "); ?></td>
        <td class="form_box">
          <div>
            <?php
              $groups = Miki_group::get_all_groups(false);
              
              if (sizeof($groups) == 0)
                echo _("Aucun groupe n'est présent dans la base de données");
              
              foreach($groups as $group){
                echo "<input type='checkbox' name='$group->name' "; if ($user->has_group($group)) echo "checked='checked'"; echo " value='1' /> $group->name<br />";           
              }
            ?>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Clé API : "); ?></td>
        <td class="form_box"><?php echo $user->apikey; ?></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=61'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
    </table>
  </form>
</div>