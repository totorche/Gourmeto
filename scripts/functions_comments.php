<?php
  // si on doit affiche la liste des commentaires (pour la mise à jour des commentaires après un post)
  if (isset($_REQUEST['print_comments']) && $_REQUEST['print_comments'] == 1 &&
      isset($_REQUEST['oclass']) &&
      isset($_REQUEST['oid']) && is_numeric($_REQUEST['oid'])){
    
    require_once("../include/headers.php");
    
    try{
      $object = new $_REQUEST['oclass']($_REQUEST['oid']);
      print_comments_list($object);
      exit();
    }
    catch(Exception $e){
      exit();
    } 
  }
  
  // si on doit poster un commentaire
  if(isset($_POST['comment_object_id']) && is_numeric($_POST['comment_object_id']) && 
    isset($_POST['comment_object_class']) && 
    isset($_POST['comment_rating']) && is_numeric($_POST['comment_rating']) &&
    isset($_POST['comment_text'])){
    
    // vérifie si on tourne sous Windows ou pas
    if (strpos($_SERVER['SERVER_SOFTWARE'], 'Win32') !== false || 
        strpos($_SERVER['SERVER_SOFTWARE'], 'Win64') !== false)
      $is_windows = true;
    else
      $is_windows = false;
    
    if ($is_windows)
      ini_set("include_path", ini_get("include_path") .";../");
    else
      ini_set("include_path", ini_get("include_path") .":../");  
      
    require_once("include/headers.php");
      
    post_comment($_REQUEST);
  }





  /**
   * Poste un commentaire
   */
  function post_comment($params){
    $ajax = (isset($params['ajax']) && $params['ajax'] == 1);
  
    // si la personne est loguée
    if(isset($params['comment_person_id']) && is_numeric($params['comment_person_id']) &&
      isset($params['comment_object_id']) && is_numeric($params['comment_object_id']) && 
      isset($params['comment_object_class']) && 
      isset($params['comment_rating']) && is_numeric($params['comment_rating']) &&
      isset($params['comment_text'])){
      
      try{
        $class = $params['comment_object_class'];
        $object = new $class($params['comment_object_id']);
        $text = nl2br(strip_tags(stripslashes($params['comment_text'])));
        $subscribe = (isset($params['comment_subscribe']) && $params['comment_subscribe'] == 1) ? true : false;
        $object->post_comment($params['comment_person_id'], $text, $params['comment_rating'], $subscribe);
        
        send_result(true, "", $ajax);
      }
      catch(Exception $e){
        send_result(false, $e->getMessage(), $ajax);
      }
    }
    // si la personne n'est pas loguée
    elseif(isset($params['comment_object_id']) && is_numeric($params['comment_object_id']) && 
          isset($params['comment_object_class']) && 
          isset($params['comment_name']) && 
          isset($params['comment_email']) && 
          isset($params['comment_website']) && 
          isset($params['comment_rating']) && is_numeric($params['comment_rating']) &&
          isset($params['comment_text'])){
      
      try{
        $class = $params['comment_object_class'];
        $object = new $class($params['comment_object_id']);
        $text = nl2br(strip_tags(stripslashes($params['comment_text'])));
        $person = new Miki_person();
        $person->lastname = $params['comment_name'];
        $person->email1 = $params['comment_email'];
        $person->web = $params['comment_website'];
        $person->save();
        
        $subscribe = (isset($params['comment_subscribe']) && $params['comment_subscribe'] == 1) ? true : false;
        
        $object->post_comment($person->id, $text, $params['comment_rating'], $subscribe);
        
        send_result(true, _("Votre commentaire a été posté avec succès"), $ajax);
      }
      catch(Exception $e){
        send_result(false, $e->getMessage(), $ajax);
      }
    }
    else{
      send_result(false, _("Il manque des informations"), $ajax);
    }
  }     

  /**
   * Affiche le formulaire pour poster un commentaire ainsi que les commentaires de l'objet passé en paramètre
   */     
  function print_comments($object, $state = ""){
    print_comment_form($object);
    print_comments_list($object, $state);
  }
  
  /**
   * Affiche le formulaire pour poster un commentaire
   */
  function print_comment_form($object){
    ?>
    
    <script src="scripts/forms.js" type="text/javascript"></script>
    <script src="scripts/jquery.scrollTo.js" type="text/javascript"></script>
    
    <script>
      
      var star_selected = null;
        
      $(document).ready(function() {
      
        // gère le clic sur les étoiles
        $(".comment_star").click(function(event){
          if (star_selected && star_selected.is($(this))){
            star_selected = null;
            $("input[name=comment_rating]").val(0);
          }
          else{
            star_selected = $(this);
            $("input[name=comment_rating]").val(parseInt($(this).attr('id')));
          }
        });
        
        // gère les étoiles
        $(".comment_star").mouseover(function(event){
          $(this).css('background-position', '0 0');
          $(this).prevAll(".comment_star").css('background-position', '0 0');
          $(this).nextAll(".comment_star").css('background-position', '-17px 0');
        });
        
        // gère les étoiles
        $(".comment_star").mouseleave(function(event){
          if (star_selected){
            star_selected.css('background-position', '0 0');
            star_selected.prevAll(".comment_star").css('background-position', '0 0');
            star_selected.nextAll(".comment_star").css('background-position', '-17px 0');
          }
          else
            $(".comment_star").css('background-position', '-17px 0');
        });
        
        // fonction qui met à jour les commentaires après un post
        function update_comments(){
          if ($("#comments").length > 0)
            $("#comments").load("scripts/functions_comments.php", "print_comments=1&oclass=<?php echo get_class($object); ?>&oid=<?php echo $object->id; ?>");
        }
        
        // si le formulaire d'ajout de commentaire est affiché, on le gère
        if ($("#form_comment").size() > 0){
          $("#form_comment").validate({
            rules: {
              comment_name: "required",
              comment_email: {
                required: true,
                email: true
              },
              comment_website: "url",
              comment_text: "required",
              tel: "required",
            },
            submitHandler: function(form) {
              form_send(form, {"onComplete": update_comments});
            }
          });
        }
      });
      
    </script>
    
    <?php
  
    // récupert la personne connectée si une personne est connectée
    global $miki_person;
    ?>
      <form action="scripts/functions_comments.php" method="post" id="form_comment" class="form_perso">
      
        <?php 
        // affiche les box de messages de résultats
        print_results(); 
        ?>
        
        <!-- Défini si on est en mode ajax ou non -->
        <input type='hidden' name='ajax' value='0' />
        
        <input type='hidden' name='comment_object_id' value='<?php echo $object->id; ?>' />
        <input type='hidden' name='comment_object_class' value='<?php echo get_class($object); ?>' />
        
        <?php
        if ($miki_person instanceof Miki_person){
          echo "<input type='hidden' name='comment_person_id' value='$miki_person->id' />";
        }
        ?>
        
        <h2><?php echo _("Laissez un commentaire"); ?></h2>
        
        <table>
          <?php
          // si personne n'est connecté, on affiche les champs supplémentaires
          if (!($miki_person instanceof Miki_person)){
          ?>
          <tr>
            <td><?php echo _("Nom :"); ?></td>
            <td><input type="text" name="comment_name" tabindex="1" style="width: 300px;" /></td>
          </tr>
          <tr>
            <td><?php echo _("Adresse e-mail :"); ?></td>
            <td><input type="text" name="comment_email" tabindex="2" style="width: 300px;" /></td>
          </tr>
          <tr>
            <td><?php echo _("Site Internet :"); ?></td>
            <td><input type="text" name="comment_website" tabindex="3" style="width: 300px;" placeholder="<?php echo _("Doit commencer par http://"); ?>" /></td>
          </tr>
          <?php
          }
          ?>
          <tr>
            <td><?php echo _("Votre appréciation :"); ?></td>
            <td>
              <input type="hidden" name="comment_rating" value="0" />
              <div class="comment_star" id="1_comment_rating">&nbsp;</div>
              <div class="comment_star" id="2_comment_rating">&nbsp;</div>
              <div class="comment_star" id="3_comment_rating">&nbsp;</div>
              <div class="comment_star" id="4_comment_rating">&nbsp;</div>
              <div class="comment_star" id="5_comment_rating">&nbsp;</div>
            </td>
          </tr>
          <tr>
            <td><?php echo _("Votre commentaire :"); ?></td>
            <td><textarea name="comment_text" style="width: 400px; height: 200px;" tabindex="4"></textarea></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>
              <input type="checkbox" name="comment_subscribe" id="comment_subscribe" value="1" tabindex="5" />
      	      <label for="comment_subscribe"><?php echo _("Je veux suivre ce post par e-mail"); ?></label>
            </td>
          </tr>
          <tr>
            <td colspan="2" style="text-align: right;">
              <input name="submit" type="submit" tabindex="6" value="<?php echo ("Envoyer"); ?>" class="button_big1" />
            </td>
          </tr>
        </table>
      </form>
    <?php
  }
  
  /**
   * Affiche les commentaires de l'objet passé en paramètre
   */     
  function print_comments_list($object, $state = ""){
    try{
      // récupert les commentaires de l'objet passé en paramètre
      $comments = $object->get_comments($state, "", "date", "asc");
      
      // puis les affiche
      echo "<div id='comments'>";
      
      foreach($comments as $comment){
        $comment_person = new Miki_person($comment->id_person);
        $url_avatar = "pictures/persons/";
        if ($comment_person->picture != "")
          $url_avatar .= $comment_person->picture;
        else
          $url_avatar .= "avatar.png";
          
        if ($comment_person->web != "") 
          $comment_web = "<a href='$comment_person->web' rel='nofollow' target='_blank' title=\"" ._("Visiter le site web de") ." " .addcslashes($comment_person_name, '"') ."\">$comment_person->web</a>";
        else
          $comment_web = "&nbsp;";
          
        $comment_person_name = "$comment_person->firstname $comment_person->lastname";
        $comment_date = date("d/m/Y H:i", strtotime($comment->date));
        
        echo "<div class='comment_contener'>
                <div class='comment_picture'><img src='timthumb.php?src=" .urlencode($url_avatar) ."&amp;w=90&amp;h=90' alt=\"". addcslashes($comment_person_name, '"') ."\" /></div>
                <div class='comment_content'>
                  <div class='comment_name'>$comment_person_name</div>
                  <div class='comment_rating'>";
                    if ($comment->rating > 0) 
                      echo "<img src='pictures/stars_$comment->rating.png' alt='$comment->rating' />";
                    else
                      echo _("Aucune note");
            echo "</div>
                  <div class='comment_website'>$comment_web</div>
                  <div class='comment_date'>$comment_date</div>
                  <div class='comment_text'>$comment->comment</div>
                </div>
              </div>";
      }
      echo "</div>";
    }
    catch(Exception $e){}
  }