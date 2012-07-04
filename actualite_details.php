<head>
  <?php
    if (isset($_GET['nid']) && is_numeric($_GET['nid']))
      $news_id = $_GET['nid'];
    else
      miki_redirect(Miki_configuration::get('site_url'));
    
    // récupert l'actualité
    $n = new Miki_news($news_id);
    
    // n'affiche pas de titre dans le template. L'affichage du titre est géré dans la page
    $title_h1 = "";
  ?>

  <style type="text/css">
    .news p{
      margin: 0;
    }
  </style>

</head>

<div class='news'>
  <?php
  
    // puis l'affiche
    $n->print_details("default_details");
    
    // affecte le titre à la page (modifie le titre du gabarit)
    $title_h1 = $n->title;
  
  ?>

<div style="text-align:center">
  <?php 
    $shrTitle = $n->title;
    include_once("scripts/sexybookmarks/sexybookmarks.php"); 
  ?>
</div>

<div style="clear:left;margin-top:20px;text-align:right">
  <a href="[miki_page='actualites']" title="Retour aux actualités">Retour aux actualités</a>
</div>