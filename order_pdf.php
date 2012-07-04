<?php
  require_once("include/payement_type.php");
  
  if (isset($_REQUEST['oid']) && is_numeric($_REQUEST['oid'])){
    
    try{
      // récupert la commande
      $order = new Miki_order($_REQUEST['oid']);
      
      /**
       * vérifie que le visiteur ait le droit de visualiser la commande. OK si : 
       *   - Administrateur
       *   - Le membre est connecté et qu'il visualise ses propres commandes
       *   - L'id du membre est passé en paramètre et qu'il correspond à l'id du membre ayant effectué la commande
       */
      $can_print = false;
      
      if (isset($_SESSION['miki_admin_user_id']))
        $can_print = true;
      
      if (isset($_REQUEST['pid']) && $_REQUEST['pid'] == $order->id_person)
        $can_print = true;
      
      if (isset($miki_person) && $miki_person instanceof Miki_person && $miki_person->id == $order->id_person)
        $can_print = true;
      
      if (!$can_print)
        echo _("Vous n'avez pas le droit de visualiser ce document");
      
      $person = new Miki_person($order->id_person);
      
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

      // récupert tous les articles de la commande en cours
      $articles = $order->get_all_articles();
      ?>
      
      <style type="text/css">
        table.headers{
          width: 100%;
          font-size: 8pt;
          margin-bottom: 10mm;
        }
        
        table.headers td{
          width: 50%;
          vertical-align: top;
        }
        
        .top_left{
          width: 105mm;
          vertical-align: top;
        }
        
          .order_date{
          }
          
          .order_no{
            font-size: 14pt;
            font-weight: bold;
            margin-top: 10mm;
          }
        
        .top_right{
          width: 77mm;
          vertical-align: top;
        }
          
          .person_address{
          }
        
          .date{
            margin-top: 20mm;
          }

        table.title{
          margin-top: 20mm;
        }
        
        table.title td{
          border-bottom: solid 1px #000000;
          color: #000000;
          padding-bottom: 5px;
        }
        
        .article_order{
          padding: 10px 0 20px 0;
          vertical-align: top;
        }
        
        .attributs{
          font-size: 8pt;
          width: 80mm;
        }
        
        .options{
          font-size: 8pt;
          width: 80mm;
        }
        
        .payement_type{
          margin-top: 10mm;
          text-align: right;
        }
        
        .message{
          margin-top: 10mm;
          text-align: right;
        }
        
        .footer{
          text-align: center;
        }
        
      </style>
      
      <page backtop="30mm" backbottom="7mm">
        <page_header> 
          <table class='headers'>
            <tr>
              <td style="text-align: left; vertical-align: top;">
                <?php
                  $logo_website = Miki_configuration::get("logo_website");
                  if ($logo_website)
                    echo "<img src='pictures/$logo_website' alt=\"" .SITENAME ."\" style='height: 70px;' />";
                ?>
              </td>
              <td style="text-align: right; vertical-align: top;">
                <?php
                  $address = nl2br(Miki_configuration::get('address_website_company'));
                  if ($address)
                    echo $address ."<br /><br />";
                    
                  $tel = Miki_configuration::get('miki_shop_service_client_tel');
                  if ($tel)
                    echo $tel ."<br /><br />";
                    
                  $email = Miki_configuration::get('miki_shop_service_client_email');
                  if ($email)
                    echo $email ."<br /><br />";
                ?>
              </td>
            </tr>
          </table>
        </page_header> 
        <page_footer>
          <div class='footer'>page [[page_cu]]/[[page_nb]]</div>
        </page_footer> 
        
        
        <table class='details'>
          <tr>
            <td class="top_left">
              <table class='order_date'>
                <tr>
                  <td><?php echo _("Date de la facture"); ?></td>
                  <td><?php echo date("d.m.Y", strtotime($order->date_completed)); ?></td>
                </tr>
              </table>
              
              <div class='order_no'>
                <?php echo _("N° facture") ." " .$order->no_order; ?>
              </div>
            </td>
            <td class="top_right">
              <div class='person_address'>
                <?php
                  echo "$person->firstname $person->lastname<br />
                        $person->address<br />
                        $person->npa $person->city<br />
                        $person->country";
                ?>
              </div>
              
              <div class='date'>Porrentruy, <?php echo date("d.m.Y", strtotime($order->date_completed)); ?></div>
            </td>
          </tr>
        </table>
        
        <table class="order title" cellspacing="0">
          <tr>
            <td style="width: 87mm; text-align: left;"><?php echo("Article"); ?></td>
            <td style="width: 25mm; text-align: left;"><?php echo("Ref"); ?></td>
            <td style="width: 20mm; text-align: center;"><?php echo("Quantité"); ?></td>
            <td style="width: 25mm; text-align: right;"><?php echo("Prix"); ?></td>
            <td style="width: 25mm; text-align: right;"><?php echo("Total"); ?></td>
          </tr>
        </table>
        
        <?php
        
        if (sizeof($articles) == 0){
          echo "<table class='order'>
                  <tr>
                    <td style='width: 182mm;' class='article_order'>Vous n'avez actuellement aucun article dans votre panier</td>
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
            echo "<table class='order'>
                    <tr>
                      <td class='article_order' style='width: 87mm; text-align: left;'>" .$article->get_name('fr');
                        if (!empty($attributes_text)) echo "<br /><div class='attributs'><div style='font-weight: bold; margin-top: 5px;'>" ._("Attributs") ."</div>$attributes_text</div>";
                        if (!empty($options_text)) echo "<br /><div class='options'><div style='font-weight: bold; margin-top: 5px;'>" ._("Options") ."</div>$options_text</div>";
                echo "</td>
                      <td class='article_order' style='width: 25mm; text-align: left;'>$article->ref</td>
                      <td class='article_order' style='width: 20mm; text-align: center;'>$a->nb</td>
                      <td class='article_order' style='width: 25mm; text-align: right;'>" .number_format($price,2,'.',"'") ." CHF</td>
                      <td class='article_order' style='width: 25mm; text-align: right;'>" .number_format($a->nb * $price,2,'.',"'") ." CHF</td>
                    </tr>
                  </table>";
          }
          
          // récupert les taxes
          $taxes = Miki_shop::get_taxes($order->shipping_country);
          if (!is_array($taxes))
            $taxes = array();
          
          echo "<table class='order'>
                  <tr>";
              echo "<td class='article_order' style='width: 132mm; text-align: right;'>
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
                    <td class='article_order' style='width: 50mm; text-align: right; border-top: solid 1px #000000;'>
                      " .number_format($total,2,'.',"'") ." CHF<br /><br />";
                      
                      // applique le rabais obtenu grâce au code de promotion
                      if ($discount_code){
                        // récupert le rabais
                        $discount = $code->get_discount($total);
                        
                        // vérifie que le rabais soit correct
                        if (is_numeric($discount)){
                          // applique le rabais
                          $total -= $discount;
                        
                          // s'il y a plus de rabais que de frais, on réduit le rabais et on affiche un total de 0 CHF
                          if ($total < 0)
                            $total = 0;
                          
                          echo "- " .number_format($discount,2,'.',"'") ." CHF<br /><br />";
                        }
                      }
                      
                echo  number_format($order->shipping_price,2,'.',"'") ." CHF<br /><br />"
                      .number_format($order->subtotal + $order->shipping_price,2,'.',"'") ." CHF<br /><br />";
                      
                      // affiche les montants des taxes
                      $total_tax = 0;
                      foreach($taxes as $tax_name=>$tax){
                        $tax_price = round(($total + $order->shipping_price) * ($tax[$order->shipping_country] / 100), 2);
                        $total_tax += $tax[$order->shipping_country];
                        if ($tax_price > 0)
                          echo number_format($tax_price,2,'.',"'") ." CHF<br /><br />";
                      }
                      
                      // calcul le prix total de la commande
                      $tax_price = round($order->price_total * ($total_tax / 100), 2);
                      
                  echo number_format($order->price_total,2,'.',"'") ." CHF
                    </td>
                  </tr>
                </table>
                
                <div class='payement_type'>" ._("Type de paiement") ." : " .$miki_payement_type[$order->payement_type] ."</div>
                
                <div class='message'>" .sprintf(_("Toute l'équipe de %s vous remercie de votre confiance"), SITENAME) ."</div>";
        }
        ?>
      </page>
      
      <?php
      
    }
    catch(Exception $e){
      echo _("Il manque des informations");
      exit();
    }
  }
  else{
    echo _("Il manque des informations");
    exit();
  }
?>