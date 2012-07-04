<?php
  require_once ("include/headers.php");
  
  if (!test_right(69)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."&h=1';</script>";
    exit();
  }

  if (!isset($_POST['category']) || !isset($_POST['id']) || !is_numeric($_POST['id'])){
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = "Il manque des informations";
    header('location: ' .$_SESSION['url_back']);
    exit();
  } 
    
  // récupération puis modification de la bonne adresse
  try{
    $place = new Miki_place($_POST['id']);
    
    $place->state         = $_POST['state'];
    $place->category      = stripslashes($_POST['category']);
    $place->address       = stripslashes($_POST['address']);
    $place->npa           = $_POST['npa'];
    $place->city          = stripslashes($_POST['city']);
    $place->country       = stripslashes($_POST['country']);
    $place->tel           = $_POST['tel'];
    $place->email         = $_POST['email'];
    $place->web           = $_POST['web'];
    
    foreach($_POST['title'] as $key => $title){
      $place->title[$key] = $title;
    }
    
    foreach($_POST['description'] as $key => $description){
      $place->description[$key] = $description;
    }
    
    $place->update();
         
    try{
      // upload des images
      $nb_pictures = $_POST['nb_pictures'];
      
      $place->picture_path = "../" .$place->picture_path;
      
      for($x=1; $x<=$nb_pictures; $x++){
        $image = $_FILES["picture$x"];
        if ($image['error'] != 4){
          $place->upload_picture($image);
        }
      }
      $place->update();
    }catch(Exception $e){
      $referer = "index.php?pid=213&id=$place->id";
      // sauvegarde la bonne adresse dans la session
      $_SESSION['success'] = 0;
      $_SESSION['msg'] = "Une erreur est survenue lors de la modification de la bonne adresse : <br />" .$e->getMessage();
      echo "<script type='text/javascript'>document.location='$referer';</script>";
      exit();
    }
  }
  catch(Exception $e){
    $referer = "index.php?pid=213&id=$place->id";
  
    $_SESSION['saved_place'] = $place;
  
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='$referer';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("La bonne adresse a été modifiée avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=211";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>