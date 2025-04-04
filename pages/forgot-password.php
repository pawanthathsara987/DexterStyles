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
                        <img src="./../img/password-reset.png" alt="Password Reset Illustration">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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
                
                // Create form data
                const formData = new FormData();
                formData.append('email', email);
                
                // Send AJAX request to check email and send reset link
                fetch('reset-password-handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.text(); // Get the raw response text first
                })
                .then(text => {
                    try {
                        // Try to parse as JSON
                        return JSON.parse(text);
                    } catch (e) {
                        // If it's not valid JSON, show the raw response
                        console.error("Response is not valid JSON:", text);
                        throw new Error(`Invalid JSON response: ${text.substring(0, 100)}...`);
                    }
                })
                .then(data => {
                    // Hide loading overlay
                    loadingOverlay.classList.remove('active');
                    
                    if (data.status === 'success') {
                        successMessage.style.display = 'block';
                        successMessage.textContent = data.message;
                        errorMessage.style.display = 'none';
                        resetBtn.disabled = true;
                        resetBtn.textContent = "Link Sent";
                        
                        // After 3 seconds, redirect back to sign in page
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
                    console.error('Error:', error);
                    errorMessage.style.display = 'block';
                    errorMessage.textContent = 'System error: ' + error.message;
                    successMessage.style.display = 'none';
                });
            });
        });
    </script>
</body>
</html>