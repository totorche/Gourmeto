<?php
  /**
   * Affiche une liste des différents événements
   */
   
  require_once("config/event_category.php");
   
  if (isset($_REQUEST['p']) && is_numeric($_REQUEST['p']))
    $p = $_REQUEST['p'];
  else
    $p = 1;
    
  $max_elements = 10;
  $nb_elements = 0;
  $image_width = 150; // en pixels
  $image_height = 150; // en pixels

  $elements = Miki_event::get_all_events("", false, "", $max_elements, $p, $nb_elements);
?>

<head>
  <link rel="stylesheet" href="css/events.css" />
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
    echo "<div>" ._("Aucun événement n'a été trouvé") ."</div>";
  }
  else{
  
    echo "<div class='events'>
            <ul class='main_table" .(($display_type == "list") ? " list_display" : "") ."'>";
              foreach($elements as $el){
                
                //$pictures = $el->get_pictures($_SESSION['lang']);
                $pictures = $el->get_pictures();

                if (is_array($pictures) && sizeof($pictures) > 0)
                  $pic = "timthumb.php?src=" .urlencode($el->file_path .'thumb/' .current($pictures)) ."&w=$image_width&h=$image_height";
                else
                  $pic = false;

                $title = $el->title[$_SESSION['lang']];
                $date_start = date("d/m/Y", strtotime($el->date_start));
                $date_stop = (date("d/m/Y", strtotime($el->date_stop)));
                $date_start_rfc = date("Y-m-d", strtotime($el->date_start));
                $date_stop_rfc = date("Y-m-d", strtotime($el->date_stop));
                $date_text = "<time itemprop='startDate' datetime='$date_start_rfc'>$date_start</time> - <time itemprop='endDate' datetime='$date_stop_rfc'>$date_stop</time>";
                
                $address = "";
                
                if ($el->city != "")
                  $address .= "<div itemprop='addressLocality'>$el->city</div>";
                
                if ($el->region != "" && $el->country != "")
                  $address .= "<div><span itemprop='addressRegion'>$el->region</span>, <span itemprop='addressCountry'>$el->country</span></div>";
                elseif ($el->country != "")
                  $address .= "<div itemprop='addressCountry'>$el->country</div>";
                
                $link = $el->get_url_simple($_SESSION['lang']);
                
                echo "<li itemscope itemtype='http://schema.org/PostalAddress'>";
                        if ($pic !== false){
                          echo "<div class='event_picture'>
                                  <a href='$link' title='" ._("Voir les détails") ."'><img src='$pic' alt=\"$title\" /></a>
                                </div>";
                        }
                  echo "<div class='event_text'>
                          <div class='event_date'>
                            <img src='pictures/calendar.png' alt='Date' />
                            $date_text
                          </div>
                          <h2 class='event_title'>
                            $title
                          </h2>
                          <h3 class='event_category'>
                            {$categories[$el->category]}
                          </h3>
                          <div class='event_address'>
                            $address
                          </div>
                        </div>
                        <div class='event_details'><a href='$link' title='" ._("Voir les détails") ."'>" ._("Détails") ."</a></div>
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