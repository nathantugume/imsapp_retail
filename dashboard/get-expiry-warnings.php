<?php
require_once('../init/init.php');

// Get expiring products (within 30 days)
$expiring_products = $product->get_expiring_products(30);
$expired_products = $product->get_expired_products();

$output = '';

if(!empty($expired_products)){
    $output .= '<div class="alert alert-danger">';
    $output .= '<h5><i class="fas fa-times-circle"></i> Expired Products (' . count($expired_products) . ')</h5>';
    $output .= '<div class="row">';
    
    foreach($expired_products as $product_item){
        $output .= '<div class="col-md-4">';
        $output .= '<div class="panel panel-danger">';
        $output .= '<div class="panel-body">';
        $output .= '<strong>' . $product_item->product_name . '</strong><br>';
        $output .= '<small>Expired: ' . date('M d, Y', strtotime($product_item->expiry_date)) . '</small><br>';
        $output .= '<small>Stock: ' . $product_item->stock . ' units</small>';
        $output .= '</div></div></div>';
    }
    
    $output .= '</div></div>';
}

if(!empty($expiring_products)){
    $output .= '<div class="alert alert-warning">';
    $output .= '<h5><i class="fas fa-exclamation-triangle"></i> Products Expiring Soon (' . count($expiring_products) . ')</h5>';
    $output .= '<div class="row">';
    
    foreach($expiring_products as $product_item){
        $days_until_expiry = ceil((strtotime($product_item->expiry_date) - time()) / (60 * 60 * 24));
        $alert_class = $days_until_expiry <= 7 ? 'panel-danger' : 'panel-warning';
        
        $output .= '<div class="col-md-4">';
        $output .= '<div class="panel ' . $alert_class . '">';
        $output .= '<div class="panel-body">';
        $output .= '<strong>' . $product_item->product_name . '</strong><br>';
        $output .= '<small>Expires: ' . date('M d, Y', strtotime($product_item->expiry_date)) . '</small><br>';
        $output .= '<small>Stock: ' . $product_item->stock . ' units</small><br>';
        $output .= '<small class="text-' . ($days_until_expiry <= 7 ? 'danger' : 'warning') . '">' . $days_until_expiry . ' days left</small>';
        $output .= '</div></div></div>';
    }
    
    $output .= '</div></div>';
}

if(empty($expired_products) && empty($expiring_products)){
    $output .= '<div class="alert alert-success text-center">';
    $output .= '<i class="fas fa-check-circle"></i> No products expiring in the next 30 days.';
    $output .= '</div>';
}

echo $output;
?>






