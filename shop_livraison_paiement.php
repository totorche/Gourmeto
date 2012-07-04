<head>

  <?php
    require_once("include/payement_type.php");
    require_once ("config/country_" .$_SESSION['lang'] .".php");
    
    
    // si pas connecté, on définit le pays par défaut (pour donner un calcul des frais de port par défaut)
    if (!($miki_person instanceof Miki_person)){
      miki_redirect("[miki_page='shop_panier']");
    }
    else{
      $shipping_text = _("Frais de livraison");
      $next_page = "[miki_page='shop_livraison_paiement']";
    }
  
    // si une commande est déjà en cours, on la récupert
    if (isset($_SESSION['miki_order']) && $_SESSION['miki_order'] instanceof Miki_order){
      $order = $_SESSION['miki_order'];
      
      // si personne n'est encore affecté à la commande, on affecte le membre connecté à la commande
      if (!is_numeric($order->id_person)){
        $order->id_person = $miki_person->id;
        $order->update();
      }
    }
    else{
      miki_redirect("[miki_page='shop_panier']");
    }
  ?>


  <style type="text/css">
    
    .shipping_address{
      float: left;
      border-bottom: dashed #848484 1px;
      padding: 0 20px 30px 0;
      width: 432px;
    }
    
    .billing_address{
      float: left;
      border-left: dashed #848484 1px;
      border-bottom: dashed #848484 1px;
      padding: 0 0 30px 20px;
      width: 431px;
    }
    
    table td{
      min-width: 0;
    }
    
    .transporters{
      clear: left;
      float: left;
      padding: 30px 20px 0 0;
      width: 560px;
    }
    
    .transporters td,
    .payement td
    {
      padding: 10px;
    }
    
    .transporters img.transporter_logo{
      height: 30px;
    }
    
    .transporter_delai_livraison{
      font-size: 0.8em;
    }
    
    .payement{
      float: left;
      border-left: dashed #848484 1px;
      padding: 30px 0 0 20px;
      width: 303px;
    }
    
    tr.selectable{
      cursor: pointer;
      border-top: dashed 1px #D1D1D1;
      border-bottom: dashed 1px #D1D1D1;
    }
    
    tr.selectable:hover td{
      background: #D1D1D1;
      color: #FFFFFF;
    }
    
    tr.selected td{
      background: #999999;
      color: #FFFFFF;
    }
    
    tr:not(.selected) .transporter_price{
      text-decoration:line-through;
    }
    
    .zone_boutons{
      clear: left;
      width: 100%;
    }
    
    .zone_boutons td{
      padding: 40px 0 20px 0;
    }
  </style>
  
  <script type="text/javascript">
    
    // sélectionne la ligne clickée
    function select_line(el){
      $(el).parents("table").find("tr.selectable").removeClass('selected');
      $(el).addClass('selected');
      $(el).find("input[type=radio]").attr('checked',true);
    }
    
    $(document).ready(function() {
      // sélectionne le premier transporteur
      //select_line($("div.transporters tr.selectable:first-child"));
      select_line($("input[type=radio][value=<?php echo (is_numeric($order->id_transporter)) ? $order->id_transporter : 1; ?>]").parents("tr"));
      select_line($("input[type=radio][value=<?php echo ($order->payement_type != "" && $order->payement_type != "NULL") ? $order->payement_type : "paypal"; ?>]").parents("tr"));
      
      // contrôle le formulaire
      $("#form_livraison_paiement").validate({
        rules: {
          shipping_lastname: "required",
          shipping_firstname: "required",
          shipping_address: "required",
          shipping_npa: {
            required: true,
            digits: true
          },
          shipping_city: "required",
          billing_lastname: "required",
          billing_firstname: "required",
          billing_address: "required",
          billing_npa: {
            required: true,
            digits: true
          },
          billing_city: "required"
        },
        submitHandler: function(form) {
          form_send(form);
        }
      });
    });

  </script>
</head>

<?php
  // inclut puis affiche la frise
  include_once("include/shop_frise.php");
  print_frise(3);
?>

<form action="[miki_page='shop_controle']" method="post" name="form_livraison_paiement" id="form_livraison_paiement" enctype="application/x-www-form-urlencoded">
  
  <div class="shipping_address">
    
    <h3><?php echo _("Adresse de livraison") ?></h3>
        
    <table>
      <tr>
        <td colspan="2">
          <?php echo _("Veuillez contrôler vos informations pour la livraison"); ?>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td>Type : <span style="color:#ff0000">*</span></td>
        <td>
          <select name="shipping_type">
            <option value="M" <?php if ($order->shipping_type == 'M') echo "selected='selected'"; elseif ($miki_person->type == 'M') echo "selected='selected'"; ?>>M</option>
            <option value="Mlle" <?php if ($order->shipping_type == 'Mlle') echo "selected='selected'"; elseif ($miki_person->type == 'Mlle') echo "selected='selected'"; ?>>Mlle</option>
            <option value="Mme" <?php if ($order->shipping_type == 'Mme') echo "selected='selected'"; elseif ($miki_person->type == 'Mme') echo "selected='selected'"; ?>>Mme</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Nom : <span style="color:#ff0000">*</span></td>
        <td><input type="text" name="shipping_lastname" value="<?php echo ($order->shipping_lastname != "" && $order->shipping_lastname != "NULL") ? $order->shipping_lastname : $miki_person->lastname; ?>" /></td>
      </tr>
      <tr>
        <td>Prénom : <span style="color:#ff0000">*</span></td>
        <td><input type="text" name="shipping_firstname" value="<?php echo ($order->shipping_lastname != "" && $order->shipping_lastname != "NULL") ? $order->shipping_firstname : $miki_person->firstname; ?>" /></td>
      </tr>
      <tr>
        <td>Adresse : <span style="color:#ff0000">*</span></td>
        <td><input type="text" name="shipping_address" value="<?php echo ($order->shipping_lastname != "" && $order->shipping_lastname != "NULL") ? $order->shipping_address : $miki_person->address; ?>" /></td>
      </tr>
      <tr>
        <td>Npa : <span style="color:#ff0000">*</span></td>
        <td><input type="text" name="shipping_npa" value="<?php echo ($order->shipping_lastname != "" && $order->shipping_lastname != "NULL") ? $order->shipping_npa : $miki_person->npa; ?>" style="width:100px" maxlength="6" /></td>
      </tr>
      <tr>
        <td>Localité : <span style="color:#ff0000">*</span></td>
        <td><input type="text" name="shipping_city" value="<?php echo ($order->shipping_lastname != "" && $order->shipping_lastname != "NULL") ? $order->shipping_city : $miki_person->city; ?>" /></td>
      </tr>
      <tr>
        <td>Pays : <span style="color:#ff0000">*</span></td>
        <td>
          <select name="shipping_country" style="width:90%">
            <?php
              foreach($country_list as $key=>$c){
                echo "<option value=\"$c\"";
                if ($order->shipping_country != "" && $order->shipping_country != "NULL"){
                  if ($order->shipping_country == $c) 
                    echo " selected='selected'";
                }
                else{
                  if ($miki_person->country == $c) 
                    echo " selected='selected'";
                }
                echo ">$c</option>"; 
              }
            ?>
          </select>
        </td>
      </tr>
    </table>
    
  </div>
  
  <div class="billing_address">
    
    <h3><?php echo _("Adresse de facturation") ?></h3>
        
    <table>
      <tr>
        <td colspan="2">
          <?php echo _("Veuillez contrôler vos informations pour la facturation"); ?>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td>Type : <span style="color:#ff0000">*</span></td>
        <td>
          <select name="billing_type">
            <option value="M" <?php if ($order->billing_type == 'M') echo "selected='selected'"; elseif ($miki_person->type == 'M') echo "selected='selected'"; ?>>M</option>
            <option value="Mlle" <?php if ($order->billing_type == 'Mlle') echo "selected='selected'"; elseif ($miki_person->type == 'Mlle') echo "selected='selected'"; ?>>Mlle</option>
            <option value="Mme" <?php if ($order->billing_type == 'Mme') echo "selected='selected'"; elseif ($miki_person->type == 'Mme') echo "selected='selected'"; ?>>Mme</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Nom : <span style="color:#ff0000">*</span></td>
        <td><input type="text" name="billing_lastname" value="<?php echo ($order->billing_lastname != "" && $order->billing_lastname != "NULL") ? $order->billing_lastname : $miki_person->lastname; ?>" /></td>
      </tr>
      <tr>
        <td>Prénom : <span style="color:#ff0000">*</span></td>
        <td><input type="text" name="billing_firstname" value="<?php echo ($order->billing_lastname != "" && $order->billing_lastname != "NULL") ? $order->billing_firstname : $miki_person->firstname; ?>" /></td>
      </tr>
      <tr>
        <td>Adresse : <span style="color:#ff0000">*</span></td>
        <td><input type="text" name="billing_address" value="<?php echo ($order->billing_lastname != "" && $order->billing_lastname != "NULL") ? $order->billing_address : $miki_person->address; ?>" /></td>
      </tr>
      <tr>
        <td>Npa : <span style="color:#ff0000">*</span></td>
        <td><input type="text" name="billing_npa" value="<?php echo ($order->billing_lastname != "" && $order->billing_lastname != "NULL") ? $order->billing_npa : $miki_person->npa; ?>" style="width:100px" maxlength="6" /></td>
      </tr>
      <tr>
        <td>Localité : <span style="color:#ff0000">*</span></td>
        <td><input type="text" name="billing_city" value="<?php echo ($order->billing_lastname != "" && $order->billing_lastname != "NULL") ? $order->billing_city : $miki_person->city; ?>" /></td>
      </tr>
      <tr>
        <td>Pays : <span style="color:#ff0000">*</span></td>
        <td>
          <select name="billing_country" style="width:90%">
            <?php
              foreach($country_list as $key=>$c){
                echo "<option value=\"$c\"";
                if ($order->billing_country != "" && $order->billing_country != "NULL"){
                  if ($order->billing_country == $c) 
                    echo " selected='selected'";
                }
                else{
                  if ($miki_person->country == $c) 
                    echo " selected='selected'";
                }
                echo ">$c</option>"; 
              }
            ?>
          </select>
        </td>
      </tr>
    </table>
    
  </div>
  
  <div class="transporters">
  
    <h3><?php echo _("Mode de livraison") ?></h3>
  
    <table>
      <?php
        // récupert tous les transporteurs activés
        $transporters = Miki_shop_transporter::search("", true);

        foreach($transporters as $transporter){
          $shipping = 0;
          // récupert les frais de ports
          $shippings = $order->get_shipping($miki_person, $transporter->id);
          if ($shippings){
            // calcul le total de frais de port
            foreach($shippings as $x => $s){
              $shipping += $s;
            }
          }
      ?>
          <tr class="selectable" onclick='javascript:select_line(this);'>
            <td style="width: 20px;"><input type="radio" name="transporter" value="<?php echo $transporter->id; ?>" /></td>
            <td style="width: 250px;"><?php echo $transporter->name ."<br /><span class='transporter_delai_livraison'>$transporter->shipping_delay</span>"; ?></td>
            <td style="width: 90px; text-align: center;"><?php if (!empty($transporter->logo) && $transporter->logo != "NULL") echo "<img class='transporter_logo' src='pictures/shop_transporter/$transporter->logo' alt=\"$transporter->name\" />"; ?></td>
            <td style="width: 120px; text-align: right;" class="transporter_price"><?php echo number_format($shipping,2,'.',"'") ." CHF"; ?></td>
          </tr>
      <?php
          }
      ?>
    </table>
  </div>
  
  <div class="payement">
  
    <h3><?php echo _("Mode de paiement") ?></h3>
  
    <table>
    
      <?php
        foreach($miki_payement_type as $index => $payement_type){
          if (Miki_configuration::get("payement_" .$index) == 1 || $index == 'ogone'){
            echo "<tr class='selectable' onclick='javascript:select_line(this);'>
                    <td style='width: 20px;'><input type='radio' name='payement_type' value='$index' /></td>
                    <td style='width: 263px;'>$payement_type</td>
                  </tr>";
          }
        }
      ?>
    </table>
  
  </div>
  
  <table class='zone_boutons'>
    <tr>
      <td style="text-align: left; vertical-align: middle;">
        <input type="button" onclick="document.location='[miki_page='shop_panier']';" value="<?php echo _("Revenir à l'étape précédente"); ?>" class="button2" />
      </td>
      <td style="text-align: right; vertical-align: middle;">
        <input type="submit" value="<?php echo _("Confirmer ma commande"); ?>" class="button_big1 confirmer_commande" />
      </td>
  </table>
</form>