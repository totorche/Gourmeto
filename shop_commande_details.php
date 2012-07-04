<?php
  // vérifie si la personne est connectée
  if (!$miki_person)
    miki_redirect(Miki_configuration::get('site_url'));
  
  // si pas d'id de contact spécifié, on retourne à la page précédente
  if (!isset($_REQUEST['oid']) || !is_numeric($_REQUEST['oid']))
    miki_redirect($_SESSION['url_back']);
    
  require_once("include/payement_type.php");
  
  try{
    // récupert la commande
    $order = new Miki_order($_REQUEST['oid']);
    
    // vérifie que la commande appartienne bien à la personne connectée
    if ($miki_person->id != $order->id_person){
      miki_redirect(Miki_configuration::get('site_url'));
    }
    
    // récupert tous les articles de la commande en cours
    $articles = $order->get_all_articles();
    
    $date_created = explode(" ", $order->date_created);
    $date_created = explode("-", $date_created[0]);
    $jour = $date_created[2];
    $mois = $date_created[1];
    $annee = $date_created[0];
    $date_created = "$jour/$mois/$annee";
    
    // pour le calcul du total HT
    $total_ht = $order->subtotal + $order->shipping_price - $order->discount;
    
    if ($order->date_completed == "" || $order->date_completed == "0000-00-00 00:00:00")
      $date_completed = "Commande non terminée";
    else{
      $date_completed = explode(" ", $order->date_completed);
      $date_completed = explode("-", $date_completed[0]);
      $jour = $date_completed[2];
      $mois = $date_completed[1];
      $annee = $date_completed[0];
      $date_completed = "$jour/$mois/$annee";
    }
  }
  catch(Exception $e){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }
?>

<style>
  
  #details_commande td{
    padding: 5px 5px 5px 0;
  }
  
  #details_commande td:first-child{
    padding-right: 30px;
  }
  
  .attributs, .options{
    font-size: 0.9em;
  }
  
  #details_commande table.articles{
    font-size: 0.9em;
  }
  
  #details_commande table.articles th{
    font-weight: bold; 
    padding-right: 10px;
    background: #e6EEEE;
    border: 1px solid #aaaaaa;
    padding: 4px;
  }
  
  #details_commande table.articles td{
    padding: 4px;
    border: 1px solid #aaaaaa;
    color: #000000;
  }
  
  #details_commande table.articles tr:nth-child(odd) td{
    background-color: #F0F0F6;
  }
</style>

<h3><?php echo _("Détails de la commande"); ?></h3>  

<table id="details_commande">
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td><?php echo _("Commande n° :"); ?></td>
    <td><?php echo $order->no_order; ?></td>
  </tr>
  <tr>
    <td><?php echo _("Membre :"); ?></td>
    <td><?php echo "$miki_person->firstname $miki_person->lastname"; ?></td>
  </tr>
  <tr>
    <td><?php echo _("Etat de la commande :"); ?></td>
    <td>
      <?php 
        if ($order->state == 0)
          echo _("Non terminée");
        elseif ($order->state == 1)
          echo _("Terminée");
        elseif ($order->state == 2)
          echo _("Payée");
      ?>
    </td>
  </tr>
  <tr>
    <td><?php echo _("Date de la commande :"); ?></td>
    <td><?php echo $date_completed; ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><?php echo _("Livraison :"); ?></td>
    <td>
      <?php echo "$order->shipping_firstname $order->shipping_lastname<br />
                  $order->shipping_address<br />
                  $order->shipping_npa $order->shipping_city<br />
                  $order->shipping_country" ?>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><?php echo _("Sous-total :"); ?></td>
    <td><?php echo number_format($order->subtotal,2,'.',"'") ." CHF"; ?></td>
  </tr>
  <?php
    if (is_numeric($order->discount) && $order->discount > 0){
      echo "<tr>
              <td style='font-weight:bold;padding-right:5px'>Rabais :</td>
              <td>" .number_format($order->discount,2,'.',"'") ." CHF</td>
            </tr>";
    }
  ?>
  <tr>
    <td><?php echo _("Frais de livraison :"); ?></td>
    <td><?php echo number_format($order->shipping_price,2,'.',"'") ." CHF"; ?></td>
  </tr>
  <tr>
    <td><?php echo _("Montant total HT :"); ?></td>
    <td><?php echo number_format($total_ht,2,'.',"'") ." CHF"; ?></td>
  </tr>
  <?php
    
    // récupert les taxes
    $taxes = Miki_shop::get_taxes($order->shipping_country);
    if (!is_array($taxes))
      $taxes = array();
      
    // affiche les taxes
    foreach($taxes as $tax_name => $tax_value){
      if ($tax_value[$order->shipping_country] > 0){
        $tax = round($total_ht * $tax_value[$order->shipping_country] / 100, 2);
        echo "<tr>
                <td>$tax_name (" .$tax_value[$order->shipping_country] ."%) :</td>
                <td>" .number_format($tax ,2,'.',"'") ." CHF" ."</td>
              </tr>";
      }
    }
  ?>
  <tr>
    <td><?php echo _("Montant total TTC :"); ?></td>
    <td><?php echo number_format($order->price_total,2,'.',"'") ." CHF"; ?></td>
  </tr>
  <tr>
    <td><?php echo _("Moyen de paiement :"); ?></td>
    <td>
      <?php 
        if (!empty($order->payement_type))
          echo $miki_payement_type[$order->payement_type];
        else
          echo _("Non défini");
      ?>
    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td style="vertical-align:top"><?php echo _("Articles :"); ?></td>
    <td>
      <table class="articles" cellspacing="1">
        <tr>
          <th><?php echo _("Quantité"); ?></th>
          <th><?php echo _("Article"); ?></th>
          <th><?php echo _("Attributs"); ?></th>
          <th><?php echo _("Prix unitaire"); ?></th>
          <th><?php echo _("Prix total"); ?></th>
        <tr>
        <?php
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
            
            // récupert le prix de l'article en prenant en compte les options éventuelles mais pas les promotions en cours
            $price = $a->get_price(false);
            
            echo "<tr>
                    <td style='text-align: center; padding-right: 10px;'>" .$a->nb ."</td>
                    <td style='text-align: left; padding-right: 10px;'>" .$article->get_name($_SESSION['lang']);
                      if (!empty($attributes_text)) echo "<div class='attributs'><div style='font-weight: bold; margin-top: 5px;'>" ._("Attributs") ."</div>$attributes_text</div>";
                      if (!empty($options_text)) echo "<div class='options'><div style='font-weight: bold; margin-top: 5px;'>" ._("Options") ."</div>$options_text</div>";
              echo "</td>
                    <td style='text-align: left; padding-right: 10px;'>$attributes_text</td>
                    <td style='text-align: left; padding-right: 10px;'>" .number_format($price,2,'.',"'") ." CHF</td>
                    <td style='text-align: left;'>" .number_format($a->nb * $price,2,'.',"'") ." CHF</td>
                  </tr>";
          }
        ?>
      </table>
    </td>
  </tr>
</table>

<div style="margin-top: 30px;">
  <input type="button" class="button2" onclick="document.location='[miki_page='account_edit']'" value="<?php echo _("Revenir à la page précédente"); ?>" />
</div>