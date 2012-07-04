<?php

if (!test_right(31) && !test_right(32))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

if (isset($_SESSION['saved_name'])) 
  $name = $_SESSION['saved_name'];
else
  $name = "";

if (isset($_SESSION['saved_stylesheet'])) 
  $stylesheet = $_SESSION['saved_stylesheet'];
else
  $stylesheet = "";
  
if (isset($_SESSION['saved_content'])) 
  $content = $_SESSION['saved_content'];
else
  $content = "";
  
if (isset($_SESSION['saved_active'])) 
  $active = $_SESSION['saved_active'] == 1 ? true : false;
else
  $active = true;
  
unset($_SESSION['saved_name']);
unset($_SESSION['saved_stylesheet']);
unset($_SESSION['saved_content']); 
unset($_SESSION['saved_active']); 

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
  <a href="#"><?php echo _("Newsletter"); ?></a> > <a href="index.php?pid=111"><?php echo _("Gabarits"); ?></a> > <?php echo _("Ajouter un gabarit"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajouter un gabarit"); ?></h1>
  <form id="formulaire" action="newsletter_template_test_new.php" method="post" name="formulaire" enctype="application/x-www-form-urlencoded">
    <table id="main_table_form">
      <tr>
        <td class="form_text"><?php echo _("Nom : "); ?> <span style="color:#ff0000">*</span></td>
        <td class="form_box"><input type="text" name="name" style="width:200px" class="required" value="<?php echo $name; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("feuille de style : "); ?></td>
        <td class="form_box">
          <select name="stylesheet">
            <option value="-1"><?php echo _("Aucune"); ?></option>
            <?php 
              $elements = Miki_stylesheet::get_all_stylesheets(false);
              foreach ($elements as $el){
                echo "<option value='$el->id' "; if ($el->id == $stylesheet) echo "selected='selected'"; echo ">$el->name</option>";
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Contenu : "); ?></td>
        <td class="form_box"><textarea name="content" style="width:800px;height:500px"><?php echo $content; ?></textarea></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Actif : "); ?></td>
        <td class="form_box"><input type="checkbox" name="active" <?php if ($active) echo "checked='checked'"; ?>" value="1" /></td>
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