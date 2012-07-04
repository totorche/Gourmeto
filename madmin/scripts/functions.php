<?php
  /**
   * Retourne une chaîne tronquée selon les paramètres donnés
   * 
   * @param string $text Le texte à tronquer
   * @param int $nb_char Nombre de caractères désirés
   * @param boolean $full_word Si TRUE, le texte est coupé par mots entiers. Si FALSE, les mots peuvent être coupé en plein milieu. True par défaut.
   * @param boolean $strip_tags Si TRUE, supprime au préalable tous les tags HTML. Si False, conserve tout. True par défaut. 
   *   
   * @return string Le texte tronqué
   */               
  function truncate_text($text, $nb_char, $full_word = true, $strip_tags = true){
    
    if ($strip_tags)
      $text = strip_tags($text);
      
    if ($nb_char < mb_strlen($text)){
      if ($full_word === false)
        $stop = $nb_char;
      else{
        $stop = mb_strpos($text, ' ', $nb_char);
        if ($stop === false){
          $stop = mb_strlen($text);
        }
      }
      return mb_substr($text, 0, $stop) ."...";
    }
    else
      return $text;
  }
?>