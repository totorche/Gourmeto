<?php 
  require_once("include/headers.php");
  
  //if (!test_right(31) && !test_right(32) && !test_right(33))
  //  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  $nb_max_sendings = 5;
  
  // envoi de la newsletter
  try{
    $newsletter = new Miki_newsletter($_GET['id']); 
    $template = new Miki_newsletter_template($newsletter->template_id);
    $template->content = stripslashes($template->content);
    
    $code = "<html>
             <head>
               <title>$newsletter->subject</title>
             </head>
             <body>
               $template->content
             </body>
             </html>";
             
    
    // évalue le code final (exécute le php s'il y en a)
    ob_start();
    eval("?>" .$code);
    $code = ob_get_contents();
    ob_end_clean();
    
    $code = str_replace("[miki_content]", $newsletter->content, $code);
    $code = str_replace("[miki_newsletter_title]", $newsletter->subject, $code);
    
    echo $code;
  }catch(Exception $e){
    $newsletter->state = 1;
    $newsletter->update();
    echo $e->getMessage();
    exit();
  }
?>

<br /><br /><br />
<div style="text-align:center">
  <a href="index.php?pid=114" title="<?php echo _("Retour aux newsletters"); ?>"><?php echo _("Retour aux newsletters"); ?></a>
</div>