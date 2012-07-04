<?php

// vérifie si on tourne sous Windows ou pas
if (strpos($_SERVER['SERVER_SOFTWARE'], 'Win32') !== false || 
    strpos($_SERVER['SERVER_SOFTWARE'], 'Win64') !== false)
  $is_windows = true;
else
  $is_windows = false;

if ($is_windows)
  ini_set("include_path", ini_get("include_path") .";../../../../../../../;../../../../../../../class;../../../../../../../include;../../../../../../../scripts;../../../../../../../class;../../../../../../../include;../../../../../../../scripts;../../../../../../../include/mail");
else
  ini_set("include_path", ini_get("include_path") .":../../../../../../../:../../../../../../../class:../../../../../../../include:../../../../../../../scripts:../../../../../../../class:../../../../../../../include:../../../../../../../scripts:../../../../../../../include/mail");


include("../../../../../../../index.php");

?>

<script type="text/javascript">
  $('miki_preview_content').set('html', tinyMCEPopup.editor.getContent());
</script>