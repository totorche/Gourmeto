var menuElement = new Class({
  text : '', // texte du menu
  link : '', // lien de l'élément
  parent : -1,
  description : '',
  picture : '',
  children : new Array(0),  // pour la gestion des enfants
  
  initialize : function(text, link, parent, description, picture){
    this.text = text;
    this.link = link;
    
    if ($defined(parent))
      this.parent = parent;
      
    this.description = description;
    this.picture = picture;
  },
  
  addChild : function(child){
    this.children.push(child);
  }
});

/***********************************************************
 *
 * Pour la gestion du menu de la console d'administration
 *  
 * *********************************************************/ 
var adminMenu = new Class({

  tabMenu  : new Array(0),
  contener : "",
  
  initialize : function(contener){
    if (!contener)
      return this;
    
    this.contener = contener;
  },
  
  // ajout un nouvel élément de menu et retourn l'index de l'élément dans le tableau (pour la gestion des parents)
  addMenu : function(text, link, parent, description, picture){
    var el = new menuElement(text, link, parent, description, picture);
    if ($defined(parent) && parent != -1)
      this.tabMenu[parent].addChild(el);
      
    return this.tabMenu.push(el) - 1;
  },
  
  generate : function(){
    var myObject = this;
    var menu = new Element('div',{
      'id':'menu2'
    });
    menu.inject(this.contener);
    var ul1 = new Element('ul',{
      'class':'niveau1'
    });
    ul1.inject(menu);
    this.tabMenu.each(function(el){
      if (el.parent == -1){
        var li1 = new Element('li',{
          'html':'<a href="' + el.link + '">'+el.text+'</a>'
        });
        li1.inject(ul1);
        
        if (el.children.length > 0){
          li1.addClass('sousmenu')
          var ul2 = new Element('ul',{
            'class':'niveau2'
          });
          ul2.inject(li1);
          el.children.each(function(el2){
            var li2 = new Element('li',{
              'html':'<a href="' + el2.link + '">'+el2.text+'</a>'
            });
            li2.addEvent('click',function(event){
              event.stopPropagation();
            });
            li2.inject(ul2);
          });
        }
      }
    });
  }

});