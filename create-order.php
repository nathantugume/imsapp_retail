<?php 
session_start();

if(!isset($_SESSION['LOGGEDIN'])){
    header("location:login.php?unauth=unauthorized access?");
}
?>
<!DOCTYPE html>
<html>
<head>

 <script src="js/jquery.min.js"></script>
 <script src="js/bootstrap.js"></script>
 <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
 
 <!-- Select2 for searchable dropdowns -->
 <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
 <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 
 <style>
 /* Select2 customization for better appearance */
 .select2-container--default .select2-selection--single {
     height: 31px !important;
     padding: 2px 8px;
     border: 1px solid #ced4da;
     border-radius: 4px;
 }
 
 .select2-container--default .select2-selection--single .select2-selection__rendered {
     line-height: 26px !important;
     color: #495057;
 }
 
 .select2-container--default .select2-selection--single .select2-selection__arrow {
     height: 29px !important;
 }
 
 .select2-dropdown {
     border: 1px solid #ced4da;
     border-radius: 4px;
 }
 
 .select2-container {
     width: 100% !important;
 }
 
 .select2-results__option--highlighted {
     background-color: #667eea !important;
 }
 
 /* Make search box more prominent */
 .select2-search--dropdown .select2-search__field {
     border: 2px solid #667eea;
     padding: 8px;
     border-radius: 4px;
 }
 
 .select2-search--dropdown .select2-search__field:focus {
     outline: none;
     border-color: #764ba2;
 }
 </style>

</head>
<body>
      <div class="container"><br>
        <div>
          <button  class="float-right" ><span>Username :</span><?php echo $_SESSION['LOGGEDIN']['name']; ?></button>
          <h2 align="center" class="">St Jude Drugshop and Cosmetic Centre</h2>
        </div>
        <!-- <div id="order-error"></div> -->
          <nav class="navbar navbar-inverse">
            </nav>
             <div class="row">
              <div class="col-md-10 mx-auto">
              <div class="card" style="box-shadow:0 0 25px 0 lightgrey;">
                <div class="card-header">
                  <h4>New order</h4>
                </div>
                 <div class="card-body">
                  <form id="order-form-data" onsubmit="return false" action="#" method="POST">
                    <div class="form-group row ml-5">
                      <label align="right">Date :</label>
                      <div class="col-sm-2">
                        <input type="text" class="form-control form-control-sm" name="order_date" id="order_date" value='<?= date("d-m-Y"); ?>' readonly>
                      </div>
                       <label align="right">Customer Name :</label>
                      <div class="col-sm-6">
                        <input type="text" name="customer_name" class="form-control form-control-sm" id="customer_name" placeholder="Enter customer name" value="Walk-in Customer">
                        <small id="c_error"></small>
                      </div>
                    </div>
                    <div class="form-group row ml-5">
                      <label class="" align="center">Address:</label>
                      <div class="col-sm-9">
                        <input type="text" name="address" class="form-control form-control-sm" id="address" placeholder="Enter customer address" value="In-store">
                        <small id="a_error"></small>
                      </div>
                    </div>
                    <div class="card" style="box-shadow:0 0 15px 0 lightgrey;">
                      <div class="card-header"><h4>Make order list</h4></div>
                      <div class="card-body">
                        <table align="center" width="800px;">
                          <thead class="bg-secondary table-bordered text-white text-center">
                            <tr>
                              <th>#</th>
                              <th>Item Name</th>
                              <th>Total Quantity</th>
                              <th>Quantity</th>
                              <th>Price (Ugx.)</th>
                              <th colspan="2" width="12%">Total (Ugx.)</th>
                            </tr>
                          </thead>
                          <tbody id="invoice_item">
                            <!-- <tr>
                              <td class="form-control form-control-sm"><b class="number">1</b></td>
                              <td>
                                  <select name="p_id[]" class="form-control form-control-sm" required>
                                    <option>Washing Machine</option> 
                                  </select>
                              </td>
                              <td><input type="text" name="total_qty[]" class="form-control form-control-sm" readonly></td>
                              <td><input type="text" name="qty[]" class="form-control form-control-sm" required></td>
                              <td><input type="text" name="price[]" class="form-control form-control-sm" readonly></td>
                              <td class="form-control form-control-sm">Rs. 1540</td>
                            </tr> -->
                          </tbody>
                        </table>
                        <div class="float-right pt-2">
                          <button id="add" class="btn btn-success btn-sm">+ Add</button>
                          <button id="remove" class="btn btn-danger btn-sm">- Remove</button>
                        </div>
                      </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                      <label for="subtotal" class="col-sm-3 col-form-label" align="right">Sub Total :</label>
                      <div class="col-sm-6">
                        <input type="text" name="subtotal" id="subtotal" class="form-control form-control-sm" readonly>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="gst" class="col-sm-3 col-form-label" align="right">GST(18%) :</label>
                      <div class="col-sm-6">
                        <input type="text" name="gst" id="gst" class="form-control form-control-sm" readonly>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="discount" class="col-sm-3 col-form-label" align="right">Discount :</label>
                      <div class="col-sm-6">
                        <input type="text" name="discount" id="discount" class="form-control form-control-sm">
                        <small id="d_error"></small>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="net_total" class="col-sm-3 col-form-label" align="right">Net Total :</label>
                      <div class="col-sm-6">
                        <input type="text" name="net_total" id="net_total" class="form-control form-control-sm" readonly>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="paid" class="col-sm-3 col-form-label" align="right">Paid :</label>
                      <div class="col-sm-6">
                        <input type="text" name="paid" id="paid" class="form-control form-control-sm">
                        <small id="paid_error"></small>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="due" class="col-sm-3 col-form-label" align="right">Due :</label>
                      <div class="col-sm-6">
                        <input type="text" name="due" id="due" class="form-control form-control-sm" readonly>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="payment_method" class="col-sm-3 col-form-label" align="right">Payment Method :</label>
                      <div class="col-sm-6">
                        <select  name="payment_method" id="payment_method" class="form-control form-control-sm">
                          <option>Cash</option>
                          <option>Credit Card</option>
                          <option>Draft</option>
                          <option>Cheque</option>
                        </select>
                      </div>
                    </div>
                      <center>
                      <input type="submit" id="order_form" class="btn btn-info btn-sm" value="Save Order and Invoice">
                      <input type="submit" id="print-invoice" class="btn btn-success d-none" value="Print Invoice"></center>
                      <a href="index.php" class="btn btn-danger btn-sm pull-left">Cancel</a>

                      <a href="index.php" class="btn btn-secondary btn-sm float-right">Go To Dashboard</a>

                  </form>
                 </div>
                    <div class="modal-footer order-footer w-100">
                            
                    </div>
                </div>
                </div>
              </div>
<!-- Custom Script -->
<script src="js/order.js"></script>
<?php include("common/footer.php"); ?>