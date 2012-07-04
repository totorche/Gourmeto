<head>
  <?php
    /**
     * Affiche une bonne adresse
     */       
     
    // récupert la bonne adresse
    if (isset($_REQUEST['eid']) && is_numeric($_REQUEST['eid'])){
      try{
        $element = new Miki_object($_REQUEST['eid']);
      }
      catch(Exception $e){
        miki_redirect($_SESSION['url_back']);
      }
    }
    else{
      miki_redirect($_SESSION['url_back']);
    }
    
    // définit la taille des images  
    $image_width = 300; // en pixels
    $image_height = 200; // en pixels
    
    $title = $element->title[$_SESSION['lang']];
    $description = $element->description[$_SESSION['lang']];
    $address = "";
    
    if ($element->address != "")
      $address .= "<div itemprop='streetAddress'>$element->address</div>";
      
    if ($element->npa != "" && $element->city != "")
      $address .= "<div><span itemprop='postalCode'>$element->npa</span> <span itemprop='addressLocality'>$element->city</span></div>";
    elseif ($element->city != "")
      $address .= "<div itemprop='addressLocality'>$element->city</div>";
    
    if ($element->region != "" && $element->country != "")
      $address .= "<div><span itemprop='addressRegion'>$element->region</span> - <span itemprop='addressCountry'>$element->country</span></div>";
    elseif ($el->country != "")
      $address .= "<div itemprop='addressCountry'>$element->country</div>";
    
    if ($element->tel != "")
      $address .= "<div itemprop='telephone'>$element->tel</div>";
      
    if ($element->fax != "")
      $address .= "<div itemprop='faxNumber'>$element->fax</div>";
      
    if ($element->email != "")
      $address .= "<a href='mailto:$element->email' target='_blank' title='" ._("Envoyer un e-mail") ."' itemprop='email'>$element->email</a><br />";
      
    if ($element->web != "")
      $address .= "<a href='$element->web' target='_blank' title='" ._("Visiter le site Internet") ."' itemprop='url'>$element->web</a><br />";
    
    $link = $element->get_url_simple($_SESSION['lang']);
    
    // affecte le titre à la page (modifie le titre du gabarit)
    $title_h1 = $title;
  ?>

  
  
  <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
  <script type="text/javascript" src="scripts/gmap3.min.js"></script>
  
  <link rel="stylesheet" href="scripts/slider-evolution/themes/carbono/jquery.slider.css" />
  <script type="text/javascript" src="scripts/slider-evolution/jquery.slider.min.js"></script>
  
  <link rel="stylesheet" href="css/objects.css" />
</head>

<script type="text/javascript">
  $(document).ready(function() {
    
    // pour l'affichage de la carte Google Map
    $('.object_map').gmap3(
      { action: 'addMarker',
        address: "<?php echo addcslashes("$element->address $element->npa $element->city $element->country", '"'); ?>",
        infowindow:{
          options:{
            content: "<?php echo $title; ?>"
          }
        },
        map:{
          center: true,
          zoom: 15
        }
      }
    );
    
    // pour l'affichage des images et du slide si plusieurs images
    $(".object_pictures").slideshow({
      width      : <?php echo $image_width; ?>,
      height     : <?php echo $image_height; ?>,
      transition : 'square',
      slideshow  : false,
      navigation : true,
      control    : false
    });
  });
  
</script>

<?php
echo "<div class='object' itemscope itemtype='http://schema.org/PostalAddress'>
        <div class='object_pictures_container'>
          <div class='object_pictures'>";
            foreach($element->pictures as $pic){
              echo "<div><img src='timthumb.php?src=" .urlencode($element->picture_path .$pic) ."&w=$image_width&h=$image_height' alt=\"$title\" /></div>";
            }
    echo "</div>
        </div>
        <div class='object_text'>
          <h2 class='object_title'>
            $title
          </h2>
          <h3 class='object_category'>
            $element->category
          </h3>
          <div class='object_description'>
            $description
          </div>
          <div class='object_address'>
            $address
          </div>
        </div>
        <div class='object_map'></div>
      </div>";
?>