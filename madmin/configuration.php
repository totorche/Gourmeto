<?php

if (!test_right(54)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

require_once("functions_pictures.php");

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

// récupert les configurations
$configuration = Miki_configuration::get_all();

?>

<script type="text/javascript" src="scripts/checkform.js"></script>
<script type="text/javascript" src="scripts/SimpleTabs.js"></script>
<link rel="stylesheet" type="text/css" href="scripts/SimpleTabs.css" />

<link rel="stylesheet" type="text/css" href="../scripts/squeezebox/SqueezeBox.css" />
<script type="text/javascript" src="../scripts/squeezebox/SqueezeBox.js"></script>

<script type="text/javascript">

  window.addEvent('domready', function() {
    var myCheckForm = new checkForm($('formulaire'),{
                                          useAjax: false,
                                          errorPlace: 'bottom',
                                          divErrorCss: {
                                            'margin':'5px 0 0 0px'
                                          }
                                        });
  
    tabs = new SimpleTabs($('tab_content'),{selector:'.tab_selector'});
    
    SqueezeBox.assign($('link_help_paypal'), {
		  size: {x: 700, y: 220}
  	});
    
    toggle($('input_payement_bank'), $('payement_bank'));
    toggle($('input_payement_paypal'), $('payement_paypal'));
    toggle($('input_payement_paypal_secure'), $('payement_paypal_secure'));
  });
  
  function toggle(chkbox, el){
    if (chkbox.checked)
      el.setStyle('display', 'block');
    else
      el.setStyle('display', 'none');
  }

</script>

<style type="text/css">

  #formulaire input[type=text]{
    width: 400px;
  }

</style>

<div id="arianne">
  <a href="#"><?php echo _("Administration"); ?></a> > <?php echo _("Configurer le site Internet"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
    
  <form id="formulaire" action="configuration_test.php" method="post" style="width:100%;margin-top:10px" name="formulaire" enctype="multipart/form-data">
    
    <div id="tab_content">
      
      <!-- Configuration générale -->
      <span class='tab_selector' style='float:left;margin:0 5px'><?php echo _("Général"); ?></span>
      
      <table>
        <tr>
          <td><?php echo _("Nom du site Internet : "); ?></td>
          <td><input type="text" name="sitename" style="width:400px" value="<?php echo (isset($configuration['sitename'])) ? $configuration['sitename'] : ""; ?>" /></td>
        </tr>
        <tr>
          <td><?php echo _("URL du site Internet : "); ?></td>
          <td><input type="text" name="site_url" class="requiredLink" style="width:400px" value="<?php echo (isset($configuration['site_url'])) ? $configuration['site_url'] : ""; ?>" /></td>
        </tr>
        <tr>
          <td><?php echo _("Adresse e-mail de l'expéditeur des e-mails : "); ?></td>
          <td><input type="text" name="email_answer" style="width:400px" value="<?php echo (isset($configuration['email_answer'])) ? $configuration['email_answer'] : ""; ?>" /></td>
        </tr>
        <tr>
          <td><?php echo _("Identifiant Google Analytics : "); ?></td>
          <td><input type="text" name="analytics" style="width:400px" value="<?php echo isset($configuration['analytics']) ? $configuration['analytics'] : ""; ?>" /></td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td style="vertical-align: top";><?php echo _("Adresse de la société") ."<br />(" ._("y compris le nom de la société") .") : "; ?></td>
          <td><textarea name="address_website_company" style="width:400px; height: 100px;"><?php echo isset($configuration['address_website_company']) ? $configuration['address_website_company'] : ""; ?></textarea></td>
        </tr>
        <tr>
          <td><?php echo _("Logo : "); ?></td>
          <td>
            <input type='hidden' name='MAX_FILE_SIZE' value='5000000' />
            <input type="file" name="logo_website" />
            <?php
              if (isset($configuration['logo_website'])){
                $size = get_image_size("../pictures/" .$configuration['logo_website'], 50, 50);
                echo "<img src='../pictures/" .$configuration['logo_website'] ."' alt=\"" ._("Logo du site Internet") ."\" style='margin-left: 10px; width: " .$size[0] ."px; height: " .$size[1] ."px; vertical-align: top;' />";
              }
            ?>
          </td>
        </tr>
      </table>
      
      <!-- Configuration des events -->
      <span class='tab_selector' style='float:left;margin:0 5px'><?php echo _("Evénements"); ?></span>
      
      <table>
        <tr>
          <td><?php echo _("Les visiteurs suivants peuvent s'inscrire aux événements : "); ?></td>
          <td>
            <select name="event_subscription">
              <option value="0" <?php echo (isset($configuration['event_subscription']) && $configuration['event_subscription'] == 0) ? "selected='selected'" : ""; ?>><?php echo _("Personne ne peut s'inscrire"); ?></option>
              <option value="1" <?php echo (isset($configuration['event_subscription']) && $configuration['event_subscription'] == 1) ? "selected='selected'" : ""; ?>><?php echo _("Seulement les membres du site"); ?></option>
              <option value="2" <?php echo (isset($configuration['event_subscription']) && $configuration['event_subscription'] == 2) ? "selected='selected'" : ""; ?>><?php echo _("Tout le monde"); ?></option>
            </select>
          </td>
        </tr>
        <tr>
          <td><?php echo _("Paiement online"); ?></td>
          <td><input type="checkbox" name="event_online_payement" value="1" <?php echo (isset($configuration['event_online_payement']) && $configuration['event_online_payement'] == 1) ? "checked='checked'" : ""; ?> /></td>
        </tr>
        <tr>
          <td><?php echo _("Les visiteurs peuvent voir les inscrits"); ?></td>
          <td><input type="checkbox" name="event_view_subscriptions" value="1" <?php echo (isset($configuration['event_view_subscriptions']) && $configuration['event_view_subscriptions'] == 1) ? "checked='checked'" : ""; ?> /></td>
        </tr>
      </table>
      
      <!-- Ping sur le blog -->
      <span class='tab_selector' style='float:left;margin:0 5px'><?php echo _("Blog Ping"); ?></span>
      
      <table>
        <tr>
          <td><?php echo _("Publier les actualités sur le blog : "); ?></td>
          <td><input type="checkbox" name="publish_news" value="1" <?php echo (isset($configuration['publish_news']) && $configuration['publish_news'] == 1) ? "checked='checked'" : ""; ?> /></td>
        </tr>
        <tr>
          <td><?php echo _("Publier les articles du shop sur le blog : "); ?></td>
          <td><input type="checkbox" name="publish_shop_article" value="1" <?php echo (isset($configuration['publish_shop_article']) && $configuration['publish_shop_article'] == 1) ? "checked='checked'" : ""; ?> /></td>
        </tr>
	      <tr>
          <td><?php echo _("Publier les événements sur le blog : "); ?></td>
          <td><input type="checkbox" name="publish_event" value="1" <?php echo (isset($configuration['publish_event']) && $configuration['publish_event'] == 1) ? "checked='checked'" : ""; ?> /></td>
        </tr>
        <tr>
          <td><?php echo _("Adresse e-mail de publication : "); ?></td>
          <td><input type="text" name="publish_email_address" class="email" style="width:400px" value="<?php echo isset($configuration['publish_email_address']) ? $configuration['publish_email_address'] : ""; ?>" /></td>
        </tr>
      </table>
      
      <!-- Gestion du paiement sur le site -->
      <span class='tab_selector' style='float:left;margin:0 5px'><?php echo _("Paiement online"); ?></span>
      
      <table>
        <tr>
          <td colspan="2"><?php echo _("Sélectionner les moyens de paiements autorisés : "); ?></td>
        </tr>
        <tr>
          <td colspan="2">
            <input type="checkbox" name="payement_facture_avant" value="1" <?php echo (isset($configuration['payement_facture_avant']) && $configuration['payement_facture_avant'] == 1) ? "checked='checked'" : ""; ?> />
            Sur facture (paiement avant envoi)<br />
            
            <input type="checkbox" name="payement_facture_apres" value="1" <?php echo (isset($configuration['payement_facture_apres']) && $configuration['payement_facture_apres'] == 1) ? "checked='checked'" : ""; ?> />
            Sur facture (paiement après envoi)<br />
            
            <input type="checkbox" id="input_payement_bank" name="payement_bank" value="1"  onchange="javascript:toggle(this, $('payement_bank'));" <?php echo (isset($configuration['payement_bank']) && $configuration['payement_bank'] == 1) ? "checked='checked'" : ""; ?> />
            Virement bancaire (paiement avant envoi)<br />
            
            <div id="payement_bank" style="margin:10px 0;padding:5px;border:solid 1px #EEEEEE">
              <table>
                <tr>
                  <td>N° IBAN</td>
                  <td><input type="text" class="" name="payement_bank_iban" value="<?php echo ((isset($configuration['payement_bank_iban'])) ? $configuration['payement_bank_iban'] : "") ?>" /></td>
                </tr>
                <tr>
                  <td>N° SWIFT/BIC</td>
                  <td><input type="text" class="" name="payement_bank_bic" value="<?php echo ((isset($configuration['payement_bank_bic'])) ? $configuration['payement_bank_bic'] : "") ?>" /></td>
                </tr>
              </table>
            </div>
            
            <input type="checkbox" id="input_payement_paypal" name="payement_paypal" value="1" onchange="javascript:toggle(this, $('payement_paypal'));" <?php echo (isset($configuration['payement_paypal']) && $configuration['payement_paypal'] == 1) ? "checked='checked'" : ""; ?> />
            Paiement via Paypal (y compris cartes de crédit Visa + Mastercard)<br />
            
            <div id="payement_paypal" style="margin:10px 0;padding:5px;border:solid 1px #EEEEEE">
              <table>
                <tr>
                  <td>Compte Paypal</td>
                  <td><input type="text" class="email" name="payement_paypal_account" value="<?php echo ((isset($configuration['payement_paypal_account'])) ? $configuration['payement_paypal_account'] : "") ?>" /></td>
                </tr>
                <tr>
                  <td>Activer le sandbox (pour les tests)</td>
                  <td><input type="checkbox" name="payement_paypal_sandbox" value="1" <?php echo (isset($configuration['payement_paypal_sandbox']) && $configuration['payement_paypal_sandbox'] == 1) ? "checked='checked'" : ""; ?> /></td>
                </tr>
                <tr>
                  <td>Page de retour en cas de succès</td>
                  <td>
                    <select name="payement_paypal_url_return">
                      <?php
                        $pages = Miki_page::get_all_pages("name", false, "asc");
                        foreach($pages as $p){
                          echo "<option value='$p->id'";
                          echo (isset($configuration['payement_paypal_url_return']) && $configuration['payement_paypal_url_return'] == $p->id) ? " selected='selected'" : "";
                          echo ">$p->name</option>\n";
                        }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>Page de retour en cas d'erreur</td>
                  <td>
                    <select name="payement_paypal_url_return_error">
                      <?php
                        $pages = Miki_page::get_all_pages("name", false, "asc");
                        foreach($pages as $p){
                          echo "<option value='$p->id'";
                          echo (isset($configuration['payement_paypal_url_return_error']) && $configuration['payement_paypal_url_return_error'] == $p->id) ? " selected='selected'" : "";
                          echo ">$p->name</option>\n";
                        }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                  <td style="vertical-align:top">Activer le chiffrement des données</td>
                  <td>
                    <input type="checkbox" id="input_payement_paypal_secure" name="payement_paypal_secure" value="1"  onchange="javascript:toggle(this, $('payement_paypal_secure'));" <?php echo (isset($configuration['payement_paypal_secure']) && $configuration['payement_paypal_secure'] == 1) ? "checked='checked'" : ""; ?> />
                  </td>
                </tr>
                <tr>
                  <td colspan="2">
                    <div id="payement_paypal_secure" style="margin:10px 0;padding:5px;border:solid 1px #EEEEEE">
                      <table>
                        <tr>
                          <td colspan="2">
                            Pour activer le chiffrement Paypal, vous devez posséder différents certificats et les activer sur votre compte Paypal.
                            <br />
                            Vous pouvez suivre <a id="link_help_paypal" href="#help_paypal_certificate" target="_blank" title="Comment faire">l'exemple suivant</a>.
                            <br /><br />
                          </td>
                        </tr>
                        <tr>
                          <td>ID de certificat récupéré </td>
                          <td><input type="text" class="" name="payement_paypal_idcert" value="<?php echo ((isset($configuration['payement_paypal_idcert'])) ? $configuration['payement_paypal_idcert'] : "") ?>" /></td>
                        </tr>
                      </table>
                    </div>
                  </td>
                </tr>
              </table>
            </div>
            
          </td>
        </tr>
      </table>
    </div>
    
    <div style="margin-top:10px">
      <input type="submit" value="<?php echo _("Envoyer"); ?>" />
    </div>
      
  </form>
  
  
  <!-- Pour l'aide sur les certificats Paypal -->
  <div style="display:none">
    <div id="help_paypal_certificate">
      <ol style="type:1">
        <li style="margin-bottom:10px">
          Créer un certificat en <a href="http://www.stellarwebsolutions.com/certificates/stellar_cert_builder.php" target="_blank" title="Créer un certificat">cliquant ici</a> et récupérer la Private Key et le Public certificate.
        </li>
        <li style="margin-bottom:10px">
          Renommer la Private Key en <span style="font-weight:bold">my-prvkey.pem</span> et le Public certificate en <span style="font-weight:bold">my-pubcert.pem</span>.
          <br />
          Placer ces deux fichiers dans le répertoire "paypal" de votre site Internet.  
        </li>
        <li style="margin-bottom:10px">
          Aller dans l’administration de votre compte Paypal, cliquer sur "Préférences" puis sur "Certificats de cryptage pour site marchand". Cliquer sur "Télécharger" et récupérer <span style="font-weight:bold">paypal_cert_pem.txt</span> puis le renommer en <span style="font-weight:bold">paypal_cert.pem</span>.
          <br />
          Placer également ce fichier dans le répertoire "paypal" de votre site Internet.
        </li>
        <li style="margin-bottom:10px">
          Toujours dans la page "Certificats pour site marchand", cliquer sur "Ajouter" et sélectionnez votre certificat public généré à l’étape 1. 
          Il vous attribue un numéro de certificat à conserver précieusement et à insérer dans le champ "ID de certificat récupéré" du formulaire.
        </li>
      </ol>
    </div>
  </div>
    
</div>