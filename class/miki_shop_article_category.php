<?php
/**
 * Classe Miki_shop_article_category
 * @package Miki
 */ 

/**
 * Représentation d'une catégorie d'articles.
 * 
 * Un article (Miki_shop_article) fait partie d'une catégorie.
 * Les catégories sont hiérarchisée. Elle peuvent avoir des parents, des enfants, etc.
 * 
 * Ici, dans un souci d'optimisation, l'algorithme "Nested Set Model" ({@link http://dev.mysql.com/tech-resources/articles/hierarchical-data.html}) a été utilisé   
 * 
 * @see Miki_shop_article
 *  
 * @package Miki  
 */
class Miki_shop_article_category{
  
  /**
   * Id de la catégorie
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * Nom de la catégorie
   * 
   * Un tableau comportant le nom dans les différentes langues. L'indice du tableau correspond au code de la langue (Code ISO 639-1 ({@link http://fr.wikipedia.org/wiki/Liste_des_codes_ISO_639-1})).      
   *      
   * @var int
   * @access public   
   */
  public $name;
  
  /**
   * Champ utilisé dans l'algorithme
   * 
   * @access private       
   */     
  private $lft;
  
  /**
   * Champ utilisé dans l'algorithme
   * 
   * @access private       
   */
  private $rgt;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge la catégorie dont l'id a été donné
   * 
   * @param int $id Id de la catégorie à charger (optionnel)
   */
  function __construct($id = ""){
    // charge la catégorie si l'id est fourni
    if (!empty($id))
      $this->load($id);
  }
  
  /**
   * Charge une catégorie depuis un id
   *    
   * Si la catégorie n'existe pas, une exception est levée.
   *    
   * @param int $id id de la catégorie à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT mc.id, mc.lft, mc.rgt, mcn.name, ml.code language_code
                    FROM miki_shop_article_category mc,
                         miki_shop_article_category_name mcn,
                         miki_language ml
                    WHERE mcn.category_id = mc.id
                      AND  ml.code = mcn.language_code
                      AND  mc.id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql");
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La catégorie demandée n'existe pas"));
    while ($row = mysql_fetch_array($result)){
      $this->id = $row['id'];
      $this->name[$row['language_code']] = $row['name'];
      $this->lft = $row['lft'];
      $this->rgt = $row['rgt'];
    }
    return true;
  }
  
  /**
   * Sauvegarde la catégorie dans la base de données.
   * 
   * La catégorie doit posséder un nom, sinon une exception est levée
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été attribué à la catégorie"));
    
    if (isset($this->id) && is_numeric($this->id)){
      $sql = sprintf("INSERT INTO miki_shop_article_category (id, lft, rgt) VALUES(%d, %d, %d)
                      ON DUPLICATE KEY UPDATE lft = %d, rgt = %d",
                mysql_real_escape_string($this->id),
                mysql_real_escape_string($this->lft),
                mysql_real_escape_string($this->rgt),
                mysql_real_escape_string($this->lft),
                mysql_real_escape_string($this->rgt),
                mysql_real_escape_string($this->id));
                
      $result = mysql_query($sql);
      if (!$result){
        throw new Exception(_("Erreur lors de l'insertion de la catégorie dans la base de données : ") ."<br />" .mysql_error());
      }
    }
    else{
      $sql = sprintf("INSERT INTO miki_shop_article_category (lft, rgt) VALUES(%d, %d)",
                mysql_real_escape_string($this->lft),
                mysql_real_escape_string($this->rgt));
                
      $result = mysql_query($sql);
      if (!$result){
        throw new Exception(_("Erreur lors de l'insertion de la catégorie dans la base de données : ") ."<br />" .mysql_error());
      }
      // récupert l'id
      $this->id = mysql_insert_id();
    }
    
    // ajoute le nom de la catégorie dans chaque langue
    foreach($this->name as $key => $n){
      $sql = sprintf("INSERT INTO miki_shop_article_category_name (category_id, language_code, name) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string(htmlspecialchars($n, ENT_NOQUOTES, "UTF-8", false)));
      $result = mysql_query($sql);
      if (!$result){
        throw new Exception(_("Erreur lors de l'insertion de la catégorie dans la base de données : ") ."<br />" .mysql_error());
      }
    }
    
    // recharge la catégorie
    $this->load($this->id);
    return true;
  }
  
  /**
   * Met à jour la catégorie dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * La catégorie doit posséder un nom, sinon une exception est levée     
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->name))
      throw new Exception(_("Aucun nom n'a été attribué à la catégorie"));
      
    // si aucun id existe, c'est que la catégorie n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // débute la transaction
    mysql_query("START TRANSACTION");
    
    $sql = sprintf("UPDATE miki_shop_article_category SET lft = %d, rgt = %d WHERE id = %d",
      mysql_real_escape_string($this->lft),
      mysql_real_escape_string($this->rgt),
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de la catégorie dans la base de données : ") ."<br />" .mysql_error());
    }
    
    // supprime le nom de la catégorie dans chaque langue
    $sql = sprintf("DELETE FROM miki_shop_article_category_name WHERE category_id = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result){
      // annule les modifications
      mysql_query("ROLLBACK");
      
      throw new Exception(_("Erreur lors de la mise à jour de la catégorie dans la base de données : ") ."<br />" .mysql_error());
    }
    
    // ajoute le nom de la catégorie dans chaque langue
    foreach($this->name as $key => $n){
      $sql = sprintf("INSERT INTO miki_shop_article_category_name (category_id, language_code, name) VALUES(%d, '%s', '%s')",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($key),
        mysql_real_escape_string(htmlspecialchars($n, ENT_NOQUOTES, "UTF-8", false)));
      $result = mysql_query($sql);
      if (!$result){
        // annule les modifications
        mysql_query("ROLLBACK");
      
        throw new Exception(_("Erreur lors de la mise à jour de la catégorie dans la base de données : ") ."<br />" .mysql_error());
      }
    }
    
    // applique les modifications
    mysql_query("COMMIT");
    
    return true;
  }
  
  /**
   * Supprime la catégorie et réaffecte les enfants au besoin
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    // débute la transaction
    mysql_query("START TRANSACTION");
    
    try{
      $sql = sprintf("SELECT lft, rgt FROM miki_shop_article_category WHERE id = %d",
                mysql_real_escape_string($this->id));
      if (!$result = mysql_query($sql))
        throw new Exception("Erreur SQL");
      $row = mysql_fetch_array($result);
      $left = $row[0];
      $right = $row[1];
      $width = $right - $left + 1;
      
      // supprime la catégorie
      $sql = sprintf("DELETE FROM miki_shop_article_category WHERE lft = %d",
                mysql_real_escape_string($left));
      if (!$result = mysql_query($sql))
        throw new Exception("Erreur SQL");
      
      // met à jour les autres catégories
      $sql = sprintf("UPDATE miki_shop_article_category SET rgt = rgt - 1, lft = lft - 1 WHERE lft BETWEEN %d AND %d",
                mysql_real_escape_string($left),
                mysql_real_escape_string($right));
      if (!$result = mysql_query($sql))
        throw new Exception("Erreur SQL");
        
      $sql = sprintf("UPDATE miki_shop_article_category SET rgt = rgt - 2 WHERE rgt > %d",
                mysql_real_escape_string($right));
      if (!$result = mysql_query($sql))
        throw new Exception("Erreur SQL");
        
      $sql = sprintf("UPDATE miki_shop_article_category SET lft = lft - 2 WHERE lft > %d",
                mysql_real_escape_string($right));
      if (!$result = mysql_query($sql))
        throw new Exception("Erreur SQL");
      
      // applique les modifications
      mysql_query("COMMIT");
    }
    catch(Exception $e){
      // annule les modifications
      mysql_query("ROLLBACK");
      exit("Une erreur est survenue dans la requête sql");
    }
  }
  
  /**
   * Recherche les articles (Miki_shop_article) faisant partie de cette catégorie
   * 
   * @see Miki_shop_article   
   * @return mixed Un tableau contenant des éléments de type Miki_shop_article représentant les articles trouvés
   */   
  public function get_articles(){
    $return = array();
    $sql = sprintf("SELECT id FROM miki_shop_article WHERE id_category = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_shop_article($row[0]);
    }
    return $return;
  }
  
  /**
   * Teste si la catégorie possède des catégories enfants
   * 
   * @param int $cat_id Si != "", on recherche un enfant dont l'id = $cat_id
   * 
   * @return boolean      
   */   
  public function has_children($cat_id = ""){
    $sql = "SELECT count(node.id)
            FROM miki_shop_article_category AS node,
                 miki_shop_article_category AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
              AND parent.id = %d
              AND node.id != %d";
              
    if($cat_id !== "" && is_numeric($cat_id)){
      $sql .= " AND node.id = %d";
      
      $sql = sprintf($sql, 
                mysql_real_escape_string($this->id),
                mysql_real_escape_string($this->id),
                mysql_real_escape_string($cat_id));
    }
    else{
      $sql = sprintf($sql, 
                mysql_real_escape_string($this->id),
                mysql_real_escape_string($this->id));
    }

    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql");
    $row = mysql_fetch_array($result);
    return $row[0] > 0;
  }
  
  /**
   * Teste si la catégorie a un parent
   * 
   * @return boolean      
   */   
  public function has_parent(){
    $sql = sprintf("SELECT count(parent.id)
                    FROM miki_shop_article_category AS node,
                         miki_shop_article_category AS parent
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt
                      AND node.id = %d
                      AND parent.id != %d", 
           mysql_real_escape_string($this->id),
           mysql_real_escape_string($this->id));
    
    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql");
    $row = mysql_fetch_array($result);
    
    return $row[0] > 0;
  }
  
  /**
   * Recherche les catégories enfants de premier niveau
   *  
   * @param boolean $empty Si true, prend toutes les catégories. Si false, prend uniquement les catégories qui ont des articles en vente
   * 
   * @return mixed Un tableau d'éléments Miki_shop_article_category représentant les catégories trouvées
   */ 
  public function get_children($empty = true){
    $return = array();
    
    if ($empty){
      $sql = "SELECT node.id, (COUNT(parent.id) - (sub_tree.depth + 1)) AS depth
              FROM miki_shop_article_category AS node,
              	   miki_shop_article_category AS parent,
              	   miki_shop_article_category AS sub_parent,
              	   (
              		    SELECT node.id, (COUNT(parent.id) - 1) AS depth
              		    FROM miki_shop_article_category AS node,
              		         miki_shop_article_category AS parent
              		    WHERE node.lft BETWEEN parent.lft AND parent.rgt
              		      AND node.id = %d
              		    GROUP BY node.id
              		    ORDER BY node.lft
              	   )AS sub_tree
                                                 
              WHERE node.lft BETWEEN parent.lft AND parent.rgt
              	AND node.lft BETWEEN sub_parent.lft AND sub_parent.rgt
              	AND sub_parent.id = sub_tree.id
              	AND node.id != %d
              	
              GROUP BY node.id
              HAVING depth <= 1
              ORDER BY node.lft";
    }                    
    else{
      $sql = "SELECT node.id, (COUNT(parent.id) - (sub_tree.depth + 1)) AS depth, nb_articles.nb as nb
              FROM miki_shop_article_category AS node,
              	   miki_shop_article_category AS parent,
              	   miki_shop_article_category AS sub_parent,
              	   (
              		    SELECT node.id, (COUNT(parent.id) - 1) AS depth
              		    FROM miki_shop_article_category AS node,
              		         miki_shop_article_category AS parent
              		    WHERE node.lft BETWEEN parent.lft AND parent.rgt
              		      AND node.id = %d
              		    GROUP BY node.id
              		    ORDER BY node.lft
              	   )AS sub_tree,
                  (
                    SELECT parent.id, COUNT(article.id) as nb
                    FROM miki_shop_article_category AS node,
                         miki_shop_article_category AS parent,
                         miki_shop_article AS article
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt
                      AND node.id = article.id_category
                      AND article.date_stop > NOW() 
                      AND article.state = 1
                    GROUP BY parent.id
                 ) AS nb_articles
                    
              WHERE node.lft BETWEEN parent.lft AND parent.rgt
              	AND node.lft BETWEEN sub_parent.lft AND sub_parent.rgt
              	AND sub_parent.id = sub_tree.id
              	AND nb_articles.id = node.id
              	AND node.id != %d
              	
              GROUP BY node.id
              HAVING depth <= 1 AND nb > 0
              ORDER BY node.lft";
    }                
        
    $sql = sprintf($sql,  
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($this->id));
     
    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql");
    
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_shop_article_category($row[0]);
    }
    
    return $return;
  }
  
  /**
   * Recherche toutes les catégories enfants récursivement
   *  
   * @param boolean $empty Si true, prend toutes les catégories. Si false, prend uniquement les catégories qui ont des articles en vente
   * 
   * @return mixed Un tableau d'éléments Miki_shop_article_category représentant les catégories trouvées
   */ 
  public function get_all_children($empty = true){
    $return = array();
    
    if ($empty){
      $sql = sprintf("SELECT node.id 
                        FROM miki_shop_article_category node,
                             miki_shop_article_category parent
                        WHERE node.lft between parent.lft AND  parent.rgt
                          AND  parent.id = %d
                          AND  node.id != %d
                        ORDER BY node.lft", 
             mysql_real_escape_string($this->id),
             mysql_real_escape_string($this->id));
    }
    else{
      $sql = sprintf("SELECT node.id 
                        FROM miki_shop_article_category AS node,
                             miki_shop_article_category AS parent,
                             (
                              SELECT parent.id, COUNT(article.id) as nb
                              FROM miki_shop_article_category AS node,
                                   miki_shop_article_category AS parent,
                                   miki_shop_article AS article
                              WHERE node.lft BETWEEN parent.lft AND parent.rgt
                                AND node.id = article.id_category
                                AND article.date_stop > NOW() 
                                AND article.state = 1
                              GROUP BY parent.id
                             ) AS nb_articles
                        WHERE node.lft BETWEEN parent.lft AND parent.rgt
                          AND nb_articles.id = node.id
                          AND parent.id = %d
                          AND node.id != %d
                        ORDER BY node.lft", 
             mysql_real_escape_string($this->id),
             mysql_real_escape_string($this->id));
    }
    
    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql");

    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_shop_article_category($row['id']);
    }
    
    return $return;
  }
  
  /**
   * Recherche la catégorie parent
   * 
   * @return Miki_shop_article_category      
   */   
  public function get_parent(){
    $return = array();
    
    $sql = sprintf("SELECT parent.id
                    FROM miki_shop_article_category AS node,
                         miki_shop_article_category AS parent
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt
                      AND node.id = %d
                      AND parent.id != %d
                      AND parent.lft = (SELECT max(parent.lft)
                                          FROM miki_shop_article_category AS node,
                                               miki_shop_article_category AS parent
                                          WHERE node.lft BETWEEN parent.lft AND parent.rgt
                                            AND node.id = %d
                                            AND parent.id != %d)
                    ORDER BY parent.lft", 
           mysql_real_escape_string($this->id),
           mysql_real_escape_string($this->id),
           mysql_real_escape_string($this->id),
           mysql_real_escape_string($this->id));
    
    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql");
    $row = mysql_fetch_array($result);
    return new Miki_shop_article_category($row['id']);
  }
  
  /**
   * Recherche tous les parents de la catégorie
   *  
   * @return mixed Un tableau d'éléments Miki_shop_article_category représentant les catégories trouvées
   */ 
  public function get_parents(){
    $return = array();
    
    $sql = sprintf("SELECT parent.id
                    FROM miki_shop_article_category AS node,
                         miki_shop_article_category AS parent
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt
                      AND node.id = %d
                      AND parent.id != %d
                    ORDER BY parent.lft DESC", 
           mysql_real_escape_string($this->id),
           mysql_real_escape_string($this->id));
    
    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql");
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_shop_article_category($row['id']);
    }
    
    return $return;
  }
  
  /**
   * Recherche le nombre d'articles dans la catégorie
   * 
   * @param boolean $enabled Si true, prend uniquement les article en vente. Si false, prend tous les articles
   * 
   * @return int               
   */   
  public function get_nb_articles($enabled = true){
    $sql = "SELECT parent.id, COUNT(article.id)
            FROM miki_shop_article_category AS node,
                 miki_shop_article_category AS parent,
                 miki_shop_article AS article
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
              AND node.id = article.id_category
              AND parent.id = %d";
              
    // si on ne prend que les articles en vente actuellement
    if ($enabled)
      $sql .= " AND article.date_stop > NOW() AND article.state = 1";
    
    $sql .= " GROUP BY parent.id";
    
    $sql = sprintf($sql, mysql_real_escape_string($this->id));
    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql");
    $row = mysql_fetch_array($result);
    
    if (mysql_num_rows($result) == 0)
      $result = 0;
    else $result = $row[1];
    
    return $result;
  }
  
  /**
   * Recherche si la catégorie ou une catégorie enfant possède au moins 1 article
   * 
   * @return boolean      
   */   
  public function has_article(){
    // si la catégorie en cours possède au moins 1 article, retourne true
    return $this->get_nb_articles(true) > 0;
  }
  
  /**
   * Vérifie si la catégorie possède le ou l'un des articles donnés
   * 
   * @param mixed $articles L'id des articles à tester (si plusieurs articles, $articles est un tableau, sinon un string)
   * @param boolean $enabled Si true, prend uniquement les article en vente. Si false, prend tous les articles
   */   
  public function has_this_article($articles, $enabled = true){
    if (is_array($articles))
      $articles = implode(" ,", $articles);
    
    $sql = "SELECT parent.id, COUNT(article.id)
            FROM miki_shop_article_category AS node,
                 miki_shop_article_category AS parent,
                 miki_shop_article article
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
              AND node.id = article.id_category
              AND parent.id = %d
              AND article.id in (%s)";
              
    // si on ne prend que les articles en vente actuellement
    if ($enabled)
      $sql .= " AND article.date_stop > NOW() AND article.state = 1";
    
    $sql .= " GROUP BY parent.id";
    
    $sql = sprintf($sql, 
              mysql_real_escape_string($this->id),
              mysql_real_escape_string($articles));
    
    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql");
    $row = mysql_fetch_array($result);
    
    return $row[0] > 0;
  }
  
  /**
   * Ajoute une catégorie par rapport à la catégorie courante
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @param Miki_shop_article_category $cat Catégorie à ajouter
   * @param int $cat_ref_id Id de la catégorie référence (pour le placement de la nouvelle catégorie)
   * @param string $pos Position de la nouvelle catégorie par rapport à la catégorie référence - first : place en première position - after : place à droite de la catégorie référence - in : place à l'intérieur de la catégorie référence (= catégorie enfant)
   * 
   * @return La catégorie ajoutée
   *      
   * @static               
   */
  public static function add_category($cat, $cat_ref_id, $pos){
    
    /************************************
     * Début de l'ajout de la catégorie
     ************************************/
    if ($pos == 'first'){
      // débute la transaction
      mysql_query("START TRANSACTION");
      
      try{
        $right = 0;
        
        // met à jour les autres catégories
        $sql = sprintf("UPDATE miki_shop_article_category SET rgt = rgt + 2 WHERE rgt > %d",
                  mysql_real_escape_string($right));
        if (!$result = mysql_query($sql))
          throw new Exception("Erreur SQL");
          
        $sql = sprintf("UPDATE miki_shop_article_category SET lft = lft + 2 WHERE lft > %d",
                  mysql_real_escape_string($right));
        if (!$result = mysql_query($sql))
          throw new Exception("Erreur SQL");
        
        // place et met à jour la nouvelle catégorie
        $cat->lft = 1;
        $cat->rgt = 2;
        $cat->save();
        
        // applique les modifications
        mysql_query("COMMIT");
      }
      catch(Exception $e){
        // annule les modifications
        mysql_query("ROLLBACK");
        throw new Exception("Une erreur est survenue dans la requête sql");
      }
    }
    elseif ($pos == 'after'){
      // débute la transaction
      mysql_query("START TRANSACTION");
      
      try{
        $sql = sprintf("SELECT rgt FROM miki_shop_article_category WHERE id = %d",
                  mysql_real_escape_string($cat_ref_id));
        if (!$result = mysql_query($sql))
          throw new Exception("Erreur SQL");
        $row = mysql_fetch_array($result);
        $right = $row[0];
        
        // met à jour les autres catégories
        $sql = sprintf("UPDATE miki_shop_article_category SET rgt = rgt + 2 WHERE rgt > %d",
                  mysql_real_escape_string($right));
        if (!$result = mysql_query($sql))
          throw new Exception("Erreur SQL");
          
        $sql = sprintf("UPDATE miki_shop_article_category SET lft = lft + 2 WHERE lft > %d",
                  mysql_real_escape_string($right));
        if (!$result = mysql_query($sql))
          throw new Exception("Erreur SQL");
        
        // place et met à jour la nouvelle catégorie
        $cat->lft = $right + 1;
        $cat->rgt = $right + 2;
        $cat->save();
        
        // applique les modifications
        mysql_query("COMMIT");
      }
      catch(Exception $e){
        // annule les modifications
        mysql_query("ROLLBACK");
        throw new Exception("Une erreur est survenue dans la requête sql");
      }
    }
    elseif ($pos == 'in'){
      try{
        // débute la transaction
        mysql_query("START TRANSACTION");
        
        $sql = sprintf("SELECT lft FROM miki_shop_article_category WHERE id = %d",
                  mysql_real_escape_string($cat_ref_id));
        if (!$result = mysql_query($sql))
          throw new Exception("Erreur SQL");
        $row = mysql_fetch_array($result);
        $left = $row[0];
        
        // met à jour les autres catégories
        $sql = sprintf("UPDATE miki_shop_article_category SET rgt = rgt + 2 WHERE rgt > %d",
                  mysql_real_escape_string($left));
        if (!$result = mysql_query($sql))
          throw new Exception("Erreur SQL");
          
        $sql = sprintf("UPDATE miki_shop_article_category SET lft = lft + 2 WHERE lft > %d",
                  mysql_real_escape_string($left));
        if (!$result = mysql_query($sql))
          throw new Exception("Erreur SQL");
        
        // place et met à jour la nouvelle catégorie
        $cat->lft = $left + 1;
        $cat->rgt = $left + 2;
        $cat->save();

        // applique les modifications
        mysql_query("COMMIT");
      }
      catch(Exception $e){
        // annule les modifications
        mysql_query("ROLLBACK");
        throw new Exception("Une erreur est survenue dans la requête sql");
      }
    }
    
    return $cat;
  }
  
  /**
   * Déplace une catégorie à l'emplacement donné
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @param Miki_shop_article_category $cat Catégorie à ajouter
   * @param int $cat_ref_id Id de la catégorie référence (pour le placement de la nouvelle catégorie)
   * @param string $pos Position de la nouvelle catégorie par rapport à la catégorie référence - after : place à droite de la catégorie référence - in : place à l'intérieur de la catégorie référence (= catégorie enfant)      
   *  
   * @static           
   */
  public static function move_category($cat, $cat_ref_id, $pos){
    
    // débute la transaction
    mysql_query("START TRANSACTION");
    
    //$articles = array();
    $children = array();
    
    // récupert les catégories enfants
    $children = $cat->get_children();
    
    // rercherche les articles faisant partie de cette catégorie
    $sql = sprintf("SELECT id FROM miki_shop_article WHERE id_category = %d",
      mysql_real_escape_string($cat->id));
    $result = mysql_query($sql);
    
    // conserve les id pour modifier par la suite l'id de la catégorie
    while($row = mysql_fetch_array($result)){
      $articles[] = $row[0];
    }
    
    
    /******************************
     * Suppression de la catégorie
     *****************************/         
    try{
      $sql = sprintf("SELECT lft, rgt FROM miki_shop_article_category WHERE id = %d",
                mysql_real_escape_string($cat->id));
      if (!$result = mysql_query($sql))
        throw new Exception("Erreur SQL");
      $row = mysql_fetch_array($result);
      $left = $row[0];
      $right = $row[1];
      $width = $right - $left + 1;
      
      // supprime la catégorie
      $sql = sprintf("DELETE FROM miki_shop_article_category WHERE lft = %d",
                mysql_real_escape_string($left));
      if (!$result = mysql_query($sql))
        throw new Exception("Erreur SQL");
      
      // met à jour les autres catégories
      $sql = sprintf("UPDATE miki_shop_article_category SET rgt = rgt - 1, lft = lft - 1 WHERE lft BETWEEN %d AND %d",
                mysql_real_escape_string($left),
                mysql_real_escape_string($right));
      if (!$result = mysql_query($sql))
        throw new Exception("Erreur SQL");
        
      $sql = sprintf("UPDATE miki_shop_article_category SET rgt = rgt - 2 WHERE rgt > %d",
                mysql_real_escape_string($right));
      if (!$result = mysql_query($sql))
        throw new Exception("Erreur SQL");
        
      $sql = sprintf("UPDATE miki_shop_article_category SET lft = lft - 2 WHERE lft > %d",
                mysql_real_escape_string($right));
      if (!$result = mysql_query($sql))
        throw new Exception("Erreur SQL");
    }
    catch(Exception $e){
      // annule les modifications
      mysql_query("ROLLBACK");
      throw new Exception("Une erreur est survenue dans la requête sql");
    }
    
    
    /******************************
     * Ajout de la catégorie
     *****************************/         
    if ($pos == 'after'){
      try{
        $sql = sprintf("SELECT rgt FROM miki_shop_article_category WHERE id = %d",
                  mysql_real_escape_string($cat_ref_id));
        if (!$result = mysql_query($sql))
          throw new Exception("Erreur SQL");
        $row = mysql_fetch_array($result);
        $right = $row[0];
        
        // met à jour les autres catégories
        $sql = sprintf("UPDATE miki_shop_article_category SET rgt = rgt + 2 WHERE rgt > %d",
                  mysql_real_escape_string($right));
        if (!$result = mysql_query($sql))
          throw new Exception("Erreur SQL");
          
        $sql = sprintf("UPDATE miki_shop_article_category SET lft = lft + 2 WHERE lft > %d",
                  mysql_real_escape_string($right));
        if (!$result = mysql_query($sql))
          throw new Exception("Erreur SQL");
        
        // place et met à jour la nouvelle catégorie
        $cat->lft = $right + 1;
        $cat->rgt = $right + 2;
        $cat->save();
      }
      catch(Exception $e){
        // annule les modifications
        mysql_query("ROLLBACK");
        throw new Exception("Une erreur est survenue dans la requête sql");
      }
    }
    elseif ($pos == 'in'){
      try{
        $sql = sprintf("SELECT lft FROM miki_shop_article_category WHERE id = %d",
                  mysql_real_escape_string($cat_ref_id));
        if (!$result = mysql_query($sql))
          throw new Exception("Erreur SQL");
        $row = mysql_fetch_array($result);
        $left = $row[0];
        
        // met à jour les autres catégories
        $sql = sprintf("UPDATE miki_shop_article_category SET rgt = rgt + 2 WHERE rgt > %d",
                  mysql_real_escape_string($left));
        if (!$result = mysql_query($sql))
          throw new Exception("Erreur SQL");
          
        $sql = sprintf("UPDATE miki_shop_article_category SET lft = lft + 2 WHERE lft > %d",
                  mysql_real_escape_string($left));
        if (!$result = mysql_query($sql))
          throw new Exception("Erreur SQL");
        
        // place et met à jour la nouvelle catégorie
        $cat->lft = $left + 1;
        $cat->rgt = $left + 2;
        $cat->save();
      }
      catch(Exception $e){
        // annule les modifications
        mysql_query("ROLLBACK");
        throw new Exception("Une erreur est survenue dans la requête sql");
      }
    }
    
    // replace les articles dans la bonne catégorie
    if (sizeof($articles) > 0){
      $liste_articles = implode(", ", $articles);
      // met à jour les autres catégories
      $sql = sprintf("UPDATE miki_shop_article SET id_category = %d WHERE id in (%s)",
                mysql_real_escape_string($cat->id),
                mysql_real_escape_string($liste_articles));
      if (!$result = mysql_query($sql))
        throw new Exception("Erreur SQL");
    }
          
    // déplace chaque enfant récursivement
    $pos2 = "in";
    $cat_ref_id2 = $cat->id;
    
    foreach($children as $child){
      Miki_shop_article_category::move_category($child, $cat_ref_id2, $pos2);
      
      $pos2 = "after";
      $cat_ref_id2 = $child->id;
    }
    
    // enregistre les modifications
    mysql_query("COMMIT");
    
    return true;
  }
  
  /**
   * Recherche toutes les catégories
   * 
   * @param string $search Critère de recherche qui sera appliqué sur le nom de la catégorie
   * @param string $name Nom (exact) des catégories à rechercher   
   * @param string $ordre Par quel champ les catégories trouvées seront triées (name). Si vide, on tri selon la position de la catégorie.
   * @param string $type Tri ascendant (asc) ou descendant (desc)   
   * @param boolean $all Si true, prend toutes les catégories. Si false, prend uniquement les catégories qui n'ont pas de parents   
   * @param boolean $empty Si true, prend toutes les catégories. Si false, prend uniquement les catégories qui ont des articles
   *             
   * @static
   * @return mixed Un tableau d'éléments Miki_shop_article_category représentant les catégories trouvées         
   */         
  public static function search($search = "", $name = "", $order = "", $type = "asc", $all = true, $empty = true){
    $return = array();
    $search = mb_strtolower($search, 'UTF-8');
    $name = mb_strtolower($name, 'UTF-8');
    
    $sql = "SELECT node.id, node_name.name, nb_parents.nb as parents, nb_articles.nb as articles
            FROM miki_shop_article_category node
            
            INNER JOIN miki_shop_article_category_name AS node_name ON node_name.category_id = node.id
                
            LEFT OUTER JOIN
                 (
                    SELECT node.id, (COUNT(parent.id) - 1) as nb
                    FROM miki_shop_article_category AS node,
                         miki_shop_article_category AS parent
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt
                    GROUP BY node.id
                 ) AS nb_parents ON nb_parents.id = node.id";
    
    // si on prend toutes les catégories on fait une jointure externe gauche             
    if ($empty)
      $sql .= " LEFT OUTER JOIN ";
    // sinon jointure interne
    else
      $sql .= " INNER JOIN ";
    
    $sql .= "   (
                    SELECT parent.id, COUNT(article.id) as nb
                    FROM miki_shop_article_category AS node,
                         miki_shop_article_category AS parent,
                         miki_shop_article AS article
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt
                      AND node.id = article.id_category
                      AND article.date_stop > NOW() AND article.state = 1
                    GROUP BY parent.id
                 ) AS nb_articles ON nb_articles.id = node.id";
    
    // si on doit rechercher selon un critère donné (nom inexact)
    if ($search != ""){
      $sql .= sprintf(" WHERE node_name.language_code = 'fr' AND 
                              LOWER(node_name.name) LIKE '%%%s%%'",
                      mysql_real_escape_string($search));
    }
    
    // si on doit recherche le nom exact d'une catégorie
    if ($name != ""){                 
      $sql .= sprintf(" WHERE node_name.language_code = 'fr' AND 
                              LOWER(node_name.name) = '%s'",
                      mysql_real_escape_string($name));
    }
    
    if (!$all)
      $sql .= " HAVING parents = 0 OR parents IS NULL";
      
    // ordonne les catégories
    if ($order == "name")
      $sql .= " ORDER BY node_name.name " .$type;
    else
      $sql .= " ORDER BY node.lft " .$type;
      
    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql : <br />$sql<br />");
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_shop_article_category($row[0]);
    }
    return $return;
  }
  
  /**
   * Recherche toutes les catégories
   * 
   * @param string $ordre Par quel champ les catégories trouvées seront triées (name). Si vide, on tri selon la position de la catégorie.
   * @param string $type Tri ascendant (asc) ou descendant (desc)   
   * @param boolean $all Si true, prend toutes les catégories. Si false, prend uniquement les catégories qui n'ont pas de parents   
   * @param boolean $empty Si true, prend toutes les catégories. Si false, prend uniquement les catégories qui ont des articles
   *             
   * @static
   * @return mixed Un tableau d'éléments Miki_shop_article_category représentant les catégories trouvées         
   */         
  public static function get_all_categories($order = "", $type = "asc", $all = true, $empty = true){
    $return = array();
    
    $sql = "SELECT node.id, node_name.name, nb_parents.nb as parents, nb_articles.nb as articles
            FROM miki_shop_article_category node
            
            INNER JOIN miki_shop_article_category_name AS node_name ON node_name.category_id = node.id
                
            LEFT OUTER JOIN
                 (
                    SELECT node.id, (COUNT(parent.id) - 1) as nb
                    FROM miki_shop_article_category AS node,
                         miki_shop_article_category AS parent
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt
                    GROUP BY node.id
                 ) AS nb_parents ON nb_parents.id = node.id";
    
    // si on prend toutes les catégories on fait une jointure externe gauche             
    if ($empty)
      $sql .= " LEFT OUTER JOIN ";
    // sinon jointure interne
    else
      $sql .= " INNER JOIN ";
    
    $sql .= "   (
                    SELECT parent.id, COUNT(article.id) as nb
                    FROM miki_shop_article_category AS node,
                         miki_shop_article_category AS parent,
                         miki_shop_article AS article
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt
                      AND node.id = article.id_category
                      /*AND article.date_stop > NOW() AND article.state = 1*/
                    GROUP BY parent.id
                 ) AS nb_articles ON nb_articles.id = node.id";
    
    $sql .= " WHERE node_name.language_code = 'fr'";
    
    if (!$all)
      $sql .= " HAVING parents = 0 OR parents IS NULL";
      
    // ordonne les catégories
    if ($order == "name")
      $sql .= " ORDER BY node_name.name " .$type;
    else
      $sql .= " ORDER BY node.lft " .$type;

    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql : <br />$sql<br />");
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_shop_article_category($row[0]);
    }
    return $return;
  }
}
?>