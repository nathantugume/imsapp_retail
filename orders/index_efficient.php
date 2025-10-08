<?php
// Enhanced orders listing with preloaded data
require_once("../init/init.php");

$page = isset($_POST['page']) ? $_POST['page'] : 1;
$record_per_page = 5;
$starting_point = ($page - 1) * $record_per_page;

$orders = $order->fetch_all_the_orders($starting_point, $record_per_page);
$total_records = $order->pagination_link();

// Preload all order details for efficient viewing
$order_details = [];
if($orders) {
    foreach($orders as $ord) {
        $details = $order->fetch_all_orders_with_invoice($ord->invoice_no);
        if($details) {
            $order_info = [
                'invoice_no' => $details[0]['invoice_no'],
                'customer_name' => $details[0]['customer_name'],
                'address' => $details[0]['address'],
                'subtotal' => $details[0]['subtotal'],
                'discount' => $details[0]['discount'],
                'net_total' => $details[0]['net_total'],
                'paid' => $details[0]['paid'],
                'due' => $details[0]['due'],
                'payment_method' => $details[0]['payment_method'],
                'order_date' => $details[0]['order_date'],
                'products' => []
            ];
            
            foreach($details as $detail) {
                if(isset($detail['id']) && $detail['product_name']) {
                    $order_info['products'][] = [
                        'id' => $detail['id'],
                        'product_name' => $detail['product_name'],
                        'order_qty' => $detail['order_qty'],
                        'price_per_item' => $detail['price_per_item']
                    ];
                }
            }
            $order_details[$ord->invoice_no] = $order_info;
        }
    }
}
?>

<script>
// Preloaded order data for instant viewing
window.orderDetails = <?php echo json_encode($order_details); ?>;
</script>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Invoice No.</th>
                <th>Customer Name</th>
                <th>Order Date</th>
                <th>Net Total</th>
                <th>Paid</th>
                <th>Due</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if($orders): ?>
                <?php foreach($orders as $order_item): ?>
                <tr>
                    <td><?php echo $order_item->invoice_no; ?></td>
                    <td style="text-transform: capitalize;"><?php echo $order_item->customer_name; ?></td>
                    <td><?php echo $order_item->order_date; ?></td>
                    <td>₹<?php echo $order_item->net_total; ?>.00</td>
                    <td>₹<?php echo $order_item->paid; ?>.00</td>
                    <td>₹<?php echo $order_item->due; ?>.00</td>
                    <td>
                        <button class="btn btn-info btn-sm view-btn-efficient" 
                                data-invoice="<?php echo $order_item->invoice_no; ?>">
                            <i class="fa fa-eye"></i> View
                        </button>
                        <button class="btn btn-success btn-sm pdf-btn" 
                                pdf-id="<?php echo $order_item->invoice_no; ?>" 
                                name="<?php echo $order_item->customer_name; ?>">
                            <i class="fa fa-file-pdf-o"></i> PDF
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center">No orders found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php
$total_pages = ceil($total_records / $record_per_page);
if($total_pages > 1): ?>
<nav>
    <ul class="pagination">
        <?php for($i = 1; $i <= $total_pages; $i++): ?>
        <li class="<?php echo ($i == $page) ? 'active' : ''; ?>">
            <a href="#" class="page_no" id="<?php echo $i; ?>"><?php echo $i; ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>
