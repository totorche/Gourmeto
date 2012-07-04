/**
 * Met à jour le widget Panier
 */ 
function update_basket(){
  if ($(".widget_panier").length > 0){
    $(".widget_panier").load("box/panier.php");
  }
}