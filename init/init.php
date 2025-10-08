<?php

session_start();

require_once __DIR__.'/../functions/function.php';
require_once __DIR__.'/dbcon.php';
require_once __DIR__.'/../exists/exists.php';
require_once __DIR__.'/../users/User.php';
require_once __DIR__.'/../brands/Brand.php';
require_once __DIR__.'/../category/Category.php';
require_once __DIR__.'/../orders/Order.php';
require_once __DIR__.'/../products/Product.php';
require_once __DIR__.'/../dashboard/Dashboard.php';
require_once __DIR__.'/../stock/StockReconciliation.php';
require_once __DIR__.'/../payments/CustomerPayment.php';
// require_once __DIR__.'/../fpdf/fpdf.php';
require_once __DIR__.'/../functions/profit_calculator.php';

// Initialize global database connection
$dbcon = getDB();
