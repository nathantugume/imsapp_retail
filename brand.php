<?php 
session_start();

if(!isset($_SESSION['LOGGEDIN'])){
    header("location:login.php?unauth=unauthorized access?");
}
include('common/top.php'); ?>
<body>
<?php include('common/navbar.php'); ?>

	<div id="brand-msg" class="text-center w-100"></div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
                <div class="panel-heading">
                	<div class="row">
                		<div class="col-md-10">
                			<h3 class="panel-title">Brand List</h3>
                		</div>
                		<div class="col-md-2" align="right">
                			<button type="button" name="add" id="add_button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#brandModal">+Add Brand</button>
                		</div>
                	</div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div id="brand-table" class="col-sm-12 table-responsive">
                            <!-- table is being display through ajax request -->
                            <!-- <table id="brand_data" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Category</th>
                                        <th>Brand Name</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <td>dssdcd</td>
                                    <td>zxcdsc</td>
                                    <td>cscsc</td>
                                    <td>xdcdc</td>
                                    <td>ccv</td>
                                    <td>ccv</td>
                                </tbody>
                            </table> -->
                  <!--   </div>
                </div> -->
            </div>
        </div>
    </div>
<!-- Add Brand -->
    <div id="brandModal" class="modal fade">
    	<div class="modal-dialog">
    		<form method="post" action="brands/add.php" onsubmit="return false" id="brand_form">
    			<div class="modal-content">
    				<div class="modal-header bg-info">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Add Brand</h4>
    				</div>
    				<div class="modal-body">
    					<div class="form-group">
							<label>Enter Brand Name</label>
							<input type="text" name="brand_name" id="brand_name" class="form-control">
                            <small id="b-error"></small>
						</div>
                        <div class="form-group">
                            <label for="status">Select Status</label>
                            <select id="status" name="status" class="form-control select-status">
                                <option value="">Select status</option>
                                <option value="1">Active</option>
                                <option value="2">Inactive</option>
                            </select>
                            <small id="e-status" class="st"></small>
                        </div>
                        <div class="form-group">
                            <div class="pull-right">
                                <input type="submit" name="brand" id="brand-btn" class="btn btn-info btn-sm" value="Add">
                                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                            </div>
                        </div>
    				</div><br>
    				<div class="modal-footer">
    					
    				</div>
    			</div>
    		</form>
    	</div>
    </div>
    <div id="UpdateBrandModal" class="modal fade">
        <div class="modal-dialog">
            <form method="post" action="brands/update.php" onsubmit="return false" id="update_brand_form">
                <input type="hidden" name="bid" id="bid" >
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-plus"></i> Update Brand</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Enter Brand Name</label>
                            <input type="text" name="update_brand_name"  id="update_brand_name" class="form-control">
                            <small id="br-error"></small>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="update-status" name="update-status" class="form-control">
                                
                            </select>
                            <small id="e-status" class="ust"></small>
                        </div>
                        <div class="form-group">
                            <div class="pull-right">
                                <input type="submit" name="brand" id="brand-btn" class="btn btn-info btn-sm" value="Update">
                                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div><br>
                    <div class="modal-footer">
                        
                    </div>
                </div>
            </form>
        </div>
    </div>
<!-- Update Brand -->
<!-- Custom Script -->
<script src="js/brand.js"></script>
<?php include("common/footer.php"); ?>