<?php 
  require_once("include/headers.php");
  
  if (!test_right(5))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_POST['name']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // sauve la feuille de style
  try{
    $template = new Miki_template(); 
    $template->name = $_POST['name'];
    $template->stylesheet_id = $_POST['stylesheet'] == -1 ? 'NULL' : $_POST['stylesheet'];
    $template->content_type = stripslashes($_POST['content_type']);
    
    if ($template->content_type == 'file'){
      $template->content = $_FILES["file"]['name'];
    }
    elseif ($template->content_type == 'code'){
      $template->content = stripslashes($_POST["code"]);
    }
    
    $template->state = $_POST['active'] == 1 ? 1:0;
    $template->save();
    
    if ($_POST['parts'] != ""){
      $parts = explode(";", $_POST['parts']);
      foreach($parts as $part_id){
        $part = new Miki_template_part($part_id);
        $template->add_part($part);
      }
    }
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Le gabarit a été ajoutée avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=21";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=22";
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