<style type="text/css">

  .shop_frise{
    position: relative;
    margin: 0 auto;
    height: 200px;
  }
  
  .shop_frise .line{
    position: absolute;
    top: 85px;
    width: 600px;
    height: 3px;
    background: url(pictures/shop_frise/line.gif) repeat-x left top;
  }
  
  .shop_frise p{
    margin: 0;
    padding: 1em 1.5em;
    text-align: center;
    color: #FFFFFF;
    font-weight: bold;
    font-size: 1.2em;
  }
  
  .shop_frise .selected{
    position: absolute;
    top: 68px;
    left: 0px;
    width: 100%;
    height: 38px;
  }
  
  .shop_frise .unselected{
    position: absolute;
    top: 68px;
    left: 0px;
    width: 100%;
    height: 38px;
  }
  
  .shop_frise .arrow{
    position: absolute;
    bottom: -17px;
    left: 0px;
    width: 100%;
    height: 17px;
  }
  
  .shop_frise .selected,
  .shop_frise .arrow,
  .shop_frise p{
    filter: alpha(opacity=0);
    -khtml-opacity: 0;
    -moz-opacity: 0;
    opacity: 0;
  }
  
  .shop_frise_1{
    position: absolute;
    top: 0px;
    min-width: 38px;
  }
  
  .shop_frise_1 p{
    background: #000CFF;
  }
  
  .shop_frise_1 .selected{
    background: url(pictures/shop_frise/step_1_2.gif) no-repeat center top;
  }
  
  .shop_frise_1 .unselected{
    background: url(pictures/shop_frise/step_1_1.gif) no-repeat center top;
  }
  
  .shop_frise_1 .arrow{
    background: url(pictures/shop_frise/step_1_3.gif) no-repeat center -11px;
  }
 
  .shop_frise_2{
    position: absolute;
    top: 0px;
    min-width: 38px;
  }
  
  .shop_frise_2 p{
    background: #029FCD;
  }
  
  .shop_frise_2 .selected{
    background: url(pictures/shop_frise/step_2_2.gif) no-repeat center top;
  }
  
  .shop_frise_2 .unselected{
    background: url(pictures/shop_frise/step_2_1.gif) no-repeat center top;
  }
  
  .shop_frise_2 .arrow{
    background: url(pictures/shop_frise/step_2_3.gif) no-repeat center -11px;
  }
  
  .shop_frise_3{
    position: absolute;
    top: 0px;
    min-width: 38px;
  }
  
  .shop_frise_3 p{
    background: #FF9000;
  }
  
  .shop_frise_3 .selected{
    background: url(pictures/shop_frise/step_3_2.gif) no-repeat center top;
  }
  
  .shop_frise_3 .unselected{
    background: url(pictures/shop_frise/step_3_1.gif) no-repeat center top;
  }
  
  .shop_frise_3 .arrow{
    background: url(pictures/shop_frise/step_3_3.gif) no-repeat center -11px;
  }
  
  .shop_frise_4{
    position: absolute;
    top: 0px;
    min-width: 38px;
  }
  
  .shop_frise_4 p{
    background: #FF0072;
  }
  
  .shop_frise_4 .selected{
    background: url(pictures/shop_frise/step_4_2.gif) no-repeat center top;
  }
  
  .shop_frise_4 .unselected{
    background: url(pictures/shop_frise/step_4_1.gif) no-repeat center top;
  }
  
  .shop_frise_4 .arrow{
    background: url(pictures/shop_frise/step_4_3.gif) no-repeat center -11px;
  }

</style>

<script type="text/javascript">
  
  $(document).ready(function() {
    var base_left = 0;
    var espace = 200;
    var el = null;
    
    // place les éléments représentant les différentes étapes
    for(x=1; x<=4; x++){
      el = $(".shop_frise_" + x);
      if (base_left == 0){
        base_left = el.outerWidth() / 2;
      }
      else{
        el.css('left', base_left + ((x - 1) * espace) - (el.outerWidth() / 2));
      }
    }
    
    // définit la grandeur de la frise afin de la centrer
    $(".shop_frise").width(parseInt(el.css('left')) + parseInt(el.outerWidth()));
    
    // positionne la barre grise
    $(".shop_frise .line").css('left', base_left);
  });
  
</script>

<?php
  function print_frise($step){
?>
    <style type="text/css">
      .shop_frise_<?php echo $step; ?> .selected,
      .shop_frise_<?php echo $step; ?> .arrow,
      .shop_frise_<?php echo $step; ?> p{
        filter: alpha(opacity=100);
        -khtml-opacity: 1;
        -moz-opacity: 1;
        opacity: 1;
      }
      
      <?php
        for ($x = 1; $x <= $step; $x++){
          echo ".shop_frise_$x:hover .selected,
                .shop_frise_$x:hover .unselected,
                .shop_frise_$x:hover .arrow,
                .shop_frise_$x:hover p
                {
                  cursor: pointer;
                  filter: alpha(opacity=100);
                  -khtml-opacity: 1;
                  -moz-opacity: 1;
                  opacity: 1;
                }";
        }
        
        for ($x = $step + 1; $x <= 4; $x++){
          echo ".shop_frise_$x:hover .selected,
                .shop_frise_$x:hover .arrow,
                .shop_frise_$x:hover p
                {
                  filter: alpha(opacity=25);
                  -khtml-opacity: 0.25;
                  -moz-opacity: 0.25;
                  opacity: 0.25;
                }";
        }
      ?>
    </style>
    
    <script type="text/javascript">
      /* Ajoute les liens sur les étapes déjà passées pour revenir à ces étapes */
      $(document).ready(function() {
        <?php
        
          $url[1] = "[miki_page='shop_panier']";
          $url[2] = "[miki_page='login' params='frise=1']";
          $url[3] = "[miki_page='shop_livraison_paiement']";
          $url[4] = "[miki_page='shop_controle']";
          
          for ($x = 1; $x <= $step; $x++){
            echo "$('.shop_frise_$x').click(function(){
              document.location='{$url[$x]}';
            });";
          }
        ?>
      });
    </script>

    <div class="shop_frise">
      <div class="line"></div>
      
      <div class="shop_frise_1">
        <p><?php echo _("Panier"); ?></p>
        <div class="selected"></div>
        <div class="unselected"></div>
        <div class="arrow"></div>
      </div>
    
      <div class="shop_frise_2">
        <p><?php echo _("Identification"); ?></p>
        <div class="selected"></div>
        <div class="unselected"></div>
        <div class="arrow"></div>
      </div>
      
      <div class="shop_frise_3">
        <p><?php echo _("Livraison / Paiement"); ?></p>
        <div class="selected"></div>
        <div class="unselected"></div>
        <div class="arrow"></div>
      </div>
      
      <div class="shop_frise_4">
        <p><?php echo _("Contrôle"); ?></p>
        <div class="selected"></div>
        <div class="unselected"></div>
        <div class="arrow"></div>
      </div>
    
    </div>    
<?php
  }
?>