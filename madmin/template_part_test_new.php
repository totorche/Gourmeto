<?php 
  require_once("include/headers.php");
  
  if (!test_right(6))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_POST['name']) && !isset($_POST['content']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // sauve la feuille de style
  try{
    $el = new Miki_template_part(); 
    $el->name = $_POST['name'];
    $el->content = $_POST['content'];
    $el->code = "[miki_part='$el->name']";
    $el->save();
    
    // ajoute les nouveaux blocs de contenu globaux sélectionnés
    if ($_POST['contents'] != ""){
      $contents = explode(";", $_POST['contents']);
      foreach($contents as $contents_id){
        $content = new Miki_global_content($contents_id);
        $el->add_global_content($content);
      }
    }
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("La section a été ajoutée avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=31";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=32";
    // sauve les élément postés
    $_SESSION['saved_name'] = $_POST['name'];
    $_SESSION['saved_content'] = $_POST['content'];
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>