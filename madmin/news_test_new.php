<?php 
  require_once("include/headers.php");
  
  if (!test_right(37)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['title'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // sauve l'actualité
  try{
    $news = new Miki_news();
    $news->title = stripslashes($_POST['title']);
    $news->text = stripslashes($_POST['text']);
    $news->poster = 'NULL';
    $news->language = stripslashes($_POST['language']);
    
    $news->save();
    
    if ($_FILES['picture']['error'] != 4)
      $news->upload_picture($_FILES['picture'], $news->id);
    
    $news->update();
    
    // et envoie l'article sur le blog si demandé
    if (Miki_configuration::get('publish_news') == 1){
      $news->send_to_blog();
    }
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("L'actualité a été envoyée avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=121";
    
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=122";
    // sauve les élément postés
    $_SESSION['saved_news'] = $news;
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }
?>