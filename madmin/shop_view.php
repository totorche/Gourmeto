<?php
  require_once ("shop_shipping_modules.php");
  require_once ("country_fr.php");
  
  // stock l'url actuel pour un retour après opérations
  $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
  
  // récupert le shop
  $shops = Miki_shop::get_all_shops();
  if (sizeof($shops) == 0){
    $shop = false;
  }
  else
    $shop = array_shift($shops);
?>

<div id="arianne">
  <a href="#"><?php echo _("Shop"); ?></a> > Mon Shop
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Mon Shop"); ?></h1>  
  
  <table>      
    <tr>
      <td colspan="2" style="font-weight:bold">Détails de mon shop</td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <?php 
    if ($shop){
    ?>
      <tr>
        <td>Nom du shop : </td>
        <td><?php echo $shop->name; ?></td>
      </tr>
      <tr>
        <td style="vertical-align:top">Description : </td>
        <td><?php echo $shop->description; ?></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td>Nombre d'articles dans mon shop : </td>
        <td><?php echo Miki_shop_article::get_nb_articles($shop->id); ?> <a href="index.php?pid=143" style="margin-left:20px" title="Voir les articles">Voir les articles</a></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><input type="button" value="Modifier mon shop" onclick="document.location='index.php?pid=142'" /></td>
      </tr>
    <?php
    }
    else{
    ?>
      <tr>
        <td colspan="2">Vous n'avez aucune shop de configuré pour le moment.</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><input type="button" value="Créer un shop" onclick="document.location='index.php?pid=142'" /></td>
      </tr>
    <?php } ?>
  </table>
</div>