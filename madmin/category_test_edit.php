<?php 
  require_once("include/headers.php");
  
  if (!test_right(26))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_POST['name']) || !isset($_POST['id']) || !is_numeric($_POST['id']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // sauve l'utilisateur
  try{
    $cat = new Miki_category($_POST['id']); 
    $cat->name = $_POST['name'];
    $cat->parent_id = $_POST['parent'] == -1 ? 'NULL' : $_POST['parent'];
    $cat->update();
    
    // récupert toutes les pages faisant partie de cette catégorie
    $pages = $cat->get_pages();
    foreach($pages as $page){
      // réécrit le fichier .htaccess    
      $page->update_htaccess();
      
      // met à jour le fichier sitemap.xml
      $page->update_sitemap();
    }
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("La catégorie a été modifiée avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=81";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=83";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>