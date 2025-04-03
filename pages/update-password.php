<?php
// Start session at the beginning
session_start();

// Include the database connection
include './../php_files/conn.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $token = trim($_POST['token']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Basic validation
    if (empty($token) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }
    
    if ($password !== $confirmPassword) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
        exit;
    }
    
    // Password strength validation
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        echo json_encode(['status' => 'error', 'message' => 'Password does not meet requirements']);
        exit;
    }
    
    try {
        // Begin transaction
        $conn->begin_transaction();
        
        // Check if token is valid and not expired
        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND email = ? AND expires_at < NOW()");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid or expired reset link']);
            $conn->rollback();
            exit;
        }
        
        // Hash the new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Update user's password
        $updateStmt = $conn->prepare("UPDATE user SET password = ? WHERE email = ?");
        $updateStmt->bind_param("ss", $hashedPassword, $email);
        $updateStmt->execute();
        
        if ($updateStmt->affected_rows === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
            $conn->rollback();
            exit;
        }
        
        // Delete the token
        $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        $deleteStmt->bind_param("s", $email);
        $deleteStmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode(['status' => 'success', 'message' => 'Your password has been reset successfully']);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'System error. Please try again later.']);
    }
    
    exit;
}
?>