<?php

// si pas d'id donné, retour
if (!isset($_GET['id']) || !is_numeric($_GET['id']))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
if (!test_right(14))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

$id = $_GET['id'];

$el = "";
$tab_contents = "";
try{
  // recherche les informations l'élément donnée en paramètre
  $el = new Miki_template_part($id);
  $tab_contents = $el->get_global_contents();
}catch(Exception $e){
  // si aucun enregistrement n'a été trouvé -> retour
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();

}

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

// fonction pour tester si un objet existe dans un tableau
function object_exist($object,$tab){
  foreach($tab as $el){
    if ($el->id == $object->id)
      return true;
  }
  return false;
}

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
  
  var partSelected = "";
  
  // lors d'un clique sur une section
  function part_click(part){
    if (partSelected != "" && part.get('part_id') == partSelected.get('part_id')){
      part.setStyle('backgroundColor','transparent');
      partSelected = "";
    }
    else{
      $$('.part').each(function(el,index){
        el.setStyle('backgroundColor','transparent');
      });
      part.setStyle('backgroundColor','#ffffff');
      partSelected = part;
    }
  }
  
  // sélectionne une section
  function part_select(){
    if (partSelected != ""){
      var newPart = new Element(partSelected);
      partSelected.dispose();
      newPart.inject($('part_right'));
      return false;
    }
  }
  
  // désélectionne une section
  function part_unselect(){
    if (partSelected != ""){
      var newPart = new Element(partSelected);
      partSelected.dispose();
      newPart.inject($('part_left'));
      return false;
    }
  }
  
  // récupert toutes les sections sélectionnées
  function get_parts(){
    var parts = "";
    $$('#part_right div').each(function(el){
      parts += ";" + el.get('part_id');
    });
    $('part_input').set("value",parts.substring(1));
  }

</script>

<div id="arianne">
  <a href="#"><?php echo _("Disposition"); ?></a> > <a href="index.php?pid=31"><?php echo _("Section"); ?></a> > <?php echo _("Modifier une section"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modifier une section"); ?></h1>
  <form id="formulaire" action="template_part_test_edit.php" method="post" name="formulaire" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="contents" id="part_input" />
    <table id="main_table_form">
      <tr>
        <td class="form_text"><?php echo _("Nom : "); ?> <span style="color:#ff0000">*</span></td>
        <td class="form_box"><input type="text" name="name" style="width:200px" class="required" value="<?php echo $el->name; ?>"></td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Contenu : "); ?></td>
        <td class="form_box">
          <textarea name="content" style="width:800px;height:500px"><?php echo $el->content; ?></textarea><br />
          <?php echo _("Rappel : La balise pour intégrer les blocs de contenu ci-dessous est la suivante : [miki_part_content]"); ?>
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td style="height:250px">
            <a name="sections"></a>
            <div style="float:left;width:380px;height:200px">
              <?php echo _("Blocs de contenu globaux disponibles"); ?>
              <div id="part_left" style="background-color:#d3d3d3;border:solid 1px #999999;float:left;width:100%;height:100%;overflow-y:scroll">
              
                <?php
                  try{
                    $contents = Miki_global_content::get_all_global_contents();
                    foreach($contents as $content){
                      if (!object_exist($content, $tab_contents))
                        echo "<div style='width:100%;cursor:pointer' class='part' onclick='javascript:part_click(this)' part_id='$content->id'>$content->name</div>";
                    }
                  }catch(Exception $e){
                    echo _("Aucun bloc de contenu global n'est disponible");
                  }
                ?>
              
              </div>
            </div>
            
            <div style="float:left;width:40px;margin-top:100px;text-align:center">
              <a href="#sections" onclick="javascript:part_select()" title="<?php echo _("Ajouter au gabarit"); ?>"><img src="./pictures/arrow-r.gif" alt="<?php echo _("Ajouter au gabarit"); ?>" border="0" /></a><br /><br />
              <a href="#sections" onclick="javascript:part_unselect()" title="<?php echo _("Retirer du gabarit"); ?>"><img src="./pictures/arrow-l.gif" alt="<?php echo _("Retirer du gabarit"); ?>" border="0" /></a>
            </div>
            
            <div style="float:left;width:380px;height:200px">
              <?php echo _("Blocs de contenu globaux sélectionnés"); ?>
              <div id="part_right" style="background-color:#d3d3d3;border:solid 1px #999999;float:left;width:100%;height:100%;overflow-y:scroll">
              
                <?php
                  foreach($tab_contents as $content){
                    echo "<div style='width:100%;cursor:pointer' class='part' onclick='javascript:part_click(this)' part_id='$content->id'>$content->name</div>";
                  }
                ?>
              
              </div>
            </div>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=31'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" onclick="javascript:get_parts();" />
        </td>
      </tr>
    </table>
  </form>
</div>