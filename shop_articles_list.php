<head>
<?php
  /**
   * Affiche une liste des articles du shop
   */
   
  // récupert la page à afficher
  if (isset($_REQUEST['p']) && is_numeric($_REQUEST['p']))
    $p = $_REQUEST['p'];
  else
    $p = 1;
  
  // récupert la catégorie à afficher
  if (isset($_REQUEST['cat']) && is_numeric($_REQUEST['cat']))
    $cat_id = $_REQUEST['cat'];
  else
    $cat_id = "";
    
  // récupert le champ par lequel les articles doivent être triés
  if (isset($_REQUEST['order']) && is_numeric($_REQUEST['order']))
    $order = $_REQUEST['order'];
  else
    $order = "";
    
  // récupert l'ordre de tri (asc ou desc)
  if (isset($_REQUEST['order_t']) && ($_REQUEST['order_t'] == 'asc' || $_REQUEST['order_t'] == 'desc'))
    $order_type = $_REQUEST['order_t'];
  else
    $order_type = "";
    
  // si on ne veut afficher que les articles en promotion
  if (isset($_REQUEST['promo']) && $_REQUEST['promo'] == 1)
    $promo = true;
  else
    $promo = false;
    
  $use_stock = Miki_configuration::get('miki_shop_gestion_stock');
    
  $max_elements = 10;
  $nb_elements = 0;
  $image_width = 150; // en pixels
  $image_height = 150; // en pixels
  
  // si une catégorie est donnée, on la récupert
  if ($cat_id != ""){
    try{
      $cat = new Miki_shop_article_category($cat_id);
    }
    catch(Exception $e){
      $cat = "";
    }
  }
  
  // masque le titre H1 de la page
  $title_h1 = "";
  
  // récupert le fil d'arianne
  $breadcrumb = print_breadcrumb_from_category($cat_id, "breadcrumb-separator.png");
  
  // récupert tous les articles de la catégorie donnée
  $elements = Miki_shop_article::search("", "", "", $cat_id, "", true, $promo, $use_stock, $order, $order_type);
?>


  <link rel="stylesheet" href="css/articles.css" />
</head>

<style type="text/css">

<?php
  $display_type = "list"; // "grid" or "list"
?>

#affichage_grille{
  <?php if ($display_type == "grid"){ ?>
  background-position: 0px 0px;
  <?php }else{ ?>
  background-position: -22px 0px;
  <?php } ?>
}

#affichage_liste{
  <?php if ($display_type == "grid"){ ?>
  background-position: -72px 0px;
  <?php }else{ ?>
  background-position: -50px 0px;
  <?php } ?>
}

</style>

<script type="text/javascript">
  function list_view_options(type){
    if (type == 1){
      $("ul.main_table").removeClass("list_display");
      $("ul.list-view-options li:first-child span").css('background-position', '0px 0px');
      $("ul.list-view-options li:last-child span").css('background-position', '-72px 0px');
    }
    else if (type == 2){
      $("ul.main_table").addClass("list_display");
      $("ul.list-view-options li:first-child span").css('background-position', '-22px 0px');
      $("ul.list-view-options li:last-child span").css('background-position', '-50px 0px');
    }
  }
</script>
<?php echo "<br />$breadcrumb<br />"; ?>
<ul class="list-view-options" style="height: 18px;">
  <li>
    <a href="javascript:list_view_options(1);" title="<?php echo _("Afficher sous forme de grille"); ?>"><?php echo "Grid"; ?><span id="affichage_grille"></span></a>
  </li>
  <li>
    <a href="javascript:list_view_options(2);" title="<?php echo _("Afficher sous forme de liste"); ?>"><?php echo "List"; ?><span id="affichage_liste"></span></a>
  </li>
</ul>

<?php
  if (sizeof($elements) == 0){
    echo "<div>" ._("Aucun article n'a été trouvé") ."</div>";
  }
  else{
  
    echo "<div class='shop_articles_list'>
            <ul class='main_table" .(($display_type == "list") ? " list_display" : "") ."'>";
              foreach($elements as $el){
                
                $name = $el->get_name($_SESSION['lang']);
                $description = $el->get_description($_SESSION['lang'], 150, true);
                $attributes = $el->get_attributes();
                $price = number_format($el->price,2,'.',"'") ." CHF";
                $link = $el->get_url_simple();
                $promo = $el->get_promotion();
                if ($promo)
                  $promo = number_format($promo,2,'.',"'") ." CHF";
                
                // s'il y a au moins une image
                if (is_array($el->pictures) && sizeof($el->pictures) > 0)
                  $pic = "pictures/shop_articles/thumb/" .current($el->pictures);
                else
                  $pic = false;
                
                
                echo "<li itemscope itemtype='http://schema.org/Product'>";
                        if ($pic !== false){
                          echo "<div class='articles_list_picture'>
                                  <a href='$link' title='" ._("Voir les détails") ."'><img src='timthumb.php?src=" .urlencode($pic) ."&w=$image_width&h=$image_height' alt=\"$name\" /></a>
                                  <meta itemprop='image' content='" .SITE_URL ."/$pic' />
                                </div>";
                        }
                  echo "<div class='articles_list_text'>
                          <h2 class='articles_list_title' itemprop='name'>
                            <a itemprop='url' href='$link' title='" ._("Voir les détails") ."'>$name</a>
                          </h2>
                          <p itemprop='description' class='articles_list_description'>$description...</p>
                        </div>
                        <div class='articles_list_details'><a href='$link' title='" ._("Voir les détails") ."'>" ._("Détails") ."</a></div>
                      </li>";
              }
      echo "</ul>
          </div>";
     
    // pagination  
    $nb_pages = (int)($nb_elements / $max_elements);
    $reste = ($nb_elements % $max_elements);
    if ($reste != 0)
  	$nb_pages++;

    if ($nb_pages > 1){
    	$code = "<div style='text-align:right;padding:0 20px;'>";
    	
    	if ($p != 1)
    	  $code .= "<a href='[miki_page='$page->name' params='p=1']' title='" ._("première page") ."'><<</a>&nbsp;&nbsp;<a href='[miki_page='$page->name' params='p=" .($p-1) ."']' title='" ._("page précédente") ."'><</a>&nbsp;&nbsp;";
    	
    	if ($nb_pages <= 12){
    	  for ($x=1; $x<=$nb_pages; $x++){
    		if ($x == $p)
    		  $code .= "<span style='font-weight:bold'>$x</span> | ";
    		else
    		  $code .= "<a href='[miki_page='$page->name' params='p=$x']'>$x</a> | ";             
    	  }
    	}
    	elseif ($p == $nb_pages){
    	  for ($x=($p-12); $x<=$p; $x++){
    		if ($x == $p)
    		  $code .= "<span style='font-weight:bold'>$x</span> | ";
    		else
    		  $code .= "<a href='[miki_page='$page->name' params='p=$x']'>$x</a> | ";             
    	  }
    	}
    	elseif ($p == 1){
    	echo $p;
    	  for ($x=$p; $x<=($p+12); $x++){
    		if ($x == $p)
    		  $code .= "<span style='font-weight:bold'>$x</span> | ";
    		else
    		  $code .= "<a href='[miki_page='$page->name' params='p=$x']'>$x</a> | ";             
    	  }
    	}
    	elseif ($p != 1){
    	  $start = $p - 6;
    	  if ($start < 1)
    	  $start = 1;
    	  $stop = $start + 12;
    	  
    	  if ($stop > $nb_pages)
    		$stop = $nb_pages;
    		
    	  for ($x=$start; $x<=$stop; $x++){
    		if ($x == $p)
    		  $code .= "<span style='font-weight:bold'>$x</span> | ";
    		else
    		  $code .= "<a href='[miki_page='$page->name' params='p=$x']'>$x</a> | ";             
    	  }
    	}
    	
    	$code = substr($code, 0, strlen($code)-3);
    	
    	if ($p != $nb_pages)
    	  $code .= "&nbsp;&nbsp;<a href='[miki_page='$page->name' params='p=" .($p+1) ."']' title='" ._("page suivante") ."'>></a>&nbsp;&nbsp;<a href='[miki_page='$page->name' params='p=$nb_pages']' title='" ._("dernière page") ."'>>></a>";
    	
    	$code .= "</div>";
    	
    	echo $code;
    }
  }
?>