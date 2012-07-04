<?php
  require_once ("config/country_" .$_SESSION['lang'] .".php");
?>

<head>
  <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/black-tie/jquery-ui.css" />
  
  <style type="text/css">
    .form_account{
      float: left;
      margin-right: 20px;
      width: 360px;
    }
    
    .form_no_account{
      border-left: dashed #848484 1px;
      padding-left: 20px;
      margin-left: 380px;
      width: 559px;
    }
    
    label.error{
      display: block;
      margin: 0px;
    }
  </style>
  
  <script type="text/javascript">
  
    function form_account_create_send(){
      /* get some values from elements on the page: */
      var form = $("#form_account_create");
      var params = form.serialize();
      var url = form.attr('action');
  
      /* Send the data using post and put the results in a div */
      $.post(url, params,
        function( data ) {
          var result = $(data).find('#result');
          var msg = $(data).find('#msg');
          if (result.html() == '1'){
            document.location = "<?php echo (isset($_SESSION['last_url'])) ? $_SESSION['last_url'] : "[miki_page='accueil']"; ?>";
          }
          else{
            $("#form_account_create_results_error p").empty().append(msg.html());
            $("#form_account_create_results_error").css('display', 'block').delay(5000).hide("slow");
          }
        }
      );
    }
    
    function form_login_send(){
      /* get some values from elements on the page: */
      var form = $("#form_login_page");
      var params = form.serialize();
      var url = form.attr('action');
      
      // masque le bouton et affiche le gif animé pour patienter
      form.find(':submit').css("display","none");
      $("<img src='pictures/loader.gif' style='margin-left: 5px;' alt='<?php echo _("Chargement..."); ?>' />").insertAfter(form.find(':submit'));
  
      /* Send the data using post and put the results in a div */
      $.post(url, params,
        function( data ) {
          if (data == '1'){
            document.location = "<?php echo (isset($_SESSION['last_url'])) ? $_SESSION['last_url'] : "[miki_page='accueil']"; ?>";
          }
          else{
            // réaffiche le bouton
            form.find(':submit').next("img").remove();
            form.find(':submit').css("display","inline");
            
            $("#form_login_results_error p").empty().append("Votre nom d'utilisateur ou votre mot de passe est incorrect");
            $("#form_login_results_error").css('display', 'block').delay(5000).hide("slow");
          }
        }
      );
    }
  
    $(document).ready(function() {
      
      $("#form_login").find("input[name='username']").val("");
      $("#form_login").find("input[name='password']").val("");
    
      $('#password1').val("");
      $('#password2').val("");
      
      $("#oubliPassword").dialog({
  			autoOpen: false,
  			height: 300,
  			width: 400,
  			modal: true,
  			show: 'slide',
  			buttons: {
  				"<?php echo _("Annuler"); ?>": function() {
  					$(this).dialog("close");
  				},
          "<?php echo _("Nouveau mot de passe"); ?>": function() {
  					$("#formOubliPassword").submit();
  				}
  			}
  		});
      
      $("#form_account_create").validate({
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
          password1: "required",
          password2: {
            equalTo: "#password1"
          }
        },
        submitHandler: function(form) {
          form_account_create_send();
        }
      });
      
      $("#form_login_page").validate({
        rules: {
          username: "required",
          password: "required"
        },
        submitHandler: function(form) {
          form_login_send();
        }
      });
      
      $("#email").keyup(function(){
        $('#username').html($(this).val());
      });
      
      /* attach a submit handler to the form */
      $("#formOubliPassword").submit(function(event) {
        
        /* stop form from submitting normally */
        event.preventDefault(); 
            
        /* get some values from elements on the page: */
        var form = $(this);
        var params = form.serialize();
        var url = form.attr('action');
        
        // masque le bouton et affiche le gif animé pour patienter
        $(".ui-dialog-buttonset").find('button').css("display","none");
        $("<img src='pictures/loader.gif' style='margin-left: 5px;' alt='<?php echo _("Chargement..."); ?>' />").appendTo($(".ui-dialog-buttonset").find('button').parent());
    
        /* Send the data using post and put the results in a div */
        $.post(url, params,
          function( data ) {
            var result = $(data).find('#result');
            var msg = $(data).find('#msg');
            if (result.html() == '0'){
              // réaffiche le bouton
              $(".ui-dialog-buttonset").find('button').parent().find("img").remove();
              $(".ui-dialog-buttonset").find('button').css("display","inline");
            
              $("#form_oubli_password_results_error p").empty().append(msg.html());
              $("#form_oubli_password_results_error").css('display', 'block').delay(5000).hide("slow");
            }
            else{
              // réaffiche le bouton
              $(".ui-dialog-buttonset").find('button').parent().find("img").remove();
              $(".ui-dialog-buttonset").find('button').css("display","inline");
              
              $("#oubliPassword").dialog("option",
              "buttons", {
        				"<?php echo _("Fermer"); ?>": function() {
        					$(this).dialog("close");
        					$(this).dialog("option", "buttons", {
            				"<?php echo _("Annuler"); ?>": function() {
            					$(this).dialog("close");
            				},
                    "<?php echo _("Nouveau mot de passe"); ?>": function() {
            					$("#formOubliPassword").submit();
            				}
            			});
            			$("#oubliPassword").find("#oubliPasswordContent").css('display', 'block');
            			$("#form_oubli_password_results_success").css('display', 'none');
        				}
        			});
              
              $("#oubliPassword").find("#oubliPasswordContent").css('display', 'none');
              
              $("#form_oubli_password_results_success p").empty().append(msg.html());
              $("#form_oubli_password_results_success").css('display', 'block');
            }
          }
        );
      });
    });
  </script>
</head>

<?php
  if (isset($_REQUEST['frise']) && $_REQUEST['frise'] == 1){
    // inclut puis affiche la frise
    include_once("include/shop_frise.php");
    print_frise(2);
  }
  
  if (isset($miki_person) && $miki_person instanceof Miki_person){
    $account = new Miki_account();
    $account->load_from_person($miki_person->id);
    echo "Vous êtes déjà connecté sous le compte suivant : <span style='font-weight: bold;'>$account->username</span><br /><br />
          Si ce compte ne vous appartient pas ou que vous souhaitez vous connecter sous un autre compte, veuillez <a href='scripts/deconnection.php?goto=" .$_SERVER["REQUEST_URI"] ."' class='miki_deconnection' title='" ._("Se déconnecter") ."'>" ._("vous déconnecter") ."</a><br /><br />
          Si vous souhaitez revenir à la page précédente, veuillez <a href='" .$_SESSION['url_back'] ."' title='" ._("Se rendre à la page précédente") ."'>" ._("cliquer ici") ."</a>";
  }
  else{
?>

    <div class="form_account">
      
      <h2><?php echo _("Je posséde déjà un compte ") .Miki_configuration::get("sitename"); ?></h2>
      
      <div id="form_login_results_success" class="box_result_success" style="display: none;"><p></p></div>
      <div id="form_login_results_error" class="box_result_error" style="display: none;"><p></p></div>
      
      <form id="form_login_page" action="scripts/test_connection.php" method="post" name="formulaire" enctype="application/x-www-form-urlencoded" class="form_perso">
      
        <table>
          <tr>
            <td><?php echo _("Adresse e-mail :"); ?></td>
            <td><input type="text" name="username" style="width: 200px;" /></td>
          </tr>
          <tr>
            <td><?php echo _("Mot de passe :"); ?></td>
            <td><input type="password" name="password" style="width: 200px;" /></td>
          </tr>
          <tr>
            <td colspan="2" style="text-align: right">
              <input type="checkbox" name="memorize" value="1" id="memorize" />&nbsp;<label for="memorize"><?php echo _("Se souvenir de moi"); ?></label>
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
    
      <h2><?php echo _("Je veux me créer un nouveau compte ") .Miki_configuration::get("sitename"); ?></h2>
    
      <div id="form_account_create_results_success" class="box_result_success" style="display: none;"><p></p></div>
      <div id="form_account_create_results_error" class="box_result_error" style="display: none;"><p></p></div>
    
      <form id="form_account_create" action="account_create.php" method="post" name="formulaire" enctype="application/x-www-form-urlencoded" class="form_perso">
        
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
            <td><input type="password" id="password2" name="password2" value="" /></td>
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
          
          <form name="formOubliPassword" id="formOubliPassword" action="oubli_password_test.php" method="GET" enctype="application/x-www-form-urlencoded">
            <?php echo _("Votre adresse e-mail :"); ?><br />
            <input type="text" name="email" class="required" />
          </form>
        </div>
      </div>
    </div>

<?php
  }
?>