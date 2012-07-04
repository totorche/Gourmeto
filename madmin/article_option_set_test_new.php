<?php 
  require_once("include/headers.php");
  
  if (!test_right(51) && !test_right(52) && !test_right(53)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}
  
  if (!isset($_POST['name'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // test que l'on aie bien un shop de configuré
  $shops = Miki_shop::get_all_shops();
  if (sizeof($shops) == 0){
    $shop = false;
  }
  else
    $shop = array_shift($shops);
    
  if (!$shop){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // sauve la feuille de style
  try{
    $el = new Miki_shop_article_option_set(); 
    $el->name = $_POST['name'];
    $el->id_shop = $shop->id;
    $el->save();
    
    // ajoute les nouvelles options sélectionnées
    if ($_POST['contents'] != ""){
      $contents = explode(";", $_POST['contents']);
      foreach($contents as $contents_id){
        $el->add_option($contents_id);
      }
    }
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Le set a été ajouté avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=291";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=292";
    // sauve les élément postés
    $_SESSION['saved_name'] = $_POST['name'];
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>