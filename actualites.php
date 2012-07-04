<head>
  <link rel="alternate" type="application/rss+xml" title="Flux RSS" href="<?php echo URL_BASE ."rss/news.php"; ?>" />
</head>

        
<h1>Les actualités de <?php echo Miki_configuration::get('sitename'); ?></h1>

<?php
  
  if (isset($_GET['page']) && is_numeric($_GET['page']))
    $page_no = $_GET['page'];
  else
    $page_no = 1;
  
  Miki_news::print_all("actualites", 10, $page_no, "default_list");
?>