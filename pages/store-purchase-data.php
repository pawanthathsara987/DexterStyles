<?php
// Include database connection 
include "./../php_files/conn.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Not logged in, return error
    echo json_encode(['success' => false, 'message' => 'Please log in to continue']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'buyNow') {
    $p_id = isset($_POST['p_id']) ? (int)$_POST['p_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $size = isset($_POST['size']) ? $_POST['size'] : 'S';
    
    // Validate inputs
    if ($p_id <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
        exit;
    }
    
    // Get product details from database
    $sql = "SELECT p.*, c.c_name FROM product_details p 
            JOIN category c ON p.c_id = c.c_id 
            WHERE p.p_id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $p_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        
        // Store purchase details in session
        $_SESSION['purchase'] = [
            'p_id' => $p_id,
            'p_title' => $product['p_title'],
            'p_brand' => $product['p_brand'],
            'p_price' => $product['p_price'],
            'quantity' => $quantity,
            'size' => $size,
            'total_price' => $product['p_price'] * $quantity,
            'p_image' => $product['p_mimage'],
            'category' => $product['c_name'],
            'timestamp' => time(),
            // Add necessary data for the cart display on the next page
            'items' => [
                [
                    'p_id' => $p_id,
                    'name' => $product['p_title'],
                    'brand' => $product['p_brand'],
                    'price' => $product['p_price'],
                    'quantity' => $quantity,
                    'size' => $size,
                    'image' => $product['p_mimage']
                ]
            ]
        ];
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>