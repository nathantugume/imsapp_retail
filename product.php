<?php session_start();

if(!isset($_SESSION['LOGGEDIN'])){
    header("location:login.php?unauth=unauthorized access?");
}
?>
<?php include('common/top.php'); ?>
<link rel="stylesheet" href="css/custom.css">
<script src="js/product-enhanced.js"></script>
<body>
<?php include('common/navbar.php'); ?>
<div id="product-msg"></div>
<div class="row">
        <div class="col-lg-12">
                <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                        <h3 class="panel-title">Product List</h3>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align='right'>
                        <button type="button" name="add" id="add_button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#productModal">
                            <i class="fa fa-plus"></i> Add Product
                        </button>                 
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div id="product-table" class="col-sm-12 table-responsive">
                        <!-- Table is coming from index.php -->
                    </div>
                 </div>
            </div>
        </div>
        </div>

<!-- Add Product Modal -->
<div id="productModal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="products/add.php" onsubmit="return false" id="product_form">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Add New Product</h4>
                </div>
                <div class="modal-body">
                    <!-- Product Basic Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category_id"><i class="fa fa-tags"></i> Category <span class="text-danger">*</span></label>
                                <select name="category_id" id="category_id" class="form-control" required>
                                    <option value="">Select Category</option>
                                </select>
                                <small id="select_cat" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="brand_id"><i class="fa fa-certificate"></i> Brand <span class="text-danger">*</span></label>
                                <select name="brand_id" id="brand_id" class="form-control" required>
                                    <option value="">Select Brand</option>
                                </select>
                                <small id="select_brand" class="text-danger"></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="product_name"><i class="fa fa-cube"></i> Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="product_name" id="product_name" class="form-control" 
                               placeholder="Enter product name (e.g., iPhone 13 Pro)" required>
                        <small class="text-muted">Enter a descriptive name for the product</small>
                    </div>

                    <!-- Product Pricing and Stock -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="stock"><i class="fa fa-boxes"></i> Stock Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="stock" id="stock" class="form-control" 
                                       placeholder="0" min="0" step="1" required>
                                <small class="text-muted">Available quantity in stock</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="buying_price"><i class="fa fa-shopping-cart"></i> Buying Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">ugx</span>
                                    <input type="number" name="buying_price" id="buying_price" class="form-control" 
                                           placeholder="0.00" min="0" step="0.01" required>
                                </div>
                                <small class="text-muted">Cost price per unit</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="price"><i class="fa fa-tag"></i> Selling Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">ugx</span>
                                    <input type="number" name="price" id="price" class="form-control" 
                                           placeholder="0.00" min="0" step="0.01" required>
                                </div>
                                <small class="text-muted">Retail price per unit</small>
                            </div>
                        </div>
                    </div>

                    <!-- Profit Preview -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info" id="profit-preview" style="display: none;">
                                <i class="fa fa-calculator"></i> 
                                <strong>Profit Preview:</strong> 
                                <span id="profit-amount">ugx0.00</span> per unit 
                                (<span id="profit-margin">0%</span> margin)
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="expiry_date"><i class="fa fa-calendar"></i> Expiry Date</label>
                        <input type="date" name="expiry_date" id="expiry_date" class="form-control">
                        <small class="text-muted">Optional: Set expiry date for perishable products</small>
                    </div>

                    <div class="form-group">
                        <label for="description"><i class="fa fa-align-left"></i> Product Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4" 
                                  placeholder="Enter product description, features, specifications..."></textarea>
                        <small class="text-muted">Optional: Add detailed product information</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button type="submit" name="product" id="product-btn" class="btn btn-primary">
                        <i class="fa fa-save"></i> Add Product
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- View Single Product  -->
<div id="Product-View-Modal" class="modal fade">
        <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">View Product Details</h4>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group">
                            <li class="list-group-item"><b>Product ID:</b><span id="pid" class="pull-right"></span></li>
                            <!-- <li class="list-group-item"><b>Main Category ID:</b><span id="mid" class="pull-right"></span></li>
                            <li class="list-group-item"><b>Category ID:</b><span id="cid" class="pull-right"></span></li> -->
                            <li class="list-group-item"><b>Brand Name:</b><span id="bn" class="pull-right"></span></li>
                            <li class="list-group-item"><b>Product Name:</b><span id="pn" class="pull-right"></span></li>
                            <li class="list-group-item"><b>Price:</b><span id="pr" class="pull-right"></span></li>
                            <li class="list-group-item"><b>Stock Available:</b><span id="qty" class="pull-right"></span></li>
                            <li class="list-group-item"><b>Status:</b><span id="st" class="pull-right"></span></li>
                            <li class="list-group-item"><b>Create at:</b><span id="dt" class="pull-right"></span></li>
                        </ul>
                        <div class="pull-right">
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                        </div>
                    </div><br>
                    <!-- <div class="modal-footer">
                        
                    </div> -->
                 </div>
            </div>
        </div>

<!-- Update Product Modal -->
<div id="UpdateProductModal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="products/update.php" onsubmit="return false" id="update_form">
            <input type="hidden" name="upid" id="upid" value="">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-edit"></i> Update Product</h4>
                </div>
                <div class="modal-body">
                    <!-- Product Basic Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="update_category_id"><i class="fa fa-tags"></i> Category <span class="text-danger">*</span></label>
                                <select name="update_category_id" id="update_category_id" class="form-control" required>
                                    <option value="">Select Category</option>
                                </select>
                                <small class="text-muted">Choose the product category</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="update_brand_id"><i class="fa fa-certificate"></i> Brand <span class="text-danger">*</span></label>
                                <select name="update_brand_id" id="update_brand_id" class="form-control" required>
                                    <option value="">Select Brand</option>
                                </select>
                                <small class="text-muted">Choose the product brand</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="update_product_name"><i class="fa fa-cube"></i> Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="update_product_name" id="update_product_name" class="form-control" 
                               placeholder="Enter product name" required>
                        <small class="text-muted">Enter a descriptive name for the product</small>
                    </div>

                    <!-- Product Pricing and Stock -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="update_stock"><i class="fa fa-boxes"></i> Stock Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="update_stock" id="update_stock" class="form-control" 
                                       placeholder="0" min="0" step="1" required>
                                <small class="text-muted">Available quantity in stock</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="update_buying_price"><i class="fa fa-shopping-cart"></i> Buying Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">ugx</span>
                                    <input type="number" name="update_buying_price" id="update_buying_price" class="form-control" 
                                           placeholder="0.00" min="0" step="0.01" required>
                                </div>
                                <small class="text-muted">Cost price per unit</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="update_price"><i class="fa fa-tag"></i> Selling Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">ugx</span>
                                    <input type="number" name="update_price" id="update_price" class="form-control" 
                                           placeholder="0.00" min="0" step="0.01" required>
                                </div>
                                <small class="text-muted">Retail price per unit</small>
                            </div>
                        </div>
                    </div>

                    <!-- Profit Preview -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info" id="update-profit-preview" style="display: none;">
                                <i class="fa fa-calculator"></i> 
                                <strong>Profit Preview:</strong> 
                                <span id="update-profit-amount">ugx0.00</span> per unit 
                                (<span id="update-profit-margin">0%</span> margin)
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="update_expiry_date"><i class="fa fa-calendar"></i> Expiry Date</label>
                        <input type="date" name="update_expiry_date" id="update_expiry_date" class="form-control">
                        <small class="text-muted">Optional: Set expiry date for perishable products</small>
                    </div>

                    <div class="form-group">
                        <label for="update_desc"><i class="fa fa-align-left"></i> Product Description</label>
                        <textarea name="update_desc" id="update_desc" class="form-control" rows="3" 
                                  placeholder="Enter product description, features, specifications..."></textarea>
                        <small class="text-muted">Optional: Add detailed product information</small>
                    </div>

                    <div class="form-group">
                        <label for="update_status"><i class="fa fa-toggle-on"></i> Product Status</label>
                        <select name="update_status" id="update_status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <small class="text-muted">Set product availability status</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button type="submit" name="submit" id="update-product-btn" class="btn btn-info">
                        <i class="fa fa-save"></i> Update Product
                    </button>
                </div>
                <div class="modal-footer update_modal">
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Add Stock Modal -->
<div id="Stock-Modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="products/addStock.php" onsubmit="return false" id="stock_form">
            <input type="hidden" name="sid" id="sid" value="">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-plus-circle"></i> Add Stock</h4>
                </div>
                <div class="modal-body">
                    <!-- Product Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="product-name-stock"><i class="fa fa-cube text-primary"></i> Product Name</label>
                                <input type="text" name="product-name-stock" id="product-name-stock" class="form-control" readonly>
                                <small class="text-muted">Selected product for stock update</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inventory"><i class="fa fa-boxes text-success"></i> Current Stock</label>
                                <input type="text" name="inventory" id="inventory" class="form-control text-right" readonly>
                                <small class="text-muted">Available quantity in inventory</small>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Addition -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stock"><i class="fa fa-plus text-warning"></i> New Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="stock" id="stock" class="form-control text-right" 
                                       placeholder="0" min="0" step="1" required>
                                <small class="text-muted">Quantity to add to stock</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sub-stock"><i class="fa fa-chart-line text-success"></i> Stock Summary</label>
                                <div class="form-control text-right sub-stock" id="sub-stock" style="background-color: #d4edda; border: 1px solid #c3e6cb;">
                                    <strong>0</strong>
                                </div>
                                <small class="text-muted">Final stock level after adding new quantity</small>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Preview Alert -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-warning" id="stock-preview" style="display: none;">
                                <i class="fa fa-info-circle"></i> 
                                <strong>Stock Update Preview:</strong> 
                                Adding <span id="new-quantity">0</span> units to current stock of <span id="current-quantity">0</span> units
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button type="submit" name="submit" id="stock-btn" class="btn btn-warning">
                        <i class="fa fa-plus"></i> Add Stock
                    </button>
                </div>
                <div class="modal-footer stock-modal">
                </div>
            </div>
        </form>
    </div>
</div>
<script src="js/product.js"></script>
<?php include("common/footer.php"); ?>
