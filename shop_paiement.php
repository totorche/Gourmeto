<script type="text/javascript">
  
  function redirect_paypal(){
    //$("#form_commande").submit();
  }
  
  $(document).ready(function() {
    if ($("#form_commande").size() > 0)
      setTimeout(redirect_paypal, 0);
  });
  
</script>

<?php
  require_once("paypal/crypt.php");
  require_once("scripts/class.phpmailer.php");
  
  // vérifie qu'une commande soit en cours
  if (isset($_SESSION['miki_order']) && $_SESSION['miki_order'] instanceof Miki_order)
    $order = $_SESSION['miki_order'];
  else
    miki_redirect("[miki_page='shop_panier']");
  
  if ($miki_person === false || !($miki_person instanceof Miki_person))
    miki_redirect("[miki_page='shop_panier']");
  
  // passe la commande dans l'état '1' : commande confirmée mais pas encore de confirmation de paiement
  $order->state = 1;
  
  // vérifie si on utilise la gestion des stock ou pas
  $use_stock = Miki_configuration::get('miki_shop_gestion_stock');
  
  // vérifie si il s'agit d'un deal
  $is_miki_deal = $order->type == 2;
  
  // si on gère les stocks ou que la commande est un deal
  if ($use_stock || $is_miki_deal){
    // met à jour le stock
    $order->update_stock();
  }

  // met à jour la commande
  $order->save();
?>

  
<?php
  // Paiement via Paypal (y compris cartes de crédit Visa + Mastercard)
  if ($order->payement_type == 'payement_paypal'){
    $sandbox = Miki_configuration::get('payement_paypal_sandbox') == 1;
    $crypt = Miki_configuration::get('payement_paypal_secure') == 1;
    
    if ($sandbox){
      echo "<form id='form_commande' action='https://www.sandbox.paypal.com/cgi-bin/webscr' method='post'>";
    }
    else{
      echo "<form id='form_commande' action='https://www.paypal.com/cgi-bin/webscr' method='post'>";
    }
    
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
    
    echo "  <!--<input type='submit' valut='aller' />-->
          </form>";
  }
  // Virement bancaire
  elseif ($order->payement_type == 'payement_bank'){
    // envoi du mail de confirmation
    $order->set_completed(true, false);
    
    // vide la commande stockée
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
          </div>";
  }
  // Sur facture (paiement avant envoi)
  elseif ($order->payement_type == 'payement_facture_avant'){
    // envoi du mail de confirmation
    $order->set_completed(true, false);
    
    // vide la commande stockée
    unset($_SESSION['miki_order']);

    echo "<div style='margin: 20px 0; text-align: center'>"
            ._("Vous aller recevoir dans les prochains jours par courrier postal une facture que vous devrez régler afin de finaliser votre commande") .".<br /><br />"
            ._("Votre commande vous sera envoyée une fois le paiement reçu") .".
          </div>";
  }
  // Sur facture (paiement après envoi)
  elseif ($order->payement_type == 'payement_facture_apres'){
    // envoi du mail de confirmation
    $order->set_completed(true, false);
    
    // vide la commande stockée
    unset($_SESSION['miki_order']);

    echo "<div style='margin: 20px 0; text-align: center'>"
            ._("Votre commande vous sera livrée tout prochainement et sera accompagnée de la facture que nous vous remercions de bien vouloir régler dans les 30 jours") .".
          </div>";
  }
  
  echo "<div style='margin: 40px 0; text-align: center'>"
          .sprintf(_("Toute l'équipe de %s vous remercie pour votre commande"), SITENAME) .".
        </div>";
?>