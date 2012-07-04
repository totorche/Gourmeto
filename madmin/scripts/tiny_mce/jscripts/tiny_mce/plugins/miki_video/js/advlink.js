/* Functions for the miki_video plugin popup */

tinyMCEPopup.requireLangPack("miki_video");

var action = "insert";

var templates = {
	"window.open" : "window.open('${url}','${target}','${options}')"
};

function preinit() {
	var url;

	if (url = tinyMCEPopup.getParam("external_link_list_url"))
		document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
}

function init() {
	tinyMCEPopup.resizeToInnerSize();

	var formObj = document.forms[0];
	var inst = tinyMCEPopup.editor;
	var elm = inst.selection.getNode();
	var html;

	document.getElementById('href_mikicontainer').innerHTML = getMikiVideosList('videoslist');
	document.getElementById('targetlistcontainer').innerHTML = getTargetListHTML('targetlist','target');

	// Resize some elements
	
  // Récupert les éléments parents si dispo
	var parentLink = inst.dom.getParent(elm, "A");

  // si il y a un div parent avec la classe "mce_miki_video", c'est qu'on édite une vidéo
	if (elm != null && elm.nodeName == "IMG" && inst.dom.hasClass(parentLink, 'miki_video')){
		action = "update";
	}

	formObj.insert.value = tinyMCEPopup.getLang(action, 'Insert', true); 

  // si on est en mode édition
	if (action == "update") {
	  // détermine si c'est une vidéo complète ou uniquement la miniature + lien et coche la bonne option dans la fenêtre
    var integration = "";
    if (elm.nodeName == "IMG"){
      integration = "thumb";
      $$('input[name=integration]')[1].set('checked',true);
    }
    else{
      integration = "video";
      $$('input[name=integration]')[0].set('checked',true);
    }
    
    // si on est dans le cas d'une miniature + lien
    if (integration == "thumb"){
      var target = inst.dom.getAttrib(parentLink, 'target');
      if (target != null)
        selectByValue(formObj, 'targetlist', target);
      $('targetlist').disabled = false;
		}
		
		// récupert l'id de la vidéo
		var video = (inst.dom.getAttrib(elm, 'id')).substring(11);
		
		// sélectionne la bonne vidéo
		selectByValue(formObj, 'videoslist', video);
		
		// définit la hauteur et la largeur
		if ($('height').value == "")
      $('height').value = '100';
    
    if ($('width').value == "")
      $('width').value = '100';

		setFormValue('width', parseInt(inst.dom.getStyle(elm, 'width')));
    setFormValue('height', parseInt(inst.dom.getStyle(elm, 'height')));
		setFormValue('styles', inst.dom.getAttrib(elm, 'style'));
	} else{
  }
}

function setFormValue(name, value) {
	document.forms[0].elements[name].value = value;
}

function insertAction() {
	var ed = tinyMCEPopup.editor, f = document.forms[0], nl = f.elements, v, args = {}, el;
  var actionType = 'mceInsertContent';
  
  // si aucune vidéo n'est sélectionnée, on ne fait rien
  if ($('videoslist').value == '')
    tinyMCEPopup.close();
    
  // définit les valeurs par défaut si aucune n'est données
  if ($('height').value == "")
    $('height').value = '100';
  
  if ($('width').value == "")
    $('width').value = '100';
  
  if ($$('input[name=integration]:checked').length == 0)
    $$('input[name=integration]')[0].checked = true;
  
	tinyMCEPopup.restoreSelection();

	// Fixes crash in Safari
	if (tinymce.isWebKit)
		ed.getWin().focus();
  
  // si on fait une mise à jour de l'élément
	if(action == "update"){
	  // on remplace l'élément, on n'en ajoute pas
    actionType = 'mceReplaceContent';
	  // place la sélection sur l'élément parent de l'image (le lien)
    el = ed.selection.getNode();
    ed.selection.select(ed.dom.getParent(el, "A"));
	}
  
  // récupert le chemin du plugin
  var url = tinyMCE.baseURL + '/plugins/miki_video';
  
  // récupert la hauteur et la largeur données pour la vidéo
  var height = $('height').value;
  var width = $('width').value;

  //ed.execCommand('mceInsertContent', false, '<div class="mce_miki_video" style="overflow: hidden;" class="mceNonEditable" title="' + $('videoslist').value + '">' + this.getMikiVideosCode() + '</div>', {skip_undo : 1});
  ed.execCommand(actionType, false, this.getMikiVideosCode(), {skip_undo : 1});
	ed.undoManager.add();
  
  /*var searchUrl = ed.convertURL(url, "src", "img");
  
  // récupert le code de la vidéo et l'encode
  encodedCode = escape(this.getMikiVideosCode());
  alert(this.getMikiVideosCode());
  
  // remplace le code de la vidéo par une image
	ed.execCommand('mceInsertContent', false, '<div id="__mce_tmp" class="miki_video_tmp" style="overflow: hidden;" class="mceNonEditable"><img id="__mce_img_tmp" /></div>', {skip_undo : 1});
  ed.dom.setAttribs('__mce_tmp', {'title': encodedCode});
	ed.dom.setAttrib('__mce_tmp', 'id', '');
	ed.dom.setAttribs('__mce_img_tmp', {'src': searchUrl + '/img/codeprotect.gif', 'border': 0, 'width': width + 'px', 'height': height + 'px'});
	ed.dom.setAttrib('__mce_img_tmp', 'id', '');
	ed.undoManager.add();*/

	tinyMCEPopup.close();
}

function getTargetListHTML(elm_id, target_form_element) {
	var targets = tinyMCEPopup.getParam('theme_advanced_link_targets', '').split(';');
	var html = '';

	html += '<select id="' + elm_id + '" name="' + elm_id + '" onchange="this.form.' + target_form_element + '.value=';
	html += 'this.options[this.selectedIndex].value;">';
	html += '<option value="_self">' + tinyMCEPopup.getLang('miki_video.target_same') + '</option>';
	html += '<option value="_blank">' + tinyMCEPopup.getLang('miki_video.target_blank') + ' (_blank)</option>';
	html += '<option value="_parent">' + tinyMCEPopup.getLang('miki_video.target_parent') + ' (_parent)</option>';
	html += '<option value="_top">' + tinyMCEPopup.getLang('miki_video.target_top') + ' (_top)</option>';

	for (var i=0; i<targets.length; i++) {
		var key, value;

		if (targets[i] == "")
			continue;

		key = targets[i].split('=')[0];
		value = targets[i].split('=')[1];

		html += '<option value="' + key + '">' + value + ' (' + key + ')</option>';
	}

	html += '</select>';

	return html;
}

/**
 * S'exécute lorsque l'on modifie le type d'intégration de la vidéo
 */ 
function change_integration(value){
  if (value == 1){
    $('targetlist').disabled = true;
  }
  else if (value == 2){
    $('targetlist').disabled = false;
  }
}

/**
 * Récupert la liste des vidéos
 */ 
function getMikiVideosList(elm_id) {
  html = '<select id="' + elm_id + '" name="' + elm_id + '"><option value="">Aucune vidéo</option>';

  window.addEvent('domready', function() {
    var req = new Request({url:'php/load_videos.php', async: false,
  		onSuccess: function(txt){
  			html += txt;
  			html += '</select>';
      }
	  });
	  req.send();
  });
  
  return html;
}

/**
 * Récupert le code de la vidéo
 */ 
function getMikiVideosCode() {
  var result = '';
  
  window.addEvent('domready', function() {
    var req = new Request({url:'php/get_video_code.php', async: false,
  		onSuccess: function(txt){
  			result = txt;
      }
	  });
	  req.send("vid=" + $('videoslist').value + "&vtype=" + $$('input[name=integration]:checked').get('value') + "&h=" + $('height').value + "&w=" + $('width').value + '&styles=' + $('styles').value + '&target=' + $('targetlist').value);
  });
  
  return result;
}

// While loading
preinit();
tinyMCEPopup.onInit.add(init);