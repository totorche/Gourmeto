<?php
  require_once("include/headers.php");
  require_once("set_links.php");
  
  if (!isset($_GET['cp']) || $_GET['cp'] == ""){
    $code = "[miki_page='shop_panier']";
    set_links($code, $_SESSION['lang']);
    miki_redirect($code);
  }
  
  $cp = $_GET['cp'];
  $code_promo = new Miki_shop_code_promo();
  
  try{
    $code_promo->load_from_code($_GET['cp']);
  }
  catch(Exception $e){
    $code = "[miki_page='shop_panier']";
    set_links($code, $_SESSION['lang']);
    miki_redirect($code);
  }
  
  // si une commande est déjà en cours, on la récupert
  if (isset($_SESSION['miki_order'])){
    $order = $_SESSION['miki_order'];
    $order->id_code_promo = $code_promo->id;
    $order->update();
  }
  else{
    $code = "[miki_page='shop_panier']";
    set_links($code, $_SESSION['lang']);
    miki_redirect($code);
  }
  
  $code = "[miki_page='shop_panier']";
  set_links($code, $_SESSION['lang']);
  miki_redirect($code);
?>