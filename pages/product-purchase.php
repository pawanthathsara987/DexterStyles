<?php
// Include database connection
include "./../php_files/conn.php";

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['user_id']) {
    header("Location: signin.php?redirect=product-purchase.php?p_id=" . (isset($_GET['p_id']) ? (int)$_GET['p_id'] : 21));
    exit;
}

// Get user info
$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['email'];

// Get product ID, quantity and size from URL
$p_id = isset($_GET['p_id']) ? (int)$_GET['p_id'] : 60; // Default to a valid p_id
$quantity = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
$size = isset($_GET['size']) ? $_GET['size'] : (isset($_SESSION['size']) ? $_SESSION['size'] : 'S');

// Store size in session for form submission
$_SESSION['size'] = $size;

if ($quantity < 1) {
    $quantity = 1;
}

// Fetch product details to get price
$sql = "SELECT p_id, p_title, p_price FROM product_details WHERE p_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $p_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Product not found.";
    exit;
}

$product = $result->fetch_assoc();
$p_title = $product['p_title'];
$p_price = $product['p_price'];
$stmt->close();

// Initialize variables
$shipping_cost = 5.00; // Default shipping cost
$item_total = $p_price * $quantity;
$total_price = $item_total + $shipping_cost;

// Store these in session for display
$_SESSION['purchase'] = [
  'p_id' => $p_id,
  'p_title' => $p_title,
  'p_price' => $p_price,
  'quantity' => $quantity,
  'size' => $size, // Add size to session
  'item_total' => $item_total,
  'shipping_cost' => $shipping_cost,
  'total_price' => $total_price
];

$full_name = '';
$phone_number = '';
$province = '';
$district = '';
$city = '';
$street = '';
$landmark = '';
$payment_method = '';
$order_success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $quantity = isset($_POST['qty']) ? (int)$_POST['qty'] : $quantity;
    $size = isset($_POST['size']) ? $_POST['size'] : $_SESSION['size'];
    $_SESSION['full_name'] = $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $_SESSION['phone_no'] = $phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
    $_SESSION['province'] = $province = isset($_POST['province']) ? trim($_POST['province']) : '';
    $_SESSION['district'] = $district = isset($_POST['district']) ? trim($_POST['district']) : '';
    $_SESSION['city'] = $city = isset($_POST['city']) ? trim($_POST['city']) : '';
    $street = isset($_POST['street']) ? trim($_POST['street']) : '';
    $landmark = isset($_POST['landmark']) ? trim($_POST['landmark']) : '';
    $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';

    // Validate required fields
    if (empty($full_name) || empty($phone_number) || empty($province) || 
        empty($district) || empty($city) || empty($street) || empty($payment_method)) {
        $error_message = 'Please fill in all required fields and select a payment method.';
    } else {
        // Validate phone number format (basic validation)
        if (!preg_match('/^\d{10}$/', preg_replace('/[^0-9]/', '', $phone_number))) {
            $error_message = 'Please enter a valid phone number.';
        } else {
            // Create the full shipping address by concatenating fields
            $shipping_address_parts = [$province, $district, $city, $street];
            if (!empty($landmark)) {
                $shipping_address_parts[] = "Landmark: $landmark";
            }
            $_SESSION['full_address'] = $shipping_address = implode(', ', array_filter($shipping_address_parts));

            // Recalculate total price to be safe
            $item_total = $p_price * $quantity;
            $total_price = $item_total + $shipping_cost;

            // Insert into successful_orders
            $insert_sql = "INSERT INTO successful_orders (
                user_id, total_price, quantity, shipping_address, shipping_cost, payment_method, product_id, product_size, order_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param(
                "idisdsis",  // Updated type string: i-d-i-s-d-s-i-s
                $user_id, $total_price, $quantity, $shipping_address, $shipping_cost, $payment_method, $p_id, $size
            );

            if ($insert_stmt->execute()) {
                // Store the order ID in session for the success page
                $_SESSION['last_order_id'] = $conn->insert_id;
                $_SESSION['price_wd'] = $total_price; // Update session variable with calculated total
                $order_success = true;
                // We'll show the popup instead of redirecting immediately
            } else {
                $error_message = 'Error placing order: ' . $conn->error;
            }
            $insert_stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Product Purchase Page</title>
  <link rel="stylesheet" href="./../css/product purchase.css">
  <style>
    /* Success Popup Styles */
    .popup-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      z-index: 1000;
      justify-content: center;
      align-items: center;
    }
    
    .popup-content {
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      text-align: center;
      max-width: 450px;
      width: 90%;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      position: relative;
      animation: popupFadeIn 0.5s ease-out;
    }
    
    @keyframes popupFadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .popup-content h2 {
      color: #4CAF50;
      margin-top: 0;
    }
    
    .popup-content p {
      margin-bottom: 20px;
      font-size: 16px;
      line-height: 1.5;
    }
    
    .success-icon {
      font-size: 60px;
      color: #4CAF50;
      margin-bottom: 20px;
    }
    
    .popup-button {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 10px 20px;
      font-size: 16px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    
    .popup-button:hover {
      background-color: #45a049;
    }

    /* Error message styling */
    .error-message {
      color: #f44336;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #f44336;
      border-radius: 5px;
      background-color: #ffebee;
      text-align: center;
    }
    
    /* Size display */
    .product-info .size-info {
      margin-top: 5px;
      font-weight: bold;
    }
  </style>
</head>
<body>

<div class="container">
  <?php if(isset($error_message)): ?>
    <div class="error-message">
      <?php echo htmlspecialchars($error_message); ?>
    </div>
  <?php endif; ?>

  <!-- Left Form Section -->
  <div class="form-section">
    <h2>Delivery Information</h2>
    <form method="POST" id="orderForm">
      <input type="hidden" name="place_order" value="1">
      <input type="hidden" name="qty" value="<?php echo $quantity; ?>">
      <input type="hidden" name="size" value="<?php echo htmlspecialchars($size); ?>">
      
      <input type="text" name="full_name" placeholder="First and Last Name" value="<?php echo htmlspecialchars($full_name); ?>" required>
      <input type="tel" name="phone_number" placeholder="Phone Number (10 digits)" value="<?php echo htmlspecialchars($phone_number); ?>" 
             pattern="[0-9]{10}" title="Please enter a valid 10 digit phone number" required>

      <select name="province" required>
        <option value="">Choose Province</option>
        <option value="Western" <?php if($province === 'Western') echo 'selected'; ?>>Western</option>
        <option value="Southern" <?php if($province === 'Southern') echo 'selected'; ?>>Southern</option>
        <option value="Central" <?php if($province === 'Central') echo 'selected'; ?>>Central</option>
      </select>
      
      <select name="district" required>
        <option value="">Choose District</option>
        <option value="Colombo" <?php if($district === 'Colombo') echo 'selected'; ?>>Colombo</option>
        <option value="Galle" <?php if($district === 'Galle') echo 'selected'; ?>>Galle</option>
        <option value="Kandy" <?php if($district === 'Kandy') echo 'selected'; ?>>Kandy</option>
      </select>

      <input type="text" name="city" placeholder="City" value="<?php echo htmlspecialchars($city); ?>" required>
      <input type="text" name="street" placeholder="Street Address" value="<?php echo htmlspecialchars($street); ?>" required>
      <input type="text" name="landmark" placeholder="Landmark (Optional)" value="<?php echo htmlspecialchars($landmark); ?>">

      <div class="full-width">
        <label>Select payment method:</label>
        <div class="payment-methods">
          <label>
            <input type="radio" name="payment_method" value="COD" <?php if($payment_method === 'COD') echo 'checked'; ?> required>
            Cash On Delivery (COD)
          </label>
          <label>
            <input type="radio" name="payment_method" value="Card" <?php if($payment_method === 'Card') echo 'checked'; ?> required>
            Visa/Master Card
          </label>
        </div>
      </div>

      <button type="submit" class="btn-pay full-width">Place Order</button>
    </form>
  </div>

  <!-- Right Summary Section -->
  <div class="summary-section">
    <h2>Order Summary</h2>
    <div class="product-info">
      <h3><?php echo htmlspecialchars($p_title); ?></h3>
      <p>Quantity: <?php echo $quantity; ?></p>
      <p>Unit Price: $. <?php echo number_format($p_price, 2); ?></p>
      <p class="size-info">Size: <?php echo htmlspecialchars($size); ?></p>
    </div>
    <hr>
    <p><span>Items Total</span><span>$. <?php echo number_format($item_total, 2); ?></span></p>
    <p><span>Delivery Fee</span><span>$. <?php echo number_format($shipping_cost, 2); ?></span></p>
    <hr>
    <p class="total"><span>Total</span><span>$. <?php echo number_format($total_price, 2); ?></span></p>
    <p style="font-size: 12px;">VAT included, where applicable</p>
  </div>
</div>

<!-- Success Popup -->
<div id="successPopup" class="popup-overlay" <?php if($order_success) echo 'style="display: flex;"'; ?>>
  <div class="popup-content">
    <div class="success-icon">✓</div>
    <h2>Order Placed Successfully!</h2>
    <p>Thank you for your purchase. Your order has been confirmed and is being processed.</p>
    <p>Order #: <?php echo isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : ''; ?></p>
    <button class="popup-button" onclick="window.location.href='./success-purchase.php'">View Order Details</button>
  </div>
</div>

<script>
  // Function to close popup and redirect
  function closePopupAndRedirect() {
    document.getElementById('successPopup').style.display = 'none';
    window.location.href = './success-purchase.php';
  }

  <?php if($order_success): ?>
  // Auto redirect after 5 seconds
  setTimeout(closePopupAndRedirect, 5000);
  <?php endif; ?>
</script>

</body>
</html>