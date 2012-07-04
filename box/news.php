<div class="miki-box-news">
  
  <?php
    // définit le nombre max de news à afficher par page
    $max_news = 2;
    
    // définit le nombre maximum de caractères à afficher par news
    $max_caracters = 90;
    
    // détermine si on affiche l'image (true) ou pas (false)
    $print_pictures = true;
    
    $news = Miki_news::get_all_news($_SESSION['lang'], "", $max_news);

    foreach ($news as $n){
      $date = date("d/m/Y", strtotime($n->date));
    
      $n->text = preg_replace("/<br( \/)?\>/i", "", $n->text);
      
      $texte = $n->title;

      if (strlen($texte) > $max_caracters){
        $tab_texte = explode(" ", $texte);
        $texte_temp = "";
        $x = 0;
        while(strlen($texte_temp) < $max_caracters){
          $texte_temp .= $tab_texte[$x] ." "; 
          $x++;
        }
        $texte = $texte_temp ."...";
      }                          
      
      echo "<div class='miki-news' style='width: 100%; overflow: hidden;'>";
              
              if ($print_pictures && $n->picture != ""){
                echo "<p style='float:left;margin-right:10px;width:70px;height:70px;position:relative'>
                        <img src='timthumb.php?src=pictures/news/thumb/$n->picture&amp;h=70&amp;w=70' alt=\"$n->title\" />
                      </p>";
              }
              
        echo "<p class='miki-news-text'><a href='" .$n->get_url_simple() ."' title='Voir cette news'>$texte</a></p>
              <p class='miki-news-date'>$date</p>
            </div>";
    }                              
  ?> 
  
  <div style="clear:both;text-align:right;margin-top:20px">
    <a href="[miki_page='actualites']" title="Voir toutes les actualités">Voir tous</a>
  </div>
  
</div>