<?php
  require_once ("include/headers.php");
  
  if (!test_right(47)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['tax_country']) || !isset($_POST['tax'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  $tax_countries = $_POST['tax_country'];
  $tax_values = $_POST['tax'];
  
  foreach($tax_countries as $tax_name=>$tax){
    foreach($tax as $index=>$country){
      // ajoute la taxe
      try{
        $value = $tax_values[$tax_name][$index];
        Miki_shop::add_tax($tax_name, $country, $value);
      }
      catch(Exception $e){
        $referer = "index.php?pid=271";
        
        // ajoute le message à la session
        $_SESSION['success'] = 0;
        $_SESSION['msg'] = "Erreur : " .$e->getMessage();
        
        // puis redirige vers la page précédente
        echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
        exit();
      }
    }
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("La taxe a été ajoutée avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=271";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>