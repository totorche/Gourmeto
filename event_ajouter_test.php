<?php
  require_once("include/headers.php");
  require_once("set_links.php");
  
  $person = is_connected();
  
  // vérifie que l'utilisateur soit connecté
  if (!$person){
    $site_url = Miki_configuration::get('site_url');
    header("location: $site_url");
  }
  
  $event = new Miki_event();
  $event->category = stripslashes(strip_tags($_POST['category']));
  $event->type = 0;
  $event->title = stripslashes(strip_tags($_POST['title']));
  $event->description = stripslashes(strip_tags($_POST['description']));
  $event->tags = stripslashes(strip_tags($_POST['tags']));
  
  //$event->place = stripslashes(strip_tags($_POST['place']));
  $event->address = stripslashes(strip_tags($_POST['address']));
  $event->npa = stripslashes(strip_tags($_POST['npa']));
  $event->city = stripslashes(strip_tags($_POST['city']));
  //$event->region = stripslashes($_POST['region']);
  $event->country = stripslashes(strip_tags($_POST['country']));
  $event->max_participants = 0;
  
  // pour l'entrée
  //$event->accompanist = $_POST['accompanist'];
  /*$event->entrance_type = $_POST['entrance_type'];
  $event->entrance_price = $_POST['entrance_price'];
  $event->entrance_currency = $_POST['entrance_currency'];*/
  //$event->entrance_text = stripslashes(strip_tags($_POST['entrance_text']));
  //$event->web = stripslashes(strip_tags($_POST['web']));
  $event->organizer = stripslashes(strip_tags($_POST['organizer']));
    
  $event->poster = $person->id;
  
  if (!empty($_POST['date_start'])){
    $date_start = stripslashes(strip_tags($_POST['date_start']));
    $date       = explode("/", $date_start);
    $day        = $date[0];
    $month      = $date[1];
    $year       = $date[2];
    $hour       = $_POST['hour_start'];
    $minute     = $_POST['minute_start'];
    
    // vérifie que la date de début soit correcte
    if ($day < 1 || $day > 31 || $month < 1 || $month > 12){
      $_SESSION['event'] = $event;
      $_SESSION['error_msg'] = "La date de début est incorrecte";
      $code = "[miki_page='event_ajouter']";
      set_links($code, $_SESSION['lang']);
      //miki_redirect($code);
    }
    
    $event->date_start = "$year-$month-$day $hour:$minute:00";
  }
  
  if (!empty($_POST['date_stop'])){
    $date_stop  = stripslashes(strip_tags($_POST['date_stop']));
    $date       = explode("/", $date_stop);
    $day        = $date[0];
    $month      = $date[1];
    $year       = $date[2];
    $hour       = $_POST['hour_stop'];
    $minute     = $_POST['minute_stop'];
    
    // vérifie que la date de fin soit correcte
    if ($day < 1 || $day > 31 || $month < 1 || $month > 12){
      $_SESSION['event'] = $event;
      $_SESSION['error_msg'] = "La date de fin est incorrecte";
      $code = "[miki_page='event_ajouter']";
      set_links($code, $_SESSION['lang']);
      //miki_redirect($code);
    }
    
    $event->date_stop = "$year-$month-$day $hour:$minute:00";
  }
  
  // récupert la lattitude et la longitude d'après une adresse
  $coordonates = get_coordonate_from_address("$event->address,+$event->npa+$event->city,+$event->country");
  $event->latitude = $coordonates['latitude'];
  $event->longitude = $coordonates['longitude'];
  
  try{
    $event->save();
    
    /*try{
      // upload des logos
      $nb_pictures = $_POST['nb_pictures'];
      
      for($x=1; $x<=$nb_pictures; $x++){
        $logo = $_FILES["picture$x"];
        if ($logo['error'] != 4){
          $system = explode('.', $logo['name']);
          $ext = $system[sizeof($system)-1]; 
          $filename = basename($logo['name'], $ext);
          $event->upload_picture($logo, $filename ."-$event->id");
        }
      }
      $event->update();
    }catch(Exception $e){
      $event->delete();
      $_SESSION['event'] = $event;
      $_SESSION['error_msg'] = "Une erreur est survenue lors de l'ajout de l'événement : <br />" .$e->getMessage();
      $code = "[miki_page='event_ajouter']";
      set_links($code, $_SESSION['lang']);
      miki_redirect($code);
    }*/
    
    // et envoie l'événement sur le blog si demandé
    if (Miki_configuration::get('publish_event') == 1){
      $event->send_to_blog();
    }
    
    $_SESSION['success_msg'] = "L'événement a été ajouté avec succès";
    $code = "[miki_page='event_voir_admin' params='eid=" .$event->id ."']";
    set_links($code, $_SESSION['lang']);
    miki_redirect($code);
  }catch(Exception $e){
    $_SESSION['event'] = $event;
    $_SESSION['error_msg'] = "Une erreur est survenue lors de l'ajout de l'événement : <br />" .$e->getMessage();
    $code = "[miki_page='event_ajouter']";
    set_links($code, $_SESSION['lang']);
    miki_redirect($code);
  }

?>