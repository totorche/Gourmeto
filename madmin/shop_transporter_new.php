<head>
  <?php
    if (!test_right(75)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    
    // test que l'on aie bien un shop de configuré
    $shops = Miki_shop::get_all_shops();
    if (sizeof($shops) == 0){
      $shop = false;
    }
    else
      $shop = array_shift($shops);
      
    if (!$shop){
      echo "<div>
              Vous n'avez aucune shop de configuré pour le moment.<br /><br />
              <input type='button' value='Créer un shop' onclick=\"document.location='index.php?pid=142'\" />
            </div>";
      exit();
    }
  ?>
  
  <script type="text/javascript">
  
    window.addEvent('domready', function() {
      <?php require_once("scripts/checkform.js"); ?>
      
      var myCheckForm = new checkForm($('form_new_transporter'),{
                                        useAjax: false,
                                        errorPlace: 'bottom'
                                      });
    });
    
  </script>
  
  <style type="text/css">
    #form_new_element table td{
      padding-bottom: 10px;
    }
    
    #form_new_element table td:first-child{
      padding-right: 20px;
    }
    
    #form_new_element input[type=file]{
      margin-bottom: 10px;
    }
  </style>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Shop"); ?></a> > <a href="index.php?pid=251"><?php echo _("Liste des transporteurs"); ?></a> > Ajouter un transporteur
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajout d'un transporteur"); ?></h1>  

  <form id="form_new_element" action="shop_transporter_test_new.php" method="post" name="form_new_element" enctype="multipart/form-data">
    <table>
      <tr>
        <td style="vertical-align:top">Nom <span style="color:#ff0000">*</span></td>
        <td><input type="text" class="required" name="name" style="width:400px" /></td>
      </tr>     
      <tr>
        <td style="vertical-align:top">Etat <span style="color:#ff0000">*</span></td> 
        <td>
          <select name="state">
            <option value="1"><?php echo _("Activé"); ?></option>
            <option value="0"><?php echo _("Désactivé"); ?></option>
          </select>
        </td>
      </tr>
      <tr>
        <td style="vertical-align:top"><?php echo _("Logo"); ?></td>
        <td>
          <input type='hidden' name='MAX_FILE_SIZE' value='5000000'>
          <input type="file" name="logo" />
        </td>
      </tr> 
      <tr>
        <td style="vertical-align:top"><?php echo _("Délai de livraison"); ?></td>
        <td><input type="text" class="" name="shipping_delay" style="width: 400px;" /></td>
      </tr>
      <tr>
        <td style="vertical-align:top"><?php echo _("Assujetti aux taxes ?"); ?></td>
        <td><input type="checkbox" value="1" name="tax" /></td>
      </tr>
      <tr>
        <td style="vertical-align:top"><?php echo _("URL de suivi de colis"); ?></td>
        <td><input type="text" class="" name="url_tracking" style="width: 400px;" /><br /><span class='advice'>entrez un '@' à la place du numéro de colis</span></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" style="font-weight:bold">Les champs munis d'une <span style="color:#ff0000">*</span> sont obligatoires</td>
      </tr> 
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=251'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
      </tr> 
    </table>
  </form>
</div>

<?php
  // stock l'url actuel pour un retour après opérations
  $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
?>