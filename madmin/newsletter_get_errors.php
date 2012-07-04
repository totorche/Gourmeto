<?php 
  
  require_once("include/headers.php");
  
  // récupert les erreurs d'envoi de la newsletter s'il y en a
  if (isset($_SESSION['newsletter_errors']) && is_array($_SESSION['newsletter_errors'])){
    $newsletter_errors = $_SESSION['newsletter_errors'];
    $return = sizeof($newsletter_errors) ."&&" .implode("&&", $newsletter_errors);
    exit($return);
  }
  else{
    exit("0");
  }
?>