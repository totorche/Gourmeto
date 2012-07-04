<?php
/**
 * Classe Miki_configuration
 * @package Miki
 */ 

/**
 * Configuration du site web.
 * 
 * Cette classe permet de stocker et de récupérer des éléments de configuration du site Internet.  
 * 
 * @package Miki  
 */ 
class Miki_configuration{

  /**
   * Recherche la valeur d'une configuration donnée
   * 
   * @param string $name Nom de la configuration dont on veut connaître la valeur
   * @return mixed La valeur de la configuration recherchée si elle existe ou False si elle n'existe pas.
   */         
  public static function get($name){
    $sql = sprintf("SELECT value FROM miki_configuration WHERE name = '%s'",
      mysql_real_escape_string($name));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0){
      $row = mysql_fetch_array($result);
      return $row[0];
    }
    else
      return false;
  }
  
  /**
   * Ajoute ou modifie une configuration
   * 
   * Si une erreur survient, une exception est levée
   * 
   * @param string $name Nom de la configuration
   * @param string value Valeur de la configuration            
   */   
  public static function add($name, $value){
    $sql = sprintf("INSERT INTO miki_configuration (name, value) values ('%s', '%s') ON DUPLICATE KEY UPDATE value = '%s'",
      mysql_real_escape_string($name),
      mysql_real_escape_string($value),
      mysql_real_escape_string($value));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Une erreur est survenue lors de l'ajout de la configuration : $sql"));
  }
  
  /**
   * Supprime une configuration donnée
   * 
   * @param string $name Nom de la configuration à supprimer      
   */   
  public static function remove($name){
    $sql = sprintf("DELETE FROM miki_configuration WHERE name = '%s'",
      mysql_real_escape_string($name));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Une erreur est survenue lors de la suppression de la configuration"));
  }
  
  /**
   * Recherche toutes les configurations enregistrées
   * 
   * @return mixed Un tableau contenant toutes les configurations trouvées. L'indice du tableau représente le nom de la configuration et l'élément correspond à sa valeur.
   */         
  public static function get_all(){
    $return = array();
    $sql = "SELECT name, value FROM miki_configuration";
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $return[$row['name']] = $row['value'];
    }
    return $return;
  }
}
?>