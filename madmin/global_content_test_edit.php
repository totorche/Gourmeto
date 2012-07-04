<?php 
  require_once("include/headers.php");
  require_once("functions_pictures.php");  
  
  if (!test_right(16))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_POST['name']) || !isset($_POST['id']) || !is_numeric($_POST['id']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // sauve la feuille de style
  try{
    $element = new Miki_global_content($_POST['id']); 
    $element->name = decode($_POST['name']);
    $element->save();
    
    // supprime tous les liens avec les sections
    $contents = $element->get_contents();
    
    $languages = Miki_language::get_all_languages(); 
    foreach ($languages as $l){
      // si un contenu dans la langue en cours existe déjà, on fait un update
      if (isset($contents[strtolower($l->code)])){
        //$content = new Miki_global_content_content(); 
        $content = $contents[strtolower($l->code)];
        $content->global_content_id = $element->id;
        $content->language_id = $l->id;
        
        $type = $_POST["content_type_$l->code"];
        $content->content_type = $type;
        
        if ($type == 'file'){
          if ($_FILES["file_$l->code"]['error'] != 4 && $_FILES["file_$l->code"]['name'] != ""){
            $file = $_FILES["file_$l->code"]['name'];
            $content->content = $file;
          }
        }
        elseif ($type == 'code'){
          $code = stripslashes($_POST["code_$l->code"]);
          $content->content = $code;      
        }
        $content->update();
      }
      else{
        $content = new Miki_global_content_content(); 
        $content->global_content_id = $element->id;
        $content->language_id = $l->id;
        
        $type = $_POST["content_type_$l->code"];
        $content->content_type = $type;
        
        if ($type == 'file'){
          if ($_FILES["file_$l->code"]['error'] != 4 && $_FILES["file_$l->code"]['name'] != ""){
            $file = $_FILES["file_$l->code"]['name'];
            $content->content = $file;
          }
        }
        elseif ($type == 'code'){
          $code = stripslashes($_POST["code_$l->code"]);
          $content->content = $code;      
        }
        $content->save();
      }
    }
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Le bloc de contenu global a été mis à jour avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=51";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=52";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>