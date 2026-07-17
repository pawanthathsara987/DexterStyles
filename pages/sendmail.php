<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './../phpmailer/Exception.php';
require './../phpmailer/PHPMailer.php';
require './../phpmailer/SMTP.php';

$mail = new PHPMailer(true);

if (isset($_POST['send'])) {
    // Sanitize input data
    $fname = trim($_POST["fname"]);
    $lname = trim($_POST["lname"]);
    $user_email = trim($_POST["mail"]);
    $phone = trim($_POST["phone"]);
    $msg = trim($_POST["msg"]);

    // Input validation
    $errors = [];

    if (!preg_match("/^[a-zA-Z\s\-']+$/", $fname)) {
        $errors['fname'] = "Invalid first name. Only letters, spaces, hyphens, and apostrophes are allowed.";
    }
    if (!preg_match("/^[a-zA-Z\s\-']+$/", $lname)) {
        $errors['lname'] = "Invalid last name. Only letters, spaces, hyphens, and apostrophes are allowed.";
    }
    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $errors['mail'] = "Invalid email address.";
    }
    if (!preg_match("/^\+?\d{10,15}$/", $phone)) {
        $errors['phone'] = "Invalid phone number format.";
    }
    if (empty($msg)) {
        $errors['msg'] = "Message cannot be empty.";
    }

    if (empty($errors)) {
        try {
            // Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'methsarasenarathne71@gmail.com';
            $mail->Password = 'lhqb egzm yyci utgk'; // Use environment variable
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('methsarasenarathne71@gmail.com', 'Contact Form');
            $mail->addAddress('methsarasenarathne71@gmail.com');
            $mail->addReplyTo($user_email, "$fname $lname"); // Ensure reply goes to sender

            $mail->isHTML(true);
            $mail->Subject = 'Message Received';
            $mail->Body = "
            <html>
            <head>
                <style>
                    body {
                        font-family: 'Arial', sans-serif;
                        background-color: #f4f4f4;
                        padding: 20px;
                    }
                    .email-container {
                        background-color: #ffffff;
                        border-radius: 8px;
                        padding: 20px;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                        max-width: 600px;
                        margin: 0 auto;
                    }
                    .email-header {
                        background-color: #007bff;
                        color: white;
                        padding: 15px;
                        border-radius: 6px;
                        text-align: center;
                    }
                    .email-header h2 {
                        margin: 0;
                    }
                    .email-content {
                        margin-top: 20px;
                    }
                    .email-content h4 {
                        color: #333;
                        font-size: 18px;
                        margin-bottom: 10px;
                    }
                    .email-content p {
                        color: #555;
                        font-size: 16px;
                        line-height: 1.6;
                    }
                    .email-footer {
                        margin-top: 20px;
                        font-size: 14px;
                        text-align: center;
                        color: #777;
                    }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        <h2>Contact Form Submission</h2>
                    </div>
                    <div class='email-content'>
                        <h4>Name:</h4>
                        <p>$fname $lname</p>
                        
                        <h4>Email:</h4>
                        <p>$user_email</p>
                        
                        <h4>Phone:</h4>
                        <p>$phone</p>
                        
                        <h4>Message:</h4>
                        <p>$msg</p>
                    </div>
                    <div class='email-footer'>
                        <p>Thank you for reaching out to us!</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            

            // Send email
            if ($mail->send()) {
                $_SESSION['STATUS'] = "Thank you for contacting us!";
                unset($_SESSION['errors']); // Clear any previous errors
            } else {
                $_SESSION['STATUS'] = "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
            }
        } catch (Exception $e) {
            $_SESSION['STATUS'] = "Message could not be sent. Error: " . $e->getMessage();
        }
    } else {
        $_SESSION['errors'] = $errors; // Store errors in session
    }

    // Redirect safely
    $redirect = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "contact.php";
    header("Location: $redirect");
    exit;
} else {
    header('Location: contact.php');
    exit;
}
