<?php

// si pas d'id donné, retour
if (!isset($_GET['id']) || !is_numeric($_GET['id']))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
if (!test_right(29))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

$id = $_GET['id'];
$menu = "";

try{
  $menu = new Miki_menu($id);
}catch(Exception $e){
  // si aucun enregistrement n'a été trouvé -> retour
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();

}

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

// récupert la date de la dernière modification
$date_modification = explode(" ",$menu->date_modification);
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
  <a href="#"><?php echo _("Disposition"); ?></a> > <a href="index.php?pid=91"><?php echo _("menus"); ?></a> > <?php echo _("Modifier un menu"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modifier un menu"); ?></h1>
  <form id="formulaire" action="menu_test_edit.php" method="post" name="formulaire" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <table id="main_table_form">
      <tr>
        <td class="form_text"><?php echo _("Nom : "); ?> <span style="color:#ff0000">*</span></td>
        <td class="form_box"><input type="text" name="name" style="width:200px" class="required" value="<?php echo $menu->name; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("feuille de style : "); ?></td>
        <td class="form_box">
          <select name="stylesheet">
            <option value="-1"><?php echo _("Aucune"); ?></option>
            <?php 
              $elements = Miki_stylesheet::get_all_stylesheets(false);
              foreach ($elements as $el){
                echo "<option value='$el->id' "; if ($el->id == $menu->stylesheet_id) echo "selected='selected'"; echo ">$el->name</option>";
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Contenu : "); ?></td>
        <td class="form_box"><textarea name="content" style="width:800px;height:500px"><?php echo $menu->content; ?></textarea></td>
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
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=91'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
    </table>
  </form>
</div>