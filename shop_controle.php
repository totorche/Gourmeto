<?php
  require_once("include/payement_type.php");
?>

<head>
  <style type="text/css">
    
    table.panier{
      width: 100%;
      margin: 0 auto 20px 0;
    }
    
    table.panier th{
      background-color: #999999;
      border: solid 1px #999999;
      color: #ffffff;
      font-weight: bold;
      padding: 10px;
      text-align: center;
    }
    
    .article_panier{
      border: solid 1px #999999;
      padding: 5px;
    }
    
    .attributs, .options{
      font-size: 0.8em;
    }
    
    .service_client{
      margin-top: 40px;
      padding: 5px;
      width: 400px;
    }
    
    .code_promotion{
      text-align: right;
      margin: 20px 0;
    }
    
    .conditions_generales{
      margin-bottom: 20px;
      overflow: hidden;
    }
    
    .conditions_generales table{
      float: right;
      width: 450px;
    }
    
    form[name=next_step]{
      display: inline;
    }
    
    table.order_informations{
      width: 100%;
    }
    
    table.order_informations td{
      border: dashed #848484 1px;
      padding: 20px;
    }
    
    img.transporter_logo{
      height: 30px;
      margin-left: 20px;
    }
    
    .zone_boutons{
      clear: left;
      width: 100%;
    }
    
    .zone_boutons td{
      padding: 20px 0;
    }
  </style>
  
  <script type="text/javascript">
    $(document).ready(function() {      
      $('form[name=next_step]').submit(function(event){
        // vérifie si la checkbox pour accepter les conditions générales de vente est présente
        if ($('input[name=conditions_generales_vente]').length > 0){
          // si présente et pas cochée, on affiche un message et on ne va pas plus loin
          if ($('input[name=conditions_generales_vente]:checked').val() === undefined){
            alert("<?php echo _('Vous devez accepter les Conditions Générales de Vente pour pouvoir continuer'); ?>");
            event.preventDefault();
          }
        }
      });
    });
  </script>
</head>

<?php
  // vérifie qu'une commande soit en cours
  if (isset($_SESSION['miki_order']) && $_SESSION['miki_order'] instanceof Miki_order)
    $order = $_SESSION['miki_order'];
  else
    miki_redirect("[miki_page='shop_panier']");
    
  if ($miki_person === false || !($miki_person instanceof Miki_person))
    miki_redirect("[miki_page='shop_panier']");
    
  // récupert le type de paiement souhaité
  if (isset($_POST['payement_type']))
    $order->payement_type = $_POST['payement_type'];
    
  // récupert le type de paiement souhaité
  if (isset($_POST['transporter']))
    $order->id_transporter = $_POST['transporter'];
  
  // affecte à la commande l'adresse de livraison 
  $order->shipping_type       = stripslashes($_POST['shipping_type']);
  $order->shipping_firstname  = stripslashes($_POST['shipping_firstname']);
  $order->shipping_lastname   = stripslashes($_POST['shipping_lastname']);
  $order->shipping_address    = stripslashes($_POST['shipping_address']);
  $order->shipping_npa        = $_POST['shipping_npa'];
  $order->shipping_city       = stripslashes($_POST['shipping_city']);
  $order->shipping_country    = stripslashes($_POST['shipping_country']);
  
  // affecte à la commande l'adresse de facturation
  $order->billing_type       = stripslashes($_POST['billing_type']);
  $order->billing_firstname  = stripslashes($_POST['billing_firstname']);
  $order->billing_lastname   = stripslashes($_POST['billing_lastname']);
  $order->billing_address    = stripslashes($_POST['billing_address']);
  $order->billing_npa        = $_POST['billing_npa'];
  $order->billing_city       = stripslashes($_POST['billing_city']);
  $order->billing_country    = stripslashes($_POST['billing_country']);
  
  // met à jour la commande
  $order->update();
  
  
  // affecte les différents prix et autres données à la commande
  
  // récupert le code de promotion (et le rabais engendré) si disponible
  if (is_numeric($order->id_code_promo)){
    try{
      $code = new Miki_shop_code_promo($order->id_code_promo);
      $discount_code = true;
    }
    catch(Exception $e){
      $discount_code = false;
    }
  }
  else{
    $discount_code = false;
  }
  
  $total = 0;
  $shipping = 0;

  // récupert les frais de ports
  $shippings = $order->get_shipping($miki_person);
  if ($shippings){
    // calcul le total de frais de port
    foreach($shippings as $x => $s){
      $shipping += $s;
    }
  }
  
  $order->shipping_price = $shipping;
  
  // récupert tous les articles de la commande en cours
  $articles = $order->get_all_articles();

  // inclut puis affiche la frise
  include_once("include/shop_frise.php");
  print_frise(4);
?>

<!--<table class='zone_boutons'>
  <tr>
    <td style="text-align: left; vertical-align: middle;">
      <input type="button" onclick="document.location='[miki_page='shop_livraison_paiement']';" value="<?php echo _("Revenir à l'étape précédente"); ?>" class="button2" />
    </td>
    <td style="text-align: right; vertical-align: middle;">
      <input type="submit" value="<?php echo _("Terminer ma commande"); ?>" class="button_big1"/>
    </td>
</table>-->

<table class="panier">
    <tr>
      <th style="width: 330px;"><?php echo("Article"); ?></th>
      <th style="width: 100px;"><?php echo("Quantité"); ?></th>
      <th style="width: 100px;"><?php echo("Prix unitaire"); ?></th>
      <th style="width: 100px;"><?php echo("Prix total"); ?></th>
    </tr>
  
  <?php
  
  if (sizeof($articles) == 0){
    echo "<tr>
            <td colspan='4' class='article_panier'>Vous n'avez actuellement aucun article dans votre panier</td>
          </tr>
        </table>";
  }
  else{
      // parcourt tous les articles de la commande
      foreach($articles as $a){
        $article = new Miki_shop_article($a->id_article);
        
        // récupert les attributs proposés pour l'article en cours
        $attributes_dispo = $article->get_attributes();
        $attributes_text = "";
        
        // recherche les valeurs des attributs entrées par le client
        if ($a->attributes != ""){
          $tab = explode("&&", $a->attributes);
          
          foreach($tab as $t){
            $temp = explode("=", $t);
            $attributes_text .= $attributes_dispo[$temp[0]]['name'] ." : " .$temp[1] ."<br />";
          }
        }
        
        // Récupert les options si l'article est un article configurable
        $options_text = "";

        if ($article->type == 2){
          $options = $a->get_options();
          foreach($options as $option){
            $options_text .= $option->ref ." - " .$option->name[$_SESSION['lang']] ."<br />";
          }
        }
        
        // récupert le prix de l'article en prenant en compte les promotions et les options éventuelles
        $price = $a->get_price(true);
        $total += $a->nb * $price;
        echo "<tr>
                <td class='article_panier'>" .$article->get_name('fr');
                  if (!empty($attributes_text)) echo "<div class='attributs'><div style='font-weight: bold; margin-top: 5px;'>" ._("Attributs") ."</div>$attributes_text</div>";
                  if (!empty($options_text)) echo "<div class='options'><div style='font-weight: bold; margin-top: 5px;'>" ._("Options") ."</div>$options_text</div>";
          echo "</td>
                <td class='article_panier' style='text-align: center;'>$a->nb</td>
                <td class='article_panier' style='text-align: right;'>" .number_format($price,2,'.',"'") ." CHF</td>
                <td class='article_panier' style='text-align: right;'>" .number_format($a->nb * $price,2,'.',"'") ." CHF</td>
              </tr>";
      }
      
      // affecte le sous-total à la commande
      $order->subtotal = $total;
      $order->price_total = $total + $shipping;
      
      // récupert les taxes
      $taxes = Miki_shop::get_taxes($order->shipping_country);
      if (!is_array($taxes))
        $taxes = array();
      
      // affecte les taxes à la commande
      $order->taxes = array();
      foreach($taxes as $tax_name=>$tax){
        $order->taxes[$tax_name] = $tax[$order->shipping_country];
      }
      
      echo "<tr>
              <td>&nbsp;</td>";
      
        echo "<td colspan='2' style='text-align: right; padding: 5px'>
                " ._("Sous-total") ." : <br /><br />";
                
                if ($discount_code)
                  echo _("Rabais 'code de promotion'") ." : <br /><br />";
                
        echo    _("Frais de livraison") ." : <br /><br />"
                ._("Montant total HT") ." : <br /><br />";
                
                // affiche les titres des taxes
                foreach($taxes as $tax_name=>$tax){
                  if ($tax[$order->shipping_country] > 0)
                    echo $tax_name ." (" .$tax[$order->shipping_country] ."%) : <br /><br />";
                }
                
            echo _("Montant total TTC") ." : <br />
              </td>
              <td class='article_panier' style='text-align: right;'>
                " .number_format($total,2,'.',"'") ." CHF<br /><br />";
                
                // applique le rabais obtenu grâce au code de promotion
                if ($discount_code){
                  // récupert le rabais
                  $discount = $code->get_discount($total);
                  
                  // vérifie que le rabais soit correct
                  if (is_numeric($discount)){
                    // l'affecte à la commande
                    $order->discount = $discount;
                    $order->price_total -= $discount;
                    
                    // applique le rabais
                    $total -= $discount;
                  
                    // s'il y a plus de rabais que de frais, on réduit le rabais et on affiche un total de 0 CHF
                    if ($total < 0)
                      $total = 0;
                    
                    echo "- " .number_format($discount,2,'.',"'") ." CHF<br /><br />";
                  }
                }
                
          echo  number_format($shipping,2,'.',"'") ." CHF<br /><br />"
                .number_format($order->subtotal + $shipping,2,'.',"'") ." CHF<br /><br />";
                
                // affiche les montants des taxes
                $total_tax = 0;
                foreach($taxes as $tax_name=>$tax){
                  $tax_price = round(($total + $shipping) * ($tax[$order->shipping_country] / 100), 2);
                  $total_tax += $tax[$order->shipping_country];
                  if ($tax_price > 0)
                    echo number_format($tax_price,2,'.',"'") ." CHF<br /><br />";
                }
                
                // calcul le prix total de la commande
                $tax_price = round($order->price_total * ($total_tax / 100), 2);
                $order->price_total = $order->price_total + $tax_price;
                
            echo number_format($order->price_total,2,'.',"'") ." CHF
              </td>
            </tr>";
  }
  
  // converti les nombres à virgule
  $order->subtotal = number_format($order->subtotal,2,'.',"");
  $order->discount = number_format($order->discount,2,'.',"");
  $order->shipping_price = number_format($order->shipping_price,2,'.',"");
  $order->price_total = number_format($order->price_total,2,'.',"");
  
  // puis met à jour la commande
  $order->update();
  ?>
  
</table>

<table class="order_informations">
  <tr>
    <td>
      <h3><?php echo _("Adresse de livraison") ?></h3>
      <?php 
        echo "$order->shipping_type<br /> 
               $order->shipping_lastname<br />
               $order->shipping_firstname<br />
               $order->shipping_address<br />
               $order->shipping_npa<br />
               $order->shipping_city<br />
               $order->shipping_country<br />";
      ?>
    </td>
    <td>
      <h3><?php echo _("Adresse de facturation") ?></h3>
      <?php 
        echo "$order->billing_type<br /> 
               $order->billing_lastname<br />
               $order->billing_firstname<br />
               $order->billing_address<br />
               $order->billing_npa<br />
               $order->billing_city<br />
               $order->billing_country<br />";
      ?>
    </td>
  </tr>
  <tr>
    <td>
      <div class="transporters">
        <h3><?php echo _("Choix du transporteur") ?></h3>
        <?php
          $transporter = new Miki_shop_transporter($order->id_transporter);
          echo $transporter->name;
          if (!empty($transporter->logo) && $transporter->logo != "NULL") echo "<img class='transporter_logo' src='pictures/shop_transporter/$transporter->logo' alt=\"$transporter->name\" />"; 
        ?>
      </div>
    </td>
    <td>
      <div class="payement">
        <h3><?php echo _("Type de paiement") ?></h3>
        <?php echo $miki_payement_type[$order->payement_type]; ?>
      </div>
    </td>
  </tr>
</table>

<?php
  // on inclu les fonctions du type de paiement
  @include_once("scripts/payment_" .$order->payement_type .".php");

  // on récupert l'adresse de destination du formulaire
  $nom_var = $order->payement_type ."_form_url";
  if (isset($$nom_var))
    $form_url = $$nom_var;
  else
    $form_url = "[miki_page='shop_paiement_ok']";
  
  echo "<form action='$form_url' method='post'>";

  $nom_fonction = $order->payement_type ."_print_form";
  if (function_exists($nom_fonction))
    $nom_fonction($order, $miki_person);
?>

  <table class='zone_boutons'>
    <tr>
      <td style="text-align: left; vertical-align: middle;">
        <input type="button" onclick="document.location='[miki_page='shop_livraison_paiement']';" value="<?php echo _("Revenir à l'étape précédente"); ?>" class="button2" />
      </td>
      <td style="text-align: right; vertical-align: middle;">
        <input type="submit" value="<?php echo _("Terminer ma commande"); ?>" class="button_big1"/>
      </td>
    </tr>
  </table>

</form>