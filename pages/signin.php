<?php
    // Start session at the beginning
    session_start();
    
    include './../php_files/conn.php';

    $error_msg = "";
    $success_msg = "";
    $email_error = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        if (isset($_POST['signinbtn'])) {
            // Handle sign in process
            $email = filter_input(INPUT_POST, 'iemail', FILTER_SANITIZE_EMAIL);
            $password = $_POST['ipassword'];
            
            if (!empty($email) && !empty($password)) {
                // Prepare statement to prevent SQL injection
                $stmt = $conn->prepare("SELECT id, name, email, password FROM user WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    // Verify password - assuming passwords are stored with password_hash()
                    if (password_verify($password, $user['password'])) {
                        // Set session variables
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['name'] = $user['name'];
                        $_SESSION['email'] = $user['email'];
                        
                        // Redirect to home page or dashboard
                        header("Location: ./../home.php");
                        exit();
                    } else {
                        $error_msg = "Invalid email or password!";
                    }
                } else {
                    $error_msg = "Invalid email or password!";
                }
                $stmt->close();
            } else {
                $error_msg = "Email and password are required!";
            }
        }
        elseif (isset($_POST['signupbtn'])) {
            $cusname = filter_input(INPUT_POST, 'uname', FILTER_SANITIZE_STRING);
            $cusemail = filter_input(INPUT_POST, 'uemail', FILTER_SANITIZE_EMAIL);
            $cuspword = $_POST['upassword'];
            $cuscpword = $_POST['uconfirm_password'];
            
            // Server-side password validation
            $password_regex = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^@a-zA-Z0-9]).{8,16}$/";
            
            if ($cuspword !== $cuscpword) {
                $error_msg = "Passwords do not match!";
            } 
            elseif (!preg_match($password_regex, $cuspword)) {
                $error_msg = "Password must be between 8-16 characters, include numbers, uppercase and lowercase letters, and symbols (excluding @).";
            }
            elseif (!filter_var($cusemail, FILTER_VALIDATE_EMAIL)) {
                $error_msg = "Please enter a valid email address.";
            }
            else {
                // Check if email exists
                $stmt = $conn->prepare("SELECT email FROM user WHERE email = ?");
                $stmt->bind_param("s", $cusemail);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $email_error = "Email already exists!";
                    $_SESSION['email_error'] = $email_error;
                    $_SESSION['name'] = $cusname;
                    $_SESSION['show_signup'] = true;
                    
                    // Redirect to the same page to refresh
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    // Hash password before storing
                    $hashed_password = password_hash($cuspword, PASSWORD_DEFAULT);
                    
                    // Create new user with prepared statement
                    $insert_stmt = $conn->prepare("INSERT INTO user(name, email, password) VALUES (?, ?, ?)");
                    $insert_stmt->bind_param("sss", $cusname, $cusemail, $hashed_password);
                    
                    if ($insert_stmt->execute()) {
                        $success_msg = "Signup Successful! Please sign in.";
                        $_SESSION['success_msg'] = $success_msg;
                        
                        // Redirect to same page but show sign in form
                        header("Location: " . $_SERVER['PHP_SELF']);
                        exit();
                    } else {
                        $error_msg = "Error creating account: " . $conn->error;
                        $_SESSION['error_msg'] = $error_msg;
                        $_SESSION['name'] = $cusname;
                        $_SESSION['show_signup'] = true;
                        
                        // Redirect to the same page to refresh
                        header("Location: " . $_SERVER['PHP_SELF']);
                        exit();
                    }
                    $insert_stmt->close();
                }
                $stmt->close();
            }
            
            $_SESSION['error_msg'] = $error_msg;
            $_SESSION['name'] = $cusname;
            $_SESSION['show_signup'] = true;
            
            // Redirect to the same page to refresh
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
    
    // Check if we need to show signup form on page load
    $show_signup = isset($_SESSION['show_signup']) && $_SESSION['show_signup'] === true;
    if ($show_signup) {
        // Clear the flag so it doesn't persist indefinitely
        unset($_SESSION['show_signup']);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up & Sign In - DexterStyles</title>
    <link rel="stylesheet" href="./../css/signin.css">
    <style>
        .signin-error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
            display: none;
        }
        .success-message {
            color: #2ecc71;
            font-size: 14px;
            margin-bottom: 10px;
            text-align: center;
        }
        .email-error {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 4px;
            margin-bottom: 10px;
            text-align: left;
            display: block;
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo"><a href="./../home.php"><img src="./../img/site-logo.png" alt="DexterStyles Logo" width="80px"></a></div> 
        </nav>
    </header>

    <div class="wrapper">
        <div class="container" id="container" class="<?php echo $show_signup ? 'active' : ''; ?>">
            <!-- Loading Overlay -->
            <div class="loading-overlay" id="loading-overlay">
                <div class="spinner"></div>
            </div>
            
            <!-- Sign In Form Container -->
            <div class="form-container sign-in-container">
                <form id="signinForm" method="POST">
                    <h1>Sign In</h1>
                    <?php if(isset($_SESSION['success_msg']) && !empty($_SESSION['success_msg'])) { ?>
                        <div class="success-message" style="display: block;"><?php echo $_SESSION['success_msg']; ?></div>
                        <?php unset($_SESSION['success_msg']); ?>
                    <?php } ?>
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
                    
                    <!-- Error message below sign in button -->
                    <?php if(isset($error_msg) && !empty($error_msg) && !isset($_POST['signupbtn'])) { ?>
                        <div class="signin-error-message" style="display: block;"><?php echo $error_msg; ?></div>
                    <?php } ?>
                    
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
                <form id="signupForm" method="POST" onsubmit="return validatePasswords()">
                    <h1>Create Account</h1>
                    <?php if(isset($_SESSION['error_msg']) && !empty($_SESSION['error_msg'])) { ?>
                        <div class="error-message" style="display: block;"><?php echo $_SESSION['error_msg']; ?></div>
                        <?php unset($_SESSION['error_msg']); ?>
                    <?php } ?>
                    <div class="form-group">
                        <input type="text" name="uname" placeholder="Name" value="<?php echo isset($_SESSION['name']) ? $_SESSION['name'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="uemail" placeholder="Email" required>
                        <?php if(isset($_SESSION['email_error']) && !empty($_SESSION['email_error'])) { ?>
                            <div class="email-error"><?php echo $_SESSION['email_error']; ?></div>
                            <?php unset($_SESSION['email_error']); ?>
                        <?php } ?>
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

        // Show signup form on page load if needed
        document.addEventListener('DOMContentLoaded', function() {
            <?php if($show_signup) { ?>
                const container = document.getElementById('container');
                container.classList.add('active');
            <?php } ?>
        });
        
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