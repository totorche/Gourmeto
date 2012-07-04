<?php 
  require_once("include/headers.php");
  
  if (!test_right(31) && !test_right(32)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['name'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // sauve la feuille de style
  try{
    $template = new Miki_newsletter_template(); 
    $template->name = $_POST['name'];
    $template->stylesheet_id = $_POST['stylesheet'] == -1 ? 'NULL' : $_POST['stylesheet'];
    $template->content = stripslashes($_POST['content']);
    $template->state = $_POST['active'] == 1 ? 1:0;
    $template->save();
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Le gabarit a été ajoutée avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=111";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=112";
    // sauve les élément postés
    $_SESSION['saved_name'] = $_POST['name'];
    $_SESSION['saved_content'] = $_POST['content'];
    $_SESSION['saved_stylesheet'] = $_POST['stylesheet'];
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>