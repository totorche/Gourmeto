<?php
  /**
   * Affiche les tweets d'un compte donné.
   * L'affichage se fait en Ajax afin de ne pas influencer la durée de chargement de la page
   */
   
  $username = "htorche"; // l'utilisateur dont on veut afficher le flux
  $nb_tweets = 2; // le nombre de tweets à afficher
  
  // si on doit afficher les tweets, on les affiche
  if (isset($_REQUEST['print']) && $_REQUEST['print'] == 1){
    $tweets = simplexml_load_file("http://twitter.com/statuses/user_timeline/$username.rss");
    $x = 0;
    
    foreach($tweets->channel->item as $tweet){
      // récupert la date dans le format désiré
      $date = date("d/m/Y - G:m", strtotime($tweet->pubDate));
      
      // supprime le nom de l'utilisateur du tweet
      $text = substr($tweet->description, strlen($username) + 2);
      
      // affiche le tweet 
      echo "<div class='twitter'>
              <p class='tweet'><a href='$tweet->link' target='_blank' title='" ._("Voir ce tweet") ."'>$text</a></p>
              <p class='tweet_date'>$date</p>
            </div>";
      
      // n'affiche que le nombre de tweets désirés
      $x++;
      if ($x == $nb_tweets)
        break;
    }
  }
  // sinon on appelle l'affichage des tweet (Ajax)
  else{
    ?>
    
    <script type="text/javascript">
      $(document).ready(function(){
        // charge les données du compte Twitter en Ajax
        $('#twitter_content').load('box/twitter.php?print=1');
      });
    </script>
    <div id="twitter_content"></div>
    
    <?php
  }
?>