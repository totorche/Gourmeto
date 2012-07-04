<?php
require_once("../scripts/config.php");
require_once("../include/headers.php");
require_once("../scripts/functions_pictures.php");

header("content-type: application/xml");

if (isset($_GET['nbels']))
  $nb_elements = $_GET['nbels'];
else
  $nb_elements = 10;

$sitename = Miki_configuration::get("sitename");
$site_url = Miki_configuration::get("site_url");

$title_rss = "Actualités $sitename";
$description_rss = "Actualités du site $sitename";
$link_rss = "$site_url/rss/news.php";
$link_news = "$site_url/";


echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<rss version=\"2.0\">\n
  <channel>\n
    <title>$title_rss</title>\n
    <description>$description_rss</description>\n
    <link>$link_rss</link>\n";
    
    $elements = Miki_news::get_all_news("", "", $nb_elements);
    
    foreach($elements as $el){
      //$link_news .= decode($el->title) .'-' .$el->id .'.php';
      echo "<item>\n
              <title>" .htmlspecialchars($el->title) ."</title>\n
              <description>" .htmlspecialchars(strip_tags($el->text)) ."</description>\n
              <pubDate>" .date(DATE_RFC822, strtotime($el->date)) ."</pubDate>\n
              <link>" .$link_news .$el->get_url_simple() ."</link>\n
            </item>\n";
    }
    
    echo "
  </channel>
</rss>";