<?php 
  require_once("include/headers.php");
  
  if (!test_right(25))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_POST['name']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // sauve l'utilisateur
  try{
    $cat = new Miki_category(); 
    $cat->name = $_POST['name'];
    $cat->parent_id = $_POST['parent'] == -1 ? 'NULL' : $_POST['parent'];
    $cat->save();
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("La catégorie a été ajoutée avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=81";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=82";
    // sauve les élément postés
    $_SESSION['saved_name']   = $_POST['name'];
    $_SESSION['saved_parent'] = $_POST['parent'];
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>