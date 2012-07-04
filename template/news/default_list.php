<?php
  // définit le nombre maximum de caractères à afficher par news
  $max_caracters = 450;
  
  $date = date("d/m/Y", strtotime($element->date));
  $text = $element->text;
  
  $text = truncate_text(strip_tags($element->text), $max_caracters, true);
  
  if (strlen($text) > $max_caracters){
    $tab_text = explode(" ", $text);
    $text_temp = "";
    $y = 0;
    while(strlen($text_temp) < $max_caracters){
      $text_temp .= $tab_text[$y] ." "; 
      $y++;
    }
    $text = $text_temp ."...";
  }
  
  // masque les vidéos de Vimeo
  /*$text = preg_replace("@http://vimeo.com/([0-9]+)@i", "", $text);
  $text = preg_replace("@http://www.vimeo.com/([0-9]+)@i", "", $text);*/

  // affiche les vidéos de Vimeo
  $text = preg_replace("@http://vimeo.com/([0-9]+)@i", "<iframe src='http://player.vimeo.com/video/$1?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff&amp;autoplay=1' width='580' height='406' frameborder='0' style='margin: 10px 0'></iframe>", $text);
  $text = preg_replace("@http://www.vimeo.com/([0-9]+)@i", "<iframe src='http://player.vimeo.com/video/$1?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff&amp;autoplay=1' width='580' height='406' frameborder='0' style='margin: 10px 0'></iframe>", $text);
  
  // affiche les vidéos de Youtube
  $text = preg_replace("@http://www.youtube.com/watch\?v=([0-9a-z]+)(&feature=[0-9a-z_-]*)?@i", "<iframe title='YouTube video player' width='470' height='293' src='http://www.youtube.com/embed/$1?rel=0&amp;autoplay=1' frameborder='0' allowfullscreen></iframe>", $text);
  
  echo "<li style='text-align: left'>";

          if ($element->picture != "")
            $picture = "pictures/news/thumb/$element->picture";
          else
            $picture = "pictures/no-picture-available.gif";
          
          echo "<div class='news_picture'>
                  <div class='news_overlay'></div>
                  <a href='" .$element->get_url_simple() ."' title='"._('View this news')."'><img src='timthumb.php?src=$picture&w=188&h=100' alt=\"$element->title\" style='margin: 5px 0;' /></a>
                </div>";
          
    echo "<div class='news_text'>
            <div class='news_date'>$date</div>
            <div class='news_title'><a href='" .$element->get_url_simple() ."' title='" ._("View this news") ."'>$element->title</a></div>
            <div class='news_description'>$text</div>
          </div>
        </li>";
?>