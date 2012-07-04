<head>
  <?php
    
    // stock l'url actuel pour un retour après opérations
    $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
  
    require_once ("../config/country_fr.php");
    require_once ("../config/genre_personne_fr.php");
    require_once("../config/activity_sector.php");
  
    if (!test_right(34)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    
    // récupert toutes les sociétés
    $companies = Miki_company::get_all_companies();
  ?>
  
  <script type="text/javascript">
  
    window.addEvent('domready', function() {
      <?php require_once("scripts/checkform.js"); ?>
      
      var myCheckForm = new checkForm($('form_new_person'),{
                                        useAjax: false,
                                        errorPlace: 'bottom'
                                      });
    });
    
    // affiche le formulaire pour ajouter une nouvelle société
    function add_company(){
      $('new_company').setStyle('display','block');
      $('choose_company').setStyle('display','none');
      
      // dit au formulaire qu'on ajoute une nouvelle société
      $('input_new_company').value = 1;
    }
    
  </script>
  
  <style type="text/css">
    
    #form_new_person td{
      padding: 2px 15px 2px 0;
      vertical-align: top;
      min-width: 150px;
    }
    
    #form_new_person input[type=text]{
      width: 250px;
    }
    
    #form_new_person textarea{
      width: 400px;
      height: 100px;
    }
    
  </style>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Utilisateurs/Groupes"); ?></a> > <a href="index.php?pid=101"><?php echo _("Liste membres"); ?></a> > Ajout d'un membre
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajout d'un membre"); ?></h1>  

  <form id="form_new_person" action="person_test_new.php" method="post" name="formulaire" enctype="multipart/form-data">
  
    <!-- Pour savoir si on doit ajouter une nouvelle société ou pas -->
    <input type="hidden" id="input_new_company" name="new_company" value="<?php echo (sizeof($companies) > 0) ? '0' : '1'; ?>" />
    
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
            <select name="person_type">
              <?php
                foreach($genre_personne as $key=>$genre){
                  echo "<option value=\"$key\">$genre</option>"; 
                }
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td>Prénom <span style="color:#ff0000">*</span></td>
          <td><input type="text" class="required" name="person_firstname" /></td>
        </tr>
        <tr>
          <td>Nom <span style="color:#ff0000">*</span></td>
          <td><input type="text" class="required" name="person_lastname" /></td>
        </tr>
        <tr>
          <td>Adresse <span style="color:#ff0000">*</span></td>
          <td><input type="text" class="required" name="person_address" /></td>
        </tr>
        <tr>
          <td>Code postal <span style="color:#ff0000">*</span></td>
          <td><input type="text" class="required numeric" name="person_npa" /></td>
        </tr>
        <tr>
          <td>Localité <span style="color:#ff0000">*</span></td>
          <td><input type="text" class="required" name="person_city" /></td>
        </tr>
        <tr>
          <td>Pays <span style="color:#ff0000">*</span></td>
          <td>
            <select name="person_country">
              <?php
                foreach($country_list as $key=>$el){
                  echo "<option value=\"$el\"";
                  if ($el == "Suisse") echo " selected='selected'";
                  echo ">$el</option>"; 
                }
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td>Téléphone principal <span style="color:#ff0000">*</span></td>
          <td><input type="text" name="person_tel1" class="required phone" /></td>
        </tr>
        <tr>
          <td>Téléphone secondaire</td>
          <td><input type="text" name="person_tel2" class="phone" /></td>
        </tr>
        <tr>
          <td>Email <span style="color:#ff0000">*</span></td>
          <td><input type="text" class="required email" name="person_email1" /></td>
        </tr>
        <tr>
          <td>Poste dans sa société <span style="color:#ff0000">*</span></td>
          <td><input type="text" class="required" name="person_job" /></td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
        
        <?php
          if (sizeof($companies) > 0){
        ?>
          
            <tr id="choose_company">
              <td>Société <span style="color:#ff0000">*</span></td>
              <td>
                <select name="person_company">
                  <?php
                    foreach($companies as $company){
                      echo "<option value=\"$company->id\">$company->name</option>"; 
                    }
                  ?>
                </select>
                <span style="padding-left:20px"><a href="javascript:add_company();" title="Ajouter une nouvelle société">Ajouter une nouvelle société</a></span>
              </td>
            </tr>
            
        <?php
          }
        ?>
        
        <tr>
          <td colspan="2" style="padding-top:20px">
            
            <table id="new_company" <?php if (sizeof($companies) > 0) echo "style='display:none'"; ?>>
              <tr>
                <td colspan="2" style="font-weight:bold">Information sur la société</td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td>Nom <span style="color:#ff0000">*</span></td>
                <td><input type="text" class="" name="company_name" /></td>
              </tr>
              <tr>
                <td>Description <span style="color:#ff0000">*</span></td>
                <td><textarea name="company_description" class=""></textarea></td>
              </tr>
              <tr>
                <td>Logo</td>
                <td>
                  <input type='hidden' name='MAX_FILE_SIZE' value='5000000'>
                  <input type="file" name="company_logo" /> (laissez vide pour ne pas modifier)
                </td>
              </tr>
              <tr>
                <td>Activités</td> 
                <td>
                  <select name="company_activities">
                    <?php
                      foreach($activity_sector as $key=>$act){
                        echo "<option value=\"$key\">$act</option>"; 
                      }
                    ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td>Produits et services</td>
                <td><textarea name="company_products_services"></textarea></td>
              </tr>
              <tr>
                <td>Projets</td>
                <td><textarea name="company_projects"></textarea></td>
              </tr>
              <tr>
                <td>Adresse <span style="color:#ff0000">*</span></td>
                <td><input type="text" class="" name="company_address" /></td>
              </tr>
              <tr>
                <td>Code postal <span style="color:#ff0000">*</span></td>
                <td><input type="text" class="numeric" name="company_npa" /></td>
              </tr>
              <tr>
                <td>Localité <span style="color:#ff0000">*</span></td>
                <td><input type="text" class="" name="company_city" /></td>
              </tr>
              <tr>
                <td>Pays <span style="color:#ff0000">*</span></td>
                <td>
                  <select name="company_country">
                    <option value="Suisse">Suisse</option>
                    <?php
                      foreach($country_list as $key=>$el){
                        echo "<option value=\"$el\">$el</option>"; 
                      }
                    ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td>Site Internet <span style="color:#ff0000">*</span></td>
                <td><input type="text" class="" name="company_web" /></td>
              </tr>
            </table>
            
            
          </td>
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