<head>
  
<link rel="alternate" type="application/rss+xml" title="Flux RSS" href="<?php echo URL_BASE ."rss/news.php"; ?>" />
  
<?php
  $display_type = "list"; // "grid" or "list"
  $max_news = 4; // définit le nombre max de news à afficher par page
?>

<style type="text/css">
  
  .bloc_links{
    font-size: 1.3em;
    margin-bottom: 20px;
    width: auto;
  }
  
  .bloc_links td{
    vertical-align: top;
    padding: 0;
  }
  
  .news_overlay{
    position: absolute;
    left: 0px;
    bottom: 6px;
    width: 96px;
    height: 20px;
    background: url(<?php echo URL_BASE; ?>pictures/bandeau-news.png) no-repeat top left;
  }
  
  .news_picture{
    position: relative;
  }
  
  .news_picture img{
    background: #FFFFFF;
    padding: 5px;
    border: 1px solid #979797;
  }
  
  .news_picture img:hover{
    background: #AC53B8;
  }
  
  .news_text{
    float: left;
    padding-top: 10px;
    text-align: left;
    width: 100%;
    height: 115px;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  
  .news_title{
    font-weight: bold;
    margin-bottom: 5px;
  }
  
  .news_description{
  }
  
  ul.main_table{
    margin: 0 0 40px 0;
    list-style-type: none;
    overflow: hidden;
  }
  
  ul.main_table li{
    float: left;
    width: 218px;
    margin: 0 10px 10px 0;
    overflow: hidden;
    list-style-type: none;
    background: none;
    padding: 10px;
    border: solid 1px #CDCDCD;
    height: 250px;
    overflow: hidden;
    
    background-image: linear-gradient(bottom, rgb(255,255,255) 24%, rgb(245,245,245) 62%);
    background-image: -o-linear-gradient(bottom, rgb(255,255,255) 24%, rgb(245,245,245) 62%);
    background-image: -moz-linear-gradient(bottom, rgb(255,255,255) 24%, rgb(245,245,245) 62%);
    background-image: -webkit-linear-gradient(bottom, rgb(255,255,255) 24%, rgb(245,245,245) 62%);
    background-image: -ms-linear-gradient(bottom, rgb(255,255,255) 24%, rgb(245,245,245) 62%);
    
    background-image: -webkit-gradient(
    	linear,
    	left bottom,
    	left top,
    	color-stop(0.24, rgb(255,255,255)),
    	color-stop(0.62, rgb(245,245,245))
    );
  }
  
  ul.list_display li{
    float: none;
    width: 978px;
    height: auto;
  }
  
  ul.list_display .news_picture{
    float: left;
    margin-right: 20px;
  }
  
  ul.list_display .news_text{
    width: 758px;
    height: auto;
  }
  
  ul.list-view-options{
    list-style-type: none;
    margin: 0 0 20px 0;
  }
  
  ul.list-view-options li{
    float: left;
    width: auto;
    background: none;
    padding: 0;
    overflow: hidden;
    margin-right: 10px;
  }
  
  ul.list-view-options li span{
    float: right;
    width: 18px;
    height: 18px;
    background: url(<?php echo URL_BASE; ?>pictures/grille-liste.png) no-repeat;
    margin-left: 5px;
  }
  
  #affichage_grille{
    <?php if ($display_type == "grid"){ ?>
    background-position: 0px 0px;
    <?php }else{ ?>
    background-position: -22px 0px;
    <?php } ?>
  }
  
  #affichage_liste{
    <?php if ($display_type == "grid"){ ?>
    background-position: -72px 0px;
    <?php }else{ ?>
    background-position: -50px 0px;
    <?php } ?>
  }
    
</style>

<script type="text/javascript">
  
  function list_view_options(type){
    if (type == 1){
      $("ul.main_table").removeClass("list_display");
      $("ul.list-view-options li:first-child span").css('background-position', '0px 0px');
      $("ul.list-view-options li:last-child span").css('background-position', '-72px 0px');
    }
    else if (type == 2){
      $("ul.main_table").addClass("list_display");
      $("ul.list-view-options li:first-child span").css('background-position', '-22px 0px');
      $("ul.list-view-options li:last-child span").css('background-position', '-50px 0px');
    }
  }

</script>
		
</head>

<ul class="list-view-options" style="height: 18px;">
  <li>
    <a href="javascript:list_view_options(1);" title="<?php echo _("Display in grid"); ?>"><?php echo "Grid"; ?><span id="affichage_grille"></span></a>
  </li>
  <li>
    <a href="javascript:list_view_options(2);" title="<?php echo _("Display in list"); ?>"><?php echo "List"; ?><span id="affichage_liste"></span></a>
  </li>
</ul>

<?php
  if (isset($_GET['p']) && is_numeric($_GET['p']))
    $p = $_GET['p'];
  else
    $p = 1;
  
  echo "<ul class='main_table" .(($display_type == "list") ? " list_display" : "") ."'>";
          Miki_news::print_all("actualites", $max_news, $_SESSION['lang'], $p, "default_list");
  echo "</ul>";    
?>