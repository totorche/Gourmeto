<?php
  // traduit les liens 'miki' en liens réels
  function set_links(&$code, $lang){  
    // recherche les balises de page
    $start = strpos($code,"[miki_page='");
    while ($start !== false){
      $lang_temp = $lang;
      $params = array();
      // récupert la balise complète
      $stop = strpos($code, "]", $start + 12);
      $data = substr($code, $start, $stop - ($start-1));

      // pour les paramètres
      if ($start_params = strpos($data,"params='")){  
        $stop_params = strpos($data, "']", $start_params + 8);
        $data_params = substr($data, $start_params + 8, $stop_params - ($start_params + 8));
        
        $tab_params = explode("&&", $data_params);

        foreach($tab_params as $param){
          $tab = explode("=", $param);
          if(sizeof($tab) == 2){
            // si on a donné la langue, on va récupérer l'url de la page dans cette langue
            if ($tab[0] == 'l')
              $lang_temp = $tab[1];
            
            $params[$tab[0]] = $tab[1];
          }
        }
      }
      
      // puis uniquement le nom de la page
      $start_page = strpos($data, "'") + 1;
      $stop_page = strpos($data, "'", $start_page);
      $name_page = substr($data, $start_page, $stop_page - $start_page);

      try{
        // récupert l'url de la page
        $page = new Miki_page();
        $page->load_from_name($name_page);
        $page_url = $page->get_url_simple($lang_temp);

        // si il n'y a pas encore de paramètres, on ajoute le '?'
        if(strpos($page_url, '?') === false)
          $page_url .= '?';
        else
          $page_url .= '&amp;';
          
        // puis ajoute les paramètres
        while(list($key, $val) = each($params)){
          // ajoute les paramètres seulement si pas encore présents
          if (!preg_match("/$key=" .str_replace("/", "\/", $val) ."/i", $page_url))
            $page_url .= "$key=$val&amp;";
        }
        
        // supprime le dernier '?'
        if (substr($page_url, -1) == '?')
          $page_url = substr($page_url, 0, strlen($page_url) - 1);
        
        // supprime le dernier '&'  
        if (substr($page_url, -1) == '&')
          $page_url = substr($page_url, 0, strlen($page_url) - 1);

        //$code = str_replace($data, $page_url, $code);
        $code = substr_replace($code, $page_url, $start, $stop - $start + 1);
        $start = strpos($code,"[miki_page='");
      }
      catch(Exception $e){
        $start = false;
      }
    }
  }
?>