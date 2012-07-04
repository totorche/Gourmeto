<?php

require_once("include/headers.php");

//ini_set("include_path", ".:..:/usr/local/lib/php:../class:../include:../scripts:../include/mail:../");

// récupert le titulaire du compte Paypal
$paypal_account = Miki_configuration::get("payement_paypal_account");
if ($paypal_account === false || $paypal_account === false){
  exit();
}

// récupert l'Id du certificat Paypal
$cert_id = Miki_configuration::get("payement_paypal_idcert");
if ($cert_id === false || $cert_id == ""){
  exit();
}

// récupert l'url de retour après paiement
$return_ok = Miki_configuration::get("payement_paypal_url_return");
if ($return_ok === false || $return_ok == ""){
  exit();
}


// récupert l'url de retour après erreur
$return_error = Miki_configuration::get("payement_paypal_url_return_error");
if ($return_error === false || $return_error == ""){
  exit();
}

// récupert l'url du site Internet
$site_url = Miki_configuration::get("site_url");
if ($site_url === false || $site_url == ""){
  exit();
}

// récupert le nom du site Internet
$sitename = Miki_configuration::get("sitename");
if ($sitename === false || $sitename == ""){
  $sitename = "le site Internet";
}

// récupert le type de compte (sandobx ou actif)
$sandbox = Miki_configuration::get("payement_paypal_sandbox") == 1;

try{
  $return_ok = new Miki_page($return_ok);
  $return_ok = $return_ok->get_url_simple($_SESSION['lang']);
}
catch(Exception $e){
  exit();
}

try{
  $return_error = new Miki_page($return_error);
  $return_error = $return_error->get_url_simple($_SESSION['lang']);
}
catch(Exception $e){
  exit();
}


//Sample PayPal Button Encryption: Copyright 2006,2007 StellarWebSolutions.com
//Not for resale  - license agreement at
//http://www.stellarwebsolutions.com/en/eula.php
//Updated: 2007 04 04
 
#Set home directory for OpenSSL
putenv("HOME=~");
 
# private key file to use
$MY_KEY_FILE = "paypal/my-prvkey.pem";
 
# public certificate file to use
$MY_CERT_FILE = "paypal/my-pubcert.pem";
 
# Paypal's public certificate
if ($sandbox)
  $PAYPAL_CERT_FILE = "paypal/paypal_cert_sandbox.pem";
else
  $PAYPAL_CERT_FILE = "paypal/paypal_cert.pem";
  
# path to the openssl binary
$OPENSSL = "/usr/bin/openssl";
//$OPENSSL = "C:\wamp\bin\apache\Apache2.2.11\bin";
 
function paypal_encrypt($hash)
{
  //Sample PayPal Button Encryption: Copyright 2006,2007 StellarWebSolutions.com
  //Not for resale – license agreement at
  //http://www.stellarwebsolutions.com/en/eula.php
   
  global $MY_KEY_FILE;
  global $MY_CERT_FILE;
  global $PAYPAL_CERT_FILE;
  global $OPENSSL;
   
  if (!file_exists($MY_KEY_FILE)) {
    echo "ERROR: MY_KEY_FILE $MY_KEY_FILE not found\n";
  }
  if (!file_exists($MY_CERT_FILE)) {
    echo "ERROR: MY_CERT_FILE $MY_CERT_FILE not found\n";
  }
  if (!file_exists($PAYPAL_CERT_FILE)) {
    echo "ERROR: PAYPAL_CERT_FILE $PAYPAL_CERT_FILE not found\n";
  }
  if (!file_exists($OPENSSL)) {
    echo "ERROR: OPENSSL $OPENSSL not found\n";
  }
   
  //Assign Build Notation for PayPal Support
  $hash['bn']= 'StellarWebSolutions.PHP_EWP';
   
  $openssl_cmd = "$OPENSSL smime -sign -signer $MY_CERT_FILE -inkey $MY_KEY_FILE " .
  "-outform der -nodetach -binary | $OPENSSL smime -encrypt " .
  "-des3 -binary -outform pem $PAYPAL_CERT_FILE";
   
  $descriptors = array(
    0 => array("pipe", "r"),
    1 => array("pipe", "w"),
  );
   
  $process = proc_open($openssl_cmd, $descriptors, $pipes);
   
  if (is_resource($process)) {
    foreach ($hash as $key => $value) {
      if ($value != "") {
        //echo "Adding to blob: $key=$value\n";
        fwrite($pipes[0], "$key=$value\n");
      }
    }
    fflush($pipes[0]);
    fclose($pipes[0]);
   
    $output = "";
    while (!feof($pipes[1])) {
      $output .= fgets($pipes[1]);
    }
    //echo $output;
    fclose($pipes[1]);
    $return_value = proc_close($process);
    return $output;
  }
  return "ERROR";
};


function paypal_button($order, $person) // choississez les variables à transmettre à la fonction (prix, nom de l'objet, etc)
{
  global $paypal_account;
  global $cert_id;
  global $return_ok;
  global $return_error;
  global $site_url;
  global $sitename;
  global $sandbox;
  
  if ($person->country == 'Suisse')
    $country = "CH";
  elseif ($person->country == 'France')
    $country = "FR";
  elseif ($person->country == 'Allemagne')
    $country = "DE";
  elseif ($person->country == 'Canada')
    $country = "CA";
  else
    $country = "";
    
  // calcule le total des taxes
  $taxes = 0;
  foreach($order->taxes as $tax_name => $tax_value){
    $taxes += $tax_value;
  }
  
  $form = array(
  'cmd' => '_cart', // panier paypal personnalisé
  'upload' => '1',
  'business' => $paypal_account, // adresse de votre compte paypal
  'cert_id' => $cert_id, // ici mettre le numéro de certificat obtenu dans l'interface d'administration paypal
  'shipping_1' => number_format($order->shipping_price,2,'.',"'"), //frais de port
  'tax_cart' => '0.00',
  'no_shipping' => '1',
  'shopping_url' => $site_url,
  'return' => $return_ok, // adresse de retour après le paiement
  'notify_url' => "$site_url/paypal/paypal_verification.php", // adresse de notification ipn
  'cancel_return' => $return_error, // adresse de retour en cas d'annulation du paiement
  'rm' => '2',
  'no_note' => '1',
  'currency_code' => 'CHF',
  'tax' => number_format($taxes,2,'.',""),
  'lc' => 'FR',
  'charset' => 'utf-8',
  'first_name' => $person->firstname,
  'last_name' => $person->lastname,
  'address1' => $person->address,
  'zip' => $person->npa,
  'city' => $person->city,
  'country' => $country,
  'email' => $person->email1,
  'invoice' => $order->no_order,
  'image_url' => "$site_url/pictures/logo.png",
  'cpp_header_image' => '',
  'cpp_headerback_color' => 'ffffff',
  'cpp_headerborder_color' => 'ffffff',
  'cbt' => "Retourner sur $sitename"
  );
  
  // ajoute tous les articles de la commande au formulaire de paiement
  $articles = $order->get_all_articles();
  $x = 1;
  foreach($articles as $a){
    $article = new Miki_shop_article($a->id_article);
    $form["item_name_$x"] = $article->get_name(Miki_language::get_main_code()); // nom
    $form["amount_$x"] = number_format($a->get_price(),2,'.',"");// prix unitaire
    $form["quantity_$x"] = $a->nb; // quantité 
    $x++;
  }
  
  return $form;
}


function crypted_button($order, $person){
  $datas = paypal_button($order, $person);
  return paypal_encrypt($datas);
}
?>