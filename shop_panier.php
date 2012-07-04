<head>
  <?php
    // si pas connecté, on définit le pays par défaut (pour donner un calcul des frais de port par défaut)
    if ($miki_person === false || !($miki_person instanceof Miki_person)){
      $person = new Miki_person();
      $person->country = isset($_SESSION['miki_person_country']) ? $_SESSION['miki_person_country'] : "Suisse";
      $shipping_text = _("Frais de livraison estimés");
      $next_page = "[miki_page='login' params='frise=1']"; 
      $_SESSION['last_url'] = "[miki_page='shop_livraison_paiement']";
    }
    else{
      $person = $miki_person;
      $shipping_text = _("Frais de livraison");
      $next_page = "[miki_page='shop_livraison_paiement']";
    }
    
    // si une commande est déjà en cours, on la récupert
    if (isset($_SESSION['miki_order']) && $_SESSION['miki_order'] instanceof Miki_order){
      $order = $_SESSION['miki_order'];
      
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
      
      // récupert tous les articles de la commande en cours
      $articles = $order->get_all_articles();
      
    }
    else{
      $order = false;
    }
  ?>
  
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
    
    .zone_boutons{
      width: 100%;
    }
    
    .zone_boutons td{
      padding: 20px 0;
    }
    
    form[name=next_step]{
      display: inline;
    }
  </style>
  
  <script type="text/javascript">
    $(document).ready(function() {
    
      // demande une validation pour la suppression d'un article
      $(".delete").each(function(index){
        $(this).click(function(){
          if (!confirm('<?php echo _("Etes-vous sûr de vouloir supprimer cet article ?"); ?>')){
            event.preventDefault();
            return false;
          }
        });
      });
      
      $('input[type=button].next_step').click(function(event){
        // vérifie si la checkbox pour accepter les conditions générales de vente est présente
        if ($('input[name=conditions_generales_vente]').length > 0){
          // si présente et pas cochée, on affiche un message et on ne va pas plus loin
          if ($('input[name=conditions_generales_vente]:checked').val() === undefined){
            alert("<?php echo _('Vous devez accepter les Conditions Générales de Vente pour pouvoir continuer'); ?>");
            event.preventDefault();
          }
          else{
            document.location = '<?php echo $next_page; ?>';
          }
        }
      });
    });
    
    // modifie la quantité d'un article
    function modifier_quantite(id, id2, n, miki_deal){
      if (!isNaN(n) && parseInt(n) == n)
        document.location = '<?php echo URL_BASE; ?>shop_modifier_panier.php?a=4&v=' + id + '&w=' + id2 + '&n=' + n + '&miki_deal=' + miki_deal;
      else 
        return false
    }
    
    // valide un code de promotion
    function test_code_promo(){
      var code = $('#code_promo').val();
      document.location = '<?php echo URL_BASE; ?>shop_code_promo_test.php?cp=' + code;
    }
  </script>
</head>

<?php
  // inclut puis affiche la frise
  include_once("include/shop_frise.php");
  print_frise(1);
  
  // affiche les box de messages de résultats
  print_results();
?>
  
  <table class='zone_boutons'>
    <tr>
      <td style="text-align: left; vertical-align: middle;">
        <input type='button' value="<?php echo _("Continuer mes achats"); ?>" class='button2' onclick="document.location='[miki_page='shop_articles_list']'" />
      </td>
      <td style="text-align: right; vertical-align: middle;">
        <?php
        if ($order && sizeof($articles) > 0){
          echo "<input type='button' value='" ._("Confirmer ma commande") ."' class='button_big1 next_step' />";
        }
        ?>
      </td>
    </tr>
  </table>
  
  <table class="panier">
    <tr>
      <th style="width: 330px;"><?php echo("Article"); ?></th>
      <th style="width: 100px;"><?php echo("Quantité"); ?></th>
      <th style="width: 100px;"><?php echo("Prix unitaire"); ?></th>
      <th style="width: 100px;"><?php echo("Prix total"); ?></th>
    </tr>
  
  <?php
  
  if (!$order || sizeof($articles) == 0){
    echo "<tr>
            <td colspan='4' class='article_panier'>Vous n'avez actuellement aucun article dans votre panier</td>
          </tr>
        </table>";
  }
  else{
      $total = 0;
      $shipping = 0;

      // récupert les frais de ports
      $shippings = $order->get_shipping($person);
      if ($shippings){
        // calcul le total de frais de port
        foreach($shippings as $x => $s){
          $shipping += $s;
        }
      }

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
                <td class='article_panier'>" .$article->get_name($_SESSION['lang']);
                  if (!empty($attributes_text)) echo "<div class='attributs'><div style='font-weight: bold; margin-top: 5px;'>" ._("Attributs") ."</div>$attributes_text</div>";
                  if (!empty($options_text)) echo "<div class='options'><div style='font-weight: bold; margin-top: 5px;'>" ._("Options") ."</div>$options_text</div>";
          echo "</td>
                <td class='article_panier' style='text-align: center;'>
                  <a href='" .URL_BASE ."shop_modifier_panier.php?a=3&amp;v=$a->id_article&amp;w=$a->id&amp;n=1&amp;miki_deal=" .(($a->miki_deal) ? 1 : 0) ."' title='Enlever un exemplaire de cet article'><img src='" .URL_BASE ."pictures/button_minus.gif' style='border:0;vertical-align:middle' alt='Ajouter' /></a>
                  <input type='text' value='$a->nb' style='width: 30px; text-align: center;' maxlength='12' onblur='javascript:modifier_quantite($a->id_article, $a->id, this.value, " .(($a->miki_deal) ? 1 : 0) .");' />
                  <a href='" .URL_BASE ."shop_modifier_panier.php?a=2&amp;v=$a->id_article&amp;w=$a->id&amp;n=1&amp;miki_deal=" .(($a->miki_deal) ? 1 : 0) ."' title='Ajouter un exemplaire de cet article'><img src='" .URL_BASE ."pictures/button_plus.gif' style='border:0;vertical-align:middle' alt='Enlever' /></a>
                  <br />
                  <a href='" .URL_BASE ."shop_modifier_panier.php?a=5&amp;w=$a->id&amp;miki_deal=" .(($a->miki_deal) ? 1 : 0) ."' title='Supprimer cet article de votre panier' class='delete' style='font-size: 0.8em;'>Supprimer</a>
                </td>
                <td class='article_panier' style='text-align: right;'>" .number_format($price,2,'.',"'") ." CHF</td>
                <td class='article_panier' style='text-align: right;'>" .number_format($a->nb * $price,2,'.',"'") ." CHF</td>
              </tr>";
      }
      
      // récupération des données du service client
      $service_client_tel = Miki_configuration::get('miki_shop_service_client_tel');
      $service_client_email = Miki_configuration::get('miki_shop_service_client_email');
      $service_client_horaire = Miki_configuration::get('miki_shop_service_client_horaire');
      
      // récupert les taxes
      $taxes = Miki_shop::get_taxes($person->country);
      if (!is_array($taxes))
        $taxes = array();
        
      echo "<tr>
              <td>
                <div class='service_client'>
                  <div style='border-bottom: solid 1px #000000; padding-bottom: 10px; margin-bottom: 10px;'>
                    <span style='font-weight: bold;'>" ._("Besoin d'aide pour valider votre commande ?") ."</span><br />"
                    ._("Contactez-nous : ") ."
                  </div>
                  <div style='font-size: 0.9em;'>";
                    if ($service_client_tel !== false && !empty($service_client_tel)){
                      echo "<span style='font-weight: bold; '>" ._("Par téléphone") ." : </span>" .$service_client_tel ."<br />";
                    }
                    
                    if ($service_client_email !== false && !empty($service_client_email)){
                      echo "<span style='font-weight: bold; '>" ._("Par e-mail") ." : </span><a href='mailto:$service_client_email' title='" ._("Contactez notre support client") ."'>" ._("ici") ."</a><br />";
                    }
                    
                    if ($service_client_horaire !== false && !empty($service_client_horaire)){
                      echo "<span style='font-size: 0.9em; '>" .$service_client_horaire ."</span>";
                    }
                    
            echo "</div>
                </div>
              </td>";
      
        echo "<td colspan='2' style='text-align: right; padding: 5px'>
                " ._("Sous-total") ." : <br /><br />";
                
                if ($discount_code)
                  echo _("Rabais 'code de promotion'") ." : <br /><br />";
                
        echo    "$shipping_text : <br /><br />"
                ._("Montant total HT") ." : <br /><br />";
                
                // affiche les titres des taxes
                foreach($taxes as $tax_name=>$tax){
                  if ($tax[$person->country] > 0)
                    echo $tax_name ." (" .$tax[$person->country] ."%) : <br /><br />";
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
                    // applique le rabais
                    $total -= $discount;
                  
                    // s'il y a plus de rabais que de frais, on réduit le rabais et on affiche un total de 0 CHF
                    if ($total < 0)
                      $total = 0;
                    
                    echo "- " .number_format($discount,2,'.',"'") ." CHF<br /><br />";
                  }
                }
                
          echo  number_format($shipping,2,'.',"'") ." CHF<br /><br />"
                .number_format($total + $shipping,2,'.',"'") ." CHF<br /><br />";
                
                // affiche les montants des taxes
                $total_tax = 0;
                foreach($taxes as $tax_name=>$tax){
                  $tax_price = round(($total + $shipping) * ($tax[$person->country] / 100), 2);
                  $total_tax += $tax[$person->country];
                  if ($tax_price > 0)
                    echo number_format($tax_price,2,'.',"'") ." CHF<br /><br />";
                }
                
            echo number_format(($total + $shipping) * (1 + $total_tax / 100),2,'.',"'") ." CHF
              </td>
            </tr>";
  ?>
  
  </table>
  
  <div class="code_promotion">
    Code de promotion : 
    &nbsp;<input type="text" id="code_promo" style="width:150px" /> 
    &nbsp;<a href="javascript:test_code_promo();" title="<?php echo _("Valider le code de promotion"); ?>"><?php echo _("Valider"); ?></a>
  </div>
  
  <?php
    // récupert la page des conditions générales de vente
    $page_conditions_vente = Miki_configuration::get('miki_shop_page_conditions_generales_de_vente');
    
    // si elle a été configurée, on affiche le bloc des conditions générales de vente
    if ($page_conditions_vente !== false && !empty($page_conditions_vente)  && $page_conditions_vente >= 0){
  ?>
      <div class="conditions_generales">
        <table>
          <tr>
            <td style="padding-right: 5px;"><input type="checkbox" name="conditions_generales_vente" id="conditions_generales_vente" /></td>
            <td>
              <label for="conditions_generales_vente">
              <?php
                $page_conditions_vente = new Miki_page($page_conditions_vente);
                echo sprintf(_("En cochant cette case, j'accepte et je reconnais avoir pris connaissance des %s de %s"),
                        "<a href='[miki_page='$page_conditions_vente->name']' title='Conditions Générales de Vente' target='_blank'>Conditions Générales de Vente</a>",
                        SITENAME); 
              ?>
              </label>
            </td>
          </tr>
        </table>
      </div>
    <?php
    }
  }
  ?>
  
  <table class='zone_boutons'>
    <tr>
      <td style="text-align: left; vertical-align: middle;">
        <input type='button' value="<?php echo _("Continuer mes achats"); ?>" class='button2' onclick="document.location='[miki_page='shop_articles_list']'" />
      </td>
      <td style="text-align: right; vertical-align: middle;">
        <?php
        if ($order && sizeof($articles) > 0){
          echo "<input type='button' value='" ._("Confirmer ma commande") ."' class='button_big1 next_step' />";
        }
        ?>
      </td>
    </tr>
  </table>