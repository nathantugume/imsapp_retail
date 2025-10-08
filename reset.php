
<div id="resetModal" class="modal fade">
    <div class="modal-dialog">
      <form method="POST" action="users/reset.php" onsubmit="return false" id="form" class="reset-form">
        <input type="hidden" name="reset_id" id="reset_id">
        <div class="modal-content">
            <div class="modal-header bg-info">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Reset Password</h4>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label>Old Password</label>
                <input type="password" name="oldpassword" id="oldpassword" placeholder="Old Password" class="form-control">
                <small id="op"></small>
              </div>
              <div class="form-group">
                <label>New Password</label>
                <input type="password" name="newpassword" id="newpassword" placeholder="New Password" class="form-control">
              </div>
              <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="cpassword" id="cpassword" placeholder="Confirm News Password" class="form-control">
              </div>
              <div class="form-group">
                <input type="submit" name="reset" id="reset-btn" class="btn btn-info btn-sm" value="Reset" />
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
              </div>
              <div class="modal-footer text-center">    
            </div>
        </div>
      </div>
    </form>
  </div>
</div>


