<?php

require_once("../include/headers.php");

ini_set("include_path", ".:/usr/local/lib/php:../class:../include:../scripts:../include/mail:../");

// si la transaction n'est pas un paiement de la part d'un client on ne fait rien
if ($_POST['payment_status'] == 'Refunded' || 
    $_POST['payment_status'] == 'Denied' || 
    $_POST['payment_status'] == 'Reversed'){
  
  exit();    
}

$paypal_account = Miki_configuration::get("payement_paypal_account");
if ($paypal_account === false){
  exit();
}

error_reporting(E_ALL ^ E_NOTICE);
$header = "";
$payed = true;
$error = "";
$order = "";

// récupert la commande
$order = new Miki_order();
$order->load_from_no($_POST['invoice']);

// Read the post from PayPal and add 'cmd'
$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')){ 
  $get_magic_quotes_exits = true;
}

// Handle escape characters, which depends on setting of magic quotes
foreach ($_POST as $key => $value){ 
  if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1){ 
    $value = urlencode(stripslashes($value));
  }else{
    $value = urlencode($value);
  }
  $req .= "&$key=" .utf8_decode($value);
}

// Post back to PayPal to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

// se connecte à Paypal (Sandbox ou version en production selon la configuration)
if (Miki_configuration::get("payement_paypal_sandbox") == 1){
  $fp = fsockopen ('www.sandbox.paypal.com', 80, $errno, $errstr, 30);
}
else{
  $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
}

// Process validation from PayPal
if (!$fp) {
  // erreur de connexion
  $payed = false;
}else {
  // connexion OK
  fputs ($fp, $header . $req);
  
  while (!feof($fp)) {
    $res = fgets ($fp, 1024);
    if (strcmp ($res, "VERIFIED") == 0) {
      $val = array();
      // TODO:
      // Check the payment_status is Completed
      // Check that txn_id has not been previously processed
      // Check that receiver_email is your Primary PayPal email
      // Check that payment_amount/payment_currency are correct
      // Process payment
      // If 'VERIFIED', send an email of IPN variables and values to the
      // specified email address
      
      try{
        // test que le status de la commande soit "Completed"
        if ($_POST['payment_status'] !== "Completed"){
          $payed = false;
          $error .= "- Le status du paiement est : " .$_POST['payment_status'] ."<br />";
          
          /*if ($_POST['payment_status'] == 'Pending')
            $error .= "  Cause : " .$_POST['pending_reason'] ."<br /><br />";*/
           
        }
          
        // teste que l'id de transaction Paypal n'existe pas encore dans la base de donnée
        if ($order->paypal_transaction_exists($_POST['txn_id'])){
          // si l'id existe déjà c'est un duplicata de commande, on annule donc tout
          exit();
        }
        else{
          // enregistre l'id de transaction Paypal
          $order->add_paypal_transaction($_POST['txn_id']);
        }
        
        // test que le montant de la commande soit correct
        if ($_POST['mc_gross'] != $order->price_total){
          $payed = false;
          $error .= "- Le montant payé ne correspond pas à celui de la commande<br />";
        }
        
        // teste que la personne qui reçoit l'argent soit la bonne
        if ($_POST['receiver_email'] !== $paypal_account){
          $payed = false;
          $error .= "- Le vendeur n'est pas le bon : " .$_POST['receiver_email'] ."<br />";
        }
        
        /*
        // Test si l'adresse e-mail donnée par l'acheteur correspond à celle de son compte 
        // On ne fait pas ce test ici car il peut donner une adresse différente lors du paiement
        
        $person = new Miki_person();
        $person->load_from_email($_POST['payer_email']);
        
        if ($order->id_person !== $person->id)
          $texte .= "L'adresse e-mail du client ne correspond pas !\n\n";*/
          
      }catch(Exception $e){
        $payed = false;
        $error .= "- " .$e->getMessage() ."<br />";
      }
    }
    elseif (strcmp ($res, "INVALID") == 0){
      $payed = false;
      $error .= "- Le contrôle IPN de Paypal est invalide<br />";
    }
  }
}


// envoi du mail de confirmation au client et passe la commande au status "payé" (state = 2) si commande payée ou "non-payé" (state = 1)
$order->set_completed(true, $payed);
$person = new Miki_person($order->id_person);

// si un problème est survenu lors du paiement on en informe le vendeur
if (!$payed){
  $sitename = Miki_configuration::get('sitename');
  $email_answer = Miki_configuration::get('email_answer');
  
  $no_facture = $_POST['invoice'];
  $no_transaction = $_POST['txn_id'];
  
  // création du mail
  $mail = new miki_email('paypal_verification', 'fr');
  
  $mail->From     = $email_answer;
  $mail->FromName = $sitename;
  
  // prépare les variables nécessaires à la création de l'e-mail
  $vars_array['no_facture'] = $no_facture;
  $vars_array['no_transaction'] = $no_transaction;
  $vars_array['error'] = $error;
  $vars_array['sitename'] = $sitename;
  
  // initialise le contenu de l'e-mail
  $mail->init($vars_array);
      
  $mail->AddAddress("herve@fbw-one.com", $sitename);
  if(!$mail->Send())
    throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
}

fclose ($fp);

?>