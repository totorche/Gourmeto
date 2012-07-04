<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <title><?php echo _("Redirection vers Paypal..."); ?></title>
  
  <style type="text/css">
    #form_paypal input[type=submit]{
      background: none; 
      padding: 0; 
      margin: 0; 
      border: none;
      font: inherit;
      cursor: pointer;
      color: #00B6EF;
    }
    
    #form_paypal input[type=submit]:hover{
      text-decoration: underline;
    }
    
    body {  
      font-size: 0.85em;
      line-height: 130%;
    }
    
    html {
      font-size: 100%;
      font-family: Arial,Helvetica,sans-serif;
    }
    
    p{
      margin: 0.75em 0;
    }
  </style>
  
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" type="text/javascript"></script>
  
  <script type="text/javascript">
    $(document).ready(function() {
      setTimeout(redirect_to_paypal, 5000);
    });
    
    function redirect_to_paypal(){
      $('#form_paypal').submit();
    }
  </script>
  
  </head>
  <body>

  <?php
    require_once("include/headers.php");
    
    // vérifie si les les informations de la personne et de l'événement ont été stockée dans la session
    if (!isset($_SESSION['paypal_person']) || !($_SESSION['paypal_person'] instanceof Miki_person) || 
        !isset($_SESSION['paypal_event']) || !($_SESSION['paypal_event'] instanceof Miki_event)){
      exit();
    }
    
    // inclut les fonctionnalité de Paypal
    require_once("paypal/crypt_event.php");
    
    // inclut le formulaire Paypal (Sandbox si demandé ou normal sinon)
    if (Miki_configuration::get("payement_paypal_sandbox") == 1)
      echo "<form id='form_paypal' action='https://www.sandbox.paypal.com/cgi-bin/webscr' method='post'>";
    else
      echo "<form id='form_paypal' action='https://www.paypal.com/cgi-bin/webscr' method='post'>";
  ?>        
              <!--<input name="cmd" type="hidden" value="_s-xclick" />-->
              <!--<input name="encrypted" type="hidden" value="<?php $bouton=paypal_button($_SESSION['paypal_event'], $_SESSION['paypal_person']); echo $bouton; ?>" />-->
              
              <?php 
                $paypa_datas = paypal_button($_SESSION['paypal_event'], $_SESSION['paypal_person']);
                foreach($paypa_datas as $index => $data){
                  echo "<input type='hidden' name='$index' value='$data' />\n";
                }
              ?>
              
              <div style="text-align: center">
                <img src="pictures/paiement.jpg" alt="Paypal Verified" />
              </div>
              <div style="text-align: center; margin-top: 30px;">
                <img src="pictures/loader-big.gif" alt="Paypal Verified" />
              </div>
              <p style="margin-top: 30px; text-align: center;">
                <?php echo _("Vous avez choisi de payer par carte de crédit ou par Paypal.") ."<br /><br />"
                          ._("Vous allez être redirigé sur Paypal d'ici quelques secondes.") ."<br /><br />"
                          ._("Si vous n'êtes pas redirigé après 10 secondes, cliquez sur le lien suivant :"); 
                ?>
                <input type="submit" value="<?php echo _("Redirection manuelle"); ?>" />
              </p>
            </form>
  </body>
</html>