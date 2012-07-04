<?php
  require_once ("include/headers.php");
  
  if (!test_right(66)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."&h=1';</script>";
    exit();
  }

  if (!isset($_POST['category']) || !is_numeric($_POST['category']) || !isset($_POST['id']) || !is_numeric($_POST['id'])){
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = "Il manque des informations";
    header('location: ' .$_SESSION['url_back']);
    exit();
  } 
    
  // récupération puis modification du document
  try{
    $document = new Miki_document($_POST['id']);
    
    $document->state         = $_POST['state'];
    $document->category      = $_POST['category'];
    
    foreach($_POST['title'] as $key => $title){
      $document->title[$key] = $title;
    }
    
    foreach($_POST['description'] as $key => $description){
      $document->description[$key] = $description;
    }
    
    $document->update();
         
    try{
      // upload du fichier
      if ($_FILES["fichier"]['error'] != 4){
        $document->delete_file();
        $document->upload_file($_FILES["fichier"]);
      }
      $document->update();
    }catch(Exception $e){
      $referer = "index.php?pid=203&id=$document->id";

      $_SESSION['success'] = 0;
      $_SESSION['msg'] = "Une erreur est survenue lors de la modification du document : <br />" .$e->getMessage();
      echo "<script type='text/javascript'>document.location='$referer';</script>";
      exit();
    }
  }
  catch(Exception $e){
    $referer = "index.php?pid=203&id=$document->id";
  
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='$referer';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("Le document a été modifié avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=201";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>