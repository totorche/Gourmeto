<?php
  require_once ("include/headers.php");
  
  if (!test_right(63)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['category']) || !isset($_POST['id']) || !is_numeric($_POST['id'])){
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = "Il manque des informations";
    header('location: ' .$_SESSION['url_back']);
    exit();
  }
  
  // création de l'événement
  try{
    // récupert l'événement à modifier
    $event = new Miki_event($_POST['id']);
    
    $date_start = explode("/", $_POST['date_start']);
    $date_start = $date_start[2] .'-' .$date_start[1] .'-' .$date_start[0] .' ' .$_POST['heure_start'] .':' .$_POST['minutes_start'] .':00';
    $date_stop = explode("/", $_POST['date_stop']);
    $date_stop = $date_stop[2] .'-' .$date_stop[1] .'-' .$date_stop[0] .' ' .$_POST['heure_stop'] .':' .$_POST['minutes_stop'] .':00';
    
    $event->type              = $_POST['type'];
    $event->category          = $_POST['category'];
    $event->tags              = stripslashes($_POST['tags']);
    $event->organizer         = stripslashes($_POST['organizer']);
    $event->place             = stripslashes($_POST['place']);
    $event->address           = stripslashes($_POST['address']);
    $event->npa               = $_POST['npa'];
    $event->city              = stripslashes($_POST['city']);
    $event->region            = stripslashes($_POST['region']);
    $event->country           = stripslashes($_POST['country']);
    $event->date_start        = $date_start;
    $event->date_stop         = $date_stop;
    $event->max_participants  = $_POST['max_participants'];
    $event->accompanist       = $_POST['accompanist'];
    $event->entrance_type     = $_POST['entrance_type'];
    $event->entrance_price    = $_POST['entrance_price'];
    $event->entrance_currency = $_POST['entrance_currency'];
    $event->payement_online   = $_POST['payement_online'];
    $event->entrance_text     = stripslashes($_POST['entrance_text']);
    $event->web               = stripslashes($_POST['web']);
    
    // gère la configuration des inscriptions online    
    $event->online_subscription = isset($_POST['online_subscription']) ? $_POST['online_subscription'] : 0;
    
    if ($event->online_subscription == 1){
      $event->subscription_information = isset($_POST['subscription_information']) ? $_POST['subscription_information'] : 0;;
      
      if ($event->subscription_information == 1){
        $event->subscription_information_email = $_POST['subscription_information_email'];
      }
      else{
        $event->subscription_information_email = '';
      }
    }
    else{
      $event->subscription_information = 0;
      $event->subscription_information_email = '';
    }
    
    // si aucune adresse e-mail de destination n'a été donnée comme destinataire de l''information lors d'une inscription, on n'informe pas.
    if ($event->subscription_information_email == '')
      $event->subscription_information = 0;
    
    foreach($_POST['title'] as $key => $title){
      $event->title[$key] = $title;
    }
    
    foreach($_POST['description'] as $key => $description){
      $event->description[$key] = $description;
    }
    
    $event->save();
         
    try{
      // upload des images
      $nb_pictures = $_POST['nb_pictures'];
      $titres_images = array();
      $event->file_path = "../" .$event->file_path;

      for($x=1; $x<=$nb_pictures; $x++){
        $image = $_FILES["picture$x"];
        $titres_images = $_POST["picture_name$x"];
        $lang_image = $_POST["picture_lang$x"];
        if ($image['error'] != 4){
          if (isset($event->files[$lang_image][$x-1]) && $event->files[$lang_image][$x-1] != ""){
            $event->delete_file($event->files[$lang_image][$x-1]);
          }
          // pour savoir si on doit mettre l'image en première position
          //$first = $x == 1;
	        $first = false;
          $event->upload_file($image, $titres_images, $lang_image, $first);
        }
      }
      //$event->files_name = $titres_images;
      $event->update();
    }catch(Exception $e){
      $referer = "index.php?pid=193&id=$event->id";
      // sauvegarde l'événement dans la session
      $_SESSION['saved_event'] = $event;
      $_SESSION['msg'] = "Une erreur est survenue lors de la modification de l'événement : <br />" .$e->getMessage();
      echo "<script type='text/javascript'>document.location='$referer';</script>";
      exit();
    }
  }
  catch(Exception $e){
    $referer = "index.php?pid=193&id=$event->id";
  
    $_SESSION['saved_event'] = $event;
  
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='$referer';</script>";
    exit();
  }
  
  if (isset($_SESSION['saved_event']))
    unset($_SESSION['saved_event']);
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("L'événement a été modifié avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=191";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>