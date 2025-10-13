<?php
// Include the proper database connection
require_once("init/init.php");
require_once("config/branding.php");
// $dbcon is already initialized in init.php as a singleton
$pdo = $dbcon->connect();

// Initialize profit calculator
try {
    $profitCalc = new ProfitCalculator();
} catch (Exception $e) {
    $profitCalc = null;
}

// Session is already started in init.php
if(!isset($_SESSION['LOGGEDIN'])){
	header("location:login.php?unauth=unauthorized access?");
	exit();
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

// Get filter parameters with validation
$filterType = isset($_GET['filter']) ? $_GET['filter'] : 'daily';

// Validate and sanitize inputs
$allowedFilters = ['daily', 'monthly', 'yearly'];
if (!in_array($filterType, $allowedFilters)) {
    $filterType = 'daily';
}

$customDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$customMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$customYear = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Validate date formats
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $customDate)) {
    $customDate = date('Y-m-d');
}
if (!preg_match('/^\d{4}-\d{2}$/', $customMonth)) {
    $customMonth = date('Y-m');
}
if ($customYear < 2000 || $customYear > date('Y')) {
    $customYear = date('Y');
}

// Initialize report data
$reportData = [];
$reportTitle = '';
$reportPeriod = '';

// Generate reports based on filter type
switch($filterType) {
    case 'daily':
        $reportTitle = 'Daily Report';
        $reportPeriod = date('F d, Y', strtotime($customDate));
        
        // Convert YYYY-MM-DD to DD-MM-YYYY format for database matching
        $dateParts = explode('-', $customDate);
        $datePattern = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0] . '%';
        
        $sql = "SELECT o.invoice_no, o.customer_name, o.subtotal, o.gst, o.discount, 
                       o.net_total, o.paid, o.due, o.payment_method, o.order_date,
                       COUNT(i.id) as item_count
                FROM orders o
                LEFT JOIN invoices i ON o.invoice_no = i.invoice_no
                WHERE o.order_date LIKE :date_pattern
                GROUP BY o.invoice_no
                ORDER BY o.order_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':date_pattern', $datePattern);
        try {
            $stmt->execute();
            $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $reportData = [];
            error_log("Daily report query error: " . $e->getMessage());
        }
        
        // Get profit data
        if($profitCalc) {
            try {
                $profitData = $profitCalc->getDailyProfit($customDate);
            } catch (Exception $e) {
                $profitData = ["profit" => 0, "revenue" => 0, "cost" => 0];
                error_log("Daily profit calculation error: " . $e->getMessage());
            }
        } else {
            $profitData = ["profit" => 0, "revenue" => 0, "cost" => 0];
        }
        break;
        
    case 'monthly':
        $reportTitle = 'Monthly Report';
        $reportPeriod = date('F Y', strtotime($customMonth . '-01'));
        
        // Convert YYYY-MM to %-MM-YYYY format for database matching (DD-MM-YYYY)
        $monthParts = explode('-', $customMonth);
        $monthPattern = '%-' . $monthParts[1] . '-' . $monthParts[0];
        
        $sql = "SELECT o.invoice_no, o.customer_name, o.subtotal, o.gst, o.discount, 
                       o.net_total, o.paid, o.due, o.payment_method, o.order_date,
                       COUNT(i.id) as item_count
                FROM orders o
                LEFT JOIN invoices i ON o.invoice_no = i.invoice_no
                WHERE o.order_date LIKE :month_pattern
                GROUP BY o.invoice_no
                ORDER BY o.order_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':month_pattern', $monthPattern);
        try {
            $stmt->execute();
            $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $reportData = [];
            error_log("Monthly report query error: " . $e->getMessage());
        }
        
        // Get profit data
        if($profitCalc) {
            try {
                $yearMonth = explode('-', $customMonth);
                $profitData = $profitCalc->getMonthlyProfit($yearMonth[0], $yearMonth[1]);
            } catch (Exception $e) {
                $profitData = ["profit" => 0, "revenue" => 0, "cost" => 0];
                error_log("Monthly profit calculation error: " . $e->getMessage());
            }
        } else {
            $profitData = ["profit" => 0, "revenue" => 0, "cost" => 0];
        }
        break;
        
    case 'yearly':
        $reportTitle = 'Yearly Report';
        $reportPeriod = $customYear;
        
        // Use %-%-YYYY pattern for DD-MM-YYYY format
        $yearPattern = '%-%-' . $customYear;
        
        $sql = "SELECT o.invoice_no, o.customer_name, o.subtotal, o.gst, o.discount, 
                       o.net_total, o.paid, o.due, o.payment_method, o.order_date,
                       COUNT(i.id) as item_count
                FROM orders o
                LEFT JOIN invoices i ON o.invoice_no = i.invoice_no
                WHERE o.order_date LIKE :year_pattern
                GROUP BY o.invoice_no
                ORDER BY o.order_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':year_pattern', $yearPattern);
        try {
            $stmt->execute();
            $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $reportData = [];
            error_log("Yearly report query error: " . $e->getMessage());
        }
        
        // Calculate yearly profit
        try {
            $profitSql = "SELECT 
                            SUM(i.order_qty * i.price_per_item) as total_revenue,
                            SUM(i.order_qty * p.buying_price) as total_cost
                          FROM invoices i
                          INNER JOIN products p ON i.product_name = p.product_name
                          INNER JOIN orders o ON i.invoice_no = o.invoice_no
                          WHERE o.order_date LIKE :year_pattern
                          AND p.buying_price > 0";
            $profitStmt = $pdo->prepare($profitSql);
            $profitStmt->bindParam(':year_pattern', $yearPattern);
            $profitStmt->execute();
            $profitResult = $profitStmt->fetch(PDO::FETCH_ASSOC);
            
            $totalRevenue = $profitResult['total_revenue'] ?? 0;
            $totalCost = $profitResult['total_cost'] ?? 0;
            $profitData = [
                'revenue' => $totalRevenue,
                'cost' => $totalCost,
                'profit' => $totalRevenue - $totalCost
            ];
        } catch (PDOException $e) {
            $profitData = ["profit" => 0, "revenue" => 0, "cost" => 0];
            error_log("Yearly profit calculation error: " . $e->getMessage());
        }
        break;
}

// Calculate summary statistics
$totalSales = 0;
$totalPaid = 0;
$totalDue = 0;
$orderCount = count($reportData);

foreach($reportData as $row) {
    $totalSales += $row['net_total'];
    $totalPaid += $row['paid'];
    $totalDue += $row['due'];
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Reports - <?php echo htmlspecialchars(Branding::getBusinessName()); ?></title>
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
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
		
		<!-- SweetAlert2 CSS and JS -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<!-- Font Awesome for icons -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
		
		<script src="js/bootstrap.js"></script>
		<script src="js/sweetalert-handler.js"></script>
		<style>
		/* Table Styling */
		#reportTable {
			width: 100% !important;
			max-width: 100%;
			table-layout: auto;
		}
		
		#reportTable th, #reportTable td { 
			padding: 8px 12px; 
			white-space: normal;
			word-wrap: break-word;
			max-width: 200px;
		}
		
		#reportTable th {
			background-color: #f8f9fa;
			font-weight: 600;
			border-bottom: 2px solid #dee2e6;
		}
		
		#reportTable td {
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
			margin-bottom: 20px;
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
		
		/* Filter section */
		.filter-section {
			background: #f8f9fa;
			padding: 20px;
			border-radius: 5px;
			margin-bottom: 20px;
		}
		
		.filter-tabs {
			margin-bottom: 15px;
		}
		
		.filter-tabs .btn {
			margin-right: 10px;
			margin-bottom: 10px;
		}
		
		/* Summary cards */
		.summary-card {
			text-align: center;
			padding: 20px 15px;
			border-radius: 8px;
			margin-bottom: 20px;
			transition: transform 0.2s, box-shadow 0.2s;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
			min-height: 120px;
			display: flex;
			flex-direction: column;
			justify-content: center;
		}
		
		.summary-card:hover {
			transform: translateY(-3px);
			box-shadow: 0 4px 12px rgba(0,0,0,0.15);
		}
		
		.summary-card h3 {
			margin: 0 0 8px 0;
			font-size: 24px;
			font-weight: bold;
			word-wrap: break-word;
			overflow-wrap: break-word;
			line-height: 1.2;
		}
		
		.summary-card p {
			margin: 0;
			color: #555;
			font-size: 13px;
			font-weight: 500;
		}
		
		.summary-card i {
			margin-right: 5px;
		}
		
		.card-blue { 
			background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); 
			border-left: 5px solid #2196F3; 
		}
		.card-green { 
			background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); 
			border-left: 5px solid #4CAF50; 
		}
		.card-orange { 
			background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); 
			border-left: 5px solid #FF9800; 
		}
		.card-red { 
			background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); 
			border-left: 5px solid #f44336; 
		}
		.card-purple { 
			background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%); 
			border-left: 5px solid #9C27B0; 
		}
		
		/* Mobile responsiveness */
		@media (max-width: 768px) {
			#reportTable th, #reportTable td {
				padding: 6px 8px;
				font-size: 12px;
				max-width: 120px;
			}
			
			.panel-body {
				padding: 10px;
			}
			
			.filter-tabs .btn {
				display: block;
				width: 100%;
				margin-bottom: 10px;
			}
			
			.summary-card {
				margin-bottom: 15px;
			}
			
			.summary-card h3 {
				font-size: 20px;
			}
			
			.summary-card p {
				font-size: 12px;
			}
		}
		
		/* Tablet responsiveness */
		@media (min-width: 768px) and (max-width: 991px) {
			.summary-card h3 {
				font-size: 22px;
			}
		}
		
		/* Small screens - Cards stack vertically */
		@media (max-width: 575px) {
			.summary-card {
				min-height: 100px;
				margin-bottom: 12px;
			}
			
			.summary-card h3 {
				font-size: 18px;
			}
		}
		</style>
		<script>
		$(document).ready(function() {
			$('#reportTable').DataTable({
				"responsive": true,
				"pageLength": 25,
				"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				"order": [[0, "desc"]],
				"columnDefs": [
					{ "orderable": true, "targets": "_all" },
					{ "className": "text-center", "targets": [3, 4] },
					{ "className": "text-right", "targets": [1, 2, 5, 6, 7] }
				],
				"language": {
					"search": "Search orders:",
					"lengthMenu": "Show _MENU_ entries",
					"info": "Showing _START_ to _END_ of _TOTAL_ entries",
					"infoEmpty": "No entries found",
					"infoFiltered": "(filtered from _MAX_ total entries)",
					"zeroRecords": "No matching records found"
				},
				"dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
					   '<"row"<"col-sm-12"B>>' +
					   '<"row"<"col-sm-12"tr>>' +
					   '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
				"buttons": [
					{
						"extend": "excel",
						"text": '<i class="fas fa-file-excel"></i> Export Excel',
						"className": "btn btn-success btn-sm",
						"title": "<?php echo $reportTitle . ' - ' . $reportPeriod; ?>"
					},
					{
						"extend": "pdf",
						"text": '<i class="fas fa-file-pdf"></i> Export PDF',
						"className": "btn btn-danger btn-sm",
						"title": "<?php echo $reportTitle . ' - ' . $reportPeriod; ?>",
						"orientation": "landscape",
						"pageSize": "A4"
					},
					{
						"extend": "print",
						"text": '<i class="fas fa-print"></i> Print',
						"className": "btn btn-info btn-sm",
						"title": "<?php echo $reportTitle . ' - ' . $reportPeriod; ?>",
						"autoPrint": false
					},
					{
						"extend": "csv",
						"text": '<i class="fas fa-file-csv"></i> Export CSV',
						"className": "btn btn-warning btn-sm",
						"title": "<?php echo $reportTitle . ' - ' . $reportPeriod; ?>"
					}
				]
			});
		});
		</script>
		<script src="js/reports.js"></script>
	</head>
<body>
<?php include('common/navbar.php'); ?>

	<div class="row">
		<div class="col-md-12">
			<h2><i class="fas fa-chart-bar"></i> Detailed Reports</h2>
			<hr>
		</div>
		
		<!-- Filter Section -->
		<div class="col-md-12">
			<div class="filter-section">
				<h4><i class="fas fa-filter"></i> Report Filters</h4>
				<div class="filter-tabs">
					<a href="?filter=daily&date=<?php echo $customDate; ?>" class="btn <?php echo $filterType == 'daily' ? 'btn-primary' : 'btn-default'; ?>">
						<i class="fas fa-calendar-day"></i> Daily
					</a>
					<a href="?filter=monthly&month=<?php echo $customMonth; ?>" class="btn <?php echo $filterType == 'monthly' ? 'btn-primary' : 'btn-default'; ?>">
						<i class="fas fa-calendar-alt"></i> Monthly
					</a>
					<a href="?filter=yearly&year=<?php echo $customYear; ?>" class="btn <?php echo $filterType == 'yearly' ? 'btn-primary' : 'btn-default'; ?>">
						<i class="fas fa-calendar"></i> Yearly
					</a>
				</div>
				
				<!-- Date pickers based on filter type -->
				<div class="row">
					<div class="col-md-12">
						<form method="GET" action="" class="form-inline">
							<input type="hidden" name="filter" value="<?php echo $filterType; ?>">
							
							<?php if($filterType == 'daily'): ?>
								<div class="form-group">
									<label>Select Date: &nbsp;</label>
									<input type="date" name="date" class="form-control" value="<?php echo $customDate; ?>" max="<?php echo date('Y-m-d'); ?>">
								</div>
							<?php elseif($filterType == 'monthly'): ?>
								<div class="form-group">
									<label>Select Month: &nbsp;</label>
									<input type="month" name="month" class="form-control" value="<?php echo $customMonth; ?>" max="<?php echo date('Y-m'); ?>">
								</div>
							<?php elseif($filterType == 'yearly'): ?>
								<div class="form-group">
									<label>Select Year: &nbsp;</label>
									<select name="year" class="form-control">
										<?php 
										$currentYear = date('Y');
										for($y = $currentYear; $y >= $currentYear - 10; $y--) {
											$selected = ($y == $customYear) ? 'selected' : '';
											echo "<option value='$y' $selected>$y</option>";
										}
										?>
									</select>
								</div>
							<?php endif; ?>
							
							<button type="submit" class="btn btn-primary">
								<i class="fas fa-search"></i> Generate Report
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Summary Cards -->
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<h4><i class="fas fa-chart-pie"></i> Summary - <?php echo $reportPeriod; ?></h4>
		</div>
		
		<!-- Main Stats Cards - Always visible -->
		<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
			<div class="summary-card card-blue">
				<h3><?php echo $orderCount; ?></h3>
				<p><i class="fas fa-shopping-cart"></i> Total Orders</p>
			</div>
		</div>
		
		<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
			<div class="summary-card card-green">
				<h3>ugx <?php echo number_format($totalSales, 2); ?></h3>
				<p><i class="fas fa-dollar-sign"></i> Total Sales</p>
			</div>
		</div>
		
		<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
			<div class="summary-card card-orange">
				<h3>ugx <?php echo number_format($totalPaid, 2); ?></h3>
				<p><i class="fas fa-money-bill-wave"></i> Total Paid</p>
			</div>
		</div>
		
		<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
			<div class="summary-card card-red">
				<h3>ugx <?php echo number_format($totalDue, 2); ?></h3>
				<p><i class="fas fa-exclamation-circle"></i> Outstanding Balance</p>
			</div>
		</div>
		
		<!-- Profit Cards - Master Only -->
		<?php if($userRole == 'Master' && $profitData): ?>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div style="height: 10px;"></div>
		</div>
		
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<div class="summary-card card-purple">
				<h3>ugx <?php echo number_format($profitData['profit'] ?? 0, 2); ?></h3>
				<p><i class="fas fa-chart-line"></i> Total Profit</p>
			</div>
		</div>
		
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<div class="summary-card card-blue">
				<h3>ugx <?php echo number_format($profitData['revenue'] ?? 0, 2); ?></h3>
				<p><i class="fas fa-hand-holding-usd"></i> Total Revenue</p>
			</div>
		</div>
		
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<div class="summary-card card-orange">
				<h3>ugx <?php echo number_format($profitData['cost'] ?? 0, 2); ?></h3>
				<p><i class="fas fa-receipt"></i> Total Cost</p>
			</div>
		</div>
		<?php endif; ?>
		
		<!-- Detailed Report Table -->
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<strong><i class="fas fa-table"></i> <?php echo $reportTitle; ?> - <?php echo $reportPeriod; ?></strong>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table id="reportTable" class="table table-striped table-bordered table-hover">
							<thead class="thead-light">
								<tr>
									<th>Invoice No</th>
									<th>Customer</th>
									<th>Subtotal</th>
									<th>Items</th>
									<th>Discount</th>
									<th>Total</th>
									<th>Paid</th>
									<th>Balance</th>
									<th>Payment Method</th>
									<th>Date</th>
								</tr>
							</thead>
							<tbody>
							<?php 
								if(count($reportData) > 0) {
									foreach($reportData as $row) {
										$invoiceNo = htmlspecialchars($row['invoice_no']);
										$customerName = htmlspecialchars($row['customer_name'] ?? 'N/A');
										$subtotal = $row['subtotal'] ?? 0;
										$itemCount = $row['item_count'] ?? 0;
										$discount = $row['discount'] ?? 0;
										$netTotal = $row['net_total'] ?? 0;
										$paid = $row['paid'] ?? 0;
										$due = $row['due'] ?? 0;
										$paymentMethod = htmlspecialchars($row['payment_method'] ?? 'N/A');
										$orderDate = $row['order_date'] ?? '';
										
										// Format date
										$formattedDate = '';
										if (!empty($orderDate)) {
											try {
												$formattedDate = date('M d, Y H:i', strtotime($orderDate));
											} catch (Exception $e) {
												$formattedDate = $orderDate;
											}
										}
										
										// Determine balance color
										$balanceClass = $due > 0 ? 'text-danger' : 'text-success';
										
										echo '<tr>';
										echo '<td><strong>' . $invoiceNo . '</strong></td>';
										echo '<td>' . $customerName . '</td>';
										echo '<td class="text-right">ugx ' . number_format($subtotal, 2) . '</td>';
										echo '<td class="text-center"><span class="badge badge-info">' . $itemCount . '</span></td>';
										echo '<td class="text-center">ugx ' . number_format($discount, 2) . '</td>';
										echo '<td class="text-right"><strong>ugx ' . number_format($netTotal, 2) . '</strong></td>';
										echo '<td class="text-right"><span class="text-success">ugx ' . number_format($paid, 2) . '</span></td>';
										echo '<td class="text-right"><span class="' . $balanceClass . '">ugx ' . number_format($due, 2) . '</span></td>';
										echo '<td>' . $paymentMethod . '</td>';
										echo '<td>' . $formattedDate . '</td>';
										echo '</tr>';
									}
								} else {
									echo '<tr><td colspan="10" class="text-center">No data found for the selected period</td></tr>';
								}
							?>
							</tbody>
							<tfoot>
								<tr style="background-color: #f0f0f0; font-weight: bold;">
									<td colspan="2" class="text-right">TOTALS:</td>
									<td class="text-right">ugx <?php echo number_format($totalSales, 2); ?></td>
									<td class="text-center"><?php echo $orderCount; ?></td>
									<td></td>
									<td class="text-right">ugx <?php echo number_format($totalSales, 2); ?></td>
									<td class="text-right">ugx <?php echo number_format($totalPaid, 2); ?></td>
									<td class="text-right">ugx <?php echo number_format($totalDue, 2); ?></td>
									<td colspan="2"></td>
								</tr>
							</tfoot>
						</table>
					</div>		
				</div>
			</div>
		</div>
	</div>

<?php include("common/footer.php"); ?>
</body>
</html>

