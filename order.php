<?php 
session_start();

if(!isset($_SESSION['LOGGEDIN'])){
    header("location:login.php?unauth=unauthorized access?");
}
?>
<?php include('common/top.php'); ?>
<body>
<?php include('common/navbar.php'); ?>
    <div id="order-msg"></div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            <h3 class="panel-title"><i class="fa fa-shopping-cart"></i> Order List</h3>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
                            <a href="create-order.php" class="btn btn-success btn-sm">
                            	<i class="fa fa-plus"></i> Create Order
                            </a>  
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div id="order-table" class="col-sm-12 table-responsive">
                        <!-- Table is comming from index.php -->
                   <!--  </div>
                </div> -->
                    </div>
                </div>
            </div>
        </div>
    <!-- View Single Product  -->
    <div id="Order-View-Modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">View Order Details</h4>
                </div>
                    <div class="modal-body">
                        <ul class="list-group">
                            <li class="list-group-item"><b>Invoice No. :</b><span id="invcno" class="pull-right"></span></li>
                            <!-- <li class="list-group-item"><b>Invoice ID :</b><span id="inid" class="pull-right"></span></li> -->
                            <li class="list-group-item"><b>Customer :</b><span id="cn" class="pull-right"></span></li>
                            <li class="list-group-item"><b>Address :</b><span id="add" class="pull-right"></span></li>
                            <!-- <li class="list-group-item"><b>Products :</b><span id="prod" class="pull-right"></span></li> -->
                            <li class="list-group-item"><b>Subtotal :</b><span id="sbt" class="pull-right"></span></li>
                            <li class="list-group-item"><b>GST :</b><span id="gst" class="pull-right"></span></li>
                            <li class="list-group-item"><b>Discount :</b><span id="dis" class="pull-right"></span></li>
                            <li class="list-group-item"><b>Net Total :</b><span id="ntt" class="pull-right"></span></li>
                            <li class="list-group-item"><b>Paid :</b><span id="pd" class="pull-right"></span></li>
                            <li class="list-group-item"><b>Due :</b><span id="due" class="pull-right"></span></li>
                            <li class="list-group-item"><b>Payment Mode :</b><span id="pm" class="pull-right"></span></li>
                            <li class="list-group-item"><b>Order Date :</b><span id="od" class="pull-right"></span></li>
                            <li class="list-group-item text-center"><span><b>Products List</b></span></li>
                            <table class="table table-bordered mylist">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Product</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Price</th>
                                    </tr>
                                </thead>
                                <tbody id="order-products-tbody">
                                    

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-right"><b>Net Total = &nbsp;&nbsp; </b><span class="pull-right total"></span></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </ul>
                        <div class="pull-right">
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                        </div>
                    </div><br>
                    <div class="modal-footer">
                        
                    </div>
            </div>
        </div>
    </div>

<script src="js/order.js"></script>
<?php include('common/footer.php'); ?>