<?php
  require_once ("config/country_" .$_SESSION['lang'] .".php");
  
  if ($miki_person){
    ?>
    <div>Bienvenue <?php echo "$miki_person->firstname"; ?></div>
    <div style="margin-top: 10px;">
      <a href="[miki_page='account_edit']" title="<?php echo _("Mon compte"); ?>"><?php echo _("Mon compte"); ?></a>
      &nbsp;&nbsp;|&nbsp;&nbsp;
      <a href="scripts/deconnection.php" class="miki_deconnection" title="<?php echo _("Se déconnecter"); ?>"><?php echo _("Se déconnecter"); ?></a>
    </div>    
    <?php
  }
  else{
?>

    <div class="form_account">
      
      <h2><?php echo _("Je posséde déjà un compte ") .SITENAME; ?></h2>
      
      <div id="form_login_results_success" class="box_result_success" style="display: none;"><p></p></div>
      <div id="form_login_results_error" class="box_result_error" style="display: none;"><p></p></div>
      
      <form id="form_login" class="form_bleu" action="scripts/test_connection.php" method="post" name="formulaire" enctype="application/x-www-form-urlencoded">
      
        <table>
          <tr>
            <td><?php echo _("Adresse e-mail :"); ?></td>
            <td><input type="text" name="username" class="required" style="width: 200px;" /></td>
          </tr>
          <tr>
            <td><?php echo _("Mot de passe :"); ?></td>
            <td><input type="password" name="password" class="required" style="width: 200px;" /></td>
          </tr>
          <tr>
            <td colspan="2" style="text-align: right">
              <input type="checkbox" name="memorize" />&nbsp;<?php echo _("Se souvenir de moi"); ?>
            </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2" style="text-align: right;"><input type="submit" value="<?php echo _("S'authentifier"); ?>" class="button_big1" /></td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2" style="text-align: right">
              <a href="javascript:void(0);" class="boxed" onclick='javascript:$("#oubliPassword").dialog("open");' title="<?php echo _("Vous avez oublié votre mot de passe ?"); ?>"><?php echo _("Mot de passe oublié ?"); ?></a>
            </td>
          </tr>
        </table>
      </form>
      
    </div>
    
    <div class="form_no_account">
    
      <h2><?php echo _("Je veux créer mon compte ") .Miki_configuration::get("sitename"); ?></h2>
    
      <div id="form_account_create_results_success" class="box_result_success" style="display: none;"><p></p></div>
      <div id="form_account_create_results_error" class="box_result_error" style="display: none;"><p></p></div>
    
      <form id="form_account_create" class="form_bleu" action="account_create.php" method="post" name="formulaire" enctype="application/x-www-form-urlencoded" style="margin:0">
        
        <table>
          <tr>
            <td><?php echo _("Type :"); ?> <span style="color:#ff0000">*</span></td>
            <td>
              <select name="type">
                <option value="M" <?php if ($miki_person && $miki_person->type == 'M') echo "selected='selected'"; ?>>M</option>
                <option value="Mlle" <?php if ($miki_person && $miki_person->type == 'Mlle') echo "selected='selected'"; ?>>Mlle</option>
                <option value="Mme" <?php if ($miki_person && $miki_person->type == 'Mme') echo "selected='selected'"; ?>>Mme</option>
              </select>
            </td>
          </tr>
          <tr>
            <td><?php echo _("Nom :"); ?> <span style="color:#ff0000">*</span></td>
            <td><input type="text" name="lastname" value="<?php if ($miki_person) echo $miki_person->lastname; ?>" /></td>
          </tr>
          <tr>
            <td><?php echo _("Prénom :"); ?> <span style="color:#ff0000">*</span></td>
            <td><input type="text" name="firstname" value="<?php if ($miki_person) echo $miki_person->firstname; ?>" /></td>
          </tr>
          <tr>
            <td><?php echo _("Adresse :"); ?> <span style="color:#ff0000">*</span></td>
            <td><input type="text" name="address" value="<?php if ($miki_person) echo $miki_person->address; ?>" /></td>
          </tr>
          <tr>
            <td><?php echo _("Code postal :"); ?> <span style="color:#ff0000">*</span></td>
            <td><input type="text" name="npa" value="<?php if ($miki_person) echo $miki_person->npa; ?>" style="width:100px" maxlength="6" /></td>
          </tr>
          <tr>
            <td><?php echo _("Localité :"); ?> <span style="color:#ff0000">*</span></td>
            <td><input type="text" name="city" value="<?php if ($miki_person) echo $miki_person->city; ?>" /></td>
          </tr>
          <tr>
            <td><?php echo _("Pays :"); ?> <span style="color:#ff0000">*</span></td>
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
            <td><?php echo _("Téléphone :"); ?> <span style="color:#ff0000">*</span></td>
            <td><input type="text" name="tel" value="<?php if ($miki_person) echo $miki_person->tel1; ?>" /></td>
          </tr>
          <tr>
            <td><?php echo _("Email :"); ?> <span style="color:#ff0000">*</span></td>
            <td><input type="text" id="email" name="email" value="<?php if ($miki_person) echo $miki_person->email1; ?>" /></td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td>Nom d'utilisateur : </td>
            <td><span id="username"><?php if ($miki_person) echo $miki_person->email1; ?></span></td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td><?php echo _("Mot de passe :"); ?> <span style="color:#ff0000">*</span></td>
            <td><input type="password" id="password1" name="password1" value="" /></td>
          </tr>
          <tr>
            <td><?php echo _("Recopier le mot de passe :"); ?> <span style="color:#ff0000">*</span></td>
            <td><input type="password" name="password2" value="" /></td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2" style="font-style: italic;"><?php echo _("Les champs munis d'une <span style='color:#ff0000'>*</span> sont obligatoires"); ?></td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2" style="text-align: right">
              <input type="submit" value="<?php echo _("Créer mon compte"); ?>" class="button_big1" />
            </td>
          </tr>
        </table>
      </form>
    </div>
    
    
    <!-- Pour la récupératrion du mot de passe -->
    <div style="display:none">
      <div id="oubliPassword">
        
        <div id="form_oubli_password_results_success" class="box_result_success" style="display: none;"><p></p></div>
        
        <div id="oubliPasswordContent">
          <div id="form_oubli_password_results_error" class="box_result_error" style="display: none;"><p></p></div>
          
          <?php echo _("Vous avez oublié votre mot de passe ?"); ?><br /><br />
          <?php echo _("Entrez votre adresse e-mail et nous vous enverrons votre nouveau mot de passe."); ?>
          <br /><br />
          
          <form name="formOubliPassword" id="formOubliPassword" action="oubli_password_test.php" method="post" enctype="application/x-www-form-urlencoded">
            <?php echo _("Votre adresse e-mail :"); ?><br />
            <input type="text" name="email" class="required" />
          </form>
        </div>
      </div>
    </div>
<?php
  }
?>