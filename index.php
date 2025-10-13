<?php
// Include the proper database connection
require_once("init/init.php");
// $dbcon is already initialized in init.php as a singleton
$pdo = $dbcon->connect();

// Initialize profit calculator
// Initialize profit calculator with error handling
try {
    $profitCalc = new ProfitCalculator();
    $dailyProfit = $profitCalc->getDailyProfit();
    $monthlyProfit = $profitCalc->getMonthlyProfit();
} catch (Exception $e) {
    // Set default values if profit calculation fails
    $dailyProfit = ["profit" => 0, "revenue" => 0, "cost" => 0];
    $monthlyProfit = ["profit" => 0, "revenue" => 0, "cost" => 0];
}

// Calculate total stock value (stock quantity × buying price)
try {
    $stockValueQuery = "SELECT SUM(stock * buying_price) as total_stock_value FROM products WHERE p_status = '1'";
    $stockValueStmt = $pdo->query($stockValueQuery);
    $stockValueResult = $stockValueStmt->fetch(PDO::FETCH_ASSOC);
    $totalStockValue = $stockValueResult['total_stock_value'] ?? 0;
} catch (Exception $e) {
    $totalStockValue = 0;
}

// Session is already started in init.php, so we do not need to start it again
session_start();

if(!isset($_SESSION['LOGGEDIN'])){
	header("location:login.php?unauth=unauthorized access?");
}

// Check user role and redirect to appropriate dashboard
$userRole = $_SESSION['LOGGEDIN']['role'] ?? '';
$userStatus = $_SESSION['LOGGEDIN']['status'] ?? '';

// If user is disabled, logout and redirect
if($userStatus == '0'){
	session_destroy();
	header("location:login.php?error=account_disabled");
	exit();
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Mini Price Hardware</title>
		<script src="js/jquery.min.js"></script>
		<script src="js/jquery.js"></script>
		<script src="js/jquery.dataTables.min.js"></script>
		<script src="js/dataTables.bootstrap4.min.js"></script>
		
		<!-- DataTables Export/Print Libraries -->
		<link rel="stylesheet" href="css/buttons.bootstrap4.min.css">
		<script src="js/dataTables.buttons.min.js"></script>
		<script src="js/buttons.bootstrap4.min.js"></script>
		<script src="js/jszip.min.js"></script>
		<script src="js/pdfmake.min.js"></script>
		<script src="js/vfs_fonts.js"></script>
		<script src="js/buttons.html5.min.js"></script>
		<script src="js/buttons.print.min.js"></script>

	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/custom.css">
	<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
	--><link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
		
		<!-- SweetAlert2 CSS and JS -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<!-- Font Awesome for icons -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
		
		<script src="js/bootstrap.js"></script>
		<script src="js/sweetalert-handler.js"></script>
		<style>
		/* Table Styling - Apply to both tables */
		#sales, #example {
			width: 100% !important;
			max-width: 100%;
			table-layout: auto;
		}
		
		#sales th, #sales td,
		#example th, #example td { 
			padding: 8px 12px; 
			white-space: normal;
			word-wrap: break-word;
			max-width: 200px;
		}
		
		#sales th, #example th {
			background-color: #f8f9fa;
			font-weight: 600;
			border-bottom: 2px solid #dee2e6;
		}
		
		#sales td, #example td {
			vertical-align: middle;
		}
		
		/* Responsive table wrapper */
		.table-responsive {
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
		}
		
		/* Panel improvements */
		.panel-default {
			border: 1px solid #ddd;
			border-radius: 4px;
			box-shadow: 0 1px 3px rgba(0,0,0,0.12);
		}
		
		.panel-heading {
			background-color: #f5f5f5;
			border-bottom: 1px solid #ddd;
			padding: 12px 15px;
			font-weight: 600;
		}
		
		.panel-body {
			padding: 15px;
		}
		
		/* DataTable improvements */
		.dataTables_wrapper {
			margin-top: 10px;
		}
		
		.dataTables_wrapper .dataTables_filter input {
			border: 1px solid #ced4da;
			border-radius: 4px;
			padding: 6px 12px;
			font-size: 14px;
		}
		
		/* Mobile responsiveness */
		@media (max-width: 768px) {
			#sales th, #sales td,
			#example th, #example td {
				padding: 6px 8px;
				font-size: 12px;
				max-width: 120px;
			}
			
			.panel-body {
				padding: 10px;
			}
		}
		</style>
		<script>
                $(document).ready(function() {
                $('#example').DataTable({
                        "responsive": true,
                        "pageLength": 10,
                        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                        "order": [[ 0, "asc" ]],
                        "searching": true,
                        "search": {
                                "smart": true,
                                "regex": false,
                                "caseInsensitive": true
                        },
                        "language": {
                                "search": "Search products:",
                                "lengthMenu": "Show _MENU_ products per page",
                                "info": "Showing _START_ to _END_ of _TOTAL_ products",
                                "infoEmpty": "Showing 0 to 0 of 0 products",
                                "infoFiltered": "(filtered from _MAX_ total products)",
                                "emptyTable": "No products found",
                                "zeroRecords": "No matching products found"
                        },
                        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                               '<"row"<"col-sm-12"B>>' +
                               '<"row"<"col-sm-12"tr>>' +
                               '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                        "buttons": [
                                {
                                        "extend": "excel",
                                        "text": "Export Excel",
                                        "className": "btn btn-success btn-sm",
                                        "title": "Product Stock Status - " + new Date().toLocaleDateString()
                                },
                                {
                                        "extend": "pdf",
                                        "text": "Export PDF",
                                        "className": "btn btn-danger btn-sm",
                                        "title": "Product Stock Status - " + new Date().toLocaleDateString(),
                                        "orientation": "landscape",
                                        "pageSize": "A4"
                                },
                                {
                                        "extend": "print",
                                        "text": "Print",
                                        "className": "btn btn-info btn-sm",
                                        "title": "Product Stock Status - " + new Date().toLocaleDateString(),
                                        "autoPrint": false
                                },
                                {
                                        "extend": "csv",
                                        "text": "Export CSV",
                                        "className": "btn btn-warning btn-sm",
                                        "title": "Product Stock Status - " + new Date().toLocaleDateString()
                                }
                        ]
                });
                } )</script>
		<script>
		$(document).ready(function() {
                $('#sales').DataTable({
                        "responsive": true,
                        "pageLength": 10,
                        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                        "order": [[5, "desc"]], // Sort by date column (6th column, 0-indexed)
                        "columnDefs": [
                                { "orderable": false, "targets": [] },
                                { "className": "text-center", "targets": [2, 5] },
                                { "className": "text-right", "targets": [3, 4] }
                        ],
                        "language": {
                                "search": "Search sales:",
                                "lengthMenu": "Show _MENU_ entries",
                                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                                "infoEmpty": "No entries found",
                                "infoFiltered": "(filtered from _MAX_ total entries)",
                                "zeroRecords": "No matching records found",
                                "paginate": {
                                        "first": "First",
                                        "last": "Last",
                                        "next": "Next",
                                        "previous": "Previous"
                                }
                        },
                        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                                   '<"row"<"col-sm-12"B>>' +
                                   '<"row"<"col-sm-12"tr>>' +
                                   '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                        "buttons": [
                                {
                                        "extend": "excel",
                                        "text": "Export Excel",
                                        "className": "btn btn-success btn-sm",
                                        "title": "Recent Sales - " + new Date().toLocaleDateString()
                                },
                                {
                                        "extend": "pdf",
                                        "text": "Export PDF",
                                        "className": "btn btn-danger btn-sm",
                                        "title": "Recent Sales - " + new Date().toLocaleDateString(),
                                        "orientation": "landscape",
                                        "pageSize": "A4"
                                },
                                {
                                        "extend": "print",
                                        "text": "Print",
                                        "className": "btn btn-info btn-sm",
                                        "title": "Recent Sales - " + new Date().toLocaleDateString(),
                                        "autoPrint": false
                                },
                                {
                                        "extend": "csv",
                                        "text": "Export CSV",
                                        "className": "btn btn-warning btn-sm",
                                        "title": "Recent Sales - " + new Date().toLocaleDateString()
                                }
                        ]
                });
		} )</script>
	</head>
<body>
<?php include('common/navbar.php'); ?>

	<div class="row">
		<?php if($userRole == 'Master'): ?>
		<!-- Master/Admin Dashboard -->
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total User</strong></div>
				<div class="panel-body total_user" align="center" data-format="number">
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Category</strong></div>
				<div class="panel-body total_category" align="center" data-format="number">
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Brand</strong></div>
				<div class="panel-body total_brand" align="center" data-format="number">
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Item in Stock</strong></div>
				<div class="panel-body total_item" align="center" data-format="number">
				</div>
			</div>
		</div>
		<?php else: ?>
		<!-- User Dashboard -->
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong><i class="fas fa-user"></i> Welcome, <?php echo htmlspecialchars($_SESSION['LOGGEDIN']['name']); ?>!</strong></div>
				<div class="panel-body" align="center">
					<h4>User Dashboard</h4>
					<p>You are logged in as a <strong>User</strong></p>
					<p><small>Limited access to system features</small></p>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong><i class="fas fa-boxes"></i> Available Products</strong></div>
				<div class="panel-body total_item" align="center" data-format="number">
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong><i class="fas fa-calendar"></i> Today's Date</strong></div>
				<div class="panel-body" align="center">
					<h4><?php echo date('M d, Y'); ?></h4>
					<p><small><?php echo date('l'); ?></small></p>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Order Value</strong></div>
				<div class="panel-body total_order_value" align="center" data-format="number">
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Cash Received</strong></div>
				<div class="panel-body cash_value" align="center" data-format="currency">
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Outstanding Balance</strong></div>
				<div class="panel-body credit_value" align="center" data-format="currency">
				</div>
			</div>
		</div>
                <div class="col-md-4">
                        <div class="panel panel-default">
                                <div class="panel-heading"><strong>Daily Profit</strong></div>
                                <div class="panel-body daily_profit" align="center">
                                        <h1>ugx <?php echo number_format($dailyProfit["profit"] ?? 0, 2); ?></h1>
                                        <small>Revenue: ugx <?php echo number_format($dailyProfit["revenue"] ?? 0, 2); ?></small>
                                </div>
                        </div>
                </div>
                <div class="col-md-4">
                        <div class="panel panel-default">
                                <div class="panel-heading"><strong>Monthly Profit</strong></div>
                                <div class="panel-body monthly_profit" align="center">
                                        <h1>ugx <?php echo number_format($monthlyProfit["profit"] ?? 0, 2); ?></h1>
                                        <small>Revenue: ugx <?php echo number_format($monthlyProfit["revenue"] ?? 0, 2); ?></small>
                                </div>
                        </div>
                </div>
                <div class="col-md-4">
                        <div class="panel panel-default">
                                <div class="panel-heading"><strong>Profit Margin</strong></div>
                                <div class="panel-body profit_margin" align="center">
                                        <h1><?php echo ($monthlyProfit["revenue"] ?? 0) > 0 ? round((($monthlyProfit["profit"] ?? 0) / ($monthlyProfit["revenue"] ?? 0)) * 100, 2) : 0; ?>%</h1>
                                        <small>Monthly Margin</small>
                                </div>
                </div>
        </div>
		
		<!-- Total Stock Value Panel -->
		<div class="col-md-12">
			<div class="panel panel-default" style="border-left: 4px solid #28a745;">
				<div class="panel-heading" style="background-color: #f8f9fa;">
					<strong><i class="fas fa-warehouse"></i> Total Stock Value</strong>
					<span class="pull-right"><small><i class="fas fa-info-circle"></i> Based on buying price × stock quantity</small></span>
				</div>
				<div class="panel-body" align="center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
					<h4 style="color: #28a745; font-size: 3em; margin: 20px 0;">
						<i class="fas fa-chart-line"></i> UGX <?php echo number_format($totalStockValue, 2); ?>
					</h4>
					<div class="row" style="margin-top: 20px;">
						<div class="col-md-4">
							<div style="padding: 10px; background: white; border-radius: 5px; margin: 5px;">
								<h4 style="color: #667eea; margin: 5px 0;"><i class="fas fa-boxes"></i> Active Products</h4>
								<p id="active-products-count" style="font-size: 1.5em; font-weight: bold; margin: 5px 0;">Loading...</p>
							</div>
						</div>
						<div class="col-md-4">
							<div style="padding: 10px; background: white; border-radius: 5px; margin: 5px;">
								<h4 style="color: #667eea; margin: 5px 0;"><i class="fas fa-calculator"></i> Avg. Value/Item</h4>
								<p id="avg-stock-value" style="font-size: 1.5em; font-weight: bold; margin: 5px 0;">Calculating...</p>
							</div>
						</div>
						<div class="col-md-4">
							<div style="padding: 10px; background: white; border-radius: 5px; margin: 5px;">
								<h4 style="color: #667eea; margin: 5px 0;"><i class="fas fa-cubes"></i> Total Units</h4>
								<p id="total-stock-units" style="font-size: 1.5em; font-weight: bold; margin: 5px 0;">Calculating...</p>
							</div>
						</div>
					</div>
					<small style="display: block; margin-top: 15px; color: #6c757d;">
						<i class="fas fa-clock"></i> Updated: <?php echo date('M d, Y H:i:s'); ?>
					</small>
				</div>
			</div>
		</div>
		
		<!-- Expiry Warnings Panel -->
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<strong><i class="fas fa-exclamation-triangle"></i> Product Expiry Warnings</strong>
				</div>
				<div class="panel-body">
					<div id="expiry-warnings">
						<!-- Expiry warnings will be loaded here -->
					</div>
				</div>
			</div>
		</div>
		
		<hr />
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading" align="center"><strong>Current Stock</strong></div>
				<div class="panel-body">
					<div class="table-responsive">
					<?php 
						// Use existing PDO connection

						$sql = "SELECT * FROM products";
                                                $stmt = $pdo->prepare($sql);
                                                $stmt->execute();
                                                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
						?>
						<table id="example" class="table table-striped table-bordered table-hover">
							<thead class="thead-light">
								<tr>
									<th>Product</th>
									<th>Stock status</th>
									<th>Availability</th>
									<th>Buying Price</th>
								</tr>
							</thead>
						<tbody>
						<?php 
							$i = 0;						
							foreach($results as $result => $value){
								if(intval($value['stock']) > 31){
									$availabality = "in stock";
								}elseif(intval($value['stock'])<30 && intval($value['stock'])>1){
									$availabality = "product is running out of stock";
								}elseif(intval($value['stock'])===0)
								{
									$availabality = "product out of stock";
								}
								echo '<tr><td>' . $value['product_name'] . '</td>';
								echo '<td>' . $value['stock'] . " (ugx " . number_format($value['price'], 2) . ")" . '</td>';
								echo '<td>' . $availabality . '</td>';
								echo '<td>ugx ' . number_format($value['buying_price'], 2) . '</td></tr>';
								$i++;
							}
								
							?>	
						</tbody>	
					</table>
					</div>		
				</div>
			</div>
		</div>
                <div class="col-md-12">
                        <div class="panel panel-default">
                                <div class="panel-heading" align="center">
                                    <strong><i class="fas fa-chart-line"></i> 
                                    <?php if($userRole == 'Master'): ?>
                                        Recent Sales
                                    <?php else: ?>
                                        Product Information
                                    <?php endif; ?>
                                    </strong>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table id="sales" class="table table-striped table-bordered table-hover">
                                            <thead class="thead-light">
                                                <tr>
                                                    <?php if($userRole == 'Master'): ?>
                                                    <th width="15%">Customer</th>
                                                    <th width="25%">Product</th>
                                                    <th width="10%">Qty</th>
                                                    <th width="15%">Paid</th>
                                                    <th width="15%">Balance</th>
                                                    <th width="20%">Date</th>
                                                    <?php else: ?>
                                                    <th width="30%">Product Name</th>
                                                    <th width="20%">Category</th>
                                                    <th width="15%">Brand</th>
                                                    <th width="15%">Price</th>
                                                    <th width="10%">Stock</th>
                                                    <th width="10%">Status</th>
                                                    <?php endif; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php 
                                                if($userRole == 'Master') {
                                                    // Master/Admin sees sales data
                                                    $salesQuery = "SELECT o.invoice_no, o.customer_name, i.product_name, 
                                                                   i.order_qty as quantity, o.paid as amount_paid,
                                                                   o.due as balance, o.order_date
                                                                   FROM orders o
                                                                   LEFT JOIN invoices i ON i.invoice_no = o.invoice_no
                                                                   ORDER BY o.order_date DESC 
                                                                   LIMIT 50";
                                                    
                                                    $stmt = $pdo->prepare($salesQuery);
                                                    $stmt->execute();
                                                    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                    foreach($sales as $sale) {
                                                        $customerName = !empty($sale['customer_name']) ? htmlspecialchars($sale['customer_name']) : 'N/A';
                                                        $productName = !empty($sale['product_name']) ? htmlspecialchars($sale['product_name']) : 'N/A';
                                                        $quantity = $sale['quantity'] ?? 0;
                                                        $amountPaid = $sale['amount_paid'] ?? 0;
                                                        $balance = $sale['balance'] ?? 0;
                                                        $orderDate = $sale['order_date'] ?? '';
                                                        
                                                        // Format date
                                                        $formattedDate = '';
                                                        if (!empty($orderDate)) {
                                                            try {
                                                                $formattedDate = date('M d, Y', strtotime($orderDate));
                                                            } catch (Exception $e) {
                                                                $formattedDate = $orderDate;
                                                            }
                                                        }
                                                        
                                                        echo '<tr>';
                                                        echo '<td><strong>' . $customerName . '</strong></td>';
                                                        echo '<td>' . $productName . '</td>';
                                                        echo '<td class="text-center"><span class="badge badge-info">' . number_format($quantity) . '</span></td>';
                                                        echo '<td class="text-right"><span class="text-success">ugx ' . number_format($amountPaid, 2) . '</span></td>';
                                                        
                                                        // Color code balance
                                                        if ($balance > 0) {
                                                            echo '<td class="text-right"><span class="text-danger">ugx ' . number_format($balance, 2) . '</span></td>';
                                                        } else {
                                                            echo '<td class="text-right"><span class="text-success">ugx ' . number_format($balance, 2) . '</span></td>';
                                                        }
                                                        
                                                        echo '<td class="text-center"><small>' . $formattedDate . '</small></td>';
                                                        echo '</tr>';
                                                    }
                                                } else {
                                                    // Users see product information
                                                    $productQuery = "SELECT p.product_name, c.category_name, b.brand_name, 
                                                                     p.price, p.stock, p.status
                                                                     FROM products p
                                                                     LEFT JOIN categories c ON p.cat_id = c.cat_id
                                                                     LEFT JOIN brands b ON p.brand_id = b.brand_id
                                                                     WHERE p.status = '1'
                                                                     ORDER BY p.product_name ASC 
                                                                     LIMIT 50";
                                                    
                                                    $stmt = $pdo->prepare($productQuery);
                                                    $stmt->execute();
                                                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                    foreach($products as $product) {
                                                        $productName = htmlspecialchars($product['product_name']);
                                                        $categoryName = htmlspecialchars($product['category_name'] ?? 'N/A');
                                                        $brandName = htmlspecialchars($product['brand_name'] ?? 'N/A');
                                                        $price = $product['price'] ?? 0;
                                                        $stock = $product['stock'] ?? 0;
                                                        
                                                        // Determine stock status
                                                        $stockStatus = '';
                                                        $statusClass = '';
                                                        if ($stock > 30) {
                                                            $stockStatus = 'In Stock';
                                                            $statusClass = 'badge-success';
                                                        } elseif ($stock > 0) {
                                                            $stockStatus = 'Low Stock';
                                                            $statusClass = 'badge-warning';
                                                        } else {
                                                            $stockStatus = 'Out of Stock';
                                                            $statusClass = 'badge-danger';
                                                        }
                                                        
                                                        echo '<tr>';
                                                        echo '<td><strong>' . $productName . '</strong></td>';
                                                        echo '<td>' . $categoryName . '</td>';
                                                        echo '<td>' . $brandName . '</td>';
                                                        echo '<td class="text-right">ugx ' . number_format($price, 2) . '</td>';
                                                        echo '<td class="text-center">' . number_format($stock) . '</td>';
                                                        echo '<td class="text-center"><span class="badge ' . $statusClass . '">' . $stockStatus . '</span></td>';
                                                        echo '</tr>';
                                                    }
                                                }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                        </div>
                </div>
<script src="js/dashboard.js"></script>
<?php include("common/footer.php"); ?>