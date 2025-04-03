<?php
    // Start session at the beginning
    session_start();
    
    include './../php_files/conn.php';

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - DexterStyles</title>
    <link rel="stylesheet" href="./../css/signin.css">
    <link rel="stylesheet" href="./../css/forgetpassword.css">
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
            
            <!-- Forgot Password Form -->
            <div class="form-container forget-password-form">
                <form id="forgetPasswordForm" method="POST">
                    <h1>Forgot Password</h1>
                    <p class="form-description">Enter your email address and we'll send you a link to reset your password.</p>
                    
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>
                    
                    <button type="submit" id="resetBtn">Send Reset Link</button>
                    
                    <div id="success-message" class="success-message">Password reset instructions have been sent to your email.</div>
                    <div id="error-message" class="error-message">Email address not found. Please check and try again.</div>
                    
                    <div class="back-to-login">
                        <a href="./signin.php">Back to Sign In</a>
                    </div>
                </form>
            </div>
            
            <!-- Illustration Container -->
            <div class="welcome-container">
                <div class="welcome-text">
                    <h1>Password Reset</h1>
                    <p>We'll help you recover your account access.</p>
                    <div class="illustration">
                        <img src="./../img/reset-password.png" alt="Password Reset Illustration">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password reset form handling
        document.addEventListener('DOMContentLoaded', function() {
            const forgetPasswordForm = document.getElementById('forgetPasswordForm');
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');
            const loadingOverlay = document.getElementById('loading-overlay');
            const resetBtn = document.getElementById('resetBtn');
            
            // Hide messages initially
            successMessage.style.display = 'none';
            errorMessage.style.display = 'none';
            
            forgetPasswordForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Show loading overlay
                loadingOverlay.classList.add('active');
                
                // Get the email
                const email = this.email.value;
                
                // Simulate API call for password reset (replace with actual API call)
                setTimeout(function() {
                    // Hide loading overlay
                    loadingOverlay.classList.remove('active');
                    
                    // For demo, we'll assume success if email contains "user" or "admin"
                    if (email.includes('user') || email.includes('admin')) {
                        successMessage.style.display = 'block';
                        errorMessage.style.display = 'none';
                        resetBtn.disabled = true;
                        resetBtn.textContent = "Link Sent";
                        
                        // After 3 seconds, redirect back to sign in page
                        setTimeout(function() {
                            window.location.href = './signin.php';
                        }, 3000);
                    } else {
                        errorMessage.style.display = 'block';
                        successMessage.style.display = 'none';
                    }
                }, 2000); // 2 second simulated processing time
            });
        });
    </script>
</body>
</html>