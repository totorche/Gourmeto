<?php
  require_once ("include/headers.php");
  
  if (!test_right(68)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // créé une nouvelle bonne adresse
  $place = new Miki_place();

  if (isset($_POST['category'])){
    
    // création de la bonne adresse
    try{
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
      
      $place->save();
           
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
        $referer = "index.php?pid=212";
        // supprime la bonne adresse
        $place->delete();
        // sauvegarde la bonne adresse dans la session
        $_SESSION['saved_place'] = $place;
        $_SESSION['success'] = 0;
        $_SESSION['msg'] = "Une erreur est survenue lors de l'ajout de la bonne adresse : <br />" .$e->getMessage();
        echo "<script type='text/javascript'>document.location='$referer';</script>";
        exit();
      }
    }
    catch(Exception $e){
      $referer = "index.php?pid=212";
    
      $_SESSION['saved_place'] = $place;
    
      // ajoute le message à la session
      $_SESSION['success'] = 0;
      $_SESSION['msg'] = $e->getMessage();
      // puis redirige vers la page précédente
      echo "<script type='text/javascript'>document.location='$referer';</script>";
      exit();
    }
  }
  else{
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = 'Il manque des informations';
    header('location: ' .$_SESSION['url_back']);
  }
  
  if (isset($_SESSION['saved_place']))
    unset($_SESSION['saved_place']);
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("La bonne adresse a été créée avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=211";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>