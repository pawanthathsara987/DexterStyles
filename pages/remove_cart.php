<?php
include "./../php_files/conn.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];

    $sql = "DELETE FROM cart WHERE cart_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cart_id);
    if ($stmt->execute()) {
        echo "Item removed from cart.";
    } else {
        echo "Failed to remove item.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
