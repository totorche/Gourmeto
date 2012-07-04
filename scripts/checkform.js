var checkForm = new Class({
  Implements: Options,  
  
  form : null,
  divError: null,
  sendResult: null,  // pour l'affichage du résultat de l'envoi du formulaire en mode "ajax"
  sendResultTxt: null,  // pour l'affichage du résultat de l'envoi du formulaire en mode "ajax"
  req: null,  // requête d'envoi du formulaire pour le mode "ajax"
  
  presets: {
    errorPlace: 'right',  // top, bottom, left, right
    focusCss: {borderColor: '#faee9f'},
    normalCss: {border: '1px solid #cccccc'},
    invalidCss: {border: '1px solid #ff0000'},
    validCss: {border: '1px solid #cccccc'},
    divErrorCss: {'color':'#ff0000'},
    sendResultCss: {'display':'none',
                    'position':'absolute',
                    'top':'400px',
                    'left':((window.getSize().x/2)-175) + 'px',
                    'width':'350px',
                    'padding':'5px',
                    'border':'solid 1px #000000',
                    'color':'#000000',
                    'zIndex':'1000',
                    'background-color':'#e4e4e4',
                    'filter':'alpha(opacity=90)',
                    '-moz-opacity':'.90',
                    'opacity':'.90',
                    'textAlign':'center'},
    requiredTxt: 'Ce champ est obligatoire !',
    numericTxt: 'Seuls les nombres sont acceptés !',
    currencyTxt: 'Seuls les nombres et les caractères "." et "\'" sont acceptés !',
    phoneTxt: 'Seuls les nombres, les caractère \'.\' et \'+\'<br />&nbsp;&nbsp;ainsi que les espaces sont aceptés<br />&nbsp;&nbsp;Exemple : +41 22 323 44 55',
    emailTxt: 'L\'adresse e-mail est invalide !',
    dateTxt: 'La date est invalide !&nbsp;&nbsp;Exemple : 01/01/1970',
    requiredLinkTxt: 'L\'url est incorrecte.&nbsp;&nbsp;Exemple : http://www.monsite.ch',
    requiredSelectTxt: 'Veuillez choisir une valeur',
    useAjax: false,  // utilise ajax pour l'envoi du formulaire
    ajaxUrl: null,   // url où les données du formulaire vont être envoyées
    ajaxStartFct: function(){}, // fonction appelée lors du succès de l'envoi du formulaire via ajax
    ajaxSuccessFct: function(txt){  // fonction appelée lors du succès de l'envoi du formulaire via ajax
                      $('sendResult').setStyle('display','block');
                      $('sendResultTxt').set('html', txt + '<br /><br />Ce message dispara&icirc;tra dans quelques instants');
                      var hide = function(){
                        $('sendResult').setStyle('display','none');
                      }
                      hide.delay(5000);
                    },
	  ajaxFailureFct: function(txt){  // fonction appelée lors d'une erreur d'envoi du formulaire via ajax
                      $('sendResult').setStyle('display','block');
                      $('sendResultTxt').set('html','Une erreur est survenue lors de l\'envoi des informations : <br />' + txt + '<br /><br />Ce message dispara&icirc;tra dans quelques instants');
                      var hide = function(){
                        $('sendResult').setStyle('display','none');
                      }
                      hide.delay(5000);
                    }
  },

  initialize: function(form, presets){
    // teste que le formulaire dont l'id a été passé en paramètre existe bien
    if (!form)
      return this;
    this.form = form;
    this.presets = $merge(this.presets, presets);
    this.options = {};
		this.setOptions(this.presets);
		
		// récupert l'action du formulaire
		if (this.options.ajaxUrl == null)
		  this.options.ajaxUrl = this.form.get('action');
		
		// pour l'affichage des erreurs du formulaire
		this.divError = new Element('div', {'rel':'error'});
		this.divError.setStyles(this.options.divErrorCss);
		
		// pour l'affichage du résultat de l'envoi du formulaire
		this.sendResult = new Element('div', {'id':'sendResult'});
		this.sendResult.setStyles(this.options.sendResultCss);
		this.sendResultTxt = new Element('div', {'id':'sendResultTxt'});
		this.sendResultTxt.setStyle('margin','10px 0');
		this.sendResult.inject($(document.body));
		this.sendResultTxt.inject($('sendResult'));
		
		// si on est en mode "ajax" pour l'envoi du formulaire
		if (this.options.useAjax && 
        this.options.ajaxUrl != null && 
        this.options.ajaxSuccessFct != null){
  		this.req = new Request({url:this.options.ajaxUrl, 
  		  onRequest: this.options.ajaxStartFct,
        onSuccess: this.options.ajaxSuccessFct,
  		  onFailure: this.options.ajaxFailureFct
      });
    }
		
		// configure le formulaire
		this.majFormValidation();
  },
  
  /*initialize: function(form){
    this.initialize(form, null);
  },*/
  
  // test si l'objet passé en paramètre a une valeur nulle (return false) ou non-nulle (return true)
  testEmptyFormValidation: function(obj){
    this.divError.set('html','&nbsp;&nbsp;' + this.options.requiredTxt);
    if (obj.value.trim() == ''){
      $(obj).setStyles(this.options.invalidCss);
      if (!$(obj).getPrevious('div[rel=error]') && !$(obj).getNext('div[rel=error]')){
        var where = '';
        if (this.options.errorPlace == 'bottom'){
          this.divError.setStyle('display','block');
          $(obj).setStyle('display','block');
          where = 'after';
        }
        else if(this.options.errorPlace == 'right'){
          this.divError.setStyle('display','inline');
          where = 'after';
        }
        else if (this.options.errorPlace == 'top'){
          this.divError.setStyle('display','block');
          where = 'before';
        }
        else if(this.options.errorPlace == 'left'){
          this.divError.setStyle('display','inline');
          where = 'before';
        }
        this.divError.clone().inject($(obj),where);
      }
      return false; 
    }
    else{
      $(obj).setStyles(this.options.validCss);
      if ($(obj).getPrevious('div[rel=error]')){
        $(obj).getPrevious('div[rel=error]').dispose();
      }
      else if ($(obj).getNext('div[rel=error]')){
        $(obj).getNext('div[rel=error]').dispose();
      }
      return true;
    }
  },
  
  // test si l'objet passé en paramètre a une valeur numérique (return false) ou non (return true)
  testNumericFormValidation: function(obj){
    this.divError.set('html','&nbsp;&nbsp;' + this.options.numericTxt);
    
    if(obj.value.trim() == ""){
      // efface les messages d'erreur
      $(obj).setStyles(this.options.validCss);
      if ($(obj).getPrevious('div[rel=error]')){
        $(obj).getPrevious('div').dispose();
      }
      else if ($(obj).getNext('div[rel=error]')){
        $(obj).getNext('div').dispose();
      }
      
      // puis reteste si le message n'a pas le droit d'être vide
      if (obj.hasClass("required")){
        return this.testEmptyFormValidation(obj);
      }
      
      return true;
    }
    
    var reg = /^[0-9]*$/;
    if (reg.exec(obj.value.trim()) === null || obj.value.trim() === ''){
      if (!$(obj).getPrevious('div[rel=error]') && !$(obj).getNext('div[rel=error]')){
        $(obj).setStyles(this.options.invalidCss);
        var where = '';
        if (this.options.errorPlace == 'bottom'){
          this.divError.setStyle('display','block');
          $(obj).setStyle('display','block');
          where = 'after';
        }
        else if(this.options.errorPlace == 'right'){
          this.divError.setStyle('display','inline');
          where = 'after';
        }
        else if (this.options.errorPlace == 'top'){
          this.divError.setStyle('display','block');
          where = 'before';
        }
        else if(this.options.errorPlace == 'left'){
          this.divError.setStyle('display','inline');
          where = 'before';
        }
        this.divError.clone().inject($(obj),where);
      }
      return false; 
    }
    else{
      $(obj).setStyles(this.options.validCss);
      if ($(obj).getPrevious('div[rel=error]')){
        $(obj).getPrevious('div').dispose();
      }
      else if ($(obj).getNext('div[rel=error]')){
        $(obj).getNext('div').dispose();
      }
      return true;
    }
  },
  
  // test si l'objet passé en paramètre est ok pour un lien (pas d'espace, pas d'acent) (return false) ou non (return true)
  testLinkFormValidation: function(obj){
    this.divError.set('html','&nbsp;&nbsp;' + this.options.requiredLinkTxt);
    
    if(obj.value.trim() == ""){
      // efface les messages d'erreur
      $(obj).setStyles(this.options.validCss);
      if ($(obj).getPrevious('div[rel=error]')){
        $(obj).getPrevious('div').dispose();
      }
      else if ($(obj).getNext('div[rel=error]')){
        $(obj).getNext('div').dispose();
      }
      
      // puis reteste si le message n'a pas le droit d'être vide
      if (obj.hasClass("required")){
        return this.testEmptyFormValidation(obj);
      }
      
      return true;
    }
    
    var reg = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
    if (reg.exec(obj.value.trim()) === null || obj.value.trim() === ''){
      if (!$(obj).getPrevious('div[rel=error]') && !$(obj).getNext('div[rel=error]')){
        $(obj).setStyles(this.options.invalidCss);
        var where = '';
        if (this.options.errorPlace == 'bottom'){
          this.divError.setStyle('display','block');
          $(obj).setStyle('display','block');
          where = 'after';
        }
        else if(this.options.errorPlace == 'right'){
          this.divError.setStyle('display','inline');
          where = 'after';
        }
        else if (this.options.errorPlace == 'top'){
          this.divError.setStyle('display','block');
          where = 'before';
        }
        else if(this.options.errorPlace == 'left'){
          this.divError.setStyle('display','inline');
          where = 'before';
        }
        this.divError.clone().inject($(obj),where);
      }
      return false; 
    }
    else{
      $(obj).setStyles(this.options.validCss);
      if ($(obj).getPrevious('div[rel=error]')){
        $(obj).getPrevious('div').dispose();
      }
      else if ($(obj).getNext('div[rel=error]')){
        $(obj).getNext('div').dispose();
      }
      return true;
    }
  },
  
  // test si l'objet passé en paramètre a une valeur monétaire (numérique + '.') (return false) ou non (return true)
  testCurrencyFormValidation: function(obj){
    this.divError.set('html','&nbsp;&nbsp;' + this.options.currencyTxt);
    
    if(obj.value.trim() == ""){
      // efface les messages d'erreur
      $(obj).setStyles(this.options.validCss);
      if ($(obj).getPrevious('div[rel=error]')){
        $(obj).getPrevious('div').dispose();
      }
      else if ($(obj).getNext('div[rel=error]')){
        $(obj).getNext('div').dispose();
      }
      
      // puis reteste si le message n'a pas le droit d'être vide
      if (obj.hasClass("required")){
        return this.testEmptyFormValidation(obj);
      }
      
      return true;
    }
    
    var reg = /^[0-9.']*$/;
    if (reg.exec(obj.value.trim()) === null || obj.value.trim() === ''){
      if (!$(obj).getPrevious('div[rel=error]') && !$(obj).getNext('div[rel=error]')){
        $(obj).setStyles(this.options.invalidCss);
        var where = '';
        if (this.options.errorPlace == 'bottom'){
          this.divError.setStyle('display','block');
          $(obj).setStyle('display','block');
          where = 'after';
        }
        else if(this.options.errorPlace == 'right'){
          this.divError.setStyle('display','inline');
          where = 'after';
        }
        else if (this.options.errorPlace == 'top'){
          this.divError.setStyle('display','block');
          where = 'before';
        }
        else if(this.options.errorPlace == 'left'){
          this.divError.setStyle('display','inline');
          where = 'before';
        }
        this.divError.clone().inject($(obj),where);
      }
      return false; 
    }
    else{
      $(obj).setStyles(this.options.validCss);
      if ($(obj).getPrevious('div[rel=error]')){
        $(obj).getPrevious('div').dispose();
      }
      else if ($(obj).getNext('div[rel=error]')){
        $(obj).getNext('div').dispose();
      }
      return true;
    }
  },
  
  // test si l'objet passé en paramètre a une valeur monétaire (numérique + '.') (return false) ou non (return true)
  testPhoneFormValidation: function(obj){
    this.divError.set('html','&nbsp;&nbsp;' + this.options.phoneTxt);
    
    if(obj.value.trim() == ""){
      // efface les messages d'erreur
      $(obj).setStyles(this.options.validCss);
      if ($(obj).getPrevious('div[rel=error]')){
        $(obj).getPrevious('div').dispose();
      }
      else if ($(obj).getNext('div[rel=error]')){
        $(obj).getNext('div').dispose();
      }
      
      // puis reteste si le message n'a pas le droit d'être vide
      if (obj.hasClass("required")){
        return this.testEmptyFormValidation(obj);
      }
      
      return true;
    }
    
    var reg = /^[0-9.+ ]*$/;
    if (reg.exec(obj.value.trim()) === null || obj.value.trim() === ''){
      if (!$(obj).getPrevious('div[rel=error]') && !$(obj).getNext('div[rel=error]')){
        $(obj).setStyles(this.options.invalidCss);
        var where = '';
        if (this.options.errorPlace == 'bottom'){
          this.divError.setStyle('display','block');
          $(obj).setStyle('display','block');
          where = 'after';
        }
        else if(this.options.errorPlace == 'right'){
          this.divError.setStyle('display','inline');
          where = 'after';
        }
        else if (this.options.errorPlace == 'top'){
          this.divError.setStyle('display','block');
          where = 'before';
        }
        else if(this.options.errorPlace == 'left'){
          this.divError.setStyle('display','inline');
          where = 'before';
        }
        this.divError.clone().inject($(obj),where);
      }
      return false; 
    }
    else{
      $(obj).setStyles(this.options.validCss);
      if ($(obj).getPrevious('div[rel=error]')){
        $(obj).getPrevious('div').dispose();
      }
      else if ($(obj).getNext('div[rel=error]')){
        $(obj).getNext('div').dispose();
      }
      return true;
    }
  },
  
  // test si l'objet passé en paramètre a une valeur ok pour un e-mail (return false) ou non (return true)
  testEmailFormValidation: function(obj){
    this.divError.set('html','&nbsp;&nbsp;' + this.options.emailTxt);
    
    if(obj.value.trim() == ""){
      // efface les messages d'erreur
      $(obj).setStyles(this.options.validCss);
      if ($(obj).getPrevious('div[rel=error]')){
        $(obj).getPrevious('div').dispose();
      }
      else if ($(obj).getNext('div[rel=error]')){
        $(obj).getNext('div').dispose();
      }
      
      // puis reteste si le message n'a pas le droit d'être vide
      if (obj.hasClass("required")){
        return this.testEmptyFormValidation(obj);
      }
      
      return true;
    }
    
    var reg = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]{2,}[.][a-zA-Z]{2,3}$/;
    if (reg.exec(obj.value.trim()) === null || obj.value.trim() === ''){
      if (!$(obj).getPrevious('div[rel=error]') && !$(obj).getNext('div[rel=error]')){
        $(obj).setStyles(this.options.invalidCss);
        var where = '';
        if (this.options.errorPlace == 'bottom'){
          this.divError.setStyle('display','block');
          $(obj).setStyle('display','block');
          where = 'after';
        }
        else if(this.options.errorPlace == 'right'){
          this.divError.setStyle('display','inline');
          where = 'after';
        }
        else if (this.options.errorPlace == 'top'){
          this.divError.setStyle('display','block');
          where = 'before';
        }
        else if(this.options.errorPlace == 'left'){
          this.divError.setStyle('display','inline');
          where = 'before';
        }
        this.divError.clone().inject($(obj),where);
      }
      return false; 
    }
    else{
      $(obj).setStyles(this.options.validCss);
      if ($(obj).getPrevious('div[rel=error]')){
        $(obj).getPrevious('div').dispose();
      }
      else if ($(obj).getNext('div[rel=error]')){
        $(obj).getNext('div').dispose();
      }
      return true;
    }
  },
  
  // test si l'objet passé en paramètre a une valeur ok pour un e-mail (return false) ou non (return true)
  testDateFormValidation: function(obj){
    this.divError.set('html','&nbsp;&nbsp;' + this.options.dateTxt);
    
    if(obj.value.trim() == ""){
      // efface les messages d'erreur
      $(obj).setStyles(this.options.validCss);
      if ($(obj).getPrevious('div[rel=error]')){
        $(obj).getPrevious('div').dispose();
      }
      else if ($(obj).getNext('div[rel=error]')){
        $(obj).getNext('div').dispose();
      }
      
      // puis reteste si le message n'a pas le droit d'être vide
      if (obj.hasClass("required")){
        return this.testEmptyFormValidation(obj);
      }
      
      return true;
    }
    
    var reg = /^[0-9]{2}[/][0-9]{2}[/][0-9]{4}$/;
    if (reg.exec(obj.value.trim()) === null || obj.value.trim() === ''){
      if (!$(obj).getPrevious('div[rel=error]') && !$(obj).getNext('div[rel=error]')){
        $(obj).setStyles(this.options.invalidCss);
        var where = '';
        if (this.options.errorPlace == 'bottom'){
          this.divError.setStyle('display','block');
          $(obj).setStyle('display','block');
          where = 'after';
        }
        else if(this.options.errorPlace == 'right'){
          this.divError.setStyle('display','inline');
          where = 'after';
        }
        else if (this.options.errorPlace == 'top'){
          this.divError.setStyle('display','block');
          where = 'before';
        }
        else if(this.options.errorPlace == 'left'){
          this.divError.setStyle('display','inline');
          where = 'before';
        }
        this.divError.clone().inject($(obj),where);
      }
      return false; 
    }
    else{
      $(obj).setStyles(this.options.validCss);
      if ($(obj).getPrevious('div[rel=error]')){
        $(obj).getPrevious('div').dispose();
      }
      else if ($(obj).getNext('div[rel=error]')){
        $(obj).getNext('div').dispose();
      }
      return true;
    }
  },
  
  // test si le select passé en paramètre a une valeur ok (return false) ou non (return true)
  testSelectFormValidation: function(obj){
    this.divError.set('html','&nbsp;&nbsp;' + this.options.requiredSelectTxt);
       
    if (obj.value.trim() === 'null'){
      if (!$(obj).getPrevious('div[rel=error]') && !$(obj).getNext('div[rel=error]')){
        $(obj).setStyles(this.options.invalidCss);
        var where = '';
        if (this.options.errorPlace == 'bottom'){
          this.divError.setStyle('display','block');
          $(obj).setStyle('display','block');
          where = 'after';
        }
        else if(this.options.errorPlace == 'right'){
          this.divError.setStyle('display','inline');
          where = 'after';
        }
        else if (this.options.errorPlace == 'top'){
          this.divError.setStyle('display','block');
          where = 'before';
        }
        else if(this.options.errorPlace == 'left'){
          this.divError.setStyle('display','inline');
          where = 'before';
        }
        this.divError.clone().inject($(obj),where);
      }
      return false; 
    }
    else{
      $(obj).setStyles(this.options.validCss);
      if ($(obj).getPrevious('div[rel=error]')){
        $(obj).getPrevious('div').dispose();
      }
      else if ($(obj).getNext('div[rel=error]')){
        $(obj).getNext('div').dispose();
      }
      return true;
    }
  },
  
  // test si tous les champs obligatoires du formulaire passé en paramètre ont été remplis 
  testFormValidation: function(item){
    var result = true;
    var object = this;
    
    item.getElements('input[type=text]').each(function(item2, index2){
      if (item2.hasClass('required')){
        if (!object.testEmptyFormValidation(item2))
          result = false;
      }
      if (item2.hasClass('numeric')){
        if (!object.testNumericFormValidation(item2))
          result = false;
      }
      if (item2.hasClass('currency')){
        if (!object.testCurrencyFormValidation(item2))
          result = false;
      }
      if (item2.hasClass('phone')){
        if (!object.testPhoneFormValidation(item2))
          result = false;
      }
      if (item2.hasClass('email')){
        if (!object.testEmailFormValidation(item2))
          result = false;
      }
      if (item2.hasClass('requiredLink')){
        if (!object.testLinkFormValidation(item2))
          result = false;
      }
      if (item2.hasClass('date')){
        if (!object.testDateFormValidation(item2))
          result = false;
      }
    });
    
    item.getElements('input[type=password]').each(function(item2, index2){
      if (item2.hasClass('required')){
        if (!object.testEmptyFormValidation(item2))
          result = false;
      }
    });
    
    item.getElements('textarea').each(function(item2, index2){
      if (item2.hasClass('required')){
        if (!object.testEmptyFormValidation(item2))
          result = false;
      }
      if (item2.hasClass('numeric')){
        if (!object.testNumericFormValidation(item2))
          result = false;
      }
      if (item2.hasClass('currency')){
        if (!object.testCurrencyFormValidation(item2))
          result = false;
      }
      if (item2.hasClass('phone')){
        if (!object.testPhoneFormValidation(item2))
          result = false;
      }
      if (item2.hasClass('email')){
        if (!object.testEmailFormValidation(item2))
          result = false;
      }
      if (item2.hasClass('requiredLink')){
        if (!object.testLinkFormValidation(item2))
          result = false;
      }
      if (item2.hasClass('date')){
        if (!object.testDateFormValidation(item2))
          result = false;
      }
    });
    
    item.getElements('select').each(function(item2, index2){
      if (item2.hasClass('required')){
        if (!object.testSelectFormValidation(item2))
          result = false;
      }
    });
    
    return result;
  },
  
  // test un objet selon sa classe
  testItemValidation: function(item){
    var object = this;
    
    item.addEvent('blur', function(){
      this.setStyles(object.options.normalCss);
    });
    
    item.addEvent('focus', function(){
      this.setStyles(object.options.focusCss);
    });
    
    if (item.hasClass('required')){
      // regarde si c'est une liste déroulante ou pas
      if (item.get('tag') == 'select'){
        item.addEvent('blur', function(){
          // test si le champ a été rempli
          object.testSelectFormValidation(this);
        });
      }
      else{
        item.addEvent('blur', function(){
          // test si le champ a été rempli
          object.testEmptyFormValidation(this);
        });
      }
    }
    if (item.hasClass('numeric')){
      item.addEvent('blur', function(){
        // test si le champ a été rempli
        object.testNumericFormValidation(this);
      });
    }
    if (item.hasClass('currency')){
      item.addEvent('blur', function(){
        // test si le champ a été rempli
        object.testCurrencyFormValidation(this);
      });
    }
    if (item.hasClass('phone')){
      item.addEvent('blur', function(){
        // test si le champ a été rempli
        object.testPhoneFormValidation(this);
      });
    } 
    if (item.hasClass('email')){
      item.addEvent('blur', function(){
        // test si le champ a été rempli
        object.testEmailFormValidation(this);
      });
    }
    if (item.hasClass('requiredLink')){
      item.addEvent('blur', function(){
        // test si le champ a été rempli
        object.testLinkFormValidation(this);
      });
    }
    if (item.hasClass('date')){
      item.addEvent('blur', function(){
        // test si le champ a été rempli
        object.testDateFormValidation(this);
      });
    }       
  },
  
  // reset un élément (supprime sa mise en forme et enlève les éventuels messages d'erreur)
  ResetItem: function(item){
    var object = this;
    var siblings = item.getSiblings('div[rel=error]');
    if (siblings[0]){
      siblings[0].dispose();
    }
    item.setStyles(object.options.normalCss);
  },
    
  majFormValidation: function(){
    var object = this;   
    
    // envoi le formulaire en mode "ajax" si demandé
    if (this.options.useAjax && 
        this.options.ajaxUrl != null && 
        this.options.ajaxSuccessFct != null){
      this.form.addEvent('submit', function(){  
        // test si tous les champs obligatoires ont été remplis
        if (!object.testFormValidation(this))
          return false;
        /*object.sendResult.setStyle('display','block');
        object.sendResultTxt.set('html','Veuillez patienter...');*/
        object.req.send(object.form.toQueryString());
        return false;
      });
    }
    // sinon envoi le formulaire en mode "normal"
    else{
      this.form.addEvent('submit', function(){  
    		// test si tous les champs obligatoires ont été remplis
    		if (!object.testFormValidation(this))
          return false;
      });
    }
    
    // ajoute l'événement "onBlur" et "onFocus" sur tous les éléments "input" (du formulaire en cours)
    this.form.getElements('input[type=text]').each(function(item2, index2){
      object.testItemValidation(item2);
    });
    
    // ajoute l'événement "onBlur" et "onFocus" sur tous les éléments "input" (du formulaire en cours)
    this.form.getElements('input[type=password]').each(function(item2, index2){
      object.testItemValidation(item2);
    });
    
    // ajoute l'événement "onBlur" et "onFocus" sur tous les éléments "textarea" (du formulaire en cours)
    this.form.getElements('textarea').each(function(item2, index2){
      object.testItemValidation(item2);
    });
    
    // ajoute l'événement "onBlur" et "onFocus" sur tous les éléments "option" (du formulaire en cours)
    this.form.getElements('select').each(function(item2, index2){
      object.testItemValidation(item2);
    });
    
    // test que l'e-mail soit confirmé
    this.form.addEvent('submit', function(){
      object.divError.set('html','&nbsp;&nbsp;' + "Les adresses e-mail ne correspondent pas");
      var obj = 'confirm_email1';
      var obj2 = 'email1';
      
      if (!$(obj) || !$(obj2))
        return;

      if ($(obj).value != $(obj2).value){
        $(obj).setStyles(object.options.invalidCss);
        var where = '';
        if (object.options.errorPlace == 'bottom'){
          object.divError.setStyle('display','block');
          $(obj).setStyle('display','block');
          where = 'after';
        }
        else if(object.options.errorPlace == 'right'){
          object.divError.setStyle('display','inline');
          where = 'after';
        }
        else if (object.options.errorPlace == 'top'){
          object.divError.setStyle('display','block');
          where = 'before';
        }
        else if(object.options.errorPlace == 'left'){
          object.divError.setStyle('display','inline');
          where = 'before';
        }
        object.divError.clone().inject($(obj),where);
        return false;
      }
    });
  }
});