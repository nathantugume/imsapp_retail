<?php 
session_start();

if(!isset($_SESSION['LOGGEDIN'])){
    header("location:login.php?unauth=unauthorized access?");
}

// Check if user has permission (Master users can approve, all users can create)
$userRole = $_SESSION['LOGGEDIN']['role'];

$page_title = "Stock Reconciliation";
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
                            <i class="fas fa-clipboard-check"></i> Stock Reconciliation
                        </h3>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
                        <button type="button" name="add" id="add_reconciliation" data-toggle="modal" data-target="#reconciliationModal" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> New Reconciliation
                        </button>
                    </div>
                </div>
                <div class="clear:both"></div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div id="table-data" class="col-sm-12 table-responsive">
                        <!-- Table will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Reconciliation Modal -->
<div id="reconciliationModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" action="stock/add-reconciliation.php" id="reconciliation-form">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fas fa-clipboard-check"></i> New Stock Reconciliation</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Product</label>
                        <select name="product_id" id="product_id" class="form-control" required>
                            <option value="">Select Product</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Physical Count</label>
                        <input type="number" name="physical_count" id="physical_count" class="form-control" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Notes (Optional)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Enter any notes about this reconciliation..."></textarea>
                    </div>
                    <div class="form-group">
                        <div class="pull-right">
                            <input type="submit" id="reconciliation-btn" class="btn btn-info btn-sm" value="Submit" />
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

<!-- Approve/Reject Modal -->
<div id="approvalModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fas fa-gavel"></i> Approve/Reject Reconciliation</h4>
            </div>
            <div class="modal-body">
                <div id="reconciliation-details">
                    <!-- Details will be loaded here -->
                </div>
                <div class="form-group">
                    <label>Action</label>
                    <select id="approval_action" class="form-control">
                        <option value="">Select Action</option>
                        <option value="approved">Approve</option>
                        <option value="rejected">Reject</option>
                    </select>
                </div>
                <div class="form-group">
                    <div class="pull-right">
                        <button type="button" id="submit-approval" class="btn btn-warning btn-sm">Submit</button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-center">
                <!-- Loading message will appear here -->
            </div>
        </div>
    </div>
</div>

<script src="js/stock-reconciliation.js"></script>
<?php include("common/footer.php"); ?>







