<?php
  require_once ("include/headers.php");
  
  if (!test_right(55)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // créé un nouvel objet
  $object = new Miki_object();

  if (isset($_POST['category'])){
    
    // création de l'objet
    try{
      $object->state         = $_POST['state'];
      $object->category      = stripslashes($_POST['category']);
      $object->address       = stripslashes($_POST['address']);
      $object->npa           = $_POST['npa'];
      $object->city          = stripslashes($_POST['city']);
      $object->region        = stripslashes($_POST['region']);
      $object->country       = stripslashes($_POST['country']);
      $object->tel           = $_POST['tel'];
      $object->email         = $_POST['email'];
      $object->email_booking = $_POST['email_booking'];
      $object->web           = $_POST['web'];
      
      foreach($_POST['title'] as $key => $title){
        $object->title[$key] = $title;
      }
      
      foreach($_POST['description'] as $key => $description){
        $object->description[$key] = $description;
      }
      
      $object->save();
           
      try{
        // upload des images
        $nb_pictures = $_POST['nb_pictures'];
        
        $object->picture_path = "../" .$object->picture_path;
        
        for($x=1; $x<=$nb_pictures; $x++){
          $image = $_FILES["picture$x"];
          if ($image['error'] != 4){
            $object->upload_picture($image);
          }
        }
        $object->update();
      }catch(Exception $e){
        $referer = "index.php?pid=172";
        // supprime l'objet
        $object->delete();
        // sauvegarde l'objet dans la session
        $_SESSION['saved_object'] = $object;
        $_SESSION['success'] = 0;
        $_SESSION['msg'] = "Une erreur est survenue lors de l'ajout de l'objet : <br />" .$e->getMessage();
        echo "<script type='text/javascript'>document.location='$referer';</script>";
        exit();
      }
    }
    catch(Exception $e){
      $referer = "index.php?pid=172";
    
      $_SESSION['saved_object'] = $object;
    
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
  
  if (isset($_SESSION['saved_object']))
    unset($_SESSION['saved_object']);
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("L'objet a été créé avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=171";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>