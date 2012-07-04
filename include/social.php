<!-- Les 3 icônes pour nos comptes sociaux -->
<!--<div>
  <a href="http://www.twitter.com" title="<?php echo _("Rejoignez-nous sur Twitter !"); ?>"><img src="pictures/twitter.png" alt="<?php echo _("Rejoignez-nous sur Twitter !"); ?>" /></a>
  <a href="http://www.facebook.com" title="<?php echo _("Rejoignez-nous sur Facebook !"); ?>"><img src="pictures/facebook.png" alt="<?php echo _("Rejoignez-nous sur Facebook !"); ?>" /></a>
  <a href="http://www.facebook.com" title="<?php echo _("Suivez nos flux RSS !"); ?>"><img src="pictures/rss.png" alt="<?php echo _("Suivez nos flux RSS !"); ?>" /></a>
</div>-->

<!-- Le bouton "J'aime", "Twitter" et "Google +1" -->
<div class="social">
  <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/fr_FR/all.js#xfbml=1";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));</script>
  
  <!-- Placez cet appel d'affichage à l'endroit approprié. -->
  <script type="text/javascript">
    window.___gcfg = {lang: 'fr'};
  
    (function() {
      var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
      po.src = 'https://apis.google.com/js/plusone.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    })();
  </script>
  
  <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
  
  <div class="share_button"><div class="g-plusone" data-size="tall"></div></div>
  
  <div class="share_button"><a href="https://twitter.com/share" class="twitter-share-button" data-lang="fr" data-count="vertical">Tweeter</a></div>
  
  <div class="share_button"><div class="fb-like" data-send="false" data-layout="box_count" data-width="450" data-show-faces="false"></div></div>
</div>