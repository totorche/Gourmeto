<head>
  <?php
    // recherche si la personne est connectée
    $miki_person = is_connected();
  ?>

  <script type="text/javascript">
    window.___gcfg = {
      lang: '<?php echo $_SESSION['lang']; ?>'
    };

    (function() {
      var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
      po.src = 'https://apis.google.com/js/plusone.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    })();
  </script>

  <script src="scripts/jquery-validation/jquery.validate.min.js" type="text/javascript"></script>
  <script src="scripts/jquery-validation/localization/messages_fr.js" type="text/javascript"></script>
  
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.15/jquery-ui.min.js"></script>
  <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/black-tie/jquery-ui.css" />
  
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
            document.location = "[miki_page='contact']";
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
      var form = $("#form_login");
      var params = form.serialize();
      var url = form.attr('action');
      
      // masque le bouton et affiche le gif animé pour patienter
      form.find(':submit').css("display","none");
      $("<img src='pictures/loader_noir.gif' style='margin-left: 5px;' alt='<?php echo _("Chargement..."); ?>' />").insertAfter(form.find(':submit'));
  
      /* Send the data using post and put the results in a div */
      $.post(url, params,
        function( data ) {
          if (data == '1'){
            document.location = "<?php echo (isset($_SESSION['last_url'])) ? $_SESSION['last_url'] : 'contact'; ?>";
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
      
      $("#form_login").validate({
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
      
      // ouvrir/fermer la partie "account"
      $("a.accout_button").click(function(e){
        e.preventDefault();
        $('section.account').slideToggle('slow');
        if ($("div.bouton_compte img").attr("src") == "pictures/fleche_bas.png")
          $("div.bouton_compte img").attr("src", "pictures/fleche_haut.png");
        else
          $("div.bouton_compte img").attr("src", "pictures/fleche_bas.png"); 
      });
      
      // déconnection d'un membre
      $(".miki_deconnection").click(function(e){
        if (!confirm("<?php echo _("Etes-vous sûr de vouloir vous déconnecter de votre compte") ." " .SITENAME; ?>"))
          e.preventDefault();
      });
    });
  </script>
</head>

<div class="main_container">
  <section class="account">
    <div class="container_12">
      <?php include("include/account.php"); ?>
    </div>
  </section>
  <header>
    <div class="container_12">
      <div class="grid_4 widget_panier">[miki_gc='panier']</div>
      <div class="top grid_8">
        <div class="bouton_compte">
          <?php
            if ($miki_person)
              $texte = _("Mon compte");
            else
              $texte = _("Login / S'enregistrer");
          ?>
          <a href="#" class="accout_button" title="<?php echo $texte; ?>"><?php echo $texte; ?></a>
          <a href="#" class="accout_button" title="<?php echo $texte; ?>"><img src="pictures/fleche_bas.png" alt="v" style="margin-left: 10px; vertical-align: top; margin-top: 3px;" /></a>
        </div>
      </div>
      <nav class="main_menu grid_9">
        <?php include_once("menus/main_menu.php"); ?>
      </nav>
      <div class="search grid_3">
        [miki_gc='search']
      </div>
      <div class="logo grid_5">
        <a href="<?php echo SITE_URL; ?>" title="<?php echo SITENAME; ?>"><img src="pictures/logo.png" alt="<?php echo SITENAME; ?>" /></a>
      </div>
      <div class="social grid_7">
        <?php include("include/social.php"); ?>
      </div>
    </div>
  </header>
  <section class="middle">
    <div class="container_12">
      <div class="miki-content-container">
        <?php 
          // si un titre pour la page a été donné on l'affiche. Si un titre vide est donné, on affiche rien
          if (isset($title_h1) && !empty($title_h1))
            echo "<h1>$title_h1</h1>";
          // sinon on a affiche le nom de la page
          elseif (!isset($title_h1))
            echo "<h1>" .$page->get_menu_name($_SESSION['lang']) ."</h1>";
        ?>        
        
        <div class="miki-content">
          [miki_content]
        </div>
        
      </div>
    </div>
  </section>
  <footer>
    <div class="container_12">
        <?php include("include/footer.php"); ?>
    </div>
  </footer>
  
  <div style="width: 100%; min-height: 20px; background: #D7D7D7"></div>
</div>