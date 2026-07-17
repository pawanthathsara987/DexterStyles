<?php
// Start session at the beginning
session_start();

// Include the database connection
include './../php_files/conn.php';

// Require PHPMailer files (assuming you've installed via Composer)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require './../vendor/autoload.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email from the form
    $email = trim($_POST['email']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit;
    }
    
    try {
        // Prepare SQL statement to check if email exists
        $stmt = $conn->prepare("SELECT id, name FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Email exists, get user details
            $user = $result->fetch_assoc();
            $userId = $user['id'];
            $userName = $user['name'];
            
            // Generate unique token
            $token = bin2hex(random_bytes(32));
            $tokenExpiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store token in password_resets table
            $insertToken = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) 
                                          VALUES (?, ?, ?) 
                                          ON DUPLICATE KEY UPDATE token = ?, expires_at = ?");
            $insertToken->bind_param("sssss", $email, $token, $tokenExpiry, $token, $tokenExpiry);
            $insertToken->execute();

            // Create a new PHPMailer instance
            $mail = new PHPMailer(true);

            $logo_path = 'C:/xampp/htdocs/Project/DexterStyles/img/site-logo.png';
            $mail->addEmbeddedImage($logo_path, 'dexterstyles_logo', 'dexterstyles_logo.png', 'base64', 'image/png');
            
            // Reset link
            $resetLink = "http://localhost/Project/DexterStyles/pages/reset-password.php?token=" . $token;
            
            // Email content
            $htmlMessage = "
            <html>
            <head>
                <title>Reset Your Password</title>
            </head>
            <body>
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background-color: #f8f9fa; padding: 20px; text-align: center;'>
                        <img src='cid:dexterstyles_logo' alt='DexterStyles Logo' width='100'>
                    </div>
                    <div style='padding: 20px; border: 1px solid #e9ecef;'>
                        <h2>Hello $userName,</h2>
                        <p>We received a request to reset your password for your DexterStyles account.</p>
                        <p>To reset your password, click the button below:</p>
                        <p style='text-align: center;'>
                            <a href='$resetLink' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;'>Reset Password</a>
                        </p>
                        <p>This link will expire in 1 hour.</p>
                        <p>If you didn't request a password reset, you can ignore this email.</p>
                        <p>Thank you,<br>The DexterStyles Team</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            // Plain text version
            $textMessage = "Hello $userName,\n\nWe received a request to reset your password for your DexterStyles account.\n\nTo reset your password, please visit this link:\n$resetLink\n\nThis link will expire in 1 hour.\n\nIf you didn't request a password reset, you can ignore this email.\n\nThank you,\nThe DexterStyles Team";
            
            
            

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'teamdexterstyles@gmail.com';
                $mail->Password   = 'xzhh bhxf gixl qmsb'; // Use app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );                             // TCP port to connect to (use 465 for SSL)
                
                // Recipients
                $mail->setFrom('teamdexterstyles@gmail.com', 'DexterStyles');
                $mail->addAddress($email, $userName);                 // Add a recipient
                $mail->addReplyTo('teamdexterstyles@gmail.com', 'Support');
                
                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Password Reset - DexterStyles';
                $mail->Body    = $htmlMessage;
                $mail->AltBody = $textMessage;
                
                $mail->send();
                echo json_encode(['status' => 'success', 'message' => 'Password reset instructions have been sent to your email.']);
            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to send email: ' . $mail->ErrorInfo]);
            }
            
        } else {
            // Email doesn't exist
            echo json_encode(['status' => 'error', 'message' => 'Email address not found. Please check and try again.']);
        }
        
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'System error. Please try again later.']);
    }
    
    exit;
}
?>