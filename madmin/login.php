<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
  <head>
  <meta http-equiv="content-Type" content="text/html; charset=utf-8">
  <title>Miki CMS - FBW-One</title>
  <META Name="Description" Lang="fr" Content="">
  <META Name="Keywords" Lang="fr" Content="">
  <META Name="Reply-To" Content="info@fbw-one.com">
  <META Name="Category" Content="CMS FBW-One">
  <META Name="Robots" Content="index, follow">
  <META Name="Distribution" Content="global">
  <META Name="Revisit-After" Content="15 days">
  <META Name="Author" Lang="fr" Content="FBW-One sarl">
  <META Name="Publisher" Content="FBW-One sarl">
  <META Name="Copyright" Content="&reg;fbw-one sarl">
  
  <link rel="stylesheet" type="text/css" href="style.css" />
  <script type="text/javascript" src="scripts/mootools.js"></script>
  <script type="text/javascript" src="scripts/checkform.js"></script>
  
  <script type="text/javascript">
  
    window.addEvent('domready', function() {
      $('caseInfos').setStyle('left',((window.getSize().x/2)-370) + 'px');
      $('caseLogin').setStyle('left',((window.getSize().x/2)+20) + 'px');
      
      $('login_input').focus();
      
      var myCheckForm = new checkForm($('formulaire'),{
                                        useAjax: true,
                                        errorPlace: 'bottom',
                                        divErrorCss: {
                                          'margin':'5px 0 0 120px'
                                        },
                                        sendResultCss: {
                                          'top':'250px',
                                          'background-color':'#ffffff'
                                        },
                                        ajaxSuccessFct: function(msg){
                                          if (msg != "-1"){
                                            // si aucune page de démarrage n'a été définie pour l'utilisateur, on affiche les pages
                                            if (msg == "")
                                              msg = "101";
                                              
                                            $('sendResult').setStyle('display','block');
                                            $('sendResultTxt').set('html', 'Vous avez été authentifié avec succès<br /><br />Vous allez être redirigé dans quelques instants');
                                            (function(){document.location.href='index.php?pid=' + msg}).delay(5000);
                                          }
                                          else{
                                            $('sendResult').setStyle('display','block');
                                            $('sendResultTxt').set('html', 'Votre nom d\'utilisateur ou votre mot de passe est incorrect<br /><br />Ce message disparaîtra dans quelques instants');
                                            (function(){$('sendResult').setStyle('display','none');}).delay(5000);
                                          }
                                        }
                                      });
    });
  </script>
  
  <style type="text/css">
    body{
      background: #ffffff url(design/bg_body.gif) repeat-x fixed top left;
      font-family: Helvetica, Geneva, Arial, SunSans-Regular, sans-serif;
      color: #000000;
      font-size: 11px;
    }
    
    #caseInfos, #caseLogin{
      position: absolute;
      top: 200px;
      border: solid 1px #cccccc;
      width: 350px;
      height: 200px;
      padding: 3px;
      background-color: #ffffff;
    }
    
    #caseTitre1{
      color: #ffffff;
      font-size: 14px;
      font-weight: bold;
      padding: 1em 0.6em;
    }
    
    #caseTitre2{
      text-align:right;
      color: #ffffff;
      font-size: 14px;
      font-weight: bold;
      padding: 1em 0.6em;
    }
    
    .bouton{
      width: 100%;
      height: 40px;
      background-image: url(design/bg_bouton.gif);
      background-repeat: repeat-x;
      background-position: top left;
      background-color: #7896d6;
      margin-bottom: 20px;
    }
    
    ol li{
      margin-left: 20px; 
    }
    
    #image{
      text-align: center;
      margin-top: 450px;
    }
    
    input, textarea{
      border: solid 1px #cccccc;
    }
    
    #send_result{
      position: absolute;
      top: 200px;
      left: 10px;
      background-color: #e4e4e4;
      display: none;
      border: 1px solid #000000;
      width: 450px;
      z-index: 500;
      text-align: center;
      filter:alpha(opacity=90);
      -moz-opacity:.90;
      opacity:.90;
    }
    
    #send_result_text{
      position: relative;
      color: #000000;
      font-family: Helvetica, Geneva, Arial, SunSans-Regular, sans-serif;
      font-size: 12px;
      font-weight: bold;
      margin: 10px auto 10px auto;
    }
  </style>
  
  </head>
  <body>
    <div id="caseInfos">
      <div class="bouton">
        <div id="caseTitre1">Informations</div>
      </div>
      <img src="design/login.gif" style="float:left;margin:0 10px" />
      <p style="color:#3b5998;padding-top:20px;font-weight:bold">A partir de ce point prendre en considération <br />les paramètres suivants :</p>
      <p style="margin-top:30px">
        <ol>
          <li>Les cookies sont autorisés dans votre navigateur</li>
          <li>Le Javascript est activé dans votre navigateur</li>
        </ol>
      </p>
      
    </div>
    
    <div id="caseLogin">
      <div class="bouton">
        <div id="caseTitre2">Connexion administration</div>
      </div>
      <form id="formulaire" action="scripts/test_connection_admin.php" method="post" name="formulaire" autocomplete="off" enctype="application/x-www-form-urlencoded" style="margin:30px 0 0 10px">
        <div style="float:left;width:100px;text-align:right;margin-right:20px">Nom d'utilisateur : </div>
        <input type="text" name="login" id="login_input" class="required" style="width:150px" />
        <div style="clear:both;float:left;width:100px;text-align:right;margin:5px 20px 0 0">Mot de passe : </div>
        <input type="password" name="password" class="required" style="margin-top:5px;width:150px" /><br /><br />
        <input type="submit" value="Go..." style="margin-left:120px" class="required" />
      </form>
    </div>
    
    <div id="image">
      Powered by<br />
      <a href="http://www.fbw-one.com" target="_blank" title="FBW-One"><img src="design/fbw-one.jpg" border="0" /></a>
    </div>
  </body>
</html>
