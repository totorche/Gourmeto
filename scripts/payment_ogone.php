<?php
  $ogone_form_url = "https://secure.ogone.com/ncol/test/orderstandard_utf8.asp";
  
  // affichage des input dans le formulaire de paiement
  function ogone_print_form($order, $miki_person){
    $shasign = '';
    $ogone_sha_in = "louischevrolet118";
    $lang = $_SESSION['lang'];
    
    // récupert l'url du site Internet
    $site_url = Miki_configuration::get("site_url");
    if ($site_url === false || $site_url == ""){
      return;
    }
    
    $ogoneParams = array();
    $ogoneParams['PSPID'] = "LouisChevroletTEST";
    $ogoneParams['OPERATION'] = 'SAL';
    $ogoneParams['ORDERID'] = $order->no_order;
    $ogoneParams['AMOUNT'] = number_format($order->price_total,2,'.','') * 100;
    $ogoneParams['CURRENCY'] = "CHF";
    $ogoneParams['LANGUAGE'] = $lang.'_'.strtoupper($lang);
    $ogoneParams['CN'] = "$order->shipping_firstname $order->shipping_lastname";
    $ogoneParams['EMAIL'] = $miki_person->email1;
    $ogoneParams['OWNERZIP'] = $order->shipping_npa;
    $ogoneParams['OWNERADDRESS'] = $order->shipping_address;
    $ogoneParams['OWNERCTY'] = $order->shipping_country;
    $ogoneParams['OWNERTOWN'] = $order->shipping_city;
    
    $ogoneParams['ACCEPTURL'] = "$site_url/paiement-valide";
    $ogoneParams['DECLINEURL'] = "$site_url/paiement-erreur";
    $ogoneParams['EXCEPTIONURL'] = "$site_url/paiement-erreur";
    $ogoneParams['CANCELURL'] = "$site_url/paiement-erreur";
    $ogoneParams['BACKURL'] = "$site_url/panier";
    
    $ogoneParams['PARAMPLUS'] = "miki_payment_type=ogone";
    $ogoneParams['PMLISTTYPE'] = '2';
    $ogoneParams['EXCLPMLIST'] = "Maestro";
    
    // pour l'apparence
    $ogoneParams['BGCOLOR'] = "#646464";
    $ogoneParams['TXTCOLOR'] = "#ffffff";
    $ogoneParams['TBLBGCOLOR'] = "#ececec";
    $ogoneParams['TBLTXTCOLOR'] = "#7b7b7b";
    $ogoneParams['BUTTONBGCOLOR'] = "#646464";
    $ogoneParams['BUTTONTXTCOLOR'] = "#ffffff";
    $ogoneParams['FONTTYPE'] = "Arial";
    //$ogoneParams['LOGO'] = "http://beta-project.net/louis-chevrolet/pictures/logo.png";
    
    if (!empty($miki_person->tel1))
        $ogoneParams['OWNERTELNO'] = $miki_person->tel1;

    ksort($ogoneParams);
    $shasign = '';
    foreach ($ogoneParams as $key => $value){
      $shasign .= strtoupper($key).'='.$value.$ogone_sha_in;
    }
    $ogoneParams['SHASign'] = strtoupper(sha1($shasign));
  
    foreach ($ogoneParams as $key => $value){
      echo "<input type='hidden' name='$key' value=\"$value\" />\n";
    }
  }
  
  // fonction servant au traitement de la commande lorsque l'utilisateur arrive sur la page de fin de paiement
  function ogone_payment_ok_user($args){
    if (isset($args['orderID']) && is_numeric($args['orderID']) && 
        isset($args['STATUS']) && 
        isset($args['NCERROR']) && 
        isset($args['CN']) && 
        isset($args['amount']) && 
        isset($args['currency']) && 
        isset($args['BRAND']) &&
        isset($args['TRXDATE'])){
      
      try{
        // récupert la commande
        $order = new Miki_order();
        $order->load_from_no($args['orderID']);
        
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
              <a href='[miki_page='accueil']' title=\"" ._("Retourner sur la page d'accueil") ."\">" ._("Retourner sur la page d'accueil") ."</a>
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
  function ogone_payment_error_user($args){
    
    $ogone_status[0] = "Incomplet ou invalide";
    $ogone_status[1] = "Annulé par client";
    $ogone_status[2] = "Autorisation refusée";
    $ogone_status[4] = "Commande encodée";
    $ogone_status[41] = "Attente paiement par client";
    $ogone_status[5] = "Autorisé";
    $ogone_status[51] = "Autorisation en attente";
    $ogone_status[52] = "Autorisation incertaine";
    $ogone_status[55] = "En suspens";
    $ogone_status[56] = "OK avec paiements planifiés";
    $ogone_status[57] = "Erreur dans les paiements planifiés";
    $ogone_status[59] = "Autor. à obtenir manuellement";
    $ogone_status[6] = "Autorisé et annulé";
    $ogone_status[61] = "Annul. d'autor. en attente";
    $ogone_status[62] = "Annul. d'autor. incertaine";
    $ogone_status[63] = "Annul. d'autor. refusée";
    $ogone_status[64] = "Autorisé et annulé";
    $ogone_status[7] = "Paiement annulé";
    $ogone_status[71] = "Annulation paiement en attente";
    $ogone_status[72] = "Annul paiement incertaine";
    $ogone_status[73] = "Annul paiement refusée";
    $ogone_status[74] = "Paiement annulé";
    $ogone_status[75] = "Annulation traitée par le marchand";
    $ogone_status[8] = "Remboursement";
    $ogone_status[81] = "Remboursement en attente";
    $ogone_status[82] = "Remboursement incertain";
    $ogone_status[83] = "Remboursement refusé";
    $ogone_status[84] = "Paiement refusé par l'acquéreur";
    $ogone_status[85] = "Rembours. traité par le marchand";
    $ogone_status[9] = "Paiement demandé";
    $ogone_status[91] = "Paiement en cours";
    $ogone_status[92] = "Paiement incertain";
    $ogone_status[93] = "Paiement refusé";
    $ogone_status[94] = "Remb. Refusé par l'acquéreur";
    $ogone_status[95] = "Paiement traité par le marchand";
    $ogone_status[99] = "En cours de traitement";
    
    if (isset($args['orderID']) && is_numeric($args['orderID']) && 
        isset($args['STATUS']) && 
        isset($args['NCERROR']) && 
        isset($args['CN']) && 
        isset($args['amount']) && 
        isset($args['currency']) && 
        isset($args['BRAND']) &&
        isset($args['TRXDATE'])){
      
      try{
        // récupert la commande
        $order = new Miki_order();
        $order->load_from_no($args['orderID']);
        
        // si un problème est survenu lors du paiement on en informe le vendeur
        $sitename = Miki_configuration::get('sitename');
        $email_answer = Miki_configuration::get('email_answer');
        
        // vide la commande stockée
        if (isset($_SESSION['miki_order']))
          unset($_SESSION['miki_order']);
        
        // si la commande a été annulée
        if ($args['STATUS'] == 1){
          // on met à jour la commande
          $order->state = 3;
          $order->update();

          echo "<div style='margin: 20px 0; text-align: center;'>"
                  ._("Vous avez annulé la procédure de paiement de votre commande") .".<br /><br />"
                  ._("Votre commande n'est donc pas terminée") .".<br /><br /><br />
                  <a href='[miki_page='accueil']' title=\"" ._("Retourner sur la page d'accueil") ."\">" ._("Retourner sur la page d'accueil") ."</a>
                </div>";
        }
        // sinon, si autre erreur
        else{
          // on envoie un mail
          $mail = new miki_email('shop_payment_ogone_verification', 'fr');
          
          $mail->From     = $email_answer;
          $mail->FromName = $sitename;
          
          // prépare les variables nécessaires à la création de l'e-mail
          $vars_array['date'] = date("d/m/Y", strtotime($args['TRXDATE']));
          $vars_array['person_name'] = $args['CN'];
          $vars_array['order_total'] = number_format($args['amount'],2,'.',"") ." " .$args['currency'];
          $vars_array['card_type'] = $args['BRAND'];
          $vars_array['error'] = $args['NCERROR'];
          $vars_array['order_no'] = $order->no_order;
          $vars_array['order_status'] = $ogone_status[$args['STATUS']];
          $vars_array['sitename'] = $sitename;
          
          // initialise le contenu de l'e-mail
          $mail->init($vars_array);
              
          $mail->AddAddress("herve.torche@fbw-one.com", $sitename);
          if(!$mail->Send())
            throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
            
          echo "<div style='margin: 20px 0; text-align: center;'>"
                  ._("Une erreur est survenue lors du paiement de votre commande") .".<br /><br />"
                  ._("Votre commande n'est donc pas terminée") .".<br /><br /><br />
                  <a href='[miki_page='accueil']' title=\"" ._("Retourner sur la page d'accueil") ."\">" ._("Retourner sur la page d'accueil") ."</a>
                </div>";
        }
      }
      catch(Exception $e){
        echo _("Il manque des informations");
      }
    }
    else{
      echo _("Il manque des informations");
    }
  }
  
  // fonction traitant le résultat du paiement lors de la notification de paiement envoyée par le système de paiement
  function ogone_payment_notification($args){
    if (isset($args['orderID']) && is_numeric($args['orderID']) && 
        isset($args['STATUS']) && 
        isset($args['NCERROR']) && 
        isset($args['CN']) && 
        isset($args['amount']) && 
        isset($args['currency']) && 
        isset($args['BRAND']) &&
        isset($args['TRXDATE'])){
      
      try{
        // récupert la commande
        $order = new Miki_order();
        $order->load_from_no($args['orderID']);
        
        // vérifie si la commande a été payée ou non
        $payed = $args['STATUS'] == 9;
        
        // envoi du mail de confirmation au client et passe la commande au status "payé" (state = 2) si commande payée ou "non-payé" (state = 1)
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
          
          // création du mail
          $mail = new miki_email('shop_payment_ogone_verification', 'fr');
          
          $mail->From     = $email_answer;
          $mail->FromName = $sitename;
          
          // prépare les variables nécessaires à la création de l'e-mail
          $vars_array['date'] = $args['TRXDATE'];
          $vars_array['person_name'] = $args['CN'];
          $vars_array['order_total'] = $args['amount'] ." " .$args['currency'];
          $vars_array['card_type'] = $args['BRAND'];
          $vars_array['error'] = $args['NCERROR'];
          $vars_array['order_no'] = $order->no_order;
          $vars_array['order_status'] = $args['STATUS'];
          $vars_array['sitename'] = $sitename;
          
          // initialise le contenu de l'e-mail
          $mail->init($vars_array);
              
          $mail->AddAddress("herve.torche@fbw-one.com", $sitename);
          if(!$mail->Send())
            throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
        }
      }
      catch(Exception $e){
        echo _("Il manque des informations");
      }
    }
    else{
      echo _("Il manque des informations");
    }
  }
?>