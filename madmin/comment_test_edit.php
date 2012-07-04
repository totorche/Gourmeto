<?php
  require_once ("include/headers.php");
  
  if (!test_right(74)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."&h=1';</script>";
    exit();
  }

  if (!isset($_POST['id']) || !is_numeric($_POST['id']) ||
      !isset($_POST['comment']) || 
      !isset($_POST['state']) || !is_numeric($_POST['state'])){
    
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = "Il manque des informations";
    header('location: ' .$_SESSION['url_back']);
    exit();
  } 
    
  // récupération puis modification du commentaire
  try{
    $comment = new Miki_comment($_POST['id']);
    $comment->comment = nl2br(strip_tags(stripslashes($_POST['comment'])));
    $comment->state = $_POST['state'];
    $comment->update();
  }
  catch(Exception $e){
    $referer = "index.php?pid=242&id=" .$_POST['id'];
  
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='$referer';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("Le commentaire a été modifié avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=242&id=" .$_POST['id'];
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>