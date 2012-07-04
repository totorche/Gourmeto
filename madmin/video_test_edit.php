<?php
  require_once ("include/headers.php");
  
  if (!test_right(72)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."&h=1';</script>";
    exit();
  }

  if (!isset($_POST['category']) || !is_numeric($_POST['category']) || !isset($_POST['id']) || !is_numeric($_POST['id'])){
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = "Il manque des informations";
    header('location: ' .$_SESSION['url_back']);
    exit();
  } 
    
  // récupération puis modification de la vidéo
  try{
    $video = new Miki_video($_POST['id']);
    
    $video->state         = $_POST['state'];
    $video->type          = $_POST['type'];
    $video->category      = $_POST['category'];
    
    // récupert l'id de la vidéo
    if ($video->type == 'youtube'){
      if (preg_match("@http://(www.)?youtube.com/watch\?v=([0-9a-z_-]+)(&feature=[0-9a-z_-]*)?@i", $_POST['video'], $matches))
        $video->video = $matches[2];
      else
        $video->video = $_POST['video'];
    }
    elseif ($video->type == 'vimeo'){
      if (preg_match("@http://(www.)?vimeo.com/([0-9a-z]+)@i", $_POST['video'], $matches))
        $video->video = $matches[2];
      else
        $video->video = $_POST['video'];
    }
    
    foreach($_POST['title'] as $key => $title){
      $video->title[$key] = $title;
    }
    
    foreach($_POST['description'] as $key => $description){
      $video->description[$key] = $description;
    }
    
    $video->update();
  }
  catch(Exception $e){
    $referer = "index.php?pid=233&id=$video->id";
  
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='$referer';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("La vidéo a été modifiée avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=231";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>