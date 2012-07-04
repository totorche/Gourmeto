<head>
  <?php
    if (!test_right(75) && !test_right(76) && !test_right(77)){
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
      
    try{
      $element = new Miki_shop_transporter($id);
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
  <a href="#"><?php echo _("Shop"); ?></a> > <a href="index.php?pid=251"><?php echo _("Liste des transporteurs"); ?></a> > Détails d'un transporteur
</div>

<div id="first_contener">
  <h1><?php echo _("Détails d'un transporteur"); ?></h1>
  
  <table id="details" cellspacing="0" cellpadding="0" style="border:0">
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Nom"); ?> :</td>
      <td><?php echo $element->name; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Etat"); ?> :</td>
      <td><?php echo $element->state; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Délai de livraison"); ?> :</td>
      <td><?php echo $element->shipping_delay; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Assujetti aux taxes"); ?> :</td>
      <td><?php echo $element->tax == 1 ? _("Oui") : _("Non"); ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Logo"); ?> :</td>
      <td>
        <?php 
          if (!empty($element->logo) && $element->logo != "NULL")
            echo "<img src='../pictures/shop_transporter/" .$element->logo ."' style='margin-right:10px' />";
        ?>
      </td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2">
        <input type="button" value="Modifier" onclick="document.location='index.php?pid=253&id=<?php echo $element->id; ?>';" />
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