(function(){tinymce.create("tinymce.plugins.miki_link",{init:function(a,b){this.editor=a;a.addCommand("mcemiki_link",function(){var c=a.selection;if(c.isCollapsed()&&!a.dom.getParent(c.getNode(),"A")){return}a.windowManager.open({file:b+"/link.htm",width:480+parseInt(a.getLang("miki_link.delta_width",0)),height:400+parseInt(a.getLang("miki_link.delta_height",0)),inline:1},{plugin_url:b})});a.addButton("link",{title:"miki_link.link_desc",cmd:"mcemiki_link",image:url+'/img/miki.gif'});a.addShortcut("ctrl+k","miki_link.miki_link_desc","mcemiki_link");a.onNodeChange.add(function(d,c,f,e){c.setDisabled("link",e&&f.nodeName!="A");c.setActive("link",f.nodeName=="A"&&!f.name)})},getInfo:function(){return{longname:"Miki Link",author:"Hervé Torche",authorurl:"http://www.fbw-one.com",infourl:"http://www.fbw-one.com",version:"1.0"}}});tinymce.PluginManager.add("miki_link",tinymce.plugins.miki_link)})();