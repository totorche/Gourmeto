<?php
  require_once ("include/headers.php");
  
  if (!test_right(65)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // créé un nouveau document
  $document = new Miki_document();

  if (isset($_POST['category']) && is_numeric($_POST['category'])){
    
    // création du document
    try{
      $document->state         = $_POST['state'];
      $document->category      = $_POST['category'];
      
      foreach($_POST['title'] as $key => $title){
        $document->title[$key] = $title;
      }
      
      foreach($_POST['description'] as $key => $description){
        $document->description[$key] = $description;
      }
      
      $document->save();
           
      try{
        // upload du fichier
        if ($_FILES["fichier"]['error'] != 4){
          $document->upload_file($_FILES["fichier"]);
        }
        $document->update();
      }catch(Exception $e){
        $referer = "index.php?pid=202";
        
        // sauvegarde le document dans la session
        $_SESSION['saved_document'] = $document;
        
        // supprime le document
        $document->delete();
        
        $_SESSION['success'] = 0;
        $_SESSION['msg'] = "Une erreur est survenue lors de l'ajout du document : <br />" .$e->getMessage();
        echo "<script type='text/javascript'>document.location='$referer';</script>";
        exit();
      }
    }
    catch(Exception $e){
      $referer = "index.php?pid=202";
    
      $_SESSION['saved_document'] = $document;
    
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
    exit();
  }
  
  if (isset($_SESSION['saved_document']))
    unset($_SESSION['saved_document']);
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("Le document a été créé avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=201";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>