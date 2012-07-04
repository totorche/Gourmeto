<?php
  // récupert le type de paiement
  if (isset($_REQUEST['miki_payment_type'])){
    $payment_type = $_REQUEST['miki_payment_type'];
  }
  elseif (isset($_REQUEST['custom'])){
    $payment_type = $_REQUEST['custom'];
  }
  else{
    echo _("Il manque des informations");
    var_dump($_REQUEST);
    exit();
  }
  
  // on inclu les fonctions du type de paiement
  include_once("scripts/payment_" .$payment_type .".php");

  // récupert la fonction de traitement de paiement correspondant au type de paiement utilisé
  $nom_fonction = $payment_type ."_payment_error_user";

  // puis appelle cette fonction
  if (function_exists($nom_fonction))
    $nom_fonction($_REQUEST);
?>