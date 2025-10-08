<!DOCTYPE html>
<html lang="<?php echo defined('Branding') ? Branding::get('language', 'en') : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <?php
    // Include branding if not already loaded
    if (!class_exists('Branding')) {
        require_once('config/branding.php');
    }
    ?>
    
    <title><?php echo Branding::getPageTitle('Login'); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo Branding::get('favicon_path'); ?>">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: <?php echo Branding::get('color_primary'); ?>;
            --secondary: <?php echo Branding::get('color_secondary'); ?>;
            --success: <?php echo Branding::get('color_success'); ?>;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 1000px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            display: flex;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 15s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .login-branding {
            position: relative;
            z-index: 1;
        }
        
        .login-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 30px;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .login-branding h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            line-height: 1.2;
        }
        
        .login-branding p.tagline {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.95;
        }
        
        .login-features {
            margin-top: 40px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            font-size: 1.05rem;
            opacity: 0.9;
        }
        
        .feature-item i {
            margin-right: 15px;
            color: var(--success);
            font-size: 1.3rem;
        }
        
        .login-right {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-form-container h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .login-subtitle {
            color: #6c757d;
            margin-bottom: 40px;
            font-size: 1rem;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group-prepend {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            background: #f8f9fa;
            border-radius: 10px 0 0 10px;
            z-index: 1;
        }
        
        .form-control {
            width: 100%;
            padding: 14px 14px 14px 60px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 2;
            transition: color 0.2s;
        }
        
        .toggle-password:hover {
            color: var(--primary);
        }
        
        .custom-checkbox {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }
        
        .custom-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            cursor: pointer;
        }
        
        .custom-checkbox label {
            margin: 0;
            cursor: pointer;
            font-weight: 400;
        }
        
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }
        
        .forgot-password a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        
        .forgot-password a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }
        
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 8px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 450px;
            }
            
            .login-left {
                padding: 40px 30px;
                text-align: center;
            }
            
            .login-left h1 {
                font-size: 2rem;
            }
            
            .login-features {
                display: none;
            }
            
            .login-right {
                padding: 40px 30px;
            }
            
            .login-form-container h2 {
                font-size: 1.75rem;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .login-right {
                padding: 30px 20px;
            }
            
            .login-left {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Branding -->
        <div class="login-left">
            <div class="login-branding">
                <?php if (file_exists(Branding::getLogo(true))): ?>
                    <img src="<?php echo Branding::getLogo(true); ?>" alt="Logo" class="login-logo">
                <?php else: ?>
                    <div class="login-logo">
                        <i class="fas fa-hospital fa-4x"></i>
                    </div>
                <?php endif; ?>
                
                <h1><?php echo htmlspecialchars(Branding::getBusinessName()); ?></h1>
                <p class="tagline"><?php echo htmlspecialchars(Branding::get('business_tagline')); ?></p>
                
                <div class="login-features">
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Real-time Inventory Tracking</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Sales & Revenue Analytics</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Customer Management</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Secure & Reliable</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="login-right">
            <div class="login-form-container">
                <h2>Welcome Back!</h2>
                <p class="login-subtitle">Please login to your account to continue</p>
                
                <form method="POST" id="loginForm">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <input type="email" name="email" id="email" class="form-control" 
                                   placeholder="Enter your email" required autofocus>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input type="password" name="password" id="password" class="form-control" 
                                   placeholder="Enter your password" required>
                            <span class="toggle-password" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>
                    
                    <div class="custom-checkbox">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    
                    <button type="submit" class="btn-login" id="loginBtn">
                        <span id="loginText">Login</span>
                        <span id="loginSpinner" style="display:none;">
                            <span class="spinner"></span>Logging in...
                        </span>
                    </button>
                    
                    <div class="forgot-password">
                        <a href="reset.php">Forgot password?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        $(document).ready(function() {
            // Handle unauthorized access
            <?php if(isset($_GET['unauth'])): ?>
            Swal.fire({
                icon: 'warning',
                title: 'Access Denied',
                text: 'You must login to access this system',
                confirmButtonColor: '<?php echo Branding::get('color_primary'); ?>',
                confirmButtonText: 'OK'
            });
            <?php endif; ?>
            
            // Handle account disabled
            <?php if(isset($_GET['error']) && $_GET['error'] == 'account_disabled'): ?>
            Swal.fire({
                icon: 'error',
                title: 'Account Disabled',
                text: 'Your account has been disabled. Please contact the administrator.',
                confirmButtonColor: '<?php echo Branding::get('color_danger'); ?>',
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
                    email: $('#email').val().trim(),
                    password: $('#password').val()
                };
                
                // Validate form
                if (!formData.email || !formData.password) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please fill in all required fields',
                        confirmButtonColor: '<?php echo Branding::get('color_danger'); ?>'
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
                        confirmButtonColor: '<?php echo Branding::get('color_danger'); ?>'
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
                                text: 'Welcome to <?php echo htmlspecialchars(Branding::getBusinessName(true)); ?> IMS',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(function() {
                                window.location.href = 'index.php';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Login Failed',
                                text: response.message || 'Invalid email or password',
                                confirmButtonColor: '<?php echo Branding::get('color_danger'); ?>'
                            });
                            resetLoginButton();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Error',
                            text: 'Unable to connect to server. Please try again.',
                            confirmButtonColor: '<?php echo Branding::get('color_danger'); ?>'
                        });
                        resetLoginButton();
                    }
                });
            });
            
            function resetLoginButton() {
                $('#loginText').show();
                $('#loginSpinner').hide();
                $('#loginBtn').prop('disabled', false);
            }
            
            // Enter key on password field
            $('#password').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#loginForm').submit();
                }
            });
        });
    </script>
</body>
</html>



