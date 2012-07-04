<?php
  require_once ("include/headers.php");
  
  if (!test_right(35)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // vérifie si on a toutes les informations pour la gestion de la société
  if ($_POST['new_company'] == 0 && !is_numeric($_POST['person_company'])){
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = 'Il manque des informations';
    header('location: ' .$_SESSION['url_back']);
  }

  // créé une nouvelle personne
  $person = new Miki_person();
  $account = new Miki_account();
  
  if (isset($_POST['person_type'])){
    
    // créé la personne
    try{
      $person->type       = $_POST['person_type'];
      $person->firstname  = stripslashes($_POST['person_firstname']);
      $person->lastname   = stripslashes($_POST['person_lastname']);
      $person->address    = stripslashes($_POST['person_address']);
      $person->npa        = $_POST['person_npa'];
      $person->city       = stripslashes($_POST['person_city']);
      $person->country    = stripslashes($_POST['person_country']);
      $person->tel1       = $_POST['person_tel1'];
      $person->tel2       = $_POST['person_tel2'];
      $person->email1     = $_POST['person_email1'];
      $person->job        = $_POST['person_job'];
      $person->language   = "fr";
      $person->company_id = "NULL";
      
      $person->save();
    }
    catch(Exception $e){
      $referer = "index.php?pid=107";
    
      // ajoute le message à la session
      $_SESSION['success'] = 0;
      $_SESSION['msg'] = $e->getMessage();
      // puis redirige vers la page précédente
      echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
      exit();
    }
    
    // créé le compte utilisateur
    try{
      $account->state = 1;
      $account->username = $person->email1;
      $account->person_id = $person->id;
      $account->save();
      
      $password = $account->new_password();
    }
    catch(Exception $e){
      $referer = "index.php?pid=107";
    
      // ajoute le message à la session
      $_SESSION['success'] = 0;
      $_SESSION['msg'] = $e->getMessage();
      // puis redirige vers la page précédente
      echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
      exit();
    }
    
    // si on a choisi une société parmi celles existantes
    if ($_POST['new_company'] == 0){
      $person->company_id = $_POST['person_company'];
    }
    // sinon si on créé une nouvelle société
    else{
      try{
        $company = new Miki_company();
        
        $company->name = $_POST['company_name'];
        $company->description = $_POST['company_description'];
        $company->activities = $_POST['company_activities'];
        $company->products_services = $_POST['company_products_services'];
        $company->projects = $_POST['company_projects'];
        $company->address = $_POST['company_address'];
        $company->npa = $_POST['company_npa'];
        $company->city = $_POST['company_city'];
        $company->country = $_POST['company_country'];
        $company->web = $_POST['company_web'];
        
        $company->save();
        
        // met à jour le logo
        if ($_FILES['company_logo']['error'] != 4){
          $company->picture_path = "../pictures/logo_companies/";
          $company->upload_picture($_FILES['company_logo'], $company->name);
        }
      
        $company->update();
        
        $person->company_id = $company->id;
      }
      catch(Exception $e){
        $referer = "index.php?pid=107";
      
        // ajoute le message à la session
        $_SESSION['success'] = 0;
        $_SESSION['msg'] = $e->getMessage();
        // puis redirige vers la page précédente
        echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
        exit();
      }
    }
    
    // met à jour la personne pour son lien avec la société
    $person->update();
    $person->send_inscription($password);
  }
  else{
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = 'Il manque des informations';
    header('location: ' .$_SESSION['url_back']);
  }

  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("Le membre a été créé avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=101";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>