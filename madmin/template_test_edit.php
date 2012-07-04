<?php 
  require_once("include/headers.php");
  
  if (!test_right(13))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_POST['name']) || !isset($_POST['id']) || !is_numeric($_POST['id']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // sauve la feuille de style
  try{
    $template = new Miki_template($_POST['id']); 
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
    $template->update();
    
    // supprime tous les liens avec les sections
    $template->remove_parts();
    
    // puis rajoute les nouvelles sections sélectionnées
    if ($_POST['parts'] != ""){
      $parts = explode(";", $_POST['parts']);
      foreach($parts as $part_id){
        $part = new Miki_template_part($part_id);
        $template->add_part($part);
      }
    }
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Le gabarit a été mis à jour avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=21";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = $_SESSION['url_back'];
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>