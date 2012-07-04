<?php

require_once 'HTTP/Request2.php';

// chemin vers le Webservice de vTiger
$endpointUrl = "http://crm.fbw-one.com/webservice.php";

// Nom d'utilisateur a utiliser
$userName="herve";

// L'Access Key de l'utilisateur
$userAccessKey = 'lMQWl22Jrxc0Jht';

// récupert le Challenge Token
$httpc = new HTTP_Request2("$endpointUrl?operation=getchallenge&username=$userName");

try{
  $response = $httpc->send();
  if ($response->getStatus == 200)){
    $response = $response->getBody();
  }
  else{
    echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .$response->getReasonPhrase();
    exit();
  }
}catch (HTTP_Request2_Exception $e){
  echo 'Error: ' . $e->getMessage();
  exit();
}

// décode la réponse
$jsonResponse = json_decode($response, true);

// vérifie si l'opération a été exécutée avec succès ou pas
if ($jsonResponse['success'] === false) 
  die('getchallenge failed:'.$jsonResponse['error']['errorMsg']);

$challengeToken = $jsonResponse['result']['token'];

// Créé la chaîne MD5 qui est une concaténation entre l'Access Key et le Challenge Token
$generatedKey = md5($challengeToken.$userAccessKey);

// login (via méthode POST).
$httpc->setUrl($endpointUrl);
$httpc->addPostParameter(array('operation'  => 'login', 
                               'username'   => $userName,
                               'accessKey'  => $generatedKey));
$httpc->setMethod(HTTP_Request2::METHOD_POST);

try{
  $response = $httpc->send();
  if (200 == $response->getStatus()){
    $response = $response->getBody();
  }
  else{
    echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .$response->getReasonPhrase();
    exit();
  }
} catch (HTTP_Request2_Exception $e){
  echo 'Error: ' . $e->getMessage();
  exit();
}

// décode la réponse
$jsonResponse = json_decode($response, true);

// vérifie si l'opération a été exécutée avec succès ou pas
if($jsonResponse['success'] === false)
  die('login failed:'.$jsonResponse['error']['errorMsg']);

// récupert les informations reçues
$sessionId = $jsonResponse['result']['sessionName']; 
$userId = $jsonResponse['result']['userId'];


include("vtiger_contacts.php");

?>