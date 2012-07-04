<head>

  <?php
    require_once ("config/event_category.php");
    
    // récupert les date de début et de fin (y compris les heures et minutes)
    if (isset($_SESSION['event'])){
      if (!empty($_SESSION['event']->date_start)){
        // pour le début
        $datetime = $_SESSION['event']->date_start;
        $datetime = explode(" ", $datetime);
        
        $date        = $datetime[0];
        $date        = explode("-", $date);
        $year_start  = $date[0];
        $month_start = $date[1];
        $day_start   = $date[2]; 
            
        $time         = $datetime[1];
        $time         = explode(":", $time);
        $hour_start   = $time[0];
        $minute_start = $time[1];
      }
      
      if (!empty($_SESSION['event']->date_stop)){
        // pour la fin
        $datetime = $_SESSION['event']->date_stop;
        $datetime = explode(" ", $datetime);
        
        $date       = $datetime[0];
        $date       = explode("-", $date);
        $year_stop  = $date[0];
        $month_stop = $date[1];
        $day_stop   = $date[2];    
        
        $time        = $datetime[1];
        $time        = explode(":", $time);
        $hour_stop   = $time[0];
        $minute_stop = $time[1];
      }
    }
  ?>
  
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.15/i18n/jquery.ui.datepicker-fr-CH.min.js"></script>
  <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
  
  <script type="text/javascript">
  
    $(document).ready(function() {
      $.datepicker.setDefaults($.datepicker.regional['fr']);
      $('#date_start').datepicker();
      $('#date_stop').datepicker();
      
      $("#form_add_event").validate({
        rules: {
          title: "required",
          address: "required",
          npa: {
            required: true,
            digits: true
          },
          city: "required",
          country: "required"
        },
      });
      
      // récupert le lieu d'où la personne se connecte
      if(navigator.geolocation){
  	    navigator.geolocation.getCurrentPosition(function(position){
  	        var latitude = position.coords.latitude;
  	        var longitude = position.coords.longitude;
  	        var altitude = position.coords.altitude;
  	        
            var street_number, street, locality, country, postal_code;
  	        
  	        var geocoder = new google.maps.Geocoder();
            var latlng = new google.maps.LatLng(latitude, longitude);
            geocoder.geocode( {'latLng': latlng, 'language': 'french'}, function(results, status) {
              $.each(results[0]['address_components'], function(key, val) {
                if (val['types'].indexOf('street_number') != -1)
                  street_number = val['long_name'];
                else if (val['types'].indexOf('route') != -1)
                  street = val['long_name'];
                else if (val['types'].indexOf('locality') != -1)
                  locality = val['long_name'];
                else if (val['types'].indexOf('country') != -1)
                  country = val['long_name'];
                else if (val['types'].indexOf('postal_code') != -1)
                  postal_code = val['long_name'];
              });
              street += ' ' + street_number;
              $('input[name=country]').val(country);
              $('input[name=city]').val(locality);
              $('input[name=npa]').val(postal_code); 
              $('input[name=address]').val(street); 
            });
  	    });
    	}
      
      var filesUpload = $("input[name=picture1]");
      filesUpload.bind("change", function () {
        traverseFiles(this.files);
      });
    
    });
  
  
    var nb_pictures = 1;
    
    // ajouter un logo supplémentaire
    function add_logo(){
      event.preventDefault();
      nb_pictures++;
      
      $('#ajouter_logo').before('<br />');
      $('#ajouter_logo').before("<input type='file' name='picture" + nb_pictures + "' />");
      $('#nb_pictures').val(nb_pictures);
    }
    
    function traverseFiles(files){
      var text = '';
      for (var i=0, l=files.length; i<l; i++) {
        var file = files[i];
        if (typeof FileReader !== "undefined" && (/image/i).test(file.type)) {
        	var img = document.createElement("img");
        	$(img).css('max-height','100px');
        	$(img).css('max-width','100px');
        	$("input[name=picture1]").after(img);
        	reader = new FileReader();
        	reader.onload = (function (theImg) {
        		return function (evt) {
        			theImg.src = evt.target.result;
        		};
        	}(img));
        	reader.readAsDataURL(file);
        }
      }
    }
    
    
  </script>
  
  <style type="text/css">
    #form_add_event{
      background: url(<?php echo URL_BASE; ?>pictures/bonhomme-event.jpg) no-repeat right bottom;
    }
  </style>
</head>
  
<?php
  if (isset($_SESSION['error_msg']) && $_SESSION['error_msg'] != ""){
    echo "<div class='box_result_error'>
            <p>" .$_SESSION['error_msg'] ."</p>
          </div>";
          
    unset($_SESSION['error_msg']);
  }
?>

<form id="form_add_event" class="form_bleu" action="<?php echo URL_BASE; ?>event_ajouter_test.php" method="post" name="formulaire" enctype="multipart/form-data">
  <input type="hidden" name="nb_pictures" id="nb_pictures" value="1" />
  
  <table>
    <tr>
      <td><?php echo _("Catégorie"); ?> <span style="color:#ff0000">*</span></td>
      <td>
        <select name="category">
          <?php
            foreach($categories as $key=>$cat){
              echo "<option value=\"$key\"";
              if (isset($_SESSION['event']) && $_SESSION['event']->category == $key) echo "selected='selected'";
              echo ">$cat</option>"; 
            }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Nom"); ?> <span style="color:#ff0000">*</span></td>
      <td><input type="text" name="title" style="width: 400px;" value="<?php if (isset($_SESSION['event'])) echo $_SESSION['event']->title; ?>" /></td>
    </tr>
    <tr>
      <td><?php echo _("Description"); ?></td>
      <td><textarea name="description"><?php if (isset($_SESSION['event'])) echo $_SESSION['event']->description; ?></textarea></td>
    </tr>
    <tr>
      <td style="vertical-align:top"><?php echo _("Mots-clé"); ?></td>
      <td>
        <input type="text" name="tags" style="width: 400px;" value="<?php if (isset($_SESSION['event'])) echo $_SESSION['event']->tags; ?>" /><br>
        <?php echo _("Séparer les mots-clé par une virgule"); ?>  
      </td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td><?php echo _("Adresse"); ?> <span style="color:#ff0000">*</span></td>
      <td><input type="text" name="address" value="<?php if (isset($_SESSION['event'])) echo $_SESSION['event']->address; ?>" /></td>
    </tr>
    <tr>
      <td><?php echo _("Numéro postal"); ?> <span style="color:#ff0000">*</span></td>
      <td><input type="text" name="npa" value="<?php if (isset($_SESSION['event'])) echo $_SESSION['event']->npa; ?>" /></td>
    </tr>
    <tr>
      <td><?php echo _("Localité"); ?> <span style="color:#ff0000">*</span></td>
      <td><input type="text" name="city" value="<?php if (isset($_SESSION['event'])) echo $_SESSION['event']->city; ?>" /></td>
    </tr>
    <tr>
      <td><?php echo _("Pays"); ?> <span style="color:#ff0000">*</span></td>
      <td><input type="text" name="country" value="<?php if (isset($_SESSION['event'])) echo $_SESSION['event']->country; ?>" /></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td><?php echo _("Organisateur"); ?></td>
      <td><input type="text" name="organizer" value="<?php if (isset($_SESSION['event'])) echo $_SESSION['event']->organizer; ?>" />&nbsp;&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td><?php echo _("Date de début"); ?></td>
      <td>
        <input id="date_start" name="date_start" type="text" maxlength="10" value="<?php if (isset($date_start)) echo $date_start; ?>"  />&nbsp;&nbsp;&nbsp;
        <?php echo _("Heure"); ?> : 
        <select name="hour_start">
          <?php
            for ($x=1; $x<=24; $x++){
              $value = $x;
              if (strlen($value) == 1)
                $value = '0' .$value;
              echo "<option value='$value'";
              if (isset($hour_start) && $hour_start == $value) echo "selected='selected'";
              echo ">$value</option>";
            }
          ?>
        </select> :
        <select name="minute_start">
          <?php
            for ($x=0; $x<=45; $x+=15){
              $value = $x;
              if (strlen($value) == 1)
                $value = '0' .$value;
              echo "<option value='$value'";
              if (isset($hour_start) && $minute_start == $value) echo "selected='selected'";
              echo ">$value</option>";
            }
          ?>
        </select> 
      </td>
    </tr>
    <tr>
      <td><?php echo _("Date de fin"); ?></td>
      <td>
        <input id="date_stop" name="date_stop" type="text" maxlength="10" value="<?php if (isset($date_stop)) echo $date_stop; ?>" />&nbsp;&nbsp;&nbsp;
        <?php echo _("Heure"); ?> : 
        <select name="hour_stop">
          <?php
            for ($x=1; $x<=24; $x++){
              $value = $x;
              if (strlen($value) == 1)
                $value = '0' .$value;
              echo "<option value='$value'";
              if (isset($hour_start) && $hour_stop == $value) echo "selected='selected'";
              echo ">$value</option>";
            }
          ?>
        </select> :
        <select name="minute_stop">
          <?php
            for ($x=0; $x<=45; $x+=15){
              $value = $x;
              if (strlen($value) == 1)
                $value = '0' .$value;
              echo "<option value='$value'";
              if (isset($hour_start) && $minute_stop == $value) echo "selected='selected'";
              echo ">$value</option>";
            }
          ?>
        </select> 
      </td>
    </tr>
    <tr>
      <td style="vertical-align:top"><?php echo _("Logos"); ?></td>
      <td>
        <a name="logos"></a>
        <input type="file" name="picture1" multiple />
        <span id="ajouter_logo">
          &nbsp;
          <a href="#logos" onclick="add_logo();" title="Ajouter un logo">
            <img src="<?php echo URL_BASE; ?>pictures/add.png" alt="Ajouter un logo" style="border:0;vertical-align:middle" />
          <a>
        </span>
      </td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" style="font-weight:bold"><?php echo _("Les champs munis d'une <span style='color:#ff0000'>*</span> sont obligatoires"); ?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" style="text-align:right">
        <div>
          <input border="0" class="button1" type="submit" value="<?php echo _("Annuler"); ?>" />
          &nbsp;&nbsp;
          <input border="0" class="button_big1" type="submit" value="<?php echo _("Envoyer"); ?>" />
        </div>
      </td>
    </tr>
  </table>
</form>

<?php
  // stock l'url actuel pour un retour après opérations
  $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
?>