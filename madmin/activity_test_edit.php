<?php
  require_once ("include/headers.php");
  
  if (!test_right(60)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."&h=1';</script>";
    exit();
  }

  if (!isset($_POST['city']) || !isset($_POST['id']) || !is_numeric($_POST['id'])){
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = "Il manque des informations";
    header('location: ' .$_SESSION['url_back']);
    exit();
  } 
    
  // récupération puis modification de l'activité
  try{
    $activity = new Miki_activity($_POST['id']);
    
    $activity->city          = stripslashes($_POST['city']);
    $activity->region        = stripslashes($_POST['region']);
    $activity->country       = stripslashes($_POST['country']);
    $activity->web           = $_POST['web'];
    
    foreach($_POST['title'] as $key => $title){
      $activity->title[$key] = $title;
    }
    
    foreach($_POST['description'] as $key => $description){
      $activity->description[$key] = $description;
    }
    
    $activity->update();
         
    try{
      // upload des images
      $nb_pictures = $_POST['nb_pictures'];
      
      for($x=1; $x<=$nb_pictures; $x++){
        $image = $_FILES["picture$x"];
        if ($image['error'] != 4){
          $activity->upload_picture($image);
        }
      }
      $activity->update();
    }catch(Exception $e){
      $referer = "index.php?pid=183";
      
      $_SESSION['success'] = 0;
      $_SESSION['msg'] = "Une erreur est survenue lors de la modification de l'activité : <br />" .$e->getMessage();
      echo "<script type='text/javascript'>document.location='$referer';</script>";
      exit();
    }
  }
  catch(Exception $e){
    $referer = "index.php?pid=183";
  
    $_SESSION['saved_activity'] = $activity;
  
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='$referer';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("L'activité a été modifié avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=181";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>