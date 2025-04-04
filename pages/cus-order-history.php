<?php
// Initialize the session
session_start();
include './../php_files/conn.php';

// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: signin.php");
    exit;
}

// Get the current user ID from session
$user_id = $_SESSION["user_id"];

// Query to fetch all successful orders for the current user
$sql = "SELECT so.order_id, so.total_price, so.quantity, so.shipping_address, 
               so.shipping_cost, pd.p_title, pd.p_price, pd.p_mimage
        FROM successful_orders so
        JOIN product_details pd ON so.product_id = pd.p_id
        WHERE so.user_id = ?
        ORDER BY so.order_id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Dexter Styles</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="./../css/cus-order-history.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>My Orders - Dexter Styles</h1>
        </div>
    </header>

    <div class="container">
        <a href="./../home.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Shopping</a>
        
        <div class="orders-container">
            <h2>Order History</h2>
            
            <?php
            if ($result->num_rows > 0) {
                // Output each order
                while($row = $result->fetch_assoc()) {
            ?>
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">Order #<?php echo $row["order_id"]; ?></span>
                    </div>
                    <div class="order-details">
                        <img src="images/<?php echo $row["p_mimage"]; ?>" alt="<?php echo $row["p_title"]; ?>" class="product-image">
                        
                        <div class="product-info">
                            <h3><?php echo $row["p_title"]; ?></h3>
                            <p>Unit Price: <span class="price">$<?php echo number_format($row["p_price"], 2); ?></span></p>
                            <p>Quantity: <?php echo $row["quantity"]; ?></p>
                            <p>Total Price: <span class="price">$<?php echo number_format($row["total_price"], 2); ?></span></p>
                            <p>Shipping Cost: $<?php echo number_format($row["shipping_cost"], 2); ?></p>
                        </div>
                        
                        <div class="shipping-info">
                            <h4>Shipping Address:</h4>
                            <p><?php echo nl2br(htmlspecialchars($row["shipping_address"])); ?></p>
                        </div>
                    </div>
                </div>
            <?php
                }
            } else {
            ?>
                <div class="no-orders">
                    <i class="fas fa-shopping-bag" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                    <h3>You haven't placed any orders yet</h3>
                    <p>Browse our collection and place your first order today!</p>
                    <a href="products.php" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;">Shop Now</a>
                </div>
            <?php
            }
            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>