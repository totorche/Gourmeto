<?php

  function remove_css($folder) {
    // vérifie si le nom du repertoire contient "/" à la fin
    if ($folder[strlen($folder)-1] != '/'){
      $folder .= '/';
    }
    
    if (is_dir($folder)) {
      $sq = opendir($folder); // lecture
      while ($f = readdir($sq)) {
        if ($f != '.' && $f != '..'){
          $fichier = $folder.$f; // chemin fichier
          if (is_dir($fichier)){
            remove_css($fichier); // rapel la fonction de manière récursive
          } 
          else{
            unlink($fichier); // sup le fichier
          } 
        }
      }
      closedir($sq);
      //rmdir($folder); // sup le répertoire
    }
    else{
      unlink($folder);  // sup le fichier
    }
  }


  require_once("include/headers.php");

  if (!isset($_REQUEST['template_id']) || !is_numeric($_REQUEST['template_id'])){
    exit("0");
  }
  
  try{
    $template = new Miki_template($_REQUEST['template_id']);
    $stylesheet = new Miki_stylesheet($template->stylesheet_id);
    ob_start();
    eval("?>" .$stylesheet->content); 
    $css = ob_get_contents();
    ob_end_clean();
    
    // supprime tous les fichiers CSS précédemment créés
    remove_css("./css/");
    
    $filename = "./css/" .uniqid ("css_temp_") .".css";
    //$filename = "css_temp.css";
    $handle = fopen($filename, "w");
    fwrite($handle, $css);
    fclose($handle);
    //chmod($filename, 0777);
    echo basename($filename);
    //unlink($filename);
  }
  catch(Exception $e){
    exit("Erreur lors de l'ouverture du template !");
  }
?>