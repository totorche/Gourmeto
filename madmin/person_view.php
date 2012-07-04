  <?php
    if (!test_right(34) && !test_right(35) && !test_right(36) && !test_right(46))
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    
    // si pas d'id de contact spécifié, on retourne à la page précédente
    if (!isset($_GET['id']) || !is_numeric($_GET['id']))
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    else
      $id = $_GET['id'];
      
    require_once ("../config/genre_personne_fr.php");
    
    try{
      $person = new Miki_person($id);
      /*$account = new Miki_account();
      $account->load_from_person($person->id);
      $company = new Miki_company($person->company_id);*/
    }
    catch(Exception $e){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    }
  ?>
<style type="text/css">

  .informations_membre td{
    vertical-align: top;
    padding: 2px 15px 2px 0;
    white-space: pre-wrap;
    min-width: 150px;
    text-align: left;
  }

</style>

<div id="arianne">
  <a href="#"><?php echo _("Membres"); ?></a> > Détails d'un membre
</div>

<div id="first_contener">
  <h1><?php echo _("Détails d'un membre"); ?></h1>
       
  <table class="informations_membre" cellspacing="0" cellpadding="0" style="border:0">
    <tr>
      <td style="font-weight:bold;padding-right:5px">Genre :</td>
      <td><?php echo $person->type; ?></td>
    </tr>
    <tr>
      <td style="font-weight:bold;padding-right:5px">Prénom / nom :</td>
      <td><?php echo "$person->firstname $person->lastname"; ?></td>
    </tr>
    <tr>
      <td style="font-weight:bold;padding-right:5px;vertical-align:top">Adresse :</td>
      <td><?php echo "$person->address<br />$person->npa $person->city<br />$person->country"; ?></td>
    </tr>
    <tr>
      <td style="font-weight:bold;padding-right:5px">Téléphone :</td>
      <td><?php echo $person->tel1; ?></td>
    </tr>
    <tr>
      <td style="font-weight:bold;padding-right:5px">Adresse e-mail :</td>
      <td><?php echo $person->email1; ?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2">
        <input type="button" value="Retour" onclick="document.location='<?php echo $_SESSION['url_back']; ?>';" />
      </td>
    </tr>
  </table>
</div>

<?php
  // stock l'url actuel pour un retour après opérations
  //$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
?>