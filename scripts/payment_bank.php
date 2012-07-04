<?php
  $bank_form_url = "[miki_page='shop_paiement_ok']";
  
  // affichage des input dans le formulaire de paiement
  function bank_print_form($order, $miki_person){
    echo "<input type='hidden' name='order_id' value='$order->id' />\n";
    echo "<input type='hidden' name='miki_payment_type' value='bank' />\n";
  }
  
  // fonction servant au traitement de la commande lorsque l'utilisateur arrive sur la page de fin de paiement
  function bank_payment_ok_user($args){
    if (isset($args['order_id']) && is_numeric($args['order_id'])){
      try{
        // récupert la commande
        $order = new Miki_order($args['order_id']);
        
        // envoi du mail de confirmation
        $order->set_completed(true, false);
        
        // vérifie si on utilise la gestion des stock ou pas
        $use_stock = Miki_configuration::get('miki_shop_gestion_stock');
        
        // vérifie si il s'agit d'un deal
        $is_miki_deal = $order->type == 2;
        
        // si on gère les stocks ou que la commande est un deal
        if ($use_stock || $is_miki_deal){
          // met à jour le stock
          $order->update_stock();
        }
        
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
        
        // vide la commande stockée
        if (isset($_SESSION['miki_order']))
          unset($_SESSION['miki_order']);
    
        $compte_bancaire['iban'] = Miki_configuration::get('payement_bank_iban');
        if ($compte_bancaire['iban'] === false)
          $compte_bancaire['iban'] = '';
        
        $compte_bancaire['bic'] = Miki_configuration::get('payement_bank_bic');
        if ($compte_bancaire['bic'] === false)
          $compte_bancaire['bic'] = '';
    
        echo "<div style='margin:20px 0;text-align:center'>"
                ._("Vous avez choisi de régler la facture au moyen d'un virement bancaire") .".<br />"
                ._(sprintf("Merci de bien vouloir régler le montant de %s sur le compte suivant", "<span style='font-weight: bold;'>" .number_format($order->price_total,2,'.',"'") ." CHF</span>")) ." : 
                <p style='margin: 20px 0; font-weight: bold'>"
                  ._("N° IBAN") ." : </span>" .$compte_bancaire['iban'] ."<br />"
                  ._("N° SWIFT/BIC") ." : </span>" .$compte_bancaire['bic'] ."
                </p>"
                ._("Votre commande vous sera envoyée une fois le paiement reçu") .".
              </div>
              <div style='margin: 40px 0; text-align: center'>"
                .sprintf(_("Toute l'équipe de %s vous remercie pour votre commande"), SITENAME) .".
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
  function bank_payment_error_user($args){}
  
  // fonction traitant le résultat du paiement lors de la notification de paiement envoyée par le système de paiement
  function bank_payment_notification($args){}
?>