<?php

$date = explode(" ", $element->date);
$date = explode("-", $date[0]);
$date = $date[2] ."/" .$date[1] ."/" .$date[0];

$text = $element->text;

// affiche les vidéos de Vimeo
$text = preg_replace("@http://vimeo.com/([0-9]+)@i", "<iframe src='http://player.vimeo.com/video/$1?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff&amp;autoplay=1' width='470' height='293' frameborder='0' style='margin: 10px 0'></iframe>", $text);
$text = preg_replace("@http://www.vimeo.com/([0-9]+)@i", "<iframe src='http://player.vimeo.com/video/$1?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff&amp;autoplay=1' width='580' height='406' frameborder='0' style='margin: 10px 0'></iframe>", $text);

// affiche les vidéos de Youtube
$text = preg_replace("@http://www.youtube.com/watch\?v=([0-9a-z]+)(&feature=[0-9a-z_-]*)?@i", "<iframe title='YouTube video player' width='470' height='293' src='http://www.youtube.com/embed/$1?rel=0&amp;autoplay=1' frameborder='0' allowfullscreen></iframe>", $text);
?>

<style type="text/css">
  p{
    margin: 10px 0;
  }
</style>

<title2><?php echo $element->get_title(65, true); ?></title2>
<description><?php echo $element->get_text(150, true, false); ?></description>
  
        
<h1>
  <span style='float:right;margin-left:10px;color:#898989'><?php echo $date; ?></span>
  <?php echo $element->title; ?>
</h1>

<?php
  echo "
    <div style='width:100%;margin-bottom:20px'>
      <div style='overflow:hidden'>";
        
        if ($element->picture != "")
          $picture = "pictures/news/thumb/$element->picture";
        else
          $picture = "pictures/no-picture-available.gif";
            
        echo "<img src='$picture' title=\"$element->title\" alt=\"$element->title\" style='float:left;margin:0 10px 10px 0' />";

        echo "$text
      </div>
    </div>
  ";
?>