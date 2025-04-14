<?php
session_start();
include "./../php_files/conn.php";

// Get user email from session
$email = $_SESSION['email'] ?? '';

// Check if user is logged in
if (empty($email)) {
    header("Location: login.php?redirect=cart.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle quantity update or item removal
    if (isset($_POST['update_quantity'])) {
        $cart_id = $_POST['cart_id'];
        $quantity = $_POST['quantity'];

        // Validate that quantity is a positive number
        if ($quantity > 0) {
            // Get the price of the product
            $priceSql = "SELECT p.p_price FROM cart c JOIN product_details p ON c.p_id = p.p_id WHERE c.cart_id = ?";
            $priceStmt = $conn->prepare($priceSql);
            $priceStmt->bind_param("i", $cart_id);
            $priceStmt->execute();
            $priceResult = $priceStmt->get_result();
            $priceRow = $priceResult->fetch_assoc();
            $price = $priceRow['p_price'];
            
            // Calculate the total price for this item
            $total_price = $quantity * $price;
            
            // Update quantity and total price in the cart
            $sql = "UPDATE cart SET quantity = ?, total_price = ? WHERE cart_id = ? AND user_email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("idis", $quantity, $total_price, $cart_id, $email);
            $stmt->execute();
        }
    } elseif (isset($_POST['remove_item'])) {
        $cart_id = $_POST['cart_id'];

        // Remove item from the cart
        $sql = "DELETE FROM cart WHERE cart_id = ? AND user_email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $cart_id, $email);
        $stmt->execute();
    }

    // Redirect to reload the page after update/removal
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch cart items
$sql = "SELECT c.cart_id, c.quantity, c.size, p.p_id, p.p_title, p.p_price, p.p_mimage,
        (c.quantity * p.p_price) as item_total 
        FROM cart c 
        JOIN product_details p ON c.p_id = p.p_id 
        WHERE c.user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total
$total = 0;
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $total += $row['item_total'];
    $cart_items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./../css/home.css">
    <style>
        .page-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            flex: 1;
            display: flex;
            justify-content: center;
        }
        .cart-container {
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
        }
        .cart-container .card-body {
            text-align: center;
        }
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .product-img:hover {
            transform: scale(1.1);
        }
        .cart-item {
            display: flex;
            align-items: center;
        }
        .cart-item a {
            text-decoration: none;
            color: #333;
        }
        .cart-item a:hover .product-title {
            color: #007bff;
            text-decoration: underline;
        }
        .quantity-control {
            display: flex;
            align-items: center;
        }
        .quantity-btn {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            cursor: pointer;
        }
        .form-control-qty {
            width: 50px;
            text-align: center;
            border-radius: 0;
        }
        @media (max-width: 576px) {
            .cart-container {
                max-width: 100%;
                padding: 0 15px;
            }
            .table {
                font-size: 0.9rem;
            }
            .product-img {
                width: 50px;
                height: 50px;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <header style="background-color: #F0EAD6;">
            <nav class="nav">
                <div class="logo"><a href="./../home.php"><img src="./../img/Logo.png" alt="DexterStyles Logo"></a></div>
                <ul class="nav-menu">
                    <li><a href="./../home.php">Home</a></li>
                    <li><a href="./product.php">Shop</a></li>
                    <li><a href="./aboutus.php">About</a></li>
                    <li><a href="./contact.php">Contact</a></li>
                </ul>
                <div class="nav-actions">
                    <a href="./view_cart.php" class="cart-icon">🛒</a>
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <a href="./profile.php" class="p-pic">
                        <img src="./../img/profile_photo/<?php echo empty($_SESSION['profile_pic']) ? 'blankprofile.jpg' : $_SESSION['profile_pic']; ?>" alt="Profile">
                    </a>
                    <?php else: ?>
                        <a href="./signin.php" class="signin-btn">Sign In</a>
                    <?php endif; ?>
                </div>
            </nav>
        </header>

        <div class="container mt-5">
            <div class="row">
                <!-- Cart Items Section -->
                <div class="col-12">
                    <div class="cart-container card shadow-sm">
                        <div class="card-body">
                            <h1 class="text-center mb-4">Your Cart</h1>

                            <?php if (count($cart_items) > 0): ?>
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Size</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cart_items as $item): ?>
                                            <tr class="cart-item-row" data-price="<?php echo $item['p_price']; ?>" data-cart-id="<?php echo $item['cart_id']; ?>">
                                                <td class="cart-item">
                                                    <a href="./product_details.php?p_id=<?php echo htmlspecialchars($item['p_id']); ?>">
                                                        <img src="./../img/<?php echo htmlspecialchars($item['p_mimage']); ?>" class="product-img me-2" alt="<?php echo htmlspecialchars($item['p_title']); ?>">
                                                        <span class="product-title"><?php echo htmlspecialchars($item['p_title']); ?></span>
                                                    </a>
                                                </td>
                                                <td><?php echo htmlspecialchars($item['size']); ?></td>
                                                <td>$<?php echo number_format($item['p_price'], 2); ?></td>
                                                <td>
                                                    <form action="" method="POST" class="d-flex align-items-center">
                                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                                        <div class="quantity-control">
                                                            <button type="button" class="quantity-btn" onclick="decrementQty(this)">-</button>
                                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="form-control form-control-qty quantity-input" onchange="updateItemPrice(this)" readonly>
                                                            <button type="button" class="quantity-btn" onclick="incrementQty(this)">+</button>
                                                        </div>
                                                        <button type="submit" name="update_quantity" class="btn btn-sm btn-outline-primary ms-2 update-btn">
                                                            <i class="fas fa-sync-alt"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                                <td class="item-total">$<?php echo number_format($item['item_total'], 2); ?></td>
                                                <td>
                                                    <form action="" method="POST">
                                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                                        <button type="submit" name="remove_item" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
                                    <p class="lead">Your cart is empty</p>
                                    <a href="./product.php" class="btn btn-primary mt-3">Continue Shopping</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <footer class="footer mt-5" id="contact">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>DexterStyles</h3>
                    <p>123 Fashion St, Style City, SC 12345</p>
                    <p>Email: info@dexterstyles.com</p>
                    <p>Phone: (555) 123-4567</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="./../home.php">Home</a></li>
                        <li><a href="./product.php">Shop</a></li>
                        <li><a href="./aboutus.php">About</a></li>
                        <li><a href="./contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Follow Us</h3>
                    <div class="social-links">
                        <a href="https://www.facebook.com">Facebook</a>
                        <a href="https://www.instagram.com">Instagram</a>
                        <a href="https://www.twitter.com">Twitter</a>
                    </div>
                </div>
            </div>
            <p class="footer-bottom">© 2025 DexterStyles. All rights reserved.</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function incrementQty(button) {
            const input = button.previousElementSibling;
            input.value = parseInt(input.value) + 1;
            updateItemPrice(input);
        }
        
        function decrementQty(button) {
            const input = button.nextElementSibling;
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                updateItemPrice(input);
            }
        }
        
        function updateItemPrice(inputElement) {
            const row = inputElement.closest('.cart-item-row');
            const price = parseFloat(row.getAttribute('data-price'));
            const quantity = parseInt(inputElement.value);
            const itemTotal = price * quantity;
            row.querySelector('.item-total').textContent = '$' + itemTotal.toFixed(2);
            updateCartTotals();
            updateDatabase(row, quantity);
        }
        
        function updateDatabase(row, quantity) {
            const cartId = row.getAttribute('data-cart-id');
            const formData = new FormData();
            formData.append('cart_id', cartId);
            formData.append('quantity', quantity);
            formData.append('update_ajax', 'true');
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_cart_ajax.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log('Database updated successfully');
                } else {
                    console.error('Error updating database');
                }
            };
            xhr.send(formData);
        }
        
        function updateCartTotals() {
            // Optional: Add total update logic if needed
        }
    </script>
</body>
</html>