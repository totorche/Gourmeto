<?php
  $sandbox = Miki_configuration::get('payement_paypal_sandbox') == 1;
  
  if ($sandbox){
    $paypal_form_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
  }
  else{
    $paypal_form_url = "https://www.paypal.com/cgi-bin/webscr";
  }
  
  // affichage des input dans le formulaire de paiement
  function paypal_print_form($order, $miki_person){
    require_once("paypal/crypt.php");

    $crypt = Miki_configuration::get('payement_paypal_secure') == 1;
    
    if ($crypt){
      $crypted = crypted_button($order, $miki_person);
      echo "<input name='cmd' type='hidden' value='_s-xclick' />
            <input name='encrypted' type='hidden' value='$crypted' />";
    }
    else{
      $datas = paypal_button($order, $miki_person);
    
      foreach($datas as $key=>$val){
        echo "<input type='hidden' name=\"$key\" value=\"$val\" />\n";
      }
    }    
  }
  
  // fonction servant au traitement de la commande lorsque l'utilisateur arrive sur la page de fin de paiement et que le paiement a abouti
  function paypal_payment_ok_user($args){
    if (isset($args['invoice']) && is_numeric($args['invoice'])){
      try{
        // récupert la commande
        $order = new Miki_order();
        $order->load_from_no($args['invoice']);
        
        // définit la commande comme étant "en attente de paiement"
        if ($order->state < 2){
          $order->state = 1;
          $order->update();
        }
        
        // vérifie si on utilise la gestion des stock ou pas
        $use_stock = Miki_configuration::get('miki_shop_gestion_stock');
        
        // vérifie si il s'agit d'un deal
        $is_miki_deal = $order->type == 2;
        
        // si on gère les stocks ou que la commande est un deal
        if ($use_stock || $is_miki_deal){
          // met à jour le stock
          $order->update_stock();
        }
        
        // vide la commande stockée
        if (isset($_SESSION['miki_order']))
          unset($_SESSION['miki_order']);
    
        echo "<div style='margin: 20px 0; text-align: center;'>"
              ._("Vous avez effectué le paiement de votre commande avec succès et nous vous en remercions") .". <br /><br />"
              ._("Votre commande est maintenant validée et vous sera livrée dans les plus brefs délais") .".<br /><br /><br />
              <a href='[miki_page='accueil']'>" ._("Retourner sur la page d'accueil") ."</a>
            </div>";
      }
      catch(Exception $e){
        echo _("Il manque des informations");
      }
    }
    else{
      echo _("Il manque des informations");
    }
  }
  
  // fonction servant au traitement de la commande lorsque l'utilisateur arrive sur la page de fin de paiement et que le paiement a échoué
  function paypal_payment_error_user($args){
    echo "<div style='margin: 20px 0; text-align: center;'>"
            ._("Vous avez annulé la procédure de paiement de votre commande") .".<br /><br />"
            ._("Votre commande n'est donc pas terminée") .".<br /><br />"
            ._("Vous pouvez choisir un autre moyen de paiement en vous rendant sur")
            ." <a href='[miki_page='shop_livraison_paiement']'>" ._("cette page") ."</a><br />" ._("ou") ." <a href='[miki_page='contact']'>" ._("nous contacter") ."</a> " ._("pour tenter de résoudre le problème rencontré") .".
          </div>";
  }
  
  // fonction traitant le résultat du paiement lors de la notification de paiement envoyée par le système de paiement
  function paypal_payment_notification($args){
    // si la transaction n'est pas un paiement de la part d'un client on ne fait rien
    if ($args['payment_status'] == 'Refunded' || 
        $args['payment_status'] == 'Denied' || 
        $args['payment_status'] == 'Reversed'){
      
      return;
    }
    
    // vérifie que Paypal soit configuré sur le système
    $paypal_account = Miki_configuration::get("payement_paypal_account");
    if ($paypal_account === false)
      return;
    
    error_reporting(E_ALL ^ E_NOTICE);
    $header = "";
    $payed = true;
    $error = "";
    $order = "";
    
    // récupert la commande
    $order = new Miki_order();
    $order->load_from_no($args['invoice']);
    
    // Read the post from PayPal and add 'cmd'
    $req = 'cmd=_notify-validate';
    if(function_exists('get_magic_quotes_gpc')){ 
      $get_magic_quotes_exits = true;
    }
    
    // Handle escape characters, which depends on setting of magic quotes
    foreach ($args as $key => $value){ 
      if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1){ 
        $value = urlencode(stripslashes($value));
      }else{
        $value = urlencode($value);
      }
      $req .= "&$key=" .utf8_decode($value);
    }
    
    // Post back to PayPal to validate
    $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
    
    // se connecte à Paypal (Sandbox ou version en production selon la configuration)
    if (Miki_configuration::get("payement_paypal_sandbox") == 1){
      $fp = fsockopen ('www.sandbox.paypal.com', 80, $errno, $errstr, 30);
    }
    else{
      $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
    }
    
    // Process validation from PayPal
    if (!$fp) {
      // erreur de connexion
      $payed = false;
      $erreur .= "- Erreur de connexion<br />";
    }else {
      // connexion OK
      fputs ($fp, $header . $req);
      
      while (!feof($fp)) {
        $res = fgets ($fp, 1024);
        if (strcmp ($res, "VERIFIED") == 0) {
          $val = array();
          // TODO:
          // Check the payment_status is Completed
          // Check that txn_id has not been previously processed
          // Check that receiver_email is your Primary PayPal email
          // Check that payment_amount/payment_currency are correct
          // Process payment
          // If 'VERIFIED', send an email of IPN variables and values to the
          // specified email address
          
          try{
            // test que le status de la commande soit "Completed"
            if ($args['payment_status'] !== "Completed"){
              $payed = false;
              $error .= "- Le status du paiement est : " .$args['payment_status'] ."<br />";
              
              /*if ($args['payment_status'] == 'Pending')
                $error .= "  Cause : " .$args['pending_reason'] ."<br /><br />";*/
            }
              
            // teste que l'id de transaction Paypal n'existe pas encore dans la base de donnée
            if ($order->paypal_transaction_exists($args['txn_id'])){
              // si l'id existe déjà c'est un duplicata de commande, on annule donc tout
              return;
            }
            else{
              // enregistre l'id de transaction Paypal
              $order->add_paypal_transaction($args['txn_id']);
            }
            
            // test que le montant de la commande soit correct
            if ($args['mc_gross'] != $order->price_total){
              $payed = false;
              $error .= "- Le montant payé ne correspond pas à celui de la commande<br />";
            }
            
            // teste que la personne qui reçoit l'argent soit la bonne
            if ($args['receiver_email'] !== $paypal_account){
              $payed = false;
              $error .= "- Le vendeur n'est pas le bon : " .$args['receiver_email'] ."<br />";
            }
            
            /*
            // Test si l'adresse e-mail donnée par l'acheteur correspond à celle de son compte 
            // On ne fait pas ce test ici car il peut donner une adresse différente lors du paiement
            
            $person = new Miki_person();
            $person->load_from_email($args['payer_email']);
            
            if ($order->id_person !== $person->id)
              $texte .= "L'adresse e-mail du client ne correspond pas !\n\n";*/
              
          }catch(Exception $e){
            $payed = false;
            $error .= "- " .$e->getMessage() ."<br />";
          }
        }
        elseif (strcmp ($res, "INVALID") == 0){
          $payed = false;
          $error .= "- Le contrôle IPN de Paypal est invalide<br />";
        }
      }
    }
    
    // envoi du mail de confirmation au client et passe la commande au status "payé" (state = 2) si commande payée ou "en attente de paiement" (state = 1)
    $order->set_completed(true, $payed);
    
    // ajoute le membre au groupe "Club" de la newsletter
    $miki_person = new Miki_person($order->id_person);
    $newsletter_member = new Miki_newsletter_member();
        
    if (Miki_newsletter_member::email_exists($miki_person->email1))
      $newsletter_member->load_from_email($miki_person->email1);
    
    $newsletter_member->firstname = $miki_person->firstname;
    $newsletter_member->lastname = $miki_person->lastname;
    $newsletter_member->email = $miki_person->email1;
    $newsletter_member->save();
    $newsletter_member->add_to_group(2);

    // si un problème est survenu lors du paiement on en informe le vendeur
    if (!$payed){
      $sitename = Miki_configuration::get('sitename');
      $email_answer = Miki_configuration::get('email_answer');
      
      $no_facture = $args['invoice'];
      $no_transaction = $args['txn_id'];
      
      // création du mail
      $mail = new miki_email('shop_payment_paypal_verification', 'fr');
      
      $mail->From     = $email_answer;
      $mail->FromName = $sitename;
      
      // prépare les variables nécessaires à la création de l'e-mail
      $vars_array['no_facture'] = $no_facture;
      $vars_array['no_transaction'] = $no_transaction;
      $vars_array['error'] = $error;
      $vars_array['sitename'] = $sitename;
      
      // initialise le contenu de l'e-mail
      $mail->init($vars_array);
          
      $mail->AddAddress("herve.torche@fbw-one.com", $sitename);
      if(!$mail->Send())
        throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
    }
    
    fclose ($fp);
  }
?>