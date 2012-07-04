<?php

if (!test_right(5)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

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
  
if (isset($_SESSION['saved_content_type'])) 
  $content_type = $_SESSION['saved_content_type'];
else
  $content_type = "code";

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
                                        
    // affiche l'éditeur de code ou la sélection du fichier selon le type de contenu
    var select = $$('select[name=content_type]');
    change_content_type(select.value);
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
  
  // lorsque l'on change de type de contenu
  function change_content_type(value){
    if (value == 'file'){
      $('editor').setStyle('display','none');
      $('file').setStyle('display','block');
    }
    else if (value == 'code'){
      $('file').setStyle('display','none');
      $('editor').setStyle('display','block');
    }
  }

</script>

<div id="arianne">
  <a href="#"><?php echo _("Disposition"); ?></a> > <a href="index.php?pid=21"><?php echo _("Gabarits"); ?></a> > <?php echo _("Ajouter un gabarit"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajouter un gabarit"); ?></h1>
  <form id="formulaire" action="template_test_new.php" method="post" name="formulaire" enctype="multipart/form-data">
    <input type="hidden" name="parts" id="part_input" />
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
        <td class='form_text'><?php echo _("Type de contenu : "); ?></td>
        <td class='form_box'>
          <select name='content_type' onchange='javacript: change_content_type(this.value);'>
            <option value='file' <?php if ($content_type == 'file') echo "selected='selected'"; ?>><?php echo _("Fichier"); ?></option>
            <option value='code' <?php if ($content_type == 'code') echo "selected='selected'"; ?>><?php echo _("Code"); ?></option>
          </select>
        </td>
      </tr>
      <tr>
        <td class="form_text"><?php echo _("Contenu : "); ?></td>
        <td class="form_box">
          <div id='editor' <?php if ($content_type != 'code') echo "style='display:none'"; ?>>
            <textarea class='tinymce' id='code' name='code' style='width:980px;height:600px'><?php if (isset($content) && $content_type == 'code') echo $content; ?></textarea>
          </div>
          <div id='file' <?php if ($content_type != 'file') echo "style='display:none'"; ?>>
            <input type='file' name='file' />
          </div>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td style="height:250px">
            <a name="sections"></a>
            <div style="float:left;width:380px;height:200px">
              <?php echo _("Sections disponibles"); ?>
              <div id="part_left" style="background-color:#d3d3d3;border:solid 1px #999999;float:left;width:100%;height:100%;overflow-y:scroll">
              
                <?php
                  try{
                    $parts = Miki_template_part::get_all_parts();
                    foreach($parts as $part){
                      echo "<div style='width:100%;cursor:pointer' class='part' onclick='javascript:part_click(this)' part_id='$part->id'>$part->name</div>";
                    }
                  }catch(Exception $e){
                    echo _("Aucune section n'est disponible");
                  }
                ?>
              
              </div>
            </div>
            
            <div style="float:left;width:40px;margin-top:100px;text-align:center">
              <a href="#sections" onclick="javascript:part_select()" title="<?php echo _("Ajouter au gabarit"); ?>"><img src="./pictures/arrow-r.gif" alt="<?php echo _("Ajouter au gabarit"); ?>" border="0" /></a><br /><br />
              <a href="#sections" onclick="javascript:part_unselect()" title="<?php echo _("Retirer du gabarit"); ?>"><img src="./pictures/arrow-l.gif" alt="<?php echo _("Retirer du gabarit"); ?>" border="0" /></a>
            </div>
            
            <div style="float:left;width:380px;height:200px">
              <?php echo _("Sections sélectionnées"); ?>
              <div id="part_right" style="background-color:#d3d3d3;border:solid 1px #999999;float:left;width:100%;height:100%;overflow-y:scroll">
              </div>
            </div>
        </td>
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
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=21'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" onclick="javascript:get_parts();" />
        </td>
    </table>
  </form>
</div>