<?php
  @session_start();  
  include_once 'captcha.class.php';
  $I = new captcha('GIF');
  $I->setStringLenght(5);
  $I->setFont('../fonts/KISSMKMK.ttf' , 15);
  $I->setBackgroundColor(255,255,255);
  $I->setBackgroundImage("fond_captcha.jpg");
  $I->getImage();
  $_SESSION['captcha-control'] = $I->getRandString();
?>