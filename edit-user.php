
  <div id="editModal" class="modal fade">
    	<div class="modal-dialog">
    		<form method="POST" action="users/update.php" id="edit-form" class="edit-form">
          <input type="hidden" name="edit_id" id="edit_id">
    			<div class="modal-content">
      			<div class="modal-header bg-info">
      				<button type="button" class="close" data-dismiss="modal">&times;</button>
					    <h4 class="modal-title">Edit User Profile</h4>
      			</div>
      			<div class="modal-body">
      				<div class="form-group">
							  <label>Full Name</label>
							  <input type="text" name="edit-name" id="edit-name" class="form-control">
  						</div>
  						<div class="form-group">
  							<label>Email Address</label>
  							<input type="text" name="edit-email" id="edit-email" class="form-control">
  						</div>
              <div class="form-group">
                <label>Country</label>
                <input type="text" name="edit-country" id="edit-country" class="form-control">
              </div>
              <div class="form-group">
                <!-- <label>Select Status</label>
                <select name="status" id="status" class="form-control status">
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
                </select> -->
              </div>
              <div class="form-group">
                <div class="pull-right">
                    <input type="submit" name="edit" id="edit-btn" class="btn btn-info btn-sm" value="Update" />
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
<!-- <script src="js/user.js"></script> -->


