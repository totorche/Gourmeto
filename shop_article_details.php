<head>
  <?php
    // taille de la grande image
    $image_width = 300;
    $image_height = 450;
    
    // taille des vignettes
    $thumb_width = 50;
    $thumb_height = 50;
    
    // définit la quantité maximum d'un article que l'on peut mettre à la fois dans le panier
    $quantity_max = 50;
    
    // vérifie si on utilise la gestion des stock ou pas
    $use_stock = Miki_configuration::get('miki_shop_gestion_stock');
    
    // si pas connecté, on définit le pays par défaut (pour donner un calcul des frais de port par défaut)
    if ($miki_person === false || !($miki_person instanceof Miki_person)){
      $person = new Miki_person();
      $person->country = isset($_SESSION['miki_person_country']) ? $_SESSION['miki_person_country'] : "Suisse";
    }
    else{
      $person = $miki_person;
    }
    
    try{
      // recherche l'article demandé
      $article = new Miki_shop_article($_REQUEST['aid']);
      $name = $article->get_name($_SESSION['lang']);
      $description = $article->get_description($_SESSION['lang']);
      $attributes = $article->get_attributes();
      $price = $article->price;
      $promo = $article->get_promotion();

      if ($promo){
        $old_price = $price;
        $price = number_format($promo,2,'.',"'");
      }
      
      // récupert les taxes
      $taxes = Miki_shop::get_taxes($person->country);
      if (!is_array($taxes))
        $taxes = array();
      
      // calcul et prépare l'affichage des taxes
      $taxes_total = 0;
      $taxes_print = "";
      foreach($taxes as $tax_name=>$tax){
        if ($tax[$person->country] > 0){
          $taxes_total += $tax[$person->country];
          $taxes_print .= $tax_name ." (" .$tax[$person->country] ."%) : " .$tax[$person->country] ." CHF<br />";
        }
      }
      $taxes_total = $taxes_total / 100 * $price;
      
      // définit le nom de l'article comme balise TITLE de la page
      echo "<title2>$name</title2>";
      
      // masque le titre H1 de la page
      $title_h1 = "";
    }catch(Exception $e){
      echo "L'article demandé n'a pas été trouvé.<br /><br /><a href='" .$_SESSION['url_back'] ."' title='Revenir à la page précédente'>Revenir à la page précédente</a>";
    }
    
    // récupert le fil d'arianne
    $breadcrumb = print_breadcrumb_from_article($article->id, "breadcrumb-separator.png");
   ?>

  <script src="scripts/jquery.scrollTo.js" type="text/javascript"></script>
  <script src="scripts/forms.js" type="text/javascript"></script>
  <script src="scripts/shop.js" type="text/javascript"></script>
  
  <script type="text/javascript" src="scripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
  <script type="text/javascript" src="scripts/fancybox/jquery.easing-1.3.pack.js"></script>
  <script type="text/javascript" src="scripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
  <link rel='stylesheet' href='scripts/fancybox/jquery.fancybox-1.3.4.css' type='text/css' media='screen' />
  
  <link rel="stylesheet" href="css/articles.css" />

</head>

<script type="text/javascript">

  // pour la gestion des images
  var pics = new Array();
  var no_pic = 0;
  
  // récupert toutes les images dans un tableau
  $(document).ready(function() {
    pics = $('.article_pictures li');
    
    // pour la Fancybox
    $("a.fancybox").fancybox({
      'transitionIn'      : 'elastic',
      'transitionOut'     : 'elastic'
    });
  });

  // Change d'image
  function change_picture(pic){
    // récupert l'image à masquer
    var pic_old = pics[no_pic];
    
    // récupert l'image à afficher
    var pic_new = pics[pic];
    
    // change les images
    if (pic_new != pic_old){
      $(pic_old).fadeOut("slow");
      $(pic_new).fadeIn("slow");
    }
    
    // stock l'image en cours dans une variable
    no_pic = pic;
  }
  
  function add_article_complete(){
    // met à jour le widget du panier
    update_basket();
    
    // effectue la mise à jour de la disponibilité de l'article
    if ($(".article_quantity").length > 0){
      $(".article_quantity").load("shop_article_disponibilite.php", "aid=<?php echo $article->id; ?>&qty_max=<?php echo $quantity_max; ?>");
    }
    
    // puis affiche la box de réussite
    var el = $("<a href='#ajout_complet'></a>").trigger('click');
    el.fancybox({
      'transitionIn'      : 'elastic',
      'transitionOut'     : 'elastic'
    });
    el.trigger('click');
  }
  
  $(document).ready(function() {
    $("#form_commander_article").validate({
      submitHandler: function(form) {
        form_send(form, {"delay": 2000, "hide": false, "onComplete": add_article_complete});
      }
    });
  });

</script>

<?php
  
  echo $breadcrumb;
  
  // affiche les box de messages de résultats
  print_results();
  
  if (!isset($_REQUEST['aid']) || !is_numeric($_REQUEST['aid'])){
    echo "Aucun article n'a été demandé.<br /><br /><a href='" .$_SESSION['url_back'] ."' title='Revenir à la page précédente'>Revenir à la page précédente</a>";
  }
  else{
      // affiche les grandes images
      echo "<table class='article' itemscope itemtype='http://schema.org/Product'>
              <tr>
                <td rowspan='2' class='article_pictures'>
                  <ul class='article_big_picture' style='width: " .$image_width ."px; height: " .$image_height ."px;'>";

      // s'il y a au moins une image
      if (is_array($article->pictures) && sizeof($article->pictures) > 0){
        foreach($article->pictures as $pic){
          // détermine le lien de l'image
          $pic_url = "pictures/shop_articles/$pic";
          $thumb_url = "pictures/shop_articles/thumb/$pic";
          
          // affiche la grande image
          echo "<li>
                  <meta itemprop='image' content='" .SITE_URL ."/$pic_url' />
                  <a href='$pic_url' class='fancybox'><img src='timthumb.php?src=" .urlencode($pic_url) ."&amp;w=$image_width&amp;h=$image_height' alt=\"". addcslashes($name, '"') ."\" /></a>
                </li>";
        }
      }
      // si aucune image
      else{
        // affiche l'image par défaut
        echo "<li><img src='pictures/no-picture-available.gif' alt=\"". addcslashes($name, '"') ."\" /></li>";
      }
      
      echo "  </ul>";
      
      // affiche les vignettes s'il y a au moins une image
      if (is_array($article->pictures) && sizeof($article->pictures) > 0){
        $x = 0;
        
        echo "<ul class='article_thumbnails' style='width: " .$image_width .";'>";
        
        foreach($article->pictures as $pic){
          // détermine le lien de l'image
          $pic_url = "pictures/shop_articles/thumb/$pic";
          
          // affiche la grande image
          echo "<li><a href='javascript:change_picture($x);'><img src='timthumb.php?src=" .urlencode($pic_url) ."&amp;w=$thumb_width&amp;h=$thumb_height' alt=\"". addcslashes($name, '"') ."\" /></a></li>";
          
          $x++;
        }
        
        echo "</ul>";
      }
      
      echo "</td>
            <td class='article_details'>
              <h2 class='article_name' itemprop='name'>$name</h2>";
              
              if ($promo && isset($old_price)){
                echo "<p class='article_price' itemprop='offers' itemscope itemtype='http://schema.org/Offer'>
                        <span class='old_price'>" .number_format($old_price,2,'.',"'") ." CHF</span><br /><br />
                        <span itemprop='price' class='promo'>" .number_format($price,2,'.',"'") ."</span> <span itemprop='priceCurrency' class='promo'>CHF</span> <span style='font-size: 0.7em; color: #848484;'>HT <br /><br />
                        $taxes_print</span>
                      </p>";
              }
              else{
                echo "<p class='article_price' itemprop='offers' itemscope itemtype='http://schema.org/Offer'>
                        <span itemprop='price'>" .number_format($price,2,'.',"'") ."</span> <span itemprop='priceCurrency'>CHF</span> <span style='font-size: 0.7em; color: #848484;'>HT <br /><br />
                        $taxes_print</span>
                      </p>";
              }
              
              
              echo "<p class='article_ref'>" ._("Référence de l'article") ." : $article->ref</p>
            </td>
            <td rowspan='2' class='article_options'>
              <form action='shop_commander.php' method='post' id='form_commander_article' name='form_commander_article' enctype='multipart/form-data'>
                <input type='hidden' name='aid' value='$article->id' />";
        
                  // ajoute les attributs de l'article
                  foreach($attributes as $a){
                    echo "<div class='article_attribute'>";
                    
                    // si c'est un champ texte
                    if ($a['type'] == 0){
                      echo "<label for='{$a['code']}'>{$a['name']} : </label><input type='text' name='{$a['code']}' id='{$a['code']}' value='' style='width:100px'>";
                    }
                    // si c'est un champ oui/non
                    elseif ($a['type'] == 1){
                      $values = explode("&&", $a['value']);
                      echo "<label for='{$a['code']}'>{$a['name']} : </label><select name='{$a['code']}' id='{$a['code']}'>";
                      
                      foreach($values as $v){
                        echo "  <option value=\"$v\">$v</option>";
                      }
                      
                      echo "</select>";
                    }
                    // si c'est une liste déroulante
                    elseif ($a['type'] == 2){
                      $values = explode("&&", $a['value']);
                      echo "<label for='{$a['code']}'>{$a['name']} : </label><select name='{$a['code']}'>";
                      
                      foreach($values as $v){
                        echo "  <option value=\"$v\">$v</option>";
                      }
                      
                      echo "</select>";
                    }
                    
                    echo "</div>";
                  }
                  
                  // affiche le choix de quantité à commander
                  echo "<div class='article_quantity'>";
                          
                          $_REQUEST['aid'] = $article->id;
                          $_REQUEST['qty_max'] = $quantity_max;
                          include("shop_article_disponibilite.php");
                          
                  echo "</div>
                          
                        <div class='addthis'>
                          <!-- AddThis Button BEGIN -->
                          <div class='addthis_toolbox addthis_default_style '>
                          <a class='addthis_button_facebook_like' fb:like:layout='box_count'></a>
                          <a class='addthis_button_tweet' tw:count='vertical'></a>
                          <a class='addthis_button_google_plusone' g:plusone:size='tall'></a>";
                          
                          // récupert la première image
                          if (is_array($article->pictures) && sizeof($article->pictures) > 0){
                            $pic = SITE_URL ."/pictures/shop_articles/" .current($article->pictures);
                            //$pic = "http://fbwone.no-ip.biz/picshare/pictures/shop_articles/" .$article->pictures[1];
                            echo "<a class='addthis_button_pinterest' pi:pinit:media='$pic' pi:pinit:layout='vertical'></a>";
                          }
                          
                    echo "<!--<a class='addthis_button_preferred_1'><img src='http://fbwone.no-ip.biz/picshare/pictures/facebook.png' /></a>-->
                          <!--<a class='addthis_button_compact'></a>-->
                          <!--<a class='addthis_counter addthis_bubble_style'></a>-->
                          </div>
                          <script type='text/javascript' src='http://s7.addthis.com/js/250/addthis_widget.js#pubid=totorche'></script>
                          <!-- AddThis Button END -->
                        </div>";
            
            // affiche le bouton "acheter"
          echo "</form>
              </td>
            </tr>
            <tr>
              <td class='article_description'>
                <p itemprop='description'>$description</p>
              </td>
            </tr>
          </table>";
  }
?>


<!-- Contenu de la fenêtre qui est affichée lorsqu'un article a été ajouté au panier -->
<div style="display: none;">
  <div style="padding: 10px; background: #ececec;" id="ajout_complet">
    <p style="text-align: center; color: #029fcd; font-size: 1.2em;"><?php echo _("Vous avez ajouté ce produit dans votre panier"); ?></p>
    <div style="background: #ffffff; padding: 20px; font-size: 1.1em; font-weight: bold; margin: 20px 0;">
      <p style="background: url('pictures/true.gif') no-repeat right center; font-size: 1.1em; font-weight: bold; margin: 0;"><?php echo $name; ?></p>
    </div>
    <p style="text-align: center; padding: 20px;">
      <input type="button" class='button1' onclick="$.fancybox.close();" value="<?php echo _("Continuer mes achats"); ?>" style="margin-right: 20px;" />
      <input type="button" class='button_big1' onclick="document.location='[miki_page='shop_panier']'" value="<?php echo _("Valider mon panier"); ?>" />
    </p>
  </div>
</div>