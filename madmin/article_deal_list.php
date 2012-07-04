<head>
  <?php
    if (!test_right(51) && !test_right(52) && !test_right(53))
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      
    // si pas d'id d'article spécifié, on retourne à la page précédente
    if (!isset($_GET['id']) || !is_numeric($_GET['id']))
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    else
      $id = $_GET['id'];
      
    try{
      $article = new Miki_shop_article($id);
      $deals = Miki_deal::get_all_deals($article->id);
    }
    catch(Exception $e){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
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
      document.location = 'article_deal_delete.php?id=' + ids;
  }
}

</script>
<div id="arianne">
  <a href="#"><?php echo _("Shop"); ?></a> > <a href="index.php?pid=143"><?php echo _("Liste articles"); ?></a> > Deals
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Deals de l'article '") .$article->get_name('fr') ."'"; ?></h1>  
  
  <div style="margin-bottom:10px">
    <?php 
    if (test_right(51)){ ?>
      <a href="index.php?pid=262&id=<?php echo $article->id; ?>" title="<?php echo _("Ajouter un deal pour cet article"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un deal pour cet article"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un deal pour cet article"); ?></a>
    <?php } ?>
  </div>
  
  <table id="main_table">
    <tr class="headers">
      <td style="width:20%;color:#ffffff;font-weight:bold"><?php echo _("Date de début"); ?></td>
      <td style="width:20%;color:#ffffff;font-weight:bold"><?php echo _("Date de fin"); ?></td>
      <td style="width:25%;color:#ffffff;font-weight:bold"><?php echo _("Prix"); ?></td>
      <td style="width:20%;color:#ffffff;font-weight:bold; text-align: center;"><?php echo _("Quantité restante"); ?></td>
      <td style="width:15%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      if (sizeof($deals) == 0)
        echo "<tr><td colspan='5'>" ._("Aucun deal n'est disponible pour cet article.") ."</td></tr>";
      
      $n = 0;
      //for ($x=0; $x < ($max_elements + $start) && $x < sizeof($articles); $x++){
      foreach($deals as $deal){
        $date_start = explode(" ", $deal->date_start);
        $time_start = explode(":", $date_start[1]);
        $date_start = explode("-", $date_start[0]);
        $date_start = $date_start[2] .'/' .$date_start[1] .'/' .$date_start[0] .'<br />' .$time_start[0] .'h' .$time_start[1] .'mn';
        
        $date_stop   = explode(" ", $deal->date_stop);
        $time_stop   = explode(":", $date_stop[1]);
        $date_stop   = explode("-", $date_stop[0]);
        $date_stop = $date_stop[2] .'/' .$date_stop[1] .'/' .$date_stop[0] .'<br />' .$time_stop[0] .'h ' .$time_stop[1] .'mn';
        
                
        // détecte la class
        if ($n === 1)
          $class = "line1";
        else
          $class = "line2";
        
        $n = ($n+1)%2;
        echo "
          <tr id='$deal->id' class='pages' onmouseover=\"colorLine('$deal->id');\" onmouseout=\"uncolorLine('$deal->id');\">
            <td class='$class' style='height:2em'>
              $date_start
            </td>
            <td class='$class' style='height:2em'>
              $date_stop
            </td>
            <td class='$class' style='height:2em'>"
              .number_format($deal->price,2,'.',"'") ." CHF
            </td>
            <td class='$class' style='height:2em; text-align: center;'>
              $deal->quantity / $deal->quantity_start (" .round(($deal->quantity * 100 / $deal->quantity_start), 2) ."%)
            </td>
            <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>";
              if (test_right(52))
                echo "<span style='margin-right:10px'><a href='index.php?pid=263&id=$deal->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
              if (test_right(53))
                echo "<span style='margin-right:10px'><a class='delete' href='article_deal_delete.php?id=$deal->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
              
              echo "<span><input type='checkbox' class='check_element' element_id='$deal->id' /></span>
            </td>
          </tr>";
      }
  ?>
  </table>
  
  <div style="margin-top:10px">
    <?php 
    if (test_right(52)){ ?>
      <a href="index.php?pid=262&id=<?php echo $article->id; ?>" title="<?php echo _("Ajouter un deal pour cet article"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter un deal pour cet article"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter un deal pour cet article"); ?></a>
    <?php } ?>
  </div>
  
   <div style="margin-top:10px;float:right">
    <?php echo _('Objets sélectionnés : '); ?>
    <select name="action" id="action">
      <?php 
        if (test_right(52)){ 
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