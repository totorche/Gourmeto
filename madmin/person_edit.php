<head>
  <?php
    
    // stock l'url actuel pour un retour après opérations
    $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
    
    require_once ("../config/country_fr.php");
    require_once ("../config/mois_fr.php");
    require_once ("../config/genre_personne_fr.php");
  
    if (!test_right(34) && !test_right(35) && !test_right(36)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    
    // si pas d'id de contact spécifié, on retourne à la page précédente
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    else
      $id = $_GET['id'];
    
    try{
      $person = new Miki_person($id);
    }
    catch(Exception $e){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    }
    
    // récupert la date d'anniversaire
    $birthday = explode("-", $person->birthday);
    $year = $birthday[0];
    $month = $birthday[1];
    $day = $birthday[2];
    
    // enlève les premiers '0'
    if (substr($day, 0, 1) == '0')
      $day = substr($day, 1, 1);
    if (substr($month, 0, 1) == '0')
      $month = substr($month, 1, 1);
  ?>
  
  <script type="text/javascript">
  
    window.addEvent('domready', function() {
      <?php require_once("scripts/checkform.js"); ?>
      
      var myCheckForm = new checkForm($('form_modif_person'),{
                                        useAjax: false,
                                        errorPlace: 'bottom'
                                      });
    });
    
  </script>
  
  <style type="text/css">
    
    #form_modif_person td{
      padding: 2px 15px 2px 0;
      vertical-align: top;
    }
    
    #form_modif_person input[type=text]{
      width: 250px;
    }
    
    #form_modif_person textarea{
      width: 400px;
      height: 100px;
    }
    
  </style>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Utilisateurs/Groupes"); ?></a> > <a href="index.php?pid=101"><?php echo _("Liste membres"); ?></a> > Modification d'un membre
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modification d'un membre"); ?></h1>  

  <form id="form_modif_person" action="person_test_edit.php" method="post" name="formulaire" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    
      <table>
        <tr>
          <td colspan="2" style="font-weight:bold">Information sur la personne</td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>        
        <tr>
          <td>Type <span style="color:#ff0000">*</span></td> 
          <td>
            <select name="type">
              <?php
                foreach($genre_personne as $key=>$genre){
                  echo "<option value=\"$key\"";
                  if ($person->type == $key) echo " selected='selected'";
                  echo ">$genre</option>"; 
                }
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td>Prénom <span style="color:#ff0000">*</span></td>
          <td><input type="text" class="required" name="firstname" value="<?php echo $person->firstname; ?>" /></td>
        </tr>
        <tr>
          <td>Nom <span style="color:#ff0000">*</span></td>
          <td><input type="text" class="required" name="lastname" value="<?php echo $person->lastname; ?>" /></td>
        </tr>
        <tr>
          <td>Date de naissance *</td>
          <td>
            <select name="birthday_day">
            <?php
              for($x=1; $x<=31; $x++){
                
                echo "<option value='$x'";
                if ($day == $x) echo " selected='selected'";
                echo ">$x</option>";
              }
            ?>
            </select>
            <select name="birthday_month">
              <?php
              foreach($month_list as $key=>$c){
                echo "<option value=\"$key\"";
                if ($month == $key) echo " selected='selected'";
                echo ">$c</option>";
              }
              ?>
            </select>
            <select name="birthday_year">
            <?php
              $year_now = date("Y");
              $year_max = $year_now - 18;
              for($x=1940; $x<=$year_max; $x++){
                echo "<option value='$x'";
                if ($year == $x) echo " selected='selected'";
                echo ">$x</option>";
              }
            ?>
            </select>
          </td>
        </tr>
        <tr>
          <td>Adresse <span style="color:#ff0000">*</span></td>
          <td><input type="text" class="required" name="address" value="<?php echo $person->address; ?>" /></td>
        </tr>
        <tr>
          <td>Code postal <span style="color:#ff0000">*</span></td>
          <td><input type="text" class="required numeric" name="npa" value="<?php echo $person->npa; ?>" /></td>
        </tr>
        <tr>
          <td>Localité <span style="color:#ff0000">*</span></td>
          <td><input type="text" class="required" name="city" value="<?php echo $person->city; ?>" /></td>
        </tr>
        <tr>
          <td>Canton/Département</td>
          <td><input type="text" name="dept" value="<?php echo $person->dept; ?>" /></td>
        </tr>
        <tr>
          <td>Pays <span style="color:#ff0000">*</span></td>
          <td>
            <select name="country">
              <option value="Suisse">Suisse</option>
              <?php
                /*foreach($country_list as $key=>$el){
                  echo "<option value=\"$el\"";
                  if ($person->country == $el) echo " selected='selected'";
                  echo ">$el</option>"; 
                }*/
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td>Téléphone principal <span style="color:#ff0000">*</span></td>
          <td><input type="text" name="tel1" class="required phone" value="<?php echo $person->tel1; ?>" /></td>
        </tr>
        <tr>
          <td>Téléphone secondaire</td>
          <td><input type="text" name="tel2" class="phone" value="<?php echo $person->tel2; ?>" /></td>
        </tr>
        <tr>
          <td>Email 1 <span style="color:#ff0000">*</span></td>
          <td><input type="text" class="required email" name="email1" value="<?php echo $person->email1; ?>" /></td>
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
            <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=101'" />
            &nbsp;&nbsp;
            <input type="submit" value="<?php echo _("Envoyer"); ?>" />
          </td>
        </tr> 
      </table>
      
  </form>
</div>