<!DOCTYPE html>
<html>
	<head>
		<?php 
		// Load branding for page title
		if (!class_exists('Branding')) {
			require_once __DIR__ . '/../config/branding.php';
		}
		// Allow pages to set custom title, otherwise use business name
		$pageTitle = isset($page_title) ? $page_title : 'IMS';
		$businessName = Branding::getBusinessName();
		?>
		<title><?php echo htmlspecialchars($pageTitle . ' - ' . $businessName); ?></title>
		<script src="./js/jquery.min.js"></script>
		<script src="./js/bootstrap.js"></script>
	<link rel="stylesheet" href="./css/bootstrap.min.css" />
	<link rel="stylesheet" href="./css/font-awesome.min.css">
	<link rel="stylesheet" href="./css/custom.css">
		<!-- DataTables CSS and JS -->
		<link rel="stylesheet" href="./css/dataTables.bootstrap4.min.css">
		<script src="./js/jquery.dataTables.min.js"></script>
		<script src="./js/dataTables.bootstrap4.min.js"></script>
		
		<!-- DataTables Export/Print Libraries -->
		<link rel="stylesheet" href="./css/buttons.bootstrap4.min.css">
		<script src="./js/dataTables.buttons.min.js"></script>
		<script src="./js/buttons.bootstrap4.min.js"></script>
		<script src="./js/jszip.min.js"></script>
		<script src="./js/pdfmake.min.js"></script>
		<script src="./js/vfs_fonts.js"></script>
		<script src="./js/buttons.html5.min.js"></script>
		<script src="./js/buttons.print.min.js"></script>
		<!-- SweetAlert2 CSS and JS -->
		<link rel="stylesheet" href="./css/sweetalert2.min.css">
		<script src="./js/sweetalert2.min.js"></script>
		<script src="./js/sweetalert-handler.js"></script>
		
	</head>