<?php

if (!test_right(26))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

// si pas d'id donné, retour
if (!isset($_GET['id']) || !is_numeric($_GET['id']))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

$id = $_GET['id'];

try{
  $cat = new Miki_category($id);
}catch(Exception $e){
  // si aucun enregistrement n'a été trouvé -> retour
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();

}
  
// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

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
  <a href="index.php?pid=1"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=81"><?php echo _("Catégories"); ?></a> > <?php echo _("Modifier une catégorie"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajouter une catégorie"); ?></h1>
  <form id="formulaire" action="category_test_edit.php" method="post" name="formulaire" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <table id="main_table_form">
      <tr>
        <td class="form_text"><?php echo _("Nom : "); ?> <span style="color:#ff0000">*</span></td>
        <td class="form_box"><input type="text" name="name" style="width:200px" class="required" value="<?php echo $cat->name; ?>"></td>
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
                if ($el->id == $cat->parent_id) 
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