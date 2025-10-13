<?php 
//session_start(); 
// Get current page for active tab highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<br>
<div class="container">
  <h2 align="center" class="">Mini Price Hardware </h2>
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<a href="index.php" class="navbar-brand <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
			</div>
			<ul class="nav navbar-nav">
		<?php if($_SESSION['LOGGEDIN']['role']  =="Master") { ?>
			<li class="<?php echo ($current_page == 'user.php') ? 'active' : ''; ?>"><a href="./user.php">User</a></li>
			<li class="<?php echo ($current_page == 'category.php') ? 'active' : ''; ?>"><a href="./category.php">Category</a></li>
			<li class="<?php echo ($current_page == 'brand.php') ? 'active' : ''; ?>"><a href="./brand.php">Brand</a></li>
		<?php } ?>
		<!-- Both Master and User roles can access these -->
		<li class="<?php echo ($current_page == 'product.php') ? 'active' : ''; ?>"><a href="./product.php">Product</a></li>
		<li class="<?php echo ($current_page == 'order.php') ? 'active' : ''; ?>"><a href="./order.php">Order</a></li>
		<li class="<?php echo ($current_page == 'stock-reconciliation.php') ? 'active' : ''; ?>"><a href="./stock-reconciliation.php">Stock Reconciliation</a></li>
		<li class="<?php echo ($current_page == 'customer-payments.php') ? 'active' : ''; ?>"><a href="./customer-payments.php">Customer Payments</a></li>
		<li class="<?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>"><a href="./reports.php">Reports</a></li>
		</ul>
			<ul class="nav navbar-nav navbar-right">
			<!-- System Update Button (Only for Master) -->
			<?php if($_SESSION['LOGGEDIN']['role'] == "Master") { ?>
			<li class="<?php echo ($current_page == 'system-updates.php') ? 'active' : ''; ?>">
				<a href="./system-updates.php" title="Check for system updates">
					<i class="fa fa-cloud-download"></i> Updates
					<span id="update-badge" class="badge" style="display:none; background-color: #f44336;">NEW</span>
				</a>
			</li>
			<?php } ?>
				<li class="dropdown <?php echo (in_array($current_page, ['profile.php', 'logout.php'])) ? 'active' : ''; ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-pill label-danger count"></span><?php echo $_SESSION['LOGGEDIN']['name']; ?></a>
					<ul class="dropdown-menu">
						<li class="<?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>"><a href="./profile.php"><i class="fa fa-user"></i> Profile</a></li>
						<li><a href="javascript:void(0)" onclick="createDesktopShortcut()"><i class="fa fa-desktop"></i> Create Desktop Shortcut</a></li>
						<li class="divider"></li>
						<li><a href="./logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</nav>
