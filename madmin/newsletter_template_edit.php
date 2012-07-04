<?php

// si pas d'id donné, retour
if (!isset($_GET['id']) || !is_numeric($_GET['id']))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
if (!test_right(31) && !test_right(32))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

$id = $_GET['id'];
$template = "";

try{
  $template = new Miki_newsletter_template($id);
}catch(Exception $e){
  // si aucun enregistrement n'a été trouvé -> retour
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();

}

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

// récupert la date de la dernière modification
$date_modification = explode(" ",$template->date_modification);
$date = explode("-",$date_modification[0]);
$heure = $date_modification[1];
$jour = $date[2];
$mois = $date[1];
$annee = $date[0];

?>

<script type="text/javascript" src="scripts/checkform.js"></script>

<script type="text/javascript">

  window.addEvent('domready', function() {
    var myCheckForm = new checkForm($('formulaire'),{
                                          useAjax: false,
                                          errorPlace: 'bottom',
                                          divErrorCss: {
                                            'margin':'5px 0 0 0px'
                                          }
                                        });
  });

</script>

<div id="arianne">
  <a href="index.php?pid=100"><?php echo _("Newsletter"); ?></a> > <a href="index.php?pid=111"><?php echo _("Gabarits"); ?></a> > <?php echo _("Modifier un gabarit"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modifier un gabarit"); ?></h1>
  <form id="formulaire" action="newsletter_template_test_edit.php" method="post" name="formulaire" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <table id="main_table_form">
      <tr>
        <td class="form_text"><?php echo _("Nom : "); ?> <span style="color:#ff0000">*</span></td>
        <td class="form_box"><input type="text" name="name" style="width:200px" class="required" value="<?php echo $template->name; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("feuille de style : "); ?></td>
        <td class="form_box">
          <select name="stylesheet">
            <option value="-1"><?php echo _("Aucune"); ?></option>
            <?php 
              $elements = Miki_stylesheet::get_all_stylesheets(false);
              foreach ($elements as $el){
                echo "<option value='$el->id' "; if ($el->id == $template->stylesheet_id) echo "selected='selected'"; echo ">$el->name</option>";
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Contenu : "); ?></td>
        <td class="form_box"><textarea name="content" style="width:800px;height:500px"><?php echo stripslashes($template->content); ?></textarea></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Actif : "); ?></td>
        <td class="form_box"><input type="checkbox" name="active" <?php if ($template->state == 1) echo "checked='checked'"; ?>" value="1" /></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Dernière modification : "); ?></td>
        <td class="form_box"><?php echo "$jour.$mois.$annee $heure"; ?></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>        
        <td colspan="2" class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=111'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
    </table>
  </form>
</div>