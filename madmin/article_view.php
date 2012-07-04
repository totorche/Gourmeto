<head>
  <?php
    if (!test_right(51) && !test_right(52) && !test_right(53)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."-1';</script>";
      exit();
    }
    
    // si pas d'id de contact spécifié, on retourne à la page précédente
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."-2';</script>";
      exit();
    }
    else
      $id = $_GET['id'];
      
    require_once ('shop_article_state.php');
    
    try{
      $article = new Miki_shop_article($id);
      $category = new Miki_shop_article_category($article->id_category);
    }
    catch(Exception $e){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
  ?>
  
  <style type="text/css">
    #details td{
      padding: 5px 10px 5px 0;
    }
  </style>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Shop"); ?></a> > <a href="index.php?pid=143"><?php echo _("Liste articles"); ?></a> > Détails d'un article
</div>

<div id="first_contener">
  <h1><?php echo _("Détails d'un article"); ?></h1>
  
  <table id="details" cellspacing="0" cellpadding="0" style="border:0">
    <tr>
      <td style="vertical-align:top;font-weight:bold; min-width: 150px;">Catégorie :</td>
      <td><?php echo $category->name['fr']; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold">Etat :</td>
      <td><?php echo $shop_article_state[$article->state]; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold">Nom :</td>
      <td><?php echo $article->get_name('fr'); ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold">Ref :</td>
      <td><?php echo $article->ref; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold">Description :</td>
      <td><?php echo $article->get_description('fr'); ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold">Poids :</td>
      <td><?php echo number_format($article->weight,2,'.',"'"); ?> Kg</td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold">Prix :</td>
      <td><?php echo number_format($article->price,2,'.',"'"); ?> CHF</td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold">Quantité disponible :</td>
      <td><?php echo $article->quantity; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold">Images :</td>
      <td>
        <?php 
          if (is_array($article->picture)){
            foreach($article->picture as $picture){
                if ($picture != "")
                  echo "<img src='../pictures/shop_articles/thumb/$picture' style='margin-right:10px' />";
            }
          }
        ?>
      </td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2">
        <input type="button" value="Modifier" onclick="document.location='index.php?pid=1452&id=<?php echo $article->id; ?>';" />
        &nbsp;&nbsp;
        <input type="button" value="Retour" onclick="document.location='<?php echo $_SESSION['url_back']; ?>';" />
      </td>
    </tr>
  </table>
    
</div>

<?php
  // stock l'url actuel pour un retour après opérations
  $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
?>