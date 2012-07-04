<?php
  $facture_apres_form_url = "[miki_page='shop_paiement_ok']";
  
  // affichage des input dans le formulaire de paiement
  function facture_apres_print_form($order, $miki_person){
    echo "<input type='hidden' name='order_id' value='$order->id' />\n";
    echo "<input type='hidden' name='miki_payment_type' value='facture_apres' />\n";
  }
  
  // fonction servant au traitement de la commande lorsque l'utilisateur arrive sur la page de fin de paiement
  function facture_apres_payment_ok_user($args){
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
    
        echo "<div style='margin: 20px 0; text-align: center'>"
                ._("Votre commande vous sera livrée tout prochainement et sera accompagnée de la facture que nous vous remercions de bien vouloir régler dans les 30 jours") .".
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
  function facture_apres_payment_error_user($args){}
  
  // fonction traitant le résultat du paiement lors de la notification de paiement envoyée par le système de paiement
  function facture_avant_payment_notification($args){}
?>