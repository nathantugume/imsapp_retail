<?php
require_once('../init/init.php');

$output = '';

// Get outstanding orders
$orders = $customerPayment->get_outstanding_orders();

if(!empty($orders)){
    $output .= '
        <table id="payments_data" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Customer Name</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Outstanding</th>
                    <th>Order Date</th>
                    <th width="15%" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach($orders as $order){
        $outstanding_class = $order->due > 0 ? 'text-danger' : 'text-success';
        
        $output .= '<tr>
            <td>' . $order->invoice_no . '</td>
            <td>' . $order->customer_name . '</td>
            <td>ugx ' . number_format($order->net_total, 2) . '</td>
            <td>ugx ' . number_format($order->paid, 2) . '</td>
            <td class="' . $outstanding_class . '"><strong>ugx ' . number_format($order->due, 2) . '</strong></td>
            <td>' . $order->order_date . '</td>
            <td align="center">
                <a href="#" order_id="' . $order->invoice_no . '" class="btn btn-info btn-sm view-payments-btn" data-toggle="modal" data-target="#paymentHistoryModal">View Payments</a>
            </td>
        </tr>';
    }
    
    $output .= '</tbody>
        </table>';
    
    echo $output;
} else {
    echo '<div class="alert alert-success text-center">No outstanding orders found. All payments are up to date!</div>';
}
?>

