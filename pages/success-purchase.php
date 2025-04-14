<?php
    // Connect to MySQL
    include "./../php_files/conn.php";

    session_start();

    // Initialize variables with defaults to prevent errors
    $order_details = [
        'id' => 'N/A',
        'created_at' => date('Y-m-d H:i:s'),
        'delivery_label' => 'N/A',
        'item_total' => 0,
        'delivery_fee' => 0,
        'total' => 0,
        'full_name' => $_SESSION['full_name'],
        'phone_number' => $_SESSION['phone_no'],
        'full_address' => 'N/A',
        'province' => 'N/A',
        'district' => 'N/A',
        'city' => 'N/A'
    ];

    // Check if user has a valid order
    if(isset($_SESSION['last_order_id'])) {
        // Get order details
        $order_id = $_SESSION['last_order_id'];
        $order_query = $conn->prepare("SELECT * FROM successful_orders WHERE order_id = ?");
        $order_query->bind_param("i", $order_id);
        $order_query->execute();
        $order_result = $order_query->get_result();
        
        if($order_result->num_rows > 0) {
            $order_details = $order_result->fetch_assoc();
        }
    }

    // Set default empty array for purchased items if not in session
    $purchased_items = [];

    // Safely get purchased items from session
    if(isset($_SESSION['purchase']) && isset($_SESSION['purchase']['items']) && is_array($_SESSION['purchase']['items'])) {
        $purchased_items = $_SESSION['purchase']['items'];
    }

    // Generate a tracking number (for display purposes)
    $tracking_number = "DS" . str_pad($order_details['order_id'], 8, "0", STR_PAD_LEFT);

    // Calculate estimated delivery date (3-5 business days from now)
    $orderdate = $order_details['order_date'];
    $delivery_date = date('M d, Y', strtotime($orderdate . '+3 day'));
    $delivery_date_max = date('M d, Y', strtotime($orderdate . '+5 day'));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation - DexterStyles</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3a86ff;
            --success-color: #4CAF50;
            --text-color: #333;
            --light-text: #777;
            --border-color: #e0e0e0;
            --bg-color: #f8f9fa;
            --card-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            --accent-color: #ff6b6b;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            padding: 0;
            margin: 0;
        }
        
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .order-success {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }
        
        .success-header {
            background-color: var(--success-color);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background-color: white;
            color: var(--success-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 40px;
        }
        
        .success-header h1 {
            margin: 10px 0;
            font-weight: 600;
            font-size: 28px;
        }
        
        .success-content {
            padding: 30px;
        }
        
        .order-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 30px;
        }
        
        .summary-box {
            flex: 1;
            min-width: 200px;
        }
        
        .summary-box h3 {
            color: var(--light-text);
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        
        .summary-box p {
            font-size: 16px;
            font-weight: 500;
        }
        
        .tracking {
            background-color: #f0f7ff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .tracking-title {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .tracking-title i {
            color: var(--primary-color);
            margin-right: 10px;
            font-size: 20px;
        }
        
        .tracking-title h3 {
            font-weight: 600;
            font-size: 18px;
        }
        
        .tracking-info {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .tracking-number, .delivery-estimate {
            margin-bottom: 10px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin: 20px 0 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .product-list {
            margin-bottom: 30px;
        }
        
        .product-item {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            border-radius: 6px;
            overflow: hidden;
            background-color: #f5f5f5;
            margin-right: 15px;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .product-details {
            flex-grow: 1;
        }
        
        .product-name {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .product-meta {
            color: var(--light-text);
            font-size: 14px;
        }
        
        .product-price {
            text-align: right;
            min-width: 100px;
            font-weight: 600;
        }
        
        .order-totals {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .grand-total {
            font-weight: 700;
            font-size: 18px;
            border-top: 2px solid var(--border-color);
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .customer-details {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .detail-block {
            flex: 1;
            min-width: 250px;
        }
        
        .detail-block h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .detail-block p {
            margin-bottom: 5px;
            color: var(--light-text);
        }
        
        .actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2a75e6;
        }
        
        .btn-outline {
            background-color: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }
        
        .btn-outline:hover {
            background-color: rgba(58, 134, 255, 0.1);
        }
        
        .footer-note {
            text-align: center;
            padding: 20px 0;
            color: var(--light-text);
            font-size: 14px;
        }
        
        .footer-note a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        /* Status Progress Bar */
        .order-status {
            margin: 30px 0;
        }
        
        .status-bar {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 30px;
        }
        
        .status-bar::before {
            content: '';
            position: absolute;
            top: 14px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: var(--border-color);
            z-index: 1;
        }
        
        .status-step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: var(--success-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
        }
        
        .status-step.current {
            background-color: var(--success-color);
        }
        
        .status-step.pending {
            background-color: var(--light-text);
        }
        
        .status-labels {
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
        
        .status-label {
            width: 80px;
            font-size: 12px;
            font-weight: 500;
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
                padding: 0 15px;
            }
            
            .success-header {
                padding: 20px;
            }
            
            .success-icon {
                width: 60px;
                height: 60px;
                font-size: 30px;
            }
            
            .success-header h1 {
                font-size: 22px;
            }
            
            .success-content {
                padding: 20px;
            }
            
            .order-summary, .customer-details {
                flex-direction: column;
                gap: 15px;
            }
            
            .status-labels {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
            
            .status-label {
                width: 100%;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="order-success">
            <div class="success-header">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h1>Thank You for Your Order!</h1>
                <p>Your order has been placed and is being processed</p>
            </div>
            
            <div class="success-content">
                <div class="order-summary">
                    <div class="summary-box">
                        <h3>Order Number</h3>
                        <p>#<?php echo htmlspecialchars($order_details['order_id']); ?></p>
                    </div>
                    <div class="summary-box">
                        <h3>Order Date</h3>
                        <p><?php echo date('M d, Y', strtotime($order_details['order_date'])); ?></p>
                    </div>
                    <div class="summary-box">
                        <h3>Payment Method</h3>
                        <p><?php echo htmlspecialchars($order_details['payment_method']); ?></p>
                    </div>
                    <div class="summary-box">
                        <h3>Total Amount</h3>
                        <p>$. <?php echo number_format(floatval($order_details['total_price']), 2); ?></p>
                    </div>
                </div>
                
                <div class="tracking">
                    <div class="tracking-title">
                        <i class="fas fa-truck"></i>
                        <h3>Shipping Information</h3>
                    </div>
                    <div class="tracking-info">
                        <div class="tracking-number">
                            <p><strong>Tracking Number:</strong></p>
                            <p><?php echo $tracking_number; ?></p>
                        </div>
                        <div class="delivery-estimate">
                            <p><strong>Estimated Delivery:</strong></p>
                            <p><?php echo $delivery_date; ?> - <?php echo $delivery_date_max; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="order-status">
                    <h3 class="section-title">Order Status</h3>
                    <div class="status-bar">
                        <div class="status-step current">
                            <i class="fas fa-check fa-sm"></i>
                        </div>
                        <div class="status-step current">
                            <i class="fas fa-check fa-sm"></i>
                        </div>
                        <div class="status-step pending">
                            <i class="fas fa-hourglass-half fa-sm"></i>
                        </div>
                        <div class="status-step pending">
                            <i class="fas fa-truck fa-sm"></i>
                        </div>
                        <div class="status-step pending">
                            <i class="fas fa-box-open fa-sm"></i>
                        </div>
                    </div>
                    <div class="status-labels">
                        <div class="status-label">Order Placed</div>
                        <div class="status-label">Payment Confirmed</div>
                        <div class="status-label">Processing</div>
                        <div class="status-label">Shipped</div>
                        <div class="status-label">Delivered</div>
                    </div>
                </div>
                
                <h3 class="section-title">Order Details</h3>
                <div class="product-list">
                    <?php if(empty($purchased_items)): ?>
                        <p>No products found in your order.</p>
                    <?php else: ?>
                        <?php foreach($purchased_items as $item): ?>
                            <div class="product-item">
                                <div class="product-image">
                                    <?php if(isset($item['image']) && !empty($item['image'])): ?>
                                        <img src="./../img/<?php echo htmlspecialchars($item['image']); ?>" alt="Product Image">
                                    <?php else: ?>
                                        <div class="no-image">No Image</div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-details">
                                    <div class="product-name"><?php echo isset($item['name']) ? htmlspecialchars($item['name']) : 'Product'; ?></div>
                                    <div class="product-meta">
                                        <?php if(isset($item['brand'])): ?>
                                            <span><?php echo htmlspecialchars($item['brand']); ?></span> •
                                        <?php endif; ?>
                                        <span>Size: <?php echo isset($item['size']) ? htmlspecialchars($item['size']) : 'N/A'; ?></span> •
                                        <span>Qty: <?php echo isset($item['quantity']) ? intval($item['quantity']) : 1; ?></span>
                                    </div>
                                </div>
                                <div class="product-price">
                                    $. <?php 
                                        $price = isset($item['price']) ? floatval($item['price']) : 0;
                                        $qty = isset($item['quantity']) ? intval($item['quantity']) : 1;
                                        echo number_format($price * $qty, 2); 
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="order-totals">
                    <div class="total-row">
                        <div>Items Subtotal:</div>
                        <div>$. <?php echo number_format(floatval($order_details['total_price'] - $order_details['shipping_cost']), 2); ?></div>
                    </div>
                    <div class="total-row">
                        <div>Shipping Fee:</div>
                        <div>$. <?php echo number_format(floatval($order_details['shipping_cost']), 2); ?></div>
                    </div>
                    <div class="total-row grand-total">
                        <div>Grand Total:</div>
                        <div>$. <?php echo number_format(floatval($order_details['total_price']), 2); ?></div>
                    </div>
                </div>
                
                <h3 class="section-title">Customer Details</h3>
                <div class="customer-details">
                    <div class="detail-block">
                        <h3>Shipping Address</h3>
                        <p><?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                        <p><?php echo htmlspecialchars($_SESSION['full_address']); ?></p>
                        <p><?php echo htmlspecialchars($_SESSION['city'] . ', ' . $_SESSION['district']); ?></p>
                        <p><?php echo htmlspecialchars($_SESSION['province']); ?></p>
                    </div>
                    <div class="detail-block">
                        <h3>Contact Information</h3>
                        <p>Phone: <?php echo htmlspecialchars($_SESSION['phone_no']); ?></p>
                        <?php if(isset($_SESSION['email'])): ?>
                            <p>Email: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="actions">
                    <a href="./product.php" class="btn btn-primary"><i class="fas fa-shopping-bag"></i> Continue Shopping</a>
                    <a href="./profile.php" class="btn btn-outline"><i class="fas fa-user"></i> My Account</a>
                </div>
            </div>
        </div>
        
        <div class="footer-note">
            <p>An order confirmation has been sent to your email address. If you have any questions, please contact our <a href="./contact.php">customer support</a>.</p>
        </div>
    </div>
    
    <?php
    // Uncomment the line below if you want to clear the cart after successful purchase
    // unset($_SESSION['purchase']);
    ?>
    
    <script>
        // Auto-fade the success message after 2 seconds
        setTimeout(function() {
            document.querySelector('.success-header').style.transition = 'all 0.5s ease';
            document.querySelector('.success-header').style.height = 'auto';
        }, 2000);
        
        // Print functionality
        function printOrderDetails() {
            window.print();
        }
    </script>
</body>
</html>