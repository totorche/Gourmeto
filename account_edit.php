<?php
  // vérifie si la personne est connectée
  if (!$miki_person)
    miki_redirect(Miki_configuration::get('site_url'));
    
  require_once ("config/country_" .$_SESSION['lang'] .".php");
?>

<head>
  <script src="scripts/jquery.scrollTo.js" type="text/javascript"></script>
  <script src="scripts/forms.js" type="text/javascript"></script>
  
  <script type="text/javascript">
    
    $(document).ready(function() {
      $("#form_account_edit").validate({
        rules: {
          lastname: "required",
          firstname: "required",
          address: "required",
          npa: {
            required: true,
            digits: true
          },
          city: "required",
          tel: "required",
          email: {
            required: true,
            email: true
          },
          password2: {
            equalTo: "#password1"
          }
        },
        submitHandler: function(form) {
          form_send(form);
        }
      });
      
      $("#email").keyup(function(){
        $('#username').html($(this).val());
      });
    });
  </script>

</head>


<?php 
  // affiche les box de messages de résultats
  print_results(); 
?>

<h2><?php echo _("Vous pouvez ici modifier vos informations personnelles"); ?></h2>

<form id="form_account_edit" action="account_edit_test.php" class="form_bleu" method="post" name="formulaire" enctype="application/x-www-form-urlencoded" style="margin:0">
  <table>
    <tr>
      <td>Type : <span style="color:#ff0000">*</span></td>
      <td>
        <select name="type">
          <option value="M" <?php if ($miki_person && $miki_person->type == 'M') echo "selected='selected'"; ?>>M</option>
          <option value="Mlle" <?php if ($miki_person && $miki_person->type == 'Mlle') echo "selected='selected'"; ?>>Mlle</option>
          <option value="Mme" <?php if ($miki_person && $miki_person->type == 'Mme') echo "selected='selected'"; ?>>Mme</option>
        </select>
      </td>
    </tr>
    <tr>
      <td>Nom : <span style="color:#ff0000">*</span></td>
      <td><input type="text" name="lastname" value="<?php echo $miki_person->lastname; ?>" /></td>
    </tr>
    <tr>
      <td>Prénom : <span style="color:#ff0000">*</span></td>
      <td><input type="text" name="firstname" value="<?php echo $miki_person->firstname; ?>" /></td>
    </tr>
    <tr>
      <td>Adresse : <span style="color:#ff0000">*</span></td>
      <td><input type="text" name="address" value="<?php echo $miki_person->address; ?>" /></td>
    </tr>
    <tr>
      <td>Npa : <span style="color:#ff0000">*</span></td>
      <td><input type="text" name="npa" value="<?php echo $miki_person->npa; ?>" style="width:100px" maxlength="6" /></td>
    </tr>
    <tr>
      <td>Localité : <span style="color:#ff0000">*</span></td>
      <td><input type="text" name="city" value="<?php echo $miki_person->city; ?>" /></td>
    </tr>
    <tr>
      <td>Pays : <span style="color:#ff0000">*</span></td>
      <td>
        <select name="country" style="width:90%">
          <?php
            foreach($country_list as $key=>$c){
              echo "<option value=\"$c\"";
              if ($miki_person && $miki_person->country == $c) echo " selected='selected'";
              if (!$miki_person && $c == 'Suisse') echo " selected='selected'";
              echo ">$c</option>"; 
            }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td>Téléphone : <span style="color:#ff0000">*</span></td>
      <td><input type="text" name="tel" value="<?php echo $miki_person->tel1; ?>" /></td>
    </tr>
    <tr>
      <td>Email : <span style="color:#ff0000">*</span></td>
      <td><input type="text" id="email" name="email" value="<?php echo $miki_person->email1; ?>" /></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td>Nom d'utilisateur : </td>
      <td><span id="username"><?php echo $miki_person->email1; ?></span></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2"><h3><?php echo _("Modifier votre mot de passe (laisser vide pour ne pas apporter de modification) : "); ?></h3></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td><?php echo _("Mot de passe :"); ?></td>
      <td><input type="password" id="password1" name="password1" value="" /></td>
    </tr>
    <tr>
      <td><?php echo _("Recopier le mot de passe :"); ?></td>
      <td><input type="password" name="password2" value="" /></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" style="font-style: italic;">Les champs munis d'une <span style="color:#ff0000">*</span> sont obligatoires</td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" style="text-align: right;">
        <input type="submit" value="<?php echo _("Modifier"); ?>" class="button_big1" />
      </td>
    </tr>
  </table>
</form>