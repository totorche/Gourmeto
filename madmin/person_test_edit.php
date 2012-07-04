<?php
  require_once ("include/headers.php");
  
  if (!test_right(35)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['company_name']) || !isset($_POST['id']) || !is_numeric($_POST['id'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }  
  
    
  // récupert les information de la personne, de la société et du compte
  try{
    $person = new Miki_person($_POST['id']);
  }catch(Exception $e){
    $referer = "index.php?pid=103&id=$person->id";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = "Il manque des informations";
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  // créé la personne
  if (isset($_POST['type'])){
    $person->type       = $_POST['type'];
    $person->firstname  = stripslashes($_POST['firstname']);
    $person->lastname   = stripslashes($_POST['lastname']);
    $person->address    = stripslashes($_POST['address']);
    $person->npa        = $_POST['npa'];
    $person->city       = stripslashes($_POST['city']);
    $person->dept       = stripslashes($_POST['dept']);
    $person->country    = stripslashes($_POST['country']);
    $person->tel1       = $_POST['tel1'];
    $person->tel2       = $_POST['tel2'];
    $person->email1     = $_POST['email1'];
    $person->company_id = "NULL";
    
    // traite la date de naissance
    if (strlen($_POST['birthday_month']) == 1)
      $birthday_month = '0' .$_POST['birthday_month'];
    else
      $birthday_month = $_POST['birthday_month'];
      
    if (strlen($_POST['birthday_day']) == 1)
      $birthday_day = '0' .$_POST['birthday_day'];
    else
      $birthday_day = $_POST['birthday_day'];
      
    $person->birthday = $_POST['birthday_year'] .'-' .$birthday_month .'-' .$birthday_day;
  }
  else{
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = 'Il manque des informations';
    header('location: ' .$_SESSION['url_back']);
  }

  // modifie la personne    
  try{
    $person->update();
  }catch(Exception $e){
    $referer = "index.php?pid=103&id=$person->id";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("Le membre a été modifié avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=101";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>