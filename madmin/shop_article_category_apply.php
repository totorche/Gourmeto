<?php
  require_once("include/headers.php");

  $job = $_POST['job'];
  
  try{
    if ($job == 0)
      throw new Exception("Aucune action sélectionnée");
    // ajout d'une catégorie
    elseif ($job == 1){
      $cat_ref_id = $_POST['cat_ref_id'];
      $place = $_POST['add_place'];
      
      // extrait les différentes catégories ajoutées (séparées par une virgule)
      $nb_cat = 0;
      $langs = Miki_language::get_all_languages();
      foreach($langs as $lang){
        $val[$lang->code] = explode(",", $_POST["name_$lang->code"]);
        
        if ($nb_cat < sizeof($val[$lang->code]))
          $nb_cat = sizeof($val[$lang->code]);
      }
      
      // parcourt chaque catégorie à ajouter
      for($x = 0; $x < $nb_cat; $x++){      
        $cat = new Miki_shop_article_category();
        
        // récupert le nom de la catégorie pour chaque langue
        foreach($langs as $lang){
          if (isset($val[$lang->code][$x]))
            $cat->name[$lang->code] = trim($val[$lang->code][$x]);
          else
            $cat->name[$lang->code] = "";
        }
        
        // ajoute la catégorie
        Miki_shop_article_category::add_category($cat, $cat_ref_id, $place);
      }
      
      $_SESSION['msg'] = "La catégorie a été ajoutée avec succès.";
    }
    // modification d'une catégorie
    elseif ($job == 2){
      $cat = new Miki_shop_article_category($_POST['cat_ref_id']);
      
      // récupert le nom de la catégorie pour chaque langue
      $langs = Miki_language::get_all_languages();
      foreach($langs as $lang){
        $cat->name[$lang->code] = $_POST["name_$lang->code"];
      }
      
      $cat->update();
      $_SESSION['msg'] = "La catégorie a été mise à jour avec succès.";      
    }
    elseif ($job == 3)
      throw new Exception("Aucune catégorie source n'a encore été sélectionnée.");
    // déplacement d'une catégorie
    elseif ($job == 4){
      $cat_ref_id = $_POST['cat_ref_id'];
      $cat_to_move_id = $_POST['cat_to_move_id'];
      $place = $_POST['move_place'];
      
      $cat = new Miki_shop_article_category($cat_to_move_id);
      
      // ajoute la catégorie
      Miki_shop_article_category::move_category($cat, $cat_ref_id, $place);
      $_SESSION['msg'] = "La catégorie a été déplacée avec succès.";
    }
    // suppression d'une catégorie
    elseif ($job == 5){
      $cat = new Miki_shop_article_category($_POST['cat_ref_id']);
      $cat->delete();
      $_SESSION['msg'] = "La catégorie a été supprimée avec succès."; 
    }
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    
    // puis redirige vers la page précédente
    $referer = "index.php?pid=149";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }
  catch(Exception $e){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    
    // puis redirige vers la page précédente
    $referer = "index.php?pid=149";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }
?>
