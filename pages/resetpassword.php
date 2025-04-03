<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - DexterStyles</title>
    <link rel="stylesheet" href="./../css/signin.css">
    <link rel="stylesheet" href="./../css/resetpassword.css">
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo"><a href="./../home.php"><img src="./../img/logo.png" alt="DexterStyles Logo" width="80px"></a></div> 
        </nav>
    </header>

    <div class="wrapper">
        <div class="container password-reset-container">
            <!-- Loading Overlay -->
            <div class="loading-overlay" id="loading-overlay">
                <div class="spinner"></div>
            </div>
            
            <!-- Password Reset Form -->
            <div class="form-container password-reset-form">
                <form id="resetPasswordForm" method="POST" onsubmit="return validatePasswords()">
                    <h1>Reset Password</h1>
                    <p class="form-description">Enter your new password below.</p>
                    
                    <div class="form-group password-wrapper">
                        <input type="password" name="password" id="password" placeholder="New Password" required>
                        <img src="./../img/hidden.png" alt="Show Password" class="toggle-password" id="toggle-new-password">
                    </div>
                    <div id="password-error" class="error-message">Password must be between 8-16 characters, include numbers, uppercase and lowercase letters, and symbols (excluding @).</div>
                    
                    <div class="form-group password-wrapper">
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" required>
                        <img src="./../img/hidden.png" alt="Show Password" class="toggle-password" id="toggle-confirm-password">
                    </div>
                    <div id="confirm-error" class="error-message">Passwords do not match!</div>
                    
                    <div class="password-strength-meter">
                        <div class="password-strength-label">Password Strength:</div>
                        <div class="strength-bar-container">
                            <div class="strength-bar" id="strength-bar"></div>
                        </div>
                        <div class="strength-text" id="strength-text">Not entered</div>
                    </div>
                    
                    <!-- Hidden field for token from URL -->
                    <input type="hidden" name="token" id="reset-token">
                    
                    <button type="submit" id="resetBtn">Reset Password</button>
                    
                    <div id="success-message" class="success-message">Your password has been successfully reset. You will be redirected to login.</div>
                    <div id="error-message" class="error-message">There was a problem resetting your password. Please try again.</div>
                </form>
            </div>
            
            <!-- Illustration Container -->
            <div class="welcome-container">
                <div class="welcome-text">
                    <h1>Create New Password</h1>
                    <p>Choose a strong password to protect your account.</p>
                    <div class="password-tips">
                        <h3>Password Tips:</h3>
                        <ul>
                            <li>Use at least 8 characters</li>
                            <li>Include uppercase and lowercase letters</li>
                            <li>Add numbers and symbols</li>
                            <li>Avoid using common words</li>
                            <li>Don't reuse passwords from other sites</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get token from URL query string
            const urlParams = new URLSearchParams(window.location.search);
            const token = urlParams.get('token');
            if (token) {
                document.getElementById('reset-token').value = token;
            } else {
                // Redirect back to forgot password page if no token
                // window.location.href = './forgetpassword.php';
                console.log("No token found in URL");
            }
            
            // Password visibility toggle functionality
            const toggleNewPassword = document.getElementById('toggle-new-password');
            const passwordField = document.getElementById('password');
            
            if (toggleNewPassword) {
                toggleNewPassword.addEventListener('click', function() {
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
            
            // Password strength meter
            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');
            const passwordInput = document.getElementById('password');
            
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = calculatePasswordStrength(password);
                
                // Update strength bar
                strengthBar.style.width = strength + '%';
                
                // Set color based on strength
                if (strength < 25) {
                    strengthBar.style.backgroundColor = '#e74c3c'; // Red
                    strengthText.textContent = 'Very Weak';
                } else if (strength < 50) {
                    strengthBar.style.backgroundColor = '#e67e22'; // Orange
                    strengthText.textContent = 'Weak';
                } else if (strength < 75) {
                    strengthBar.style.backgroundColor = '#f1c40f'; // Yellow
                    strengthText.textContent = 'Medium';
                } else if (strength < 90) {
                    strengthBar.style.backgroundColor = '#2ecc71'; // Green
                    strengthText.textContent = 'Strong';
                } else {
                    strengthBar.style.backgroundColor = '#27ae60'; // Dark Green
                    strengthText.textContent = 'Very Strong';
                }
                
                validatePassword(password);
            });
            
            function calculatePasswordStrength(password) {
                let strength = 0;
                
                // If password is empty, return 0
                if (password.length === 0) {
                    return 0;
                }
                
                // Length
                strength += Math.min(password.length * 6, 30);
                
                // Lowercase letters
                if (password.match(/[a-z]/)) {
                    strength += 10;
                }
                
                // Uppercase letters
                if (password.match(/[A-Z]/)) {
                    strength += 15;
                }
                
                // Numbers
                if (password.match(/[0-9]/)) {
                    strength += 15;
                }
                
                // Symbols
                if (password.match(/[^a-zA-Z0-9]/)) {
                    strength += 20;
                }
                
                // Variety of characters
                const uniqueChars = new Set(password.split('')).size;
                strength += uniqueChars * 2;
                
                // Cap at 100
                return Math.min(strength, 100);
            }
            
            // Form validation
            const confirmPasswordInput = document.getElementById('confirm_password');
            const passwordError = document.getElementById('password-error');
            const confirmError = document.getElementById('confirm-error');
            
            passwordInput.addEventListener('input', function() {
                validatePassword(this.value);
            });
            
            confirmPasswordInput.addEventListener('input', function() {
                validateConfirmPassword();
            });
            
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
            
            // Form submit handler
            const resetPasswordForm = document.getElementById('resetPasswordForm');
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');
            const loadingOverlay = document.getElementById('loading-overlay');
            
            resetPasswordForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate form
                if (!validatePassword(passwordInput.value) || !validateConfirmPassword()) {
                    return false;
                }
                
                // Show loading overlay
                loadingOverlay.classList.add('active');
                
                // Simulate API call for password reset (replace with actual API call)
                setTimeout(function() {
                    // Hide loading overlay
                    loadingOverlay.classList.remove('active');
                    
                    // For demo purposes, always show success
                    successMessage.style.display = 'block';
                    errorMessage.style.display = 'none';
                    document.getElementById('resetBtn').disabled = true;
                    
                    // After 3 seconds, redirect to sign in page
                    setTimeout(function() {
                        window.location.href = './signin.php';
                    }, 3000);
                    
                }, 2000); // 2 second simulated processing time
            });
        });
        
        // Form validation for submit button
        function validatePasswords() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Password validation regex
            const regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^@a-zA-Z0-9]).{8,16}$/;
            
            if (!regex.test(password)) {
                document.getElementById('password-error').style.display = 'block';
                return false;
            }
            
            if (password !== confirmPassword) {
                document.getElementById('confirm-error').style.display = 'block';
                return false;
            }
            
            return true;
        }
    </script>
</body>
</html>