<?php

    include './../php_files/conn.php';

    $error_msg = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        if (isset($_POST['signinbtn'])) {
            
        }
        elseif (isset($_POST['signupbtn'])) {
            $cusname = $_POST['uname'];
            $cusemail = $_POST['uemail'];
            $cuspword = $_POST['upassword'];
            $cuscpword = $_POST['uconfirm_password'];

            $_SESSION['name'] = $cusname;

            if ($cusemail != null) {
                $qry_email_exist = "SELECT email FROM WHERE email = '$cusemail' ";
                $run_email_exist = $conn->query($qry_email_exist);
                if ($run_email_exist->num_rows > 0) {
                    $error_msg = "Email already exist!";
                    $_SESSION['error_msg'] = $error_msg;
                    return;
                }
            }
            
            $qry_create_user = "INSERT INTO user(name, email, password) VALUES ('$cusname', '$cusemail', '$cuspword') ";
            if ($conn->query($qry_create_user)) {
                $suc_msg = "Signup Succesful!";
                
            }

        }
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up & Sign In - DexterStyles</title>
    <link rel="stylesheet" href="./../css/signin.css">
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo"><a href="./../home.php"><img src="./../img/site-logo.png" alt="DexterStyles Logo" width="80px"></a></div> 
        </nav>
    </header>

    <div class="wrapper">
        <div class="container" id="container">
            <!-- Loading Overlay -->
            <div class="loading-overlay" id="loading-overlay">
                <div class="spinner"></div>
            </div>
            
            <!-- Sign In Form Container -->
            <div class="form-container sign-in-container">
                <form id="signinForm" method="POST">
                    <h1>Sign In</h1>
                    <div class="form-group">
                        <input type="email" name="iemail" placeholder="Email" required>
                    </div>
                    <div class="form-group password-wrapper">
                        <input type="password" name="ipassword" id="signin-password" placeholder="Password" required>
                        <img src="./../img/hidden.png" alt="Show Password" class="toggle-password" id="toggle-signin-password">
                    </div>
                    <div class="remember-forgot">
                        <label class="remember-me">
                            <input type="checkbox" name="remember" id="remember"> Remember Me
                        </label>
                        <a href="./forgetpassword.php" class="forgot-password">Forgot Password?</a>
                    </div>
                    <button type="submit" name="signinbtn">Sign In</button>
                    <div class="separator">
                        <span>OR</span>
                    </div>
                    <button type="button" class="google-login-btn">
                        <img src="./../img/google.png" alt="Google Icon" class="google-icon">
                        Sign in with Google
                    </button>
                </form>
            </div>
            
            <!-- Sign Up Form Container -->
            <div class="form-container sign-up-container">
                <form id="signupForm" action="" method="POST" onsubmit="return validatePasswords()">
                    <h1>Create Account</h1>
                    <div class="form-group">
                        <input type="text" name="uname" placeholder="Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="uemail" placeholder="Email" required>
                    </div>
                    <div class="form-group password-wrapper">
                        <input type="password" name="upassword" id="password" placeholder="Password" required>
                        <img src="./../img/hidden.png" alt="Show Password" class="toggle-password" id="toggle-signup-password">
                    </div>
                    <div id="password-error" class="error-message">Password must be between 8-16 characters, include numbers, uppercase and lowercase letters, and symbols (excluding @).</div>
                    <div class="form-group password-wrapper">
                        <input type="password" name="uconfirm_password" id="confirm_password" placeholder="Confirm Password" required>
                        <img src="./../img/hidden.png" alt="Show Password" class="toggle-password" id="toggle-confirm-password">
                    </div>
                    <div id="error-message" class="error-message">Passwords do not match!</div>
                    <button type="submit" name="signupbtn">Sign Up</button>
                    <div class="separator">
                        <span>OR</span>
                    </div>
                    <button type="button" class="google-login-btn">
                        <img src="./../img/google.png" alt="Google Icon" class="google-icon">
                        Sign up with Google
                    </button>
                </form>
            </div>

            <!-- Welcome Section with Toggle Links -->
            <div class="welcome-container">
                <div class="welcome-text welcome-signin">
                    <h1>Welcome!</h1>
                    <p>Sign in to access your account.</p>
                    <div class="toggle-link">
                        <a onclick="toggleFormWithLoading()">Don't have an account? Sign Up</a>
                    </div>
                </div>
                <div class="welcome-text welcome-signup">
                    <h1>Welcome Back!</h1>
                    <p>Join our community to get started.</p>
                    <div class="toggle-link">
                        <a onclick="toggleFormWithLoading()">Already have an account? Sign In</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle with loading animation
        function toggleFormWithLoading() {
            const container = document.getElementById('container');
            const loadingOverlay = document.getElementById('loading-overlay');
            
            // Show loading animation
            loadingOverlay.classList.add('active');
            container.classList.add('loading');
            
            // Wait for 1 second before toggling
            setTimeout(function() {
                container.classList.toggle('active');
                
                // Hide loading animation
                setTimeout(function() {
                    loadingOverlay.classList.remove('active');
                    container.classList.remove('loading');
                }, 300);
            }, 1000);
        }
        
        // Original toggle function (kept for reference)
        function toggleForm() {
            const container = document.getElementById('container');
            container.classList.toggle('active');
        }
        
        // Password visibility toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Sign up password toggle
            const toggleSignupPassword = document.getElementById('toggle-signup-password');
            const passwordField = document.getElementById('password');
            
            if (toggleSignupPassword) {
                toggleSignupPassword.addEventListener('click', function() {
                    togglePasswordVisibility(passwordField, this);
                });
            }
            
            // Confirm password toggle
            const toggleConfirmPassword = document.getElementById('toggle-confirm-password');
            const confirmPasswordField = document.getElementById('confirm_password');
            
            if (toggleConfirmPassword) {
                toggleConfirmPassword.addEventListener('click', function() {
                    togglePasswordVisibility(confirmPasswordField, this);
                });
            }
            
            // Sign in password toggle
            const toggleSigninPassword = document.getElementById('toggle-signin-password');
            const signinPasswordField = document.getElementById('signin-password');
            
            if (toggleSigninPassword) {
                toggleSigninPassword.addEventListener('click', function() {
                    togglePasswordVisibility(signinPasswordField, this);
                });
            }
            
            function togglePasswordVisibility(inputField, toggleButton) {
                if (inputField.type === 'password') {
                    inputField.type = 'text';
                    toggleButton.src = './../img/eye.png';
                } else {
                    inputField.type = 'password';
                    toggleButton.src = './../img/hidden.png';
                }
            }
            
            // Password validation
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const passwordError = document.getElementById('password-error');
            const confirmError = document.getElementById('error-message');
            
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    validatePassword(this.value);
                });
            }
            
            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', function() {
                    validateConfirmPassword();
                });
            }
            
            function validatePassword(password) {
                // Password must be 8-16 characters, include numbers, uppercase and lowercase letters, and symbols
                const regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^@a-zA-Z0-9]).{8,16}$/;
                
                if (!regex.test(password)) {
                    passwordError.style.display = 'block';
                    return false;
                } else {
                    passwordError.style.display = 'none';
                    return true;
                }
            }
            
            function validateConfirmPassword() {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    confirmError.style.display = 'block';
                    return false;
                } else {
                    confirmError.style.display = 'none';
                    return true;
                }
            }
        });
        
        // Form validation
        function validatePasswords() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const passwordError = document.getElementById('password-error');
            const confirmError = document.getElementById('error-message');
            
            // Password validation regex
            const regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^@a-zA-Z0-9]).{8,16}$/;
            
            if (!regex.test(password)) {
                passwordError.style.display = 'block';
                return false;
            }
            
            if (password !== confirmPassword) {
                confirmError.style.display = 'block';
                return false;
            }
            
            return true;
        }
    </script>
</body>
</html>