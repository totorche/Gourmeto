<script type="text/javascript">
  $(document).ready(function() {
  
    $("#form_box_login").find("input[name='username']").val("");
    $("#form_box_login").find("input[name='password']").val("");
  
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
		
		
		/* attach a submit handler to the form */
    $("#form_box_login").submit(function(event) {
      
      /* stop form from submitting normally */
      event.preventDefault(); 
          
      /* get some values from elements on the page: */
      var form = $(this);
      var params = form.serialize();
      var url = form.attr('action');
      
      // masque le bouton et affiche le gif animé pour patienter
      form.find(':submit').css("display","none");
        $("<img src='pictures/loader_black.gif' style='margin-left: 5px;' alt='<?php echo _("Chargement..."); ?>' />").insertAfter(form.find(':submit'));
  
      /* Send the data using post and put the results in a div */
      $.post(url, params,
        function( data ) {
          if (data != '1'){
            // réaffiche le bouton
            form.find(':submit').next("img").remove();
            form.find(':submit').css("display","inline");
          
            $("#form_box_login_results_error p").empty().append("<?php echo _("Adresse e-mail ou mot de passe incorrecte"); ?>");
            $("#form_box_login_results_error").css('display', 'block').delay(5000).hide("slow");
          }
          else{
            // réaffiche le bouton
            form.find(':submit').next("img").remove();
            form.find(':submit').css("display","inline");
            
            alert('connecté');
          }
        }
      );
    });
  });
</script>

<form id="form_box_login" action="<?php echo URL_BASE; ?>scripts/test_connection.php" method="post" name="formulaire" enctype="application/x-www-form-urlencoded">
  
  <div id="form_box_login_results_error" class="box_result_error" style="display: none;"><p></p></div>
  
  <div>
    <div><?php echo _("Adresse e-mail :"); ?></div>
    <div style="margin-top:5px">
      <input type="text" name="username" style="width:150px" />
    </div>
  </div>
  
  <div>
    <div style="margin-top:5px"><?php echo _("Mot de passe :"); ?></div>
    <div style="margin-top:5px">
      <input type="password" name="password" style="width:150px" />
    </div>
  </div>
  
  <div style="margin-top:5px">
    <input type="checkbox" name="memorize" />&nbsp;<?php echo _("Se souvenir de moi"); ?>
  </div>
  
  <div style="margin-top:20px">
    <input border="0" class="button1" type="submit" value="<?php echo _("Envoyer"); ?>" />
  </div>
</form>