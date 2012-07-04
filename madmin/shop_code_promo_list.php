<head>
  <?php
    if (!test_right(47)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    
    try{
      $codes_promo = Miki_shop_code_promo::get_all_codes_promo();
    }
    catch(Exception $e){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    
    // test que l'on aie bien un shop de configuré
    $shops = Miki_shop::get_all_shops();
    if (sizeof($shops) == 0){
      $shop = false;
    }
    else
      $shop = array_shift($shops);
      
    if (!$shop){
      echo "<div>
              Vous n'avez aucune shop de configuré pour le moment.<br /><br />
              <input type='button' value='Créer un shop' onclick=\"document.location='index.php?pid=142'\" />
            </div>";
      exit();
    }
  ?>
</head>

<script type="text/javascript">

// colorie une ligne d'un tableau
function colorLine(lineId){
  var td = $(lineId).getElements('td');
  td.each(function(item){
    item.addClass('mouseOver');
  });
}

// remet une ligne dans sa couleur normale
function uncolorLine(lineId){
  var td = $(lineId).getElements('td');
  td.each(function(item){
    item.removeClass('mouseOver');
  });
}

// coche ou décoche tous les éléments de la liste
function check_element(item){
  $$('.check_element').each(function(el){
    el.checked = item.checked;
  });
}

function action_send(){
  var ids = "";
  // récupert les pages cochées
  $$('.check_element').each(function(el){
    if (el.checked)
      ids += ";" + el.get('element_id');
  });
  
  // si aucune page n'est cochée
  if (ids == "")
    return false;
  
  // enlève le 1er ';'
  ids = ids.substring(1);
  
  // effectue l'opération demandée
  if ($('action').value == "delete"){
    if (confirm("<?php echo _('Etes-vous sûr de vouloir supprimer les éléments sélectionnés ?'); ?>"))
      document.location = 'shop_code_promo_delete.php?id=' + ids;
  }
}

</script>
<div id="arianne">
  <a href="#"><?php echo _("Shop"); ?></a> > <a href="index.php?pid=143"><?php echo _("Codes de promotion"); ?></a> > Promotions
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Codes de promotion"); ?></h1>  
  
  <div style="margin-bottom:10px">
    <?php 
    if (test_right(47)){ ?>
      <a href="index.php?pid=157" title="<?php echo _("Ajouter un code de promotion"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une promotion pour cet article"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un code de promotion"); ?></a>
    <?php } ?>
  </div>
  
  <table id="main_table">
    <tr class="headers">
      <td style="width:25%;color:#ffffff;font-weight:bold"><?php echo _("Code"); ?></td>
      <td style="width:15%;color:#ffffff;font-weight:bold"><?php echo _("Date de début"); ?></td>
      <td style="width:15%;color:#ffffff;font-weight:bold"><?php echo _("Date de fin"); ?></td>
      <td style="width:15%;color:#ffffff;font-weight:bold"><?php echo _("Rabais"); ?></td>
      <td style="width:15%;color:#ffffff;font-weight:bold"><?php echo _("Type de rabais"); ?></td>
      <td style="width:15%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      if (sizeof($codes_promo) == 0)
        echo "<tr><td colspan='6'>" ._("Aucun code de promotion n'est disponible pour le moment.") ."</td></tr>";
      
      $n = 0;
      //for ($x=0; $x < ($max_elements + $start) && $x < sizeof($articles); $x++){
      foreach($codes_promo as $code){
        $date_start = explode("-", $code->date_start);
        $date_start = $date_start[2] .'/' .$date_start[1] .'/' .$date_start[0];
        
        $date_stop = explode("-", $code->date_stop);
        $date_stop = $date_stop[2] .'/' .$date_stop[1] .'/' .$date_stop[0];
        
                
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        echo "
          <tr id='$code->id' class='pages' onmouseover=\"colorLine('$code->id');\" onmouseout=\"uncolorLine('$code->id');\">
            <td class='$class' style='height:2em'>
              $code->code
            </td>
            <td class='$class' style='height:2em'>
              $date_start
            </td>
            <td class='$class' style='height:2em'>
              $date_stop
            </td>
            <td class='$class' style='height:2em'>"
              .number_format($code->discount,2,'.',"'") ."
            </td>
            <td class='$class' style='height:2em'>";
              
              if ($code->type == 1)
                echo " CHF";
              elseif ($code->type == 2)
                echo " %";
              
      echo "</td>
            <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>";
              if (test_right(47))
                echo "<span style='margin-right:10px'><a href='index.php?pid=158&id=$code->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
              if (test_right(47))
                echo "<span style='margin-right:10px'><a class='delete' href='shop_code_promo_delete.php?id=$code->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
              
              echo "<span><input type='checkbox' class='check_element' element_id='$code->id' /></span>
            </td>
          </tr>";
      }
  ?>
  </table>
  
  <div style="margin-top:10px">
    <?php 
    if (test_right(47)){ ?>
      <a href="index.php?pid=157" title="<?php echo _("Ajouter un code de promotion"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une promotion pour cet article"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un code de promotion"); ?></a>
    <?php } ?>
  </div>
  
   <div style="margin-top:10px;float:right">
    <?php echo _('Objets sélectionnés : '); ?>
    <select name="action" id="action">
      <?php 
        if (test_right(47)){
      ?>
      <option value="delete"><?php echo _('Supprimer'); ?></option>
      <?php } ?>
    </select>&nbsp;
    <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
  </div>
  <div style="clear:both"></div>
  
</div>

<?php
  // stock l'url actuel pour un retour après opérations
  $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
?>