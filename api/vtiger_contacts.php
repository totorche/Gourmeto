<?php

//vtiger Object name which need be described or whose information is requested.
$moduleName = 'Contacts';

//use sessionId created at the time of login.
$params = "sessionName=$sessionId&operation=describe&elementType=$moduleName";
$httpc->setUrl("$endpointUrl?$params");
$httpc->setMethod(HTTP_Request2::METHOD_GET);

try{
  $response = $httpc->send();
  if (200 == $response->getStatus()) {
    $response = $response->getBody();
    //var_dump($response); echo "<br /><br />";
  }
  else{
    echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .$response->getReasonPhrase();
    exit();
  }
} catch (HTTP_Request2_Exception $e) {
  echo 'Error: ' . $e->getMessage();
  exit();
}


//decode the json encode response from the server.
$jsonResponse = json_decode($response, true);
//operation was successful get the token from the reponse.
if($jsonResponse['success']===false)
    die('describe object failed:'.$jsonResponse['error']['errorMsg']);
//get describe result object.
$description = $jsonResponse['result'];
var_dump($description);
?>