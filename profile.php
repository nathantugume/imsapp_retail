<?php  session_start(); 

if(!isset($_SESSION['LOGGEDIN'])){
    header('location:login.php');
}

include("common/top.php"); 

?>
<body>
<!-- ================= NAVBAR CONTENTS=============-->
  <?php include("common/navbar.php"); ?>
        <div class="container-fluid body-section">
            <center>
              <div class='alert alert-success w-100' style='color:green;'>
              
              <h5>|| Welcome&nbsp;<b><?php echo $_SESSION['LOGGEDIN']['name']; ?></b> ||</h5>
              </div>

            </center>
               
            <div class="row">
          <!-- =================PAGE CONTENTS================== -->
            <div class="col-md-12">
              <h1><i style="color: #013243;" class="fa fa-user ft-c text-white"></i> Profile <small style="font-size: 25px;"> Personal Details</small></h1><hr>
            <div class="row">
            <div class="col-sm-12">
             <span class="float-right">
              <a href="user.php" class="btn btn-success pull-right" >Dashboard</a><br><br>
              <a href="#" reset-id="<?php echo $_SESSION['LOGGEDIN']['id']; ?>" class="btn btn-success pull-right reset-btn" data-toggle="modal" data-target="#resetModal">Reset Password</a>
             </span>
              <center><img src='upload/<?php echo $_SESSION['img'];?>' class="img-thumbnail shadow-lg p-3 mb-5 bg-white rounded w-50" name="profile-image" id="profile-image"></center><br><br>
              <div>

                
                 <center><h3>Profile Details</h3></center><br>
               </div>
                 <table class="table table-bordered border border-warning">
                   <tr>
                     <th width="20%" class="bg-success text-white"><b>User ID:</b></th>
                     <td width="20%"><?php echo $_SESSION['LOGGEDIN']['id']; ?></td>
                     <td width="20%" class="bg-success text-white"><b>Date of Birth:</b></td>
                     <td width="20%"><?php echo date('D-M-Y'); ?></td>
                   </tr>
                   <tr>
                     <td width="20%" class="bg-success text-white"><b>First Name:</b></td>
                     <td width="20%"><?php echo  $_SESSION['LOGGEDIN']['name'];  ?> </td>
                     <td width="20%" class="bg-success text-white"><b>Role:</b></td>
                     <td width="20%"><?php echo  $_SESSION['LOGGEDIN']['role']; ?></td>
                   </tr>
                   <tr>
                     <td width="20%" class="bg-success text-white"><b>Verified Code:</b></td>
                     <td width="20%"><?php echo  $_SESSION['LOGGEDIN']['vcode']; ?></td>
                     <td width="20%" class="bg-success text-white"><b>E-mail:</b></td>
                     <td width="20%"><?php  echo $_SESSION['LOGGEDIN']['email'] ?></td>
                   </tr>
                 </table>
                <div class="row" >
                   <div class="col-sm-12 col-lg-12">
                     <table class="table table-bordered">
                   <tr>
                     <th width="20%" class="bg-success text-white"><b>Details:</b></th>
                   </tr>
                   <tr>
                     <td width="20%">  Dummy Text </p></td>
                   </tr>
                 </table>
              </div>
            </div>
<?php include('edit-user.php'); ?>
<?php include('reset.php'); ?>

<script src="js/user.js"></script>

<?php include("common/footer.php"); ?>

      
			
