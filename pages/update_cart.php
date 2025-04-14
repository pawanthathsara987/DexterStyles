<?php
include 'connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'], $_POST['quantity'])) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);
    if ($quantity < 1) $quantity = 1;

    $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $cart_id);
    
    if ($stmt->execute()) {
        echo "Quantity updated successfully.";
    } else {
        echo "Failed to update quantity.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
