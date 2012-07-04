<?php 
  require_once("include/headers.php");
  
  // récupert la personne connectée si disponible
  $miki_person = is_connected();
  
  ob_start();
  include("order_pdf.php");
  $content = ob_get_clean();
  require_once('scripts/html2pdf/html2pdf.class.php'); 
  $pdf = new HTML2PDF('P','A4','fr', true, 'UTF-8', array(20, 6, 8, 10));
  $pdf->writeHTML($content);
  $pdf->setDefaultFont("Arial");
  $pdf->Output();
?>