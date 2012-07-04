<?php
  /**
   * Affiche une liste des différents objets
   */       
   
  if (isset($_REQUEST['p']) && is_numeric($_REQUEST['p']))
    $p = $_REQUEST['p'];
  else
    $p = 1;
    
  $max_elements = 10;
  $nb_elements = 0;
  $image_width = 150; // en pixels
  $image_height = 150; // en pixels

  $elements = Miki_object::get_all_objects(false, "", "asc", $max_elements, $p, $nb_elements);
?>

<head>
  <link rel="stylesheet" href="css/objects.css" />
</head>

<style type="text/css">

<?php
  $display_type = "grid"; // "grid" or "list"
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
    echo "<div>" ._("Aucun élément n'a été trouvé") ."</div>";
  }
  else{
  
    echo "<div class='objects'>
            <ul class='main_table" .(($display_type == "list") ? " list_display" : "") ."'>";
              foreach($elements as $el){
                
                if (is_array($el->pictures) && sizeof($el->pictures) > 0)
                  $pic = "timthumb.php?src=" .urlencode($el->picture_path .'thumb/' .current($el->pictures)) ."&w=$image_width&h=$image_height";
                else
                  $pic = false;
                  
                $title = $el->title[$_SESSION['lang']];
                $address = "";
                
                if ($el->address != "")
                  $address .= "<div itemprop='streetAddress'>$el->address</div>";
                  
                if ($el->npa != "" && $el->city != "")
                  $address .= "<div><span itemprop='postalCode'>$el->npa</span> <span itemprop='addressLocality'>$el->city</span></div>";
                elseif ($el->city != "")
                  $address .= "<div itemprop='addressLocality'>$el->city</div>";
                
                if ($el->region != "" && $el->country != "")
                  $address .= "<div><span itemprop='addressRegion'>$el->region</span> - <span itemprop='addressCountry'>$el->country</span></div>";
                elseif ($el->country != "")
                  $address .= "<div itemprop='addressCountry'>$el->country</div>";
                
                if ($el->tel != "")
                  $address .= "<div itemprop='telephone'>$el->tel</div>";
                  
                if ($el->fax != "")
                  $address .= "<div itemprop='faxNumber'>$el->fax</div>";
                  
                if ($el->email != "")
                  $address .= "<a href='mailto:$el->email' target='_blank' title='" ._("Envoyer un e-mail") ."' itemprop='email'>$el->email</a><br />";
                  
                if ($el->web != "")
                  $address .= "<a href='$el->web' target='_blank' title='" ._("Visiter le site Internet") ."' itemprop='url'>$el->web</a><br />";
                
                $link = $el->get_url_simple($_SESSION['lang']);
                
                echo "<li itemscope itemtype='http://schema.org/PostalAddress'>";
                        if ($pic !== false){
                          echo "<div class='object_picture'>
                                  <a href='$link' title='" ._("Voir les détails") ."'><img src='$pic' alt=\"$title\" /></a>
                                </div>";
                        }
                  echo "<div class='object_text'>
                          <h2 class='object_title'>
                            $title
                          </h2>
                          <h3 class='object_category'>
                            $el->category
                          </h3>
                          <div class='object_address'>
                            $address
                          </div>
                        </div>
                        <div class='object_details'><a href='$link' title='" ._("Voir les détails") ."'>" ._("Détails") ."</a></div>
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