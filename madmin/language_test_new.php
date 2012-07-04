<?php 
  require_once("include/headers.php");
  
  if (!test_right(3))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_POST['name']) || !isset($_POST['code']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // sauve la feuille de style
  try{
    $el = new Miki_language(); 
    $el->name = $_POST['name'];
    $el->code = $_POST['code'];
    
    // si on définit une nouvelle langue principale, on passe toutes les autres en langues secondaires
    if ($_POST['mainLanguage'] == 1){
      $langs = Miki_language::get_all_languages();
      foreach($langs as $lang){
        $lang->mainLanguage = 0;
        $lang->update();
      }
    }
    
    // si c'est la première langue a être configurée, elle devient automatiquement la langue principale
    if (Miki_language::get_nb_languages() == 0)
      $el->mainLanguage = 1;
    else
      $el->mainLanguage = $_POST['mainLanguage'];
        
    if ($_FILES['picture']['error'] != 4) 
      $el->upload_picture($_FILES['picture'], "pictures/flags/", $el->code);
    
    $el->save();
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("La langue a été ajoutée avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=41";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=42";
    // sauve les élément postés
    $_SESSION['saved_lang'] = $el;
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>