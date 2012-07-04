<?php
  require_once ("include/headers.php");
  
  if (!test_right(71)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // créé une nouvelle activité
  $redaction = new Miki_redaction();

  if (isset($_POST['id_person']) && isset($_POST['title'])){
  
    if (!isset($_POST['keywords']) || !is_array($_POST['keywords']) || sizeof($_POST['keywords']) < 4){
      $referer = "index.php?pid=222";
      $_SESSION['success'] = 0;
      $_SESSION['msg'] = _("4 mots-clés au minimum sont requis pour valider une rédaction");
      echo "<script type='text/javascript'>document.location='$referer';</script>";
      exit();
    }
  
    // vérifie que 4 mots-clés au moins soient remplis    
    $nb_keywords = 0;
    foreach($_POST['keywords'] as $kw){
      if (!empty($kw)) $nb_keywords++;
    }
    
    if ($nb_keywords < 4){
      $referer = "index.php?pid=222";
      $_SESSION['success'] = 0;
      $_SESSION['msg'] = _("4 mots-clés au minimum sont requis pour valider une rédaction : $nb_keywords");
      echo "<script type='text/javascript'>document.location='$referer';</script>";
      exit();
    }
    
    // création de l'activité
    try{
      $redaction->id_person = stripslashes($_POST['id_person']);
      $redaction->title     = stripslashes($_POST['title']);
      $redaction->nb_words  = stripslashes($_POST['nb_words']);
      $redaction->comment   = stripslashes($_POST['comment']);
      
      $redaction->keywords = array();
      foreach($_POST['keywords'] as $kw){
        $redaction->keywords[] = stripslashes(trim($kw));
      }
      
      $redaction->state = 0;
      
      $redaction->save();
           
      try{
        // upload des images
        $nb_pictures = $_POST['nb_pictures'];
        
        for($x=1; $x<=$nb_pictures; $x++){
          $image = $_FILES["picture$x"];
          if ($image['error'] != 4){
            $redaction->upload_picture($image);
          }
        }
        $redaction->update();
      }catch(Exception $e){
        $referer = "index.php?pid=222";
        // supprime l'activity
        $redaction->delete();
        // sauvegarde l'activité dans la session
        $_SESSION['saved_redaction'] = $redaction;
        $_SESSION['success'] = 0;
        $_SESSION['msg'] = "Une erreur est survenue lors de l'ajout de la rédaction : <br />" .$e->getMessage();
        echo "<script type='text/javascript'>document.location='$referer';</script>";
        exit();
      }
    }
    catch(Exception $e){
      $referer = "index.php?pid=222";
    
      $_SESSION['saved_redaction'] = $redaction;
    
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
  
  if (isset($_SESSION['saved_redaction']))
    unset($_SESSION['saved_redaction']);
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("La rédaction a été créée avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=221";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>