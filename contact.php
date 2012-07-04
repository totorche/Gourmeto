<head>
  <script src="<?php echo URL_BASE; ?>scripts/jquery.scrollTo.js" type="text/javascript"></script>
  
  <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
  <script type="text/javascript" src="scripts/gmap3.min.js"></script>
  
  <!--<script type="text/javascript" src="http://api.recaptcha.net/js/recaptcha_ajax.js"></script>-->

  <style type="text/css">
    
    #form_contact{
      background: url(<?php echo URL_BASE; ?>pictures/bonhomme-contact.jpg) no-repeat bottom right;
      margin-bottom: 10px;
    }
    
    #form_contact td{
      vertical-align: top;
      min-width: 100px;
    }
    
    #form_contact input[type=text]{
      width: 250px;
    }
    
    #form_contact textarea{
      width: 400px;
      height: 100px;
    }
    
    label.error{
      display: inline;
      margin: 3px;
    }
    
    .company_map{
      width: 100%;
      height: 250px;
    }
    
  </style>
  
  <script language="javascript" type="text/javascript">
    $(document).ready(function() {
      
      $("#form_contact").validate({
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
          message: "required"
        },
        submitHandler: function(form) {
          form_contact_send();
        }
      });
      
      // pour l'affichage de la carte Google Map
      $('.company_map').gmap3(
        { action: 'addMarker',
          address: "<?php echo preg_replace('#(\r\n|\r|\n)#', ', ', Miki_configuration::get('address_website_company')); ?>",
          infowindow:{
            options:{
              content: "<?php echo Miki_configuration::get('sitename'); ?>"
            }
          },
          map:{
            center: true,
            zoom: 15
          }
        }
      );
      
      /*Recaptcha.create("6LeOdwsAAAAAANWVCkvkvu5pvj2F-5CAB8ZzTv-j", "captcha", {
       lang : 'fr',  
       theme : 'white',  
       custom_translations : { instructions_visual : "Recopiez les mots ici :"}
      });*/
    });
    
    function form_contact_send(){
      var form = $("#form_contact");
      var params = form.serialize();
      var url = form.attr('action');
      
      // masque le bouton et affiche le gif animé pour patienter
      form.find(':submit').css("display","none");
      $("<img src='pictures/loader.gif' style='margin-left: 5px;' alt='<?php echo _("Chargement..."); ?>' />").insertAfter(form.find(':submit'));
  
      /* Send the data using post and put the results in a div */
      $.post(url, params,
        function( data ) {
          var result = $(data).find('#result');
          var msg = $(data).find('#msg');
          if (result.html() == '0'){
            // réaffiche le bouton
            form.find(':submit').next("img").remove();
            form.find(':submit').css("display","inline");
          
            $("#form_contact_results_error p").empty().append(msg.html());
            $("#form_contact_results_error").css('display', 'block').delay(5000).hide("slow");
            $(window).scrollTo("#form_contact_results_error");
          }
          else{
            // réaffiche le bouton
            form.find(':submit').next("img").remove();
            form.find(':submit').css("display","inline");
            
            $("#form_contact_results_success p").empty().append(msg.html());
            $("#form_contact_results_success").css('display', 'block').delay(5000).hide("slow");
            $(window).scrollTo("#form_contact_results_success");
          }
        }
      );
    }
  </script>
</head>


<h2>Contactez-nous en remplissant ce formulaire</h2>

<div id="form_contact_results_success" class="box_result_success" style="display: none;"><p></p></div>
<div id="form_contact_results_error" class="box_result_error" style="display: none;"><p></p></div>

<form id="form_contact" class="form_bleu" action="<?php echo URL_BASE; ?>contact_send.php" method="post" name="form_contact" enctype="application/x-www-form-urlencoded">
  <table class="" cellspacing="0" cellpadding="0">
    <tr>
      <td class="form_td"><label for="input_lastname"><?php echo _("Nom"); ?> <span style="color:#ff0000">*</span></label></td>
      <td class="form_td">
        <input type="text" id="input_lastname" name="lastname" value="<?php if (isset($_SESSION['person'])) echo $_SESSION['person']->lastname; ?>" />
      </td>
    </tr>
    <tr>
      <td class="form_td"><label for="input_firstname"><?php echo _("Prénom"); ?> <span style="color:#ff0000">*</span></label></td>
      <td class="form_td">
        <input type="text" id="input_firstname" name="firstname" value="<?php if (isset($_SESSION['person'])) echo $_SESSION['person']->firstname; ?>" />
      </td>
    </tr>
    <tr>
      <td class="form_td"><label for="input_address"><?php echo _("Adresse"); ?> <span style="color:#ff0000">*</span></label></td>
      <td class="form_td">
        <input type="text" id="input_address" name="address" value="<?php if (isset($_SESSION['person'])) echo $_SESSION['person']->address; ?>" />
      </td>
    </tr>
    <tr>
      <td class="form_td"><label for="input_npa"><?php echo _("Code postal"); ?> <span style="color:#ff0000">*</span></label></td>
      <td class="form_td">
        <input type="text" id="input_npa" name="npa" value="<?php if (isset($_SESSION['person'])) echo $_SESSION['person']->npa; ?>" />
      </td>
    </tr>
    <tr>
      <td class="form_td"><label for="input_city"><?php echo _("Localité"); ?> <span style="color:#ff0000">*</span></label></td>
      <td class="form_td">
        <input type="text" id="input_city" name="city" value="<?php if (isset($_SESSION['person'])) echo $_SESSION['person']->city; ?>" />
      </td>
    </tr>
    <tr>
      <td class="form_td"><label for="input_country"><?php echo _("Pays"); ?> <span style="color:#ff0000">*</span></label></td>
      <td class="form_td">
        <input type="text" id="input_country" name="country" value="<?php if (isset($_SESSION['person'])) echo $_SESSION['person']->country; ?>" />
      </td>
    </tr>
    <tr>
      <td class="form_td"><label for="input_tel"><?php echo _("Téléphone"); ?> <span style="color:#ff0000">*</span></label></td>
      <td class="form_td">
        <input type="text" id="input_tel" name="tel" value="<?php if (isset($_SESSION['person'])) echo $_SESSION['person']->tel1; ?>" />
      </td>
    </tr>
    <tr>
      <td class="form_td"><label for="input_email"><?php echo _("Adresse e-mail"); ?> <span style="color:#ff0000">*</span></label></td>
      <td class="form_td">
        <input type="text" id="input_email" name="email" value="<?php if (isset($_SESSION['person'])) echo $_SESSION['person']->email1; ?>" />
      </td>
    </tr>
    <tr>
      <td class="form_td"><label for="input_message"><?php echo _("Votre message"); ?> <span style="color:#ff0000">*</span></label></td>
      <td class="form_td">
        <textarea id="input_message" name="message" rows="15" cols="60" style="vertical-align:top"><?php if (isset($_SESSION['message'])) echo $_SESSION['message']; ?></textarea>
      </td>
    </tr>
    <!--<tr>
      <td class="form_td"><label for="captcha"><?php echo _("Veuillez recopier ce texte"); ?></label></td>
      <td class="form_td">
        <div id="captcha"></div>
      </td>
    </tr>-->
    <tr>
      <td class="form_td" colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td class="form_td" colspan="2" style="font-weight:bold"><?php echo _("Les champs munis d'une") ." <span style='color:#ff0000'>*</span> " ._("sont obligatoires"); ?></td>
    </tr>
    <tr>
      <td class="form_td" colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td class="form_td" colspan="2" style="text-align: right;">
        <input border="0" class="button_big1" type="submit" value="<?php echo _("Envoyer"); ?>" />
      </td>
    </tr>
  </table>
</form>

<div class="company_map"></div>