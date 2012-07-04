<?php 
  require_once("include/headers.php");
  
  if (!test_right(32)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['name']) || !isset($_POST['id']) || !is_numeric($_POST['id'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // sauve la page
  try{
    $newsletter = new Miki_newsletter($_POST['id']); 
    $newsletter->name = $_POST['name'];
    $newsletter->template_id = $_POST['template'] == "" ? 'NULL' : $_POST['template'];
    $newsletter->subject = $_POST['subject'];
    $newsletter->content_type = $_POST['content_type'];
    
    if ($newsletter->content_type == 'file'){
      $newsletter->content = $_FILES["file_content"]['name'];
    }
    elseif ($newsletter->content_type == 'code'){
      $newsletter->content = stripslashes($_POST["code"]);      
    }

    $newsletter->update();

    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("La newsletter a été mise à jour avec succès");
    
    // puis redirige vers la page précédente
    $referer = "index.php?pid=114";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=116";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>