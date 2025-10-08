<?php 
session_start();

if(!isset($_SESSION['LOGGEDIN'])){
    header("location:login.php?unauth=unauthorized access?");
}
?>
<?php include('common/top.php'); ?>
<body>
<?php include('common/navbar.php'); ?>
	<div id="msg" class="text-center w-100"></div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
                <div class="panel-heading">
                    <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                        <div class="row">
                            <h3 class="panel-title">Category List</h3>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                        <div class="row" align="right">
                             <button type="button" name="add" id="add_button" data-toggle="modal" data-target="#categoryModal" class="btn btn-success btn-sm">+Add Category</button>   		
                        </div>
                    </div>
                  <div style="clear:both"></div>
                </div>
                <div class="panel-body">
                    <div class="row">
                    	<div id="category-table" class="col-sm-12 table-responsive">
                    		<!-- table is being display through ajax request -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div id="categoryModal" class="modal fade">
    	<div class="modal-dialog">
    		<form method="POST" action="category/add.php" onsubmit="return false" id="category-form">
    			<div class="modal-content">
    				<div class="modal-header bg-info">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Add Category</h4>
    				</div>
    				<div class="modal-body">
                        <div class="form-group">
    					   <label>Select  Category Name</label>
                            <select id="maincategory"  name="maincategory" class="form-control select-main">
                                <!-- Data coming from ajax request -->
                            </select>
                            <small id="maincat"></small>
                        </div>
                        <div class="form-group">
                            <label>Enter Main Category / Subcategory </label>
                            <input type="text" name="category_name" id="category_name" class="form-control subcat" placeholder="Please Enter Category or Subcategory" pattern="[A-Za-z].{3,}" title="Only alphabet characters allowed, atleast 3 characters">
                            <small id="cat_error" class="cat_error"></small>
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
                            <button type="submit" name="update" id="category-btn" class="btn btn-info btn-sm">Add</button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="window.location.reload();" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div><br>
                    <div class="modal-footer" id="ft-modal">
                        
                    </div>
    			 </div>
    		</div>
    	</form>
    </div>
    <div id="Cat-View-Modal" class="modal fade">
        <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-plus"></i>View Category</h4>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group">
                            <li class="list-group-item"><b>Main Category ID:</b><span id="main-id" class="pull-right"></span></li>
                          <li class="list-group-item maincat"><b>Main Category:</b><span id="main" class="pull-right"></span></li>
                          
                          <li class="list-group-item"><b>Category ID:</b><span id="sub-id" class="pull-right"></span></li>
                          <li class="list-group-item subcat"><b>Subcategory Name:</b><span id="sub" class="pull-right"></span></li>
                          <li class="list-group-item"><b>Status:</b><span id="st" class="pull-right"></span></li>
                          <li class="list-group-item"><b>Create at:</b><span id="dt" class="pull-right"></span></li>
                        </ul>
                        <div class="pull-right">
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                        </div><br>
                    </div>
                 </div>
            </div>
        </div>
        <!-- Update category Modal -->
    <div id="UpdatecategoryModal" class="modal fade">
        <div class="modal-dialog">
            <form method="POST" action="category/update.php" onsubmit="return false" id="update-category-form">
                <input type="hidden" name="cat_id" id="cat_id" class="cat_id" value="">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-plus"></i> Update Category</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                           <label>Select  Category Name</label>
                            <select id="update-main-category"  name="update-main-category" class="form-control select-main">
                                <!-- Data coming from ajax request -->
                            </select>
                            <small id="maincat"></small>
                        </div>
                        <div class="form-group">
                            <label>Enter Main Category / Subcategory </label>
                            <input type="text" name="update-category-name" id="update-category-name" class="form-control subcat" placeholder="Please Enter Category or Subcategory" pattern="[A-Za-z].{3,}" title="Only alphabet characters allowed, atleast 3 characters">
                            <small id="up_error"></small>
                        </div>
                        <div class="form-group">
                            <label for="status">Select Status</label>
                            <select id="update-status" name="update-status" class="form-control select-status">
                               
                            </select>
                            <small id="up-status" class="st"></small>
                        </div>
                        <div class="form-group">
                            <div class="pull-right">
                            <button type="submit" name="submit" id="category-btn" class="btn btn-info btn-sm">Update</button>
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div><br>
                    <div class="modal-footer">
                        
                    </div>
                 </div>
                </div>
            </form>
        </div>
<script src="js/category.js"></script>
<!-- Custom Script -->
<?php include("common/footer.php"); ?>


				