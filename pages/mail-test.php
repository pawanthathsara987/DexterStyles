<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if PHPMailer is installed
echo "<h2>PHPMailer Installation Check</h2>";

// Check if autoload.php exists
if (file_exists('./../vendor/autoload.php')) {
    echo "<p style='color:green'>✓ Composer autoload file found</p>";
} else {
    echo "<p style='color:red'>✗ Composer autoload file not found at ./../vendor/autoload.php</p>";
    echo "<p>Make sure you've installed PHPMailer using Composer or adjust the path.</p>";
}

// Try to include PHPMailer
try {
    require './../vendor/autoload.php';
    echo "<p style='color:green'>✓ Autoload file loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error loading autoload file: " . $e->getMessage() . "</p>";
}

// Check for PHPMailer classes
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "<p style='color:green'>✓ PHPMailer class found</p>";
} else {
    echo "<p style='color:red'>✗ PHPMailer class not found</p>";
    echo "<p>Make sure you've installed PHPMailer correctly using:<br>composer require phpmailer/phpmailer</p>";
}

// Database connection check
echo "<h2>Database Connection Check</h2>";
try {
    include './../connection/conn.php';
    echo "<p style='color:green'>✓ Database connection file included</p>";
    
    if (isset($conn) && $conn instanceof mysqli) {
        echo "<p style='color:green'>✓ Database connection established</p>";
        
        // Check if connection is alive
        if ($conn->ping()) {
            echo "<p style='color:green'>✓ Database connection is working</p>";
        } else {
            echo "<p style='color:red'>✗ Database connection is not responding</p>";
        }
    } else {
        echo "<p style='color:red'>✗ Database connection variable not found or not a mysqli instance</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error with database connection: " . $e->getMessage() . "</p>";
}

// Check if password_resets table exists
echo "<h2>Database Table Check</h2>";
try {
    if (isset($conn) && $conn instanceof mysqli) {
        $result = $conn->query("SHOW TABLES LIKE 'password_resets'");
        if ($result->num_rows > 0) {
            echo "<p style='color:green'>✓ password_resets table exists</p>";
        } else {
            echo "<p style='color:red'>✗ password_resets table does not exist</p>";
            echo "<p>Create the table using the SQL provided in the implementation instructions.</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error checking for password_resets table: " . $e->getMessage() . "</p>";
}

// File path check
echo "<h2>File Path Check</h2>";
$filesToCheck = [
    'reset-password-handler.php',
    'forgot-password.php',
    './signin.php'
];

foreach ($filesToCheck as $file) {
    if (file_exists($file)) {
        echo "<p style='color:green'>✓ File found: $file</p>";
    } else {
        echo "<p style='color:red'>✗ File not found: $file</p>";
    }
}

// Check mail function
echo "<h2>Mail Function Check</h2>";
if (function_exists('mail')) {
    echo "<p style='color:green'>✓ PHP mail() function exists</p>";
} else {
    echo "<p style='color:red'>✗ PHP mail() function is not available</p>";
}

// Summary
echo "<h2>What to do next</h2>";
echo "<p>If any checks above show red ✗ marks, you need to fix those issues.</p>";
echo "<p>Common issues:</p>";
echo "<ol>";
echo "<li>PHPMailer not installed correctly - Run: composer require phpmailer/phpmailer</li>";
echo "<li>Database connection issues - Check your connection details</li>";
echo "<li>password_resets table missing - Create the table using the SQL provided</li>";
echo "<li>File paths incorrect - Make sure all files are in the correct directories</li>";
echo "</ol>";
echo "<p>If all checks pass but you're still having errors, check your SMTP settings:</p>";
echo "<ul>";
echo "<li>Host: Make sure it's the correct SMTP server for your email provider</li>";
echo "<li>Username/Password: Verify these are correct</li>";
echo "<li>Port: Should be 587 for STARTTLS or 465 for SSL</li>";
echo "</ul>";
?>