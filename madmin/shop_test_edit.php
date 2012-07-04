<?php
  require_once("include/headers.php");
  
  if (!test_right(47)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
    
  // test que les informations du shop soient bien présentes
  if (!isset($_POST['name'])){
    $referer = "index.php?pid=142";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = _("Les informations du shop sont manquantes");
    
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  $shops = Miki_shop::get_all_shops();
  $shop = array_shift($shops);
  
  $shop->name = stripslashes($_POST['name']);
  $shop->description = stripslashes($_POST['description']);
  $shop->id_person = 'null';
  $shop->update();
  
  // affecte le transporteur par défaut
  if (isset($_POST['default_transporter']) && is_numeric($_POST['default_transporter'])){
    Miki_configuration::add('default_shop_transporter', $_POST['default_transporter']);
  }
  
  // récupert tous les transporteurs activés
  $tranporters = Miki_shop_transporter::search("", true);
  
  // et affiche la gestion des frais de port pour chaque transporteur
  foreach($tranporters as $transporter){
    
    if (isset($_POST["shipping_method_transporter_$transporter->id"])){
      
      // récupert le type de gestion des frais de port choisi pour le transporteur en cours
      $shipping_method = $_POST["shipping_method_transporter_$transporter->id"];
      
      // affecte le type de gestion des frais de port choisi au transporteur
      $transporter->set_shipping_method($shop->id, $shipping_method);
      
      // Frais en fonction du poids ou du montant total de la commande
      if ($shipping_method == 1 || $shipping_method == 2){
        
        // test qu'une zone d'envoi au moins soit complétée
        for($x=0; $x < sizeof($_POST["country_$transporter->id"]); $x++){
          $complete = true;
          
          if ($_POST["shipping_table_$transporter->id"][$x] == ""){
            $complete = false;
          }
          
          if (!$complete){
            // stock le shop dans la session
            $_SESSION['shop'] = $shop;
            
            // ajoute le message à la session
            $_SESSION['success'] = 0;
            $_SESSION['msg'] = _("Les informations concernant les frais de port sont manquantes");
            
            // puis redirige vers la page précédente
            echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
            exit();
          }
        }
      
        Miki_shop_shipping_configuration::delete_from_shop($shop->id, $transporter->id);
        
        // créé les configurations
        $config = new Miki_shop_shipping_configuration();
        $config->id_shop_shipping = 0;
        $config->id_shop = $shop->id;
        $config->id_shop_transporter = $transporter->id;
        $config->title = "shipping_method";
        $config->value = $_POST["shipping_method_name_$transporter->id"];
        $config->country = "";
        $config->save();
        unset($config);
        
        for($x=0; $x < sizeof($_POST["country_$transporter->id"]); $x++){
          if ($_POST["shipping_table_$transporter->id"][$x] !== ""){
            $config = new Miki_shop_shipping_configuration();
            $config->id_shop_shipping = 0;
            $config->id_shop = $shop->id;
            $config->id_shop_transporter = $transporter->id;
            $config->title = "shipping_table";
            $config->value = $_POST["shipping_table_$transporter->id"][$x];
            $config->country = $_POST["country_$transporter->id"][$x];
            $config->save();
            unset($config);
          }
        }
      }
      // Frais fixes en fonction du pays
      elseif ($shipping_method == 3){
        // test qu'une zone d'envoi au moins soit complétée
        for($x=0; $x < sizeof($_POST["country_$transporter->id"]); $x++){
          $complete = false;
          
          if ($_POST["shipping_table_$transporter->id"][$x] != ""){
            $complete = true;
          }
          
          if (!$complete){
            // stock le shop dans la session
            $_SESSION['shop'] = $shop;
            
            // ajoute le message à la session
            $_SESSION['success'] = 0;
            $_SESSION['msg'] = _("Vous devez renseigner au moins une zone d'envoi");
            
            // puis redirige vers la page précédente
            echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
            exit();
          }
        }
      
        Miki_shop_shipping_configuration::delete_from_shop($shop->id, $transporter->id);
        
        // créé les configurations
        for($x=0; $x < sizeof($_POST["country_$transporter->id"]); $x++){
          if ($_POST["shipping_table_$transporter->id"][$x] !== ""){
            $config = new Miki_shop_shipping_configuration();
            $config->id_shop_shipping = 1;
            $config->id_shop = $shop->id;
            $config->id_shop_transporter = $transporter->id;
            $config->title = "shipping_table";
            $config->value = $_POST["shipping_table_$transporter->id"][$x];
            $config->country = $_POST["country_$transporter->id"][$x];
            $config->save();
            unset($config);
          }
        }
      }
      // Pas de frais d'envoi
      elseif ($shipping_method == 4){
        // supprime uniquement les configurations
        Miki_shop_shipping_configuration::delete_from_shop($shop->id, $transporter->id);
      }
      
      
      // Divers
      Miki_configuration::add('miki_shop_gestion_stock', $_POST['gestion_stock'] == 1 ? 1 : 0);
      Miki_configuration::add('miki_shop_page_conditions_generales_de_vente', strip_tags($_POST['page_conditions_vente']));
      Miki_configuration::add('miki_shop_service_client_tel', strip_tags($_POST['service_client_tel']));
      Miki_configuration::add('miki_shop_service_client_horaire', strip_tags($_POST['service_client_horaire']));
      Miki_configuration::add('miki_shop_service_client_email', strip_tags($_POST['service_client_email']));
    }
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("Votre shop a été modifié avec succès");
  // puis redirige vers la page précédente
  echo "<script type='text/javascript'>document.location='index.php?pid=141';</script>";
?>