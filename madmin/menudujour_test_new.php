<?php 
  require_once("include/headers.php");
  
  if (!test_right(79)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['name'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // sauve la feuille de style
  try{
    $menu = new miki_menudujour();
    
    $date = explode("/", $_POST['date']);
    $date = $date[2] .'-' .$date[1] .'-' .$date[0];

    $menu->name = stripslashes($_POST['name']);
    $menu->date = $date;
    $menu->save();
    
    // ajoute les plats
    if (isset($_POST['menu_type']) && is_array($_POST['menu_type'])){
      for($x=0; $x<sizeof($_POST['menu_type']); $x++){
        $menu_type = $_POST['menu_type'][$x];
        $menu_name = isset($_POST['menu_name'][$x]) ? stripslashes($_POST['menu_name'][$x]) : "";
        $menu->add_plat($menu_name, $menu_type);
      }
    }
    
    // ajoute les prix
    if (isset($_POST['menu_price']) && is_array($_POST['menu_price'])){
      for($x=0; $x<sizeof($_POST['menu_price']); $x++){
        $menu_price = $_POST['menu_price'][$x];
        $menu_price_description = isset($_POST['menu_price_description'][$x]) ? stripslashes($_POST['menu_price_description'][$x]) : "";
        $menu->add_price($menu_price_description, $menu_price);
      }
    }
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Le menu a été ajouté avec succès");
    
    // puis redirige vers la page précédente
    $referer = "index.php?pid=302";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=302";

    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();

    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>