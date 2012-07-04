<?php

require_once("include/headers.php");

try{
  // create root element
  header('Content-Type: text/xml');
  $sitemap = new DomDocument('1.0', 'utf-8');

  // create root element
  $root = $sitemap->createElement("urlset");
  $sitemap->appendChild($root);

  $root_attr = $sitemap->createAttribute('xmlns'); 
  $root->appendChild($root_attr); 

  $root_attr_text = $sitemap->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9'); 
  $root_attr->appendChild($root_attr_text); 

  $site_url = Miki_configuration::get('site_url');
  if (substr($site_url, 0, 1) != '/')
    $site_url .= "/";
    
  $pages = Miki_page::get_all_pages("position", false, "asc");

  foreach($pages as $page){
    // create child element
    $url = $sitemap->createElement("url");
    $root->appendChild($url);

    $loc = $sitemap->createElement("loc");
    $lastmod = $sitemap->createElement("lastmod");
    $changefreq = $sitemap->createElement("changefreq");
    $priority = $sitemap->createElement("priority");

    $url->appendChild($loc);
    $url_text = $sitemap->createTextNode($site_url .$page->get_url_simple(Miki_language::get_main_code()));
    $loc->appendChild($url_text);

    $url->appendChild($lastmod);
    $lastmod_text = $sitemap->createTextNode(date("Y-m-d"));
    $lastmod->appendChild($lastmod_text);

    $url->appendChild($changefreq);
    $changefreq_text = $sitemap->createTextNode("weekly");
    $changefreq->appendChild($changefreq_text);
    
    $url->appendChild($priority);
    $priority_text = $sitemap->createTextNode("0.5");
    $priority->appendChild($priority_text);
  }
  
  try{
    $news = Miki_news::get_all_news();

    foreach($news as $n){
      // create child element
      $url = $sitemap->createElement("url");
      $root->appendChild($url);
  
      $loc = $sitemap->createElement("loc");
      $lastmod = $sitemap->createElement("lastmod");
      $changefreq = $sitemap->createElement("changefreq");
      $priority = $sitemap->createElement("priority");
  
      $url->appendChild($loc);
      $url_text = $sitemap->createTextNode($site_url .$n->get_url_simple());
      $loc->appendChild($url_text);
  
      $url->appendChild($lastmod);
      $lastmod_text = $sitemap->createTextNode(date("Y-m-d", strtotime($n->date)));
      $lastmod->appendChild($lastmod_text);
  
      $url->appendChild($changefreq);
      $changefreq_text = $sitemap->createTextNode("monthly");
      $changefreq->appendChild($changefreq_text);
      
      $url->appendChild($priority);
      $priority_text = $sitemap->createTextNode("0.8");
      $priority->appendChild($priority_text);
    }
  }
  catch(Exception $e){}

  $file = "sitemap.xml";
  $sitemap->formatOutput = true;
  $fh = fopen($file, 'w') or die("Can't open the sitemap file.");
  fwrite($fh, $sitemap->saveXML());
  fclose($fh);
  
  echo $sitemap->saveXML();
}
catch(Exception $e){
  return false;
}





/**
 * modification du fichier robots.txt pour prendre en compte le sitemap
 */

// créé la ligne qui définit l'emplacement du sitemap
$sitemap = "Sitemap: " .SITE_URL ."/sitemap.xml";

$found = false;
$temp = array();

// si le fichier robots.txt existe
if (file_exists('robots.txt')){
  
  // lit puis modifie le fichier robots.txt
  $lines = file('robots.txt');
  
  foreach ($lines as $line) {
    // si on est sur la ligne qui définit déjà le sitemap
    if (stristr($line, "Sitemap:")){
      // modifie cette ligne
      $temp[] = "$sitemap\n";
      $found = true;
    }
    else{
      $temp[] = $line;
    }
  }
}

// si le sitemap n'est pas encore définit dans le fichier robots.txt on l'ajoute ou que le fichier robots.txt n'existe pas
if (!$found){
  //$temp[] = "\n";
  $temp[] = "\n\n# Sitemap\n";
  $temp[] = "$sitemap\n";
}

// réécrit le fichier htaccess
$fd = @fopen("robots.txt", "w");
if ($fd){
  foreach ($temp as $line){
    fwrite($fd, $line);
  }
}

?>