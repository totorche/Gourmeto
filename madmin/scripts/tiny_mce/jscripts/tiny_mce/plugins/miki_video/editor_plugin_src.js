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
	//tinymce.PluginManager.requireLangPack('miki_link');

	tinymce.create('tinymce.plugins.miki_link', {
		init : function(ed, url) {
			this.editor = ed;

			// Register commands
			ed.addCommand('mcemiki_link', function() {
				var se = ed.selection;

				// No selection and not in link
				if (se.isCollapsed() && !ed.dom.getParent(se.getNode(), 'A'))
					return;

				ed.windowManager.open({
					file : url + '/link.htm',
					width : 480 + parseInt(ed.getLang('miki_link.delta_width', 0)),
					height : 400 + parseInt(ed.getLang('miki_link.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('link', {
				title : 'miki_link.link_desc',
				cmd : 'mcemiki_link',
				image : url + '/img/miki.gif'
			});

			ed.addShortcut('ctrl+k', 'miki_link.miki_link_desc', 'mcemiki_link');

			ed.onNodeChange.add(function(ed, cm, n, co) {
				cm.setDisabled('link', co && n.nodeName != 'A');
				cm.setActive('link', n.nodeName == 'A' && !n.name);
			});
		},

		getInfo : function() {
			return {
				longname : 'Miki Link',
				author : 'Hervé Torche',
				authorurl : 'http://www.fbw-one.com',
				infourl : 'http://www.fbw-one.com',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('miki_link', tinymce.plugins.miki_link);
})();