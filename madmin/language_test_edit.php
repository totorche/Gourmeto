<?php 
  require_once("include/headers.php");
  
  if (!test_right(11))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_POST['name']) || !isset($_POST['code']) || !isset($_POST['id']) || !is_numeric($_POST['id']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // sauve la feuille de style
  try{
    $el = new Miki_language($_POST['id']); 
    $el->name = $_POST['name'];
    $el->code = $_POST['code'];
    
    $main_lang_exists = false;
    
    // si on définit une nouvelle langue principale, on passe toutes les autres en langues secondaires
    if ($_POST['mainLanguage'] == 1){
      $langs = Miki_language::get_all_languages();
      foreach($langs as $lang){
        $lang->mainLanguage = 0;
        $lang->update();
      }
    }
    // si la langue n'est pas définie comme la langue principale, on vérifie qu'une autre le soit.
    else{
      $langs = Miki_language::get_all_languages();
      foreach($langs as $lang){
        // si une autre langue est la langue principale
        if ($lang->is_main() && $lang->id != $el->id)
          $main_lang_exists = true;
      }
    }
    
    // si c'est la seule langue a être configurée ou qu'aucune autre langue n'est la langue principale, 
    // elle devient automatiquement la langue principale
    if (Miki_language::get_nb_languages() == 1 || !$main_lang_exists)
      $el->mainLanguage = 1;
    else
      $el->mainLanguage = $_POST['mainLanguage'];
    
    if ($_FILES['picture']['error'] != 4)
      $el->upload_picture($_FILES['picture'], "pictures/flags/", $el->code);
      
    $el->update();
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("La langue a été mise à jour avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=41";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=43";
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>