<?php
session_start();
include "./../php_files/conn.php";

// This file handles AJAX requests to update cart quantities

// Only process POST requests with the update_ajax flag
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_ajax'])) {
    // Get user email from session
    $email = $_SESSION['email'] ?? '';
    
    // Check if user is logged in
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit();
    }
    
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];
    
    // Validate that quantity is a positive number
    if ($quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
        exit();
    }
    
    // First get the price of the product
    $priceSql = "SELECT p.p_price FROM cart c JOIN product_details p ON c.p_id = p.p_id WHERE c.cart_id = ?";
    $priceStmt = $conn->prepare($priceSql);
    $priceStmt->bind_param("i", $cart_id);
    $priceStmt->execute();
    $priceResult = $priceStmt->get_result();
    
    if ($priceResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit();
    }
    
    $priceRow = $priceResult->fetch_assoc();
    $price = $priceRow['p_price'];
    
    // Calculate the total price for this item
    $total_price = $quantity * $price;
    
    // Update quantity and total price in the cart
    $sql = "UPDATE cart SET quantity = ?, total_price = ? WHERE cart_id = ? AND user_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idis", $quantity, $total_price, $cart_id, $email);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cart updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating cart: ' . $conn->error]);
    }
} else {
    // Not a valid request
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>