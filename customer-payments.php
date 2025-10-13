<?php 
session_start();

if(!isset($_SESSION['LOGGEDIN'])){
    header("location:login.php?unauth=unauthorized access?");
}

$page_title = "Customer Payments";
?>
<?php include('common/top.php'); ?>
<body>
<?php include('common/navbar.php'); ?>
<div id="msg" class="text-center w-100"></div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                        <h3 class="panel-title">
                            <i class="fas fa-credit-card"></i> Customer Payments
                        </h3>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
                        <button type="button" name="add" id="add_payment" data-toggle="modal" data-target="#paymentModal" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Record Payment
                        </button>
                    </div>
                </div>
                <div class="clear:both"></div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>Total Outstanding Balance:</strong> 
                            <span id="total-outstanding" class="text-danger">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div id="table-data" class="col-sm-12 table-responsive">
                        <!-- Table will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div id="paymentModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" action="payments/record-payment.php" id="payment-form">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fas fa-credit-card"></i> Record Customer Payment</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Order</label>
                        <select name="order_id" id="order_id" class="form-control" required>
                            <option value="">Select Order</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount to Pay</label>
                        <input type="number" name="amount_paid" id="amount_paid" class="form-control" step="0.01" min="0.01" required>
                        <small class="text-muted">Maximum: <span id="max-amount">ugx 0</span></small>
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-control" required>
                            <option value="">Select Payment Method</option>
                            <option value="Cash">Cash</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes (Optional)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Enter any notes about this payment..."></textarea>
                    </div>
                    <div class="form-group">
                        <div class="pull-right">
                            <input type="submit" id="payment-btn" class="btn btn-info btn-sm" value="Record Payment" />
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <!-- Loading message will appear here -->
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Payment History Modal -->
<div id="paymentHistoryModal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fas fa-history"></i> Payment History</h4>
            </div>
            <div class="modal-body">
                <div id="payment-history-content">
                    <!-- Payment history will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="js/customer-payments.js"></script>
<?php include("common/footer.php"); ?>

