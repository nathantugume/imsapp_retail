<!DOCTYPE html>
<html>
	<head>
		<title>St Jude Drugshop and Cosmetic Centre</title>
		
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.js"></script>	
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<!-- SweetAlert2 CSS and JS -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<!-- Font Awesome for icons -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
	</head>
<body>
		<br>
		<div class="container"><br>
			<h2 align="center">St Jude Drugshop and Cosmetic Centre</h2>
			<br>
			<div class="panel panel-default" style="width:500px; margin-left: 320px;">
			    <div class="panel-heading">Login</div>
				<div class="panel-body">
					<form method="POST" id="loginForm" class="login-form">
						<div class="form-group">
							<label>User Email</label>
							<input type="text" name="email" id="email" class="form-control" required>
						</div>
						<div class="form-group">
							<label>Password</label>
							<input type="password" name="password" id="password" class="form-control" required>
						</div>
						<div class="form-group">
							<button type="submit" id="loginBtn" class="btn btn-info">
								<span id="loginText">Login</span>
								<span id="loginSpinner" style="display:none;">
									<i class="fa fa-spinner fa-spin"></i> Logging in...
								</span>
							</button>
						</div>
					</form>
					<div class="modal-footer">
          				
                    </div>
				</div>
			</div>
			<div class="msg text-center w-100"></div>
		</div>	

		<script>
		$(document).ready(function() {
			// Handle unauthorized access
			<?php if(isset($_GET['unauth'])): ?>
			Swal.fire({
				icon: 'warning',
				title: 'Access Denied',
				text: 'You are not authorized to access without login',
				confirmButtonColor: '#3085d6',
				confirmButtonText: 'OK'
			});
			<?php endif; ?>

			// Handle login form submission
			$('#loginForm').on('submit', function(e) {
				e.preventDefault();
				
				// Show loading state
				$('#loginText').hide();
				$('#loginSpinner').show();
				$('#loginBtn').prop('disabled', true);
				
				// Get form data
				var formData = {
					email: $('#email').val(),
					password: $('#password').val()
				};
				
				// Validate form
				if (!formData.email || !formData.password) {
					Swal.fire({
						icon: 'error',
						title: 'Validation Error',
						text: 'Please fill in all required fields',
						confirmButtonColor: '#d33'
					});
					resetLoginButton();
					return;
				}
				
				// Email validation
				var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
				if (!emailRegex.test(formData.email)) {
					Swal.fire({
						icon: 'error',
						title: 'Invalid Email',
						text: 'Please enter a valid email address',
						confirmButtonColor: '#d33'
					});
					resetLoginButton();
					return;
				}
				
				// Send AJAX request
				$.ajax({
					url: 'users/login.php',
					type: 'POST',
					data: formData,
					dataType: 'json',
					success: function(response) {
						if (response.success) {
							Swal.fire({
								icon: 'success',
								title: 'Login Successful!',
								text: 'Welcome back!',
								timer: 1500,
								showConfirmButton: false
							}).then(function() {
								window.location.href = 'index.php';
							});
						} else {
							Swal.fire({
								icon: 'error',
								title: 'Login Failed',
								text: response.message || 'An error occurred during login',
								confirmButtonColor: '#d33'
							});
						}
					},
					error: function(xhr, status, error) {
						console.error('AJAX Error:', error);
						Swal.fire({
							icon: 'error',
							title: 'Connection Error',
							text: 'Unable to connect to server. Please try again.',
							confirmButtonColor: '#d33'
						});
					},
					complete: function() {
						resetLoginButton();
					}
				});
			});
			
			function resetLoginButton() {
				$('#loginText').show();
				$('#loginSpinner').hide();
				$('#loginBtn').prop('disabled', false);
			}
		});
		</script>
</body>
</html>