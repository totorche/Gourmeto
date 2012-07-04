<?php
  if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
  	$_SERVER['DOCUMENT_ROOT'] = dirname($_SERVER['SCRIPT_FILENAME']);
  } else {
  	//$_SERVER['DOCUMENT_ROOT'] = '/var/www/' . $_SERVER['SERVER_NAME'] . '/html';
  }
?>
