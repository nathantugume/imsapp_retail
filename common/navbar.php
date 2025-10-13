<?php //session_start(); ?>
<br>
<div class="container">
  <h2 align="center" class="">Mini Price Hardware </h2>
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<a href="index.php" class="navbar-brand">Home</a>
			</div>
			<ul class="nav navbar-nav">
			<?php if($_SESSION['LOGGEDIN']['role']  =="Master") { ?>
				<li><a href="./user.php">User</a></li>
				<li><a href="./category.php">Category</a></li>
				<li><a href="./brand.php">Brand</a></li>
				<li><a href="./migrations.php">Database Migrations</a></li>
			<?php } ?>
		<!-- Both Master and User roles can access these -->
		<li><a href="./product.php">Product</a></li>
		<li><a href="./order.php">Order</a></li>
		<li><a href="./stock-reconciliation.php">Stock Reconciliation</a></li>
		<li><a href="./customer-payments.php">Customer Payments</a></li>
		<li><a href="./reports.php">Reports</a></li>
		</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-pill label-danger count"></span><?php echo $_SESSION['LOGGEDIN']['name']; ?></a>
					<ul class="dropdown-menu">
						<li><a href="./profile.php">Profile</a></li>
						<li><a href="./logout.php">Logout</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</nav>
