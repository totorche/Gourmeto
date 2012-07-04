<?php 
  require_once("include/headers.php");
  
  if (!test_right(19))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_GET['id']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  $ids = explode(";",$_GET['id']);
  
  foreach($ids as $id){
    if (!is_numeric($id))
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }
  
  try{
    $is_main_deleted = false;
    
    foreach($ids as $id){
      $sh = new Miki_language($id);
      
      // vérifie si la langue que l'on supprime est la langue principale
      if ($sh->is_main())
        $is_main_deleted = true;
         
      $sh->delete();
    }
    
    // si on a supprimé la langue principale, on définit la prochaine langue comme langue principale
    if ($is_main_deleted){
      $langs = Miki_language::get_all_languages();
      $lang = reset($langs);
      $lang->mainLanguage = 1;
      $lang->update();
    }
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Les langues sélectionnées ont été supprimées avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=41";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    $referer = "index.php?pid=41";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>