/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Hervé Torche
 * Released under LGPL License.
 *
 * License: http://www.fbw-one.com/license
 * Contributing: http://www.fbw-one.com/contributing
 */

(function() {
  // Load plugin specific language pack
	//tinymce.PluginManager.requireLangPack('miki_video');

	tinymce.create('tinymce.plugins.miki_video', {
		init : function(ed, url) {
			this.editor = ed;

			// Register commands
			ed.addCommand('mcemiki_video', function() {
				var se = ed.selection;

				// No selection and not in link
				/*if (se.isCollapsed() && !ed.dom.getParent(se.getNode(), 'A'))
					return;*/

				ed.windowManager.open({
					file : url + '/link.htm',
					width : 480 + parseInt(ed.getLang('miki_video.delta_width', 0)),
					height : 300 + parseInt(ed.getLang('miki_video.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('miki_video', {
				title : 'Inérer / Editer une vidéo',
				cmd : 'mcemiki_video',
				image : url + '/img/miki.gif'
			});

			//ed.addShortcut('ctrl+k', 'miki_video.miki_video_desc', 'mcemiki_video');

			ed.onNodeChange.add(function(ed, cm, n, co) {
				//cm.setDisabled('miki_video', !co);
				var parentLink = ed.dom.getParent(n, "A");
				cm.setActive('miki_video', parentLink != null && ed.dom.hasClass(parentLink, 'miki_video') && n.nodeName == 'IMG');
			});
			
			// remplace l'image Miki_Video par le code
			/*ed.onPostProcess.add(function(ed, o) {
        if (o.get){
        	var startPos = 0;
          var endPos = 0;
          var finalPos = 0;
        
          var searchUrl = ed.convertURL(url, "src", "img");
          
          var startTag = '<div class="miki_video_tmp" style="overflow: hidden;" title="';
          var endTag = '">';
          var finalTag = '</div>';
          var running = true;
          
          
          while((running == true) && (o.content.indexOf(startTag) != -1)){
            startPos = o.content.indexOf(startTag);
            endPos = o.content.indexOf(endTag, startPos);
            finalPos = o.content.indexOf(finalTag, endPos);
            if(endPos != -1){
              encodedPHPCode = o.content.substr(startPos + startTag.length, endPos - (startPos + startTag.length));
              decodedPHPCode = unescape(encodedPHPCode);
            
              o.content = o.content.substr(0, startPos) + decodedPHPCode + o.content.substr(finalPos + finalTag.length);
            } else {
              running = false;
            }
          }
        
          o.content = o.content.replace(/&lt;\?/gi, "<?");
          o.content = o.content.replace(/\?&gt;/gi, "?>");
          //firefox javascript mceNonEditable insert fix
          o.content = o.content.replace(/&amp;quot;mceNonEditable&amp;quot;/gi, "mceNonEditable");
          //url encoding fix
          o.content = o.content.replace(/'/gi, "'");
          o.content = o.content.replace(/&quot;/gi, '"');
          //End Fixes URL Encoding---				
        }
      });*/
		},

		getInfo : function() {
			return {
				longname : 'Miki Video',
				author : 'Hervé Torche',
				authorurl : 'http://www.fbw-one.com',
				infourl : 'http://www.fbw-one.com',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('miki_video', tinymce.plugins.miki_video);
})();