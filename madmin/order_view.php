<head>
  
  <style type="text/css">
    .titre_panier{
      background-color: #336699;
      border: solid 1px #336699;
      color: #ffffff;
      font-weight: bold;
      padding: 10px;
      text-align: center;
    }
    
    .article_panier{
      border: solid 1px #336699;
      padding: 5px;
    }
    
    .attributs, .options{
      font-size: 0.8em;
    }
  </style>

  <?php
    if (!test_right(48))
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    
    // si pas d'id de contact spécifié, on retourne à la page précédente
    if (!isset($_GET['id']) || !is_numeric($_GET['id']))
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    else
      $id = $_GET['id'];
    
    require_once("../include/payement_type.php");
    
    try{
      $order = new Miki_order($id);
      $person = new Miki_person($order->id_person);
      //$account = new Miki_account();
      //$account->load_from_person($person->id);
      
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
</head>

<div id="arianne">
  <a href="#"><?php echo _("Shops"); ?></a> > <a href="index.php?pid=147"><?php echo _("Liste commandes"); ?></a> > Détails d'une commande
</div>

<div id="first_contener">
  <h1><?php echo _("Détails de la commande"); ?></h1>  
  
    <table cellspacing="5px" cellpadding="0" style="border:0">
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td style="font-weight:bold;padding-right:5px;width:250px">Commande n° :</td>
        <td style="width:750px;text-align:left"><?php echo $order->no_order; ?></td>
      </tr>
      <tr>
        <td style="font-weight:bold;padding-right:5px">Membre :</td>
        <td><a href="index.php?pid=102&id=<?php echo $person->id; ?>" title="Voir les détails du membre"><?php echo "$person->firstname $person->lastname"; ?></a></td>
      </tr>
      <tr>
        <td style="font-weight:bold;padding-right:5px">Etat de la commande :</td>
        <td>
          <?php 
            if ($order->state == 0)
              echo "Non terminée";
            elseif ($order->state == 1)
              echo "Terminée";
            elseif ($order->state == 2)
              echo "Payée";
          ?>
        </td>
      </tr>
      <tr>
        <td style="font-weight:bold;padding-right:5px">Date de la commande :</td>
        <td><?php echo $date_completed; ?></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td style="font-weight:bold;padding-right:5px">Sous-total :</td>
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
        <td style="font-weight:bold;padding-right:5px">Frais de livraison :</td>
        <td><?php echo number_format($order->shipping_price,2,'.',"'") ." CHF"; ?></td>
      </tr>
      <tr>
        <td style="font-weight:bold;padding-right:5px">Montant total HT :</td>
        <td><?php echo number_format($total_ht,2,'.',"'") ." CHF"; ?></td>
      </tr>
      <?php
        foreach($order->taxes as $tax_name => $tax_value){
          if ($tax_value[$order->shipping_country] > 0){
            $tax = round($total_ht * $tax_value / 100, 2);
            echo "<tr>
                    <td style='font-weight:bold;padding-right:5px'>$tax_name (" .$tax_value ."%) :</td>
                    <td>" .number_format($tax ,2,'.',"'") ." CHF" ."</td>
                  </tr>";
          }
        }
      ?>
      <tr>
        <td style="font-weight:bold;padding-right:5px">Montant total TTC :</td>
        <td><?php echo number_format($order->price_total,2,'.',"'") ." CHF"; ?></td>
      </tr>
      <tr>
        <td style="font-weight:bold;padding-right:5px">Moyen de paiement :</td>
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
        <td style="font-weight:bold;padding-right:5px;vertical-align:top">Articles :</td>
        <td> 
          <table id="main_table">
            <tr class="headers">
              <td style="width: 50px;">Quantité</td>
              <td style="width: 300px;">Article</td>
              <td style="width: 100px;">Prix unitaire</td>
              <td style="width: 100px;">Prix total</td>
            <tr>
            <?php
              $n = 0;
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
                
                // récupert le prix de l'article en prenant en compte les options éventuelles mais pas les promotions
                $price = $a->get_price();
                
                // détecte la class
                if ($n === 1)
                  $class = "line1";
                else
                  $class = "line2";
                
                $n = ($n+1)%2;
                
                echo "<tr id='$a->id' class='pages' onmouseover=\"colorLine('$order->id');\" onmouseout=\"uncolorLine('$order->id');\">
                        <td class='$class' style='text-align: center;'>" .$a->nb ."</td>
                        <td class='$class' style='text-align: left;'>
                          <a href='index.php?pid=146&id=$article->id' title=\"Voir l'article\">" .$article->get_name('fr') ."</a>";
                          if (!empty($attributes_text)) echo "<div class='attributs'><div style='font-weight: bold; margin-top: 5px;'>" ._("Attributs") ."</div>$attributes_text</div>";
                          if (!empty($options_text)) echo "<div class='options'><div style='font-weight: bold; margin-top: 5px;'>" ._("Options") ."</div>$options_text</div>";
                  echo "</td>
                        <td class='$class' style='text-align: left;'>" .number_format($price,2,'.',"'") ." CHF</td>
                        <td class='$class' style='text-align: left;'>" .number_format($a->nb * $price,2,'.',"'") ." CHF</td>
                      </tr>";
              }
            ?>
          </table>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">
          <input type="button" value="Retour" onclick="document.location='index.php?pid=147';" />
        </td>
      </tr>
    </table>
    
</div>

<?php
  // stock l'url actuel pour un retour après opérations
  $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
?>