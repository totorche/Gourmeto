<?php

if (!test_right(1))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

if (isset($_SESSION['saved_name'])) 
  $name = $_SESSION['saved_name'];
else
  $name = "";
  
if (isset($_SESSION['saved_password'])) 
  $password = $_SESSION['saved_password'];
else
  $password = "";
  
if (isset($_SESSION['saved_firstname'])) 
  $firstname = $_SESSION['saved_firstname'];
else
  $firstname = "";

if (isset($_SESSION['saved_lastname'])) 
  $lastname = $_SESSION['saved_lastname'];
else
  $lastname = "";

if (isset($_SESSION['saved_email'])) 
  $email = $_SESSION['saved_email'];
else
  $email = "";
  
if (isset($_SESSION['saved_active'])) 
  $active = $_SESSION['saved_active'] == 1 ? true : false;
else
  $active = true;
  
if (isset($_SESSION['saved_default_page'])) 
  $default_page = $_SESSION['saved_default_page'] == 1 ? true : false;
else
  $default_page = "";
  
if (isset($_SESSION['saved_groups'])){
  $saved_groups = explode(";;",$_SESSION['saved_groups']);
}
  
unset($_SESSION['saved_name']);
unset($_SESSION['saved_password']);
unset($_SESSION['saved_firstname']);
unset($_SESSION['saved_lastname']);
unset($_SESSION['saved_email']); 
unset($_SESSION['saved_active']);
unset($_SESSION['saved_groups']); 

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
  <a href="#"><?php echo _("Utilisateurs/Groupes"); ?></a> > <a href="index.php?pid=61"><?php echo _("Utilisateurs"); ?></a> > <?php echo _("Ajouter un utilisateur"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajouter un utilisateur"); ?></h1>
  <form id="formulaire" action="user_test_new.php" method="post" name="formulaire" enctype="application/x-www-form-urlencoded">
    <table id="main_table_form">
      <tr>
        <td class="form_text"><?php echo _("Nom : "); ?> <span style="color:#ff0000">*</span></td>
        <td class="form_box"><input type="text" name="name" style="width:200px" class="required" value="<?php echo $name; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Mot de passe : "); ?> <span style="color:#ff0000">*</span></td>
        <td class="form_box"><input type="password" name="password" style="width:200px" class="required" value=""></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Mot de passe (vérification) : "); ?> <span style="color:#ff0000">*</span></td>
        <td class="form_box"><input type="password" name="password2" style="width:200px" class="required" value=""></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Nom : "); ?></td>
        <td class="form_box"><input type="text" name="firstname" style="width:200px" value="<?php echo $firstname; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Prénom : "); ?></td>
        <td class="form_box"><input type="text" name="lastname" style="width:200px" value="<?php echo $lastname; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Adresse e-mail : "); ?></td>
        <td class="form_box"><input type="text" name="email" style="width:200px" class="email" value="<?php echo $email; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Page de démarrage : "); ?></td>
        <td class="form_box"><input type="text" name="default_page" style="width:200px" value="<?php echo $default_page; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Actif : "); ?></td>
        <td class="form_box"><input type="checkbox" name="active" <?php if ($active) echo "checked='checked'"; ?> value="1" /></td>
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
                echo "<input type='checkbox' name='$group->name' "; if (isset($saved_groups) && in_array($group->id, $saved_groups)) echo "checked='checked'"; echo " value='1' /> $group->name<br />";           
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
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=61'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
      </tr>
    </table>
  </form>
</div>