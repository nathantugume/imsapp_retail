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
                    	<div class="row">
                        	<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            	<h3 class="panel-title">User List</h3>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
                            	<button type="button" name="add" id="add_button" data-toggle="modal" data-target="#userModal" class="btn btn-success btn-sm">Add</button>
                        	</div>
                        </div>
                      <div class="clear:both"></div>
                   	</div>
                   	<div class="panel-body">
                   		<div  class="row">
                        <div id="table-data" class="col-sm-12 table-responsive">
                   			 <!-- <table id="user_data" class="table table-bordered table-striped">
                   				<thead>
            									<tr>
            										<th>ID</th>
            										<th>Email</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Country</th>
            										<th>Verification Code</th>
            										<th class="text-center">Status</th>
            										<th width="15%" class="text-center" colspan="2">Action</th>
            									</tr>
            							</thead>
                          <tbody id="user-data">

                        </tbody>
                   			</table>
                        </div>
                        <div  id="pagination">
                          <ul class="pagination">
                            <li class="page-item disabled">
                              <a class="page-link" href="#" tabindex="-1">Previous</a>
                            </li>
                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                            <li class="page-item active">
                              <a class="page-link" href="#">2 <span class="sr-only">(current)</span></a>
                            </li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                              <a class="page-link" href="#">Next</a>
                            </li>
                          </ul> 
                        </div> -->
                   		</div>
                   	</div>
               	</div>
        </div>
    </div>
        <div id="userModal" class="modal fade">
        	<div class="modal-dialog">
        		<form method="post" action="users/register.php" id="form" class="register-form">
        			<div class="modal-content">
          			<div class="modal-header bg-info">
          				<button type="button" class="close" data-dismiss="modal">&times;</button>
  						    <h4 class="modal-title"><i class="fa fa-plus"></i> Add User</h4>
          			</div>
          			<div class="modal-body">
          				<div class="form-group">
    							  <label>Full Name</label>
    							  <input type="text" name="name" id="name" class="form-control">
      						</div>
      						<div class="form-group">
      							<label>Email Address</label>
      							<input type="text" name="email" id="email" class="form-control">
                    <span id="em"></span>
      						</div> 
      						<div class="form-group">
      							<label>Password</label>
      							<input type="password" name="password" id="password" class="form-control">
      						</div>
                  <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="cpassword" id="cpassword" class="form-control">
                  </div>
                   <div class="form-group">
                    <label>Country</label>
                    <input type="text" name="country" id="country" class="form-control">
                  </div>
                  <div class="form-group">
                    <div class="pull-right">
                      <input type="submit" id="user-btn" class="btn btn-info btn-sm" value="Submit" />
                      <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                    </div>
                  </div>
          			</div><br>
                <div class="modal-footer text-center">
                  
                </div>
        		  </div>
      		  </form>
      	</div>
      </div>
<?php include('edit-user.php'); ?>
  <script src="js/user.js"></script>
<?php include("common/footer.php"); ?>
