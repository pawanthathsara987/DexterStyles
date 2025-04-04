<?php
    // Start session at the beginning
    session_start();
    
    include './../php_files/conn.php';
    
    // Check if token is provided
    if (!isset($_GET['token']) || empty($_GET['token'])) {
        header("Location: ./signin.php");
        exit;
    }
    
    $token = $_GET['token'];
    $tokenValid = false;
    $email = '';
    
    // Verify token
    try {
        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at < NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $tokenValid = true;
            $row = $result->fetch_assoc();
            $email = $row['email'];
        }
    } catch (Exception $e) {
        // Token verification failed
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - DexterStyles</title>
    <link rel="stylesheet" href="./../css/signin.css">
    <link rel="stylesheet" href="./../css/forgot-password.css">
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo"><a href="./../home.php"><img src="./../img/logo.png" alt="DexterStyles Logo" width="80px"></a></div> 
        </nav>
    </header>

    <div class="wrapper">
        <div class="container forget-password-container">
            <!-- Loading Overlay -->
            <div class="loading-overlay" id="loading-overlay">
                <div class="spinner"></div>
            </div>
            
            <?php if ($tokenValid): ?>
            <!-- Reset Password Form -->
            <div class="form-container forget-password-form">
                <form id="resetPasswordForm" method="POST">
                    <h1>Reset Password</h1>
                    <p class="form-description">Please enter your new password below.</p>
                    
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    
                    <div class="form-group">
                        <input type="password" name="password" id="password" placeholder="New Password" required minlength="8">
                        <img src="./../img/hidden.png" alt="Show Password" class="toggle-password" id="toggle-new-password">
                    </div>
                    
                    <div class="form-group">
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" required minlength="8">
                        <img src="./../img/hidden.png" alt="Show Password" class="toggle-password" id="toggle-confirm-password">
                    </div>
                    
                    <div id="password-requirements" class="password-requirements">
                        <p>Password must:</p>
                        <ul>
                            <li id="req-length">Be at least 8 characters long</li>
                            <li id="req-uppercase">Contain at least one uppercase letter</li>
                            <li id="req-lowercase">Contain at least one lowercase letter</li>
                            <li id="req-number">Contain at least one number</li>
                            <li id="req-match">Passwords must match</li>
                        </ul>
                    </div>
                    
                    <button type="submit" id="resetBtn">Reset Password</button>
                    
                    <div id="success-message" class="success-message">Your password has been reset successfully.</div>
                    <div id="error-message" class="error-message">Error resetting password. Please try again.</div>
                </form>
            </div>
            <?php else: ?>
            <!-- Invalid or Expired Token -->
            <div class="form-container forget-password-form">
                <div class="token-invalid">
                    <h1>Invalid or Expired Link</h1>
                    <?php echo $token; ?>
                    <p>The password reset link is invalid or has expired.</p>
                    <div class="back-to-login">
                        <a href="./forgot-password.php">Request a new password reset link</a>
                    </div>
                    <div class="back-to-login">
                        <a href="./signin.php">Back to Sign In</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Illustration Container -->
            <div class="welcome-container">
                <div class="welcome-text">
                    <h1>Reset Your Password</h1>
                    <p>Create a strong, secure password for your account.</p>
                    <div class="illustration">
                        <img src="./../img/password-reset.png" alt="Password Reset Illustration">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($tokenValid): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const resetPasswordForm = document.getElementById('resetPasswordForm');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');
            const loadingOverlay = document.getElementById('loading-overlay');
            
            // Password requirement elements
            const reqLength = document.getElementById('req-length');
            const reqUppercase = document.getElementById('req-uppercase');
            const reqLowercase = document.getElementById('req-lowercase');
            const reqNumber = document.getElementById('req-number');
            const reqMatch = document.getElementById('req-match');
            
            // Hide messages initially
            successMessage.style.display = 'none';
            errorMessage.style.display = 'none';

            const togglenewPassword = document.getElementById('toggle-new-password');
            const passwordField = document.getElementById('password');
            
            if (togglenewPassword) {
                togglenewPassword.addEventListener('click', function() {
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
            
            function togglePasswordVisibility(inputField, toggleButton) {
                if (inputField.type === 'password') {
                    inputField.type = 'text';
                    toggleButton.src = './../img/eye.png';
                } else {
                    inputField.type = 'password';
                    toggleButton.src = './../img/hidden.png';
                }
            }
            
            
            // Password validation functions
            function validatePassword() {
                const val = password.value;
                const confirmVal = confirmPassword.value;
                
                // Check length
                if (val.length >= 8) {
                    reqLength.classList.add('valid');
                } else {
                    reqLength.classList.remove('valid');
                }
                
                // Check uppercase
                if (/[A-Z]/.test(val)) {
                    reqUppercase.classList.add('valid');
                } else {
                    reqUppercase.classList.remove('valid');
                }
                
                // Check lowercase
                if (/[a-z]/.test(val)) {
                    reqLowercase.classList.add('valid');
                } else {
                    reqLowercase.classList.remove('valid');
                }
                
                // Check number
                if (/[0-9]/.test(val)) {
                    reqNumber.classList.add('valid');
                } else {
                    reqNumber.classList.remove('valid');
                }
                
                // Check if passwords match
                if (val && confirmVal && val === confirmVal) {
                    reqMatch.classList.add('valid');
                } else {
                    reqMatch.classList.remove('valid');
                }
            }
            
            // Validate on input
            password.addEventListener('input', validatePassword);
            confirmPassword.addEventListener('input', validatePassword);
            
            resetPasswordForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate password
                validatePassword();
                
                const val = password.value;
                const confirmVal = confirmPassword.value;
                
                // Check if all requirements are met
                if (val.length < 8 || !/[A-Z]/.test(val) || !/[a-z]/.test(val) || 
                    !/[0-9]/.test(val) || val !== confirmVal) {
                    errorMessage.style.display = 'block';
                    errorMessage.textContent = 'Please meet all password requirements.';
                    return;
                }
                
                // Show loading overlay
                loadingOverlay.classList.add('active');
                
                // Create form data
                const formData = new FormData(resetPasswordForm);
                
                // Send AJAX request to update password
                fetch('update-password.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Hide loading overlay
                    loadingOverlay.classList.remove('active');
                    
                    if (data.status === 'success') {
                        successMessage.style.display = 'block';
                        errorMessage.style.display = 'none';
                        resetPasswordForm.reset();
                        
                        // Redirect to login page after 3 seconds
                        setTimeout(function() {
                            window.location.href = './signin.php';
                        }, 3000);
                    } else {
                        errorMessage.style.display = 'block';
                        errorMessage.textContent = data.message;
                        successMessage.style.display = 'none';
                    }
                })
                .catch(error => {
                    // Hide loading overlay
                    loadingOverlay.classList.remove('active');
                    errorMessage.style.display = 'block';
                    errorMessage.textContent = 'System error. Please try again later.';
                    successMessage.style.display = 'none';
                });
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>