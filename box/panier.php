<?php
  // l'url du panier
  $url_panier = "[miki_page='shop_panier']";
  
  // si on appelle ce script via Ajax
  if (file_exists("../include/headers.php")){
    require_once("../include/headers.php");
    require_once("../scripts/set_links.php");
    
    set_links($url_panier, $_SESSION['lang']);
  }
  
  // si une commande est déjà en cours, on la récupert et on affiche le contenu du panier
  if (isset($_SESSION['miki_order']) && $_SESSION['miki_order'] instanceof Miki_order){
    $order = $_SESSION['miki_order'];
    
    $total = $order->get_total();
    $nb_articles = $order->get_nb_articles(true);
    
    echo "<div class='box_panier'>
            <span class='box_panier_link'><a href='$url_panier' title=\"" ._("Voir mon panier") ."\">" ._("Voir mon panier") ."</a></span>
            <img src='pictures/panier.gif' alt='" ._("Panier") ."' style='margin-right: 10px; vertical-align: middle' /><span class='box_panier_title'>" ._("Panier") ."</span>
            <p><span class='box_panier_articles'>$nb_articles " ._("article(s)") ." : </span><span class='box_panier_price'>" .number_format($total,2,'.',"'") ." CHF</span></p>
          </div>";
  }
  else{
    echo "&nbsp;";
  }
?>