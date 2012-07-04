<?php

  include("../class/miki_shop.php");
  include("../class/miki_shop_article.php");
  include("../class/miki_shop_article_category.php");
  include("../class/miki_configuration.php");
  
  /**
   * Récupert les news et les retourn sous la forme d'un fichier XML
   * 
   * Concernant le paramètre passé en paramètre, les données suivante sont possibles : 
   * 
   *  $article['name'] : nom de l'article
   *  $article['ref'] : référence de l'article   
   *  $article['category'] : catégorie de l'article   
   *  $article['description'] : description de l'article
   *  $article['price'] : prix de l'article
   *  $article['quantity'] : quantité disponible
   *  $article['name'] : nom de l'article
   *  $article['name'] : nom de l'article
   *  $article['name'] : nom de l'article
   *  $article['name'] : nom de l'article                              
   *      
   * @param mixed Tableau contenant les données de l'article    
   * @return string Les news au format XML      
   */     
  function add_article($article){
    // génère les données en XML
    try{
      // vérifie qu'il y ait un et un seul shop de configuré
      if (Miki_shop::get_nb_shops() < 1)
        return mapi_error("Aucun shop n'a été configuré");
      
      if (Miki_shop::get_nb_shops() > 1)
        return mapi_error("Trop de shops sont configurés");
      
      // le nom de l'article est obligatoire
      if (!isset($article['name']))
        return mapi_error("Le nom de l'article est manquant");
      
      // récupert le shop
      $shop = current(Miki_shop::get_all_shops());
      
      // créé le nouvel article
      $miki_article = new Miki_shop_article();
      
      // vérifie si l'article que l'on veut insérer n'existe pas encore (via sa référence)
      if (isset($article['ref'])){
        try{
          $miki_article->load_by_ref($article['ref']);
          //return mapi_error(var_dump($miki_article));
        }
        catch(Exception $e){}
      }
      
      // met à jour l'article
      $miki_article->id_shop = $shop->id;
      $miki_article->name['fr'] = stripslashes(urldecode($article['name']));
      
      if (isset($article['ref']))
        $miki_article->ref = stripslashes(urldecode($article['ref']));
      
      if (isset($article['description']))
        $miki_article->description['fr'] = stripslashes(urldecode($article['description']));
        
      if (isset($article['price']))
        $miki_article->price = stripslashes(urldecode($article['price']));
        
      if (isset($article['quantity']))
        $miki_article->quantity = stripslashes(urldecode($article['quantity']));
        
      if (isset($article['state']))
        $miki_article->state = stripslashes(urldecode($article['state']));
      
      // si une catégorie a été donnée
      if (isset($article['category'])){
        // recherche  si la catégorie existe déjà
        $categories = Miki_shop_article_category::search("", $article['category']);
        
        // si la catégorie existe déjà
        if (is_array($categories) && sizeof($categories) > 0){
          // récupert la première catégorie trouvée
          $cat = current($categories);
          $miki_article->id_category = $cat->id;
        }
        // sinon, si la catégorie n'existe pas encore
        else{
          // crée une nouvelle catégorie
          $cat = new Miki_shop_article_category();
          $cat->name['fr'] = $article['category'];
          
          // recherche toutes les catégories parent présentes
          $all_categories = Miki_shop_article_category::get_all_categories("", "asc", false);
          // s'il existe 1 ou plusieurs catégories
          if (sizeof($all_categories) > 0){
            // on place la nouvelle catégorie à la fin
            $cat_ref = end($all_categories);
            $cat = Miki_shop_article_category::add_category($cat, $cat_ref->id, 'after');
          }
          // sinon on la place au début
          else{
            $cat = Miki_shop_article_category::add_category($cat, '', 'after');
          }
          
          // définit la catégorie
          $miki_article->id_category = $cat->id;
        }
      }
      // si aucune catégorie n'a été donnée
      else{
        // définit la catégorie par défaut
        $miki_article->id_category = 1;
      }
    
      $miki_article->save();
      
      return mapi_success();
    }
    catch(Exception $e){
      return mapi_error($e->getMessage());
    }
  }  
?>