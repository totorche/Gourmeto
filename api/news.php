<?php

  include("../class/miki_news.php");
  include("../class/miki_configuration.php");
  
  /**
   * Récupert les news et les retourn sous la forme d'un fichier XML
   * 
   * @return string Les news au format XML      
   */     
  function get_news(){
    // génère les données en XML
    try{
      // récupert les news
      $elements = Miki_news::get_all_news();
      
      $dom = new DomDocument('1.0', 'utf-8');
      
      $root = $dom->createElement("news");
      $dom->appendChild($root);
      
      // parcourt chaque élément
      foreach($elements as $e){
      
        // création de l'élément en cours
        $el = $dom->createElement("news");
        $el->setAttribute("id", $e->id);
        $root->appendChild($el);
        
        $element = $dom->createElement('title');
        $element->appendChild($dom->createTextNode($e->title));
        $el->appendChild($element);
        
        $element = $dom->createElement('text');
        $element->appendChild($dom->createTextNode($e->text));
        $el->appendChild($element);
        
        $element = $dom->createElement('date');
        $element->appendChild($dom->createTextNode($e->date));
        $el->appendChild($element);
        
        $picture = Miki_configuration::get("site_url") ."/pictures/news/" .$e->picture; 
        $element = $dom->createElement('picture');
        $element->appendChild($dom->createTextNode($picture));
        $el->appendChild($element);
        
        $url = Miki_configuration::get("site_url") .'/' .$e->get_url_simple();
        $element = $dom->createElement('url');
        $element->appendChild($dom->createTextNode($url));
        $el->appendChild($element);
      }
      
      $dom->formatOutput = true;
      $dom->preserveWhiteSpace = false; 
      
      // génération du XML
      header("content-type: application/xml");
      return $dom->saveXML();
    }
    catch(Exception $e){
      return false;
    }
  }  
?>