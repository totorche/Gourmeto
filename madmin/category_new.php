<?php

if (!test_right(25))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

if (isset($_SESSION['saved_name'])) 
  $name = $_SESSION['saved_name'];
else
  $name = "";
  
if (isset($_SESSION['saved_parent'])) 
  $parent = $_SESSION['saved_parent'];
else
  $parent = "";
  
unset($_SESSION['saved_name']); 
unset($_SESSION['saved_parent']); 

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
  <a href="index.php?pid=1"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=81"><?php echo _("Catégories"); ?></a> > <?php echo _("Ajouter une catégorie"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajouter une catégorie"); ?></h1>
  <form id="formulaire" action="category_test_new.php" method="post" name="formulaire" enctype="application/x-www-form-urlencoded">
    <table id="main_table_form">
      <tr>
        <td class="form_text"><?php echo _("Nom : "); ?> <span style="color:#ff0000">*</span></td>
        <td class="form_box"><input type="text" name="name" style="width:200px" class="required" value="<?php echo $name; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Parent : "); ?></td>
        <td class="form_box">
          <select name="parent">
            <option value="-1"><?php echo _("Aucun"); ?></option>
            <?php 
              $elements = Miki_category::get_all_categories(false);
              foreach ($elements as $el){
                echo "<option value='$el->id' "; 
                if ($el->id == $parent) 
                  echo "selected='selected'"; 
                echo ">$el->name</option>";
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=81'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
    </table>
  </form>
</div>