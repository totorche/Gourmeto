<?php

//require_once("include/headers.php");

//ini_set("include_path", ".:..:/usr/local/lib/php:../class:../include:../scripts:../include/mail:../");

// récupert le titulaire du compte Paypal
$paypal_account = Miki_configuration::get("payement_paypal_account");
if ($paypal_account === false || $paypal_account === false){
  exit();
}

// vérifie si le formulaire de paiement via Paypal est sécurisé ou non
$paypal_secure = Miki_configuration::get("payement_paypal_secure");
if ($paypal_secure == 1){
  // récupert l'Id du certificat Paypal
  $cert_id = Miki_configuration::get("payement_paypal_idcert");
  if ($cert_id === false || $cert_id == ""){
    exit();
  }
}
else
  $cert_id = false;

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

/*$site_url = "http://www.euredac.com";
$return_ok = "http://www.euredac.com";
$return_error = "http://www.euredac.com";*/


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


function paypal_button($event, $person) // choississez les variables à transmettre à la fonction (prix, nom de l'objet, etc)
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
    
  $title = $event->title[$_SESSION['lang']];
  
  $form = array(
  'cmd' => '_cart', // panier paypal personnalisé
  'upload' => '1',
  'business' => $paypal_account, // adresse de votre compte paypal
  'no_shipping' => '1',
  'shopping_url' => $site_url,
  'return' => $return_ok, // adresse de retour après le paiement
  'notify_url' => "$site_url/paypal/paypal_verification.php", // adresse de notification ipn
  'cancel_return' => $return_error, // adresse de retour en cas d'annulation du paiement
  'rm' => '2',
  'no_note' => '1',
  'currency_code' => 'CHF',
  'lc' => 'FR',
  'charset' => 'utf-8',
  'first_name' => $person->firstname,
  'last_name' => $person->lastname,
  'address1' => $person->address,
  'zip' => $person->npa,
  'city' => $person->city,
  'country' => $country,
  'email' => $person->email1,
  //'invoice' => $order->no_order,
  /*'image_url' => "$site_url/pictures/logo.png",
  'cpp_header_image' => '',
  'cpp_headerback_color' => 'ffffff',
  'cpp_headerborder_color' => 'ffffff',
  'cbt' => "Retourner sur $sitename",*/
  //'tax_cart' => '0.00',
  'item_name_1' => $title,
  'amount_1' => number_format($event->entrance_price,2,'.',"'"),
  'quantity_1' => '1',
  'tax_1' => '2.00',
  'shipping_1' => '0.00' //frais de port
  );
  
  // on ajoute l'id du certificat si disponible
  if ($cert_id)
    $form['cert_id'] = $cert_id;
  
  return $form;
}


function crypted_button($event, $person){
  $datas = paypal_button($event, $person);
  return paypal_encrypt($datas);
}
?>