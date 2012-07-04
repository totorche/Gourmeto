<?php 
  require_once("include/headers.php");
  require_once("functions_pictures.php");  
  
  if (!test_right(8))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";  
  
  if (!isset($_POST['name']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // sauve le bloc de contenu global
  try{
    $element = new Miki_global_content(); 
    $element->name = decode($_POST['name']);
    $element->save();
    
    $languages = Miki_language::get_all_languages(); 
    foreach ($languages as $l){
      $content = new Miki_global_content_content(); 
      $content->global_content_id = $element->id;
      $content->language_id = $l->id;
      
      $type = $_POST["content_type_$l->code"];
      $content->content_type = $type;
      
      if ($type == 'file'){
        $file = $_FILES["file_$l->code"]['name'];
        $content->content = $file;
      }
      elseif ($type == 'code'){
        $code = stripslashes($_POST["code_$l->code"]);
        $content->content = $code;      
      }
      $content->save();
    }
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Le bloc de contenu global a été ajouté avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=51";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=52";
    // sauve les élément postés
    $_SESSION['saved_name'] = $_POST['name'];
    
    // sauve tous les contenus
    $content = "";
    $languages = Miki_language::get_all_languages(); 
    foreach ($languages as $l){
      $content_type .= ";;$l->code%%" .$_POST["content_type_$l->code"];
      if ($_POST["content_type_$l->code"] == 'code')
        $content .= ";;$l->code%%" .$_POST["code_$l->code"];
    }
    $_SESSION['saved_content_type']  = $content_type;
    $_SESSION['saved_content'] = $content;
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>