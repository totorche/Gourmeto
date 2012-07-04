<?php
/**
 * Classe abstraite Miki_comment_object
 * @package Miki
 */ 

/**
 * Classe abstraite qui servira de classe parent pour tous les objets supportant les commentaires
 * 
 * @package Miki  
 */
abstract class Miki_comment_object{
  
  /**
   * Ajoute un commentaire à l'objet.
   * 
   * Si l'objet n'est pas encore sauvé dans la base de données, on le sauve. 
   * Le commentaire est testé contre le Spam via l'API d'Akismet   id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean
   */
  public function post_comment($id_person, $comment_text, $rating, $subscribe){
    // si aucun id existe, c'est que l'objet n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      $this->save();
      
    try{
      // ajoute le commentaire
      $comment = new Miki_comment();
      $comment->id_person = $id_person;
      $comment->id_object = $this->id;
      $comment->state = 1;  // si le commentairte est contrôlé comme un Spam, le state vaudra 3 via la fonction test_spam()
      $comment->object_class = get_class($this);
      $comment->comment = $comment_text;
      $comment->rating = $rating;
      $comment->is_subscribed = $subscribe;

      $comment->save();
      
      // et le teste contre le Spam
      $comment->test_spam();
    }
    catch(Exception $e){
      return false;
    }
  }
  
  /**
   * Récupert tous les commentaires de l'objet en cours.
   * 
   * Si $id_person est renseigné, on ne récupert que les commentaires de la personne dont l'id est donné
   *
   * @param int $state Ne récupert que les commentaires dans l'état donné. Si = "", on récupert tous les commentaires
   * @param int $id_person Si donné, récupert uniquement les commentaires de la personne dont l'id est donné
   * @param string $order Par quel commentaire les commentaires trouvés seront triés (author, source, firstname, lastname, state, type, date). Si vide, on tri selon la position.
   * @param string $order_type Tri ascendant (asc) ou descendant (desc)   
   *
   * @return mixed Un tableau d'éléments de type Miki_comment
   */     
  public function get_comments($state = "", $id_person = "", $order = "", $order_type = "asc"){
    // si aucun id existe, c'est que l'objet n'existe pas encore dans la bdd. On s'arrête donc là
    if (!isset($this->id))
      return false;
    
    return Miki_comment::get_all_comments($id_person, $this->id, $state, get_class($this), $order, $order_type, $nb_comments);
  }
  
  /**
   * Supprime tous les commentaires de l'objet en cours.
   * 
   * Si $id_person est renseigné, on ne supprime que les commentaires de la personne dont l'id est donné
   * 
   * @param int $id_person Si donné, supprime uniquement les commentaires de la personne dont l'id est donné
   * @return boolean
   */     
  public function delete_all_comments($id_person = ""){
    // si aucun id existe, c'est que l'objet n'existe pas encore dans la bdd. On s'arrête donc là
    if (!isset($this->id))
      return false;
    
    try{
      Miki_comment::delete_from_object($this->id, get_class($this), $id_person);
      return true;
    }
    catch(Exception $e){
      return false;
    }
  }
  
  /**
   * Récupert la moyenne des évaluations
   * 
   * @param int $state Ne récupert que les commentaires dans l'état donné. Si = "", on récupert tous les commentaires
   * @param boolean $round Si TRUE, arrondi à l'entier le plus proche. Si FALSE, retourne la moyenne réelle en Float       
   * @return float La moyenne des évaluations      
   */     
  public function get_rating($state = "", $round = false){
    $sql = sprintf("SELECT AVG(rating) FROM miki_comment WHERE id_object = %d AND object_class = '%s' AND rating > 0",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string(get_class($this)));
    
    // ne récupert que les commentaires dans l'état donné
    if ($state != "" && is_numeric($state)){
      $sql .= sprintf(" AND state = %d", 
        mysql_real_escape_string($state));
    }
    
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    
    // Arrondi si demandé
    if ($round)
      return round($row[0]);
    else
      return $row[0];
  }
}
?>