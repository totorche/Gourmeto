/**
 * Envoie un formulaire en utilisant Ajax.
 * 
 * Le formulaire doit être fourni en paramètre.
 *  
 * Les options possibles (en second paramètre) sont : 
 *   - delay : le délai (en ms) avant que le message ne soit masqué
 *   - hide : si le message est masqué au bout d'un moment (TRUE) ou n'est jamais masqué (FALSE)
 *   - reset : si le formulaire doit être vidé (TRUE) ou pas (FALSE) après une réussite. Si échec, on ne le vide pas. 
 */ 
function form_send(el){
  /* get some values from elements on the page: */
  var form = $(el);
  var url = form.attr('action');
  var params = form.serialize();
  var delay = 5000;
  var hide = true;
  var reset = true;
  var onComplete = function(){};
  
  // récupert les options
  var options = (arguments.length == 1) ? false : arguments[1];
  if (options.delay != undefined)
    delay = options.delay;
  if (options.hide != undefined)
    hide = options.hide;
  if (options.reset != undefined)
    reset = options.reset;
  if (options.onComplete != undefined)
    onComplete = options.onComplete;
  
  // dit qu'on utilise Ajax
  params += '&ajax=1';
  
  // masque le bouton et affiche le gif animé pour patienter
  form.find(':submit').css("display","none");
  $("<img src='pictures/loader.gif' style='margin-left: 5px;' alt='Chargement...' />").insertAfter(form.find(':submit').parent());

  /* Send the data using post and put the results in a div */
  $.post(url, params,
    function( data ) {
      // masque les 2 box
      $("#form_results_success").hide();
      $("#form_results_error").hide();
        
      var result = $(data).find('#result');
      var msg = $(data).find('#msg');
      if (result.html() == '1'){
        // réaffiche le bouton
        form.find(':submit').parent().next("img").remove();
        form.find(':submit').css("display","inline");
        
        // s'il y a un message à afficher
        if (msg.html() != ""){
          $("#form_results_success p").empty().append(msg.html());
          
          if (hide)
            $("#form_results_success").css('display', 'block').delay(delay).hide("slow");
          else
            $("#form_results_success").css('display', 'block');
          
          $(window).scrollTo("#form_results_success");
        }
        
        // si on doit vider le formulaire
        if (reset)
          el.reset();
        
        // exécute la fonction de callback lors d'une réussite
        onComplete();
      }
      else{
        // réaffiche le bouton
        form.find(':submit').parent().next("img").remove();
        form.find(':submit').css("display","inline");
        
        $("#form_results_error p").empty().append(msg.html());
        
        if (hide)
          $("#form_results_error").css('display', 'block').delay(delay).hide("slow");
        else
          $("#form_results_error").css('display', 'block');
        
        $(window).scrollTo("#form_results_error");
      }
    }
  );
}