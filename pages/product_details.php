<?php
    // Include database connection 
    include "./../php_files/conn.php";
    
    session_start();

    // Get product ID from URL parameter
    $p_id = isset($_GET['p_id']) ? filter_var($_GET['p_id'], FILTER_VALIDATE_INT) : false;
    $u_pic = isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'blankprofile.jpg';

    if ($p_id) {
        include "./../php_files/conn.php";
        $price_sql = "SELECT p_price FROM product_details WHERE p_id = ?";
        $price_stmt = $conn->prepare($price_sql);
        $price_stmt->bind_param("i", $p_id);
        $price_stmt->execute();
        $price_result = $price_stmt->get_result();
        
        if ($price_result->num_rows > 0) {
            $product_data = $price_result->fetch_assoc();
            $p_price = $product_data['p_price'];
        } else {
            // Handle product not found
            $_SESSION['cart_message'] = '<div class="alert alert-danger">Product not found!</div>';
            // Redirect or exit as appropriate
        }
        $price_stmt->close();
    }

    // Store the selected size in session when "Buy Now" is clicked
    if (isset($_POST['buyNow'])) {
        $_SESSION['size'] = isset($_POST['size']) ? $_POST['size'] : 'S';
    }

    // Check if Add to Cart button was clicked
    if (isset($_POST['addToCart'])) {
        // Check if user is logged in
        if (!isset($_SESSION['user_id']) || !$_SESSION['user_id']) {
            // Redirect to login page with return URL
            header("Location: signin.php?redirect=product_details.php?p_id=" . $p_id);
            exit;
        }
        
        // Get user info and form data
        $user_id = $_SESSION['user_id'];
        $user_email = $_SESSION['email'];
        $quantity = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
        $size = isset($_POST['size']) ? $_POST['size'] : 'S';
        
        // Reopen database connection if it was closed
        if (!isset($conn) || $conn->connect_error) {
            include "./../php_files/conn.php";
        }
        
        // Check if product already in cart
        $check_sql = "SELECT cart_id, quantity FROM cart WHERE user_email = ? AND p_id = ? AND size = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("sis", $user_email, $p_id, $size);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Product already in cart, update quantity
            $cart_item = $check_result->fetch_assoc();
            $new_quantity = $cart_item['quantity'] + $quantity;
            $new_total_price = $new_quantity * $p_price;

            $update_sql = "UPDATE cart SET quantity = ?, total_price = ?, added_at = NOW() WHERE cart_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("idi", $new_quantity, $new_total_price, $cart_item['cart_id']);
            
            if ($update_stmt->execute()) {
                $_SESSION['cart_message'] = '<div class="alert alert-success">Product quantity updated in cart!</div>';
            } else {
                $_SESSION['cart_message'] = '<div class="alert alert-danger">Error updating cart: ' . $conn->error . '</div>';
            }
            $update_stmt->close();
        } else {
            // Product not in cart, insert new item
            $item_total_price = $quantity * $p_price;
            $insert_sql = "INSERT INTO cart (user_email, p_id, quantity, size, total_price, added_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("siisd", $user_email, $p_id, $quantity, $size, $item_total_price);
            
            if ($insert_stmt->execute()) {
                $_SESSION['cart_message'] = '<div class="alert alert-success">Product added to cart!</div>';
            } else {
                $_SESSION['cart_message'] = '<div class="alert alert-danger">Error adding to cart: ' . $conn->error . '</div>';
            }
            $insert_stmt->close();
        }
        
        $check_stmt->close();
        
        // Redirect back to the same page to prevent form resubmission
        header("Location: product_details.php?p_id=" . $p_id);
        exit;
    }

    // Fetch product data from database
    $sql = "SELECT p.*, c.c_name, pi.img_path 
            FROM product_details p 
            JOIN category c ON p.c_id = c.c_id 
            LEFT JOIN product_images pi ON p.p_id = pi.p_id 
            WHERE p.p_id = ? "; 
           
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $p_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if product exists
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        $p_id = $product['p_id'];
        $_SESSION['p_title'] = $product['p_title'];
        $p_description = $product['p_description'];
        $p_brand = $product['p_brand'];
        $p_price = $product['p_price'];
        $p_qty = $product['p_qty'];
        $p_material = $product['p_material'];
        $p_mimage = $product['p_mimage'];
        $catogory_id = $product['c_id'];
        $c_name = $product['c_name'];
    } else {
        echo "Product not found. ($p_id)";
    }

    $stmt->close();

    // Fetch additional product images
    $images = [];
    $sql_images = "SELECT img_path FROM product_images WHERE p_id = ?";
    $stmt_images = $conn->prepare($sql_images);
    if ($stmt_images) {
        $stmt_images->bind_param("i", $p_id);
        $stmt_images->execute();
        $result_images = $stmt_images->get_result();

        while ($row = $result_images->fetch_assoc()) {
            $images[] = $row['img_path'];
        }
        $stmt_images->close();
    }


    
    // Fetch available sizes from the database
    $sql_sizes = "SELECT p_size FROM product_details WHERE p_id = ?";
    $stmt_sizes = $conn->prepare($sql_sizes);
    if ($stmt_sizes) {
        $stmt_sizes->bind_param("i", $p_id);
        $stmt_sizes->execute();
        $result_sizes = $stmt_sizes->get_result();
        
        if ($row_sizes = $result_sizes->fetch_assoc()) {
            // Split the sizes string by the pipe character
            $available_sizes = explode('|', $row_sizes['p_size']);
            // Trim any whitespace from each size
            $available_sizes = array_map('trim', $available_sizes);
        } else {
            // Default sizes if none found
            $available_sizes = ['S', 'M', 'L', 'XL'];
        }
        
        $stmt_sizes->close();
    } else {
        // Default sizes if query fails
        $available_sizes = ['S', 'M', 'L', 'XL'];
    }
    
    // Define all possible sizes for comparison
    $all_sizes = ['S', 'M', 'L', 'XL', 'XXL'];
    

    // Get cart message from session if it exists
    $cart_message = '';
    if (isset($_SESSION['cart_message'])) {
        $cart_message = $_SESSION['cart_message'];
        // Clear the message so it only shows once
        unset($_SESSION['cart_message']);
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_SESSION['p_title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./../css/product-details.css">
    <link rel="stylesheet" href="./../css/home.css">
</head>
<body>
<div class="page-wrapper">
    <header style="background-color: #F0EAD6;">
        <nav class="nav">
            <div class="logo"><a href="./../home.php"><img src="./../img/Logo.png" alt="DexterStyles Logo"></a></div>
            <div class="nav-actions">
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <a href="./view_cart.php" class="cart-icon">🛒</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <a href="./profile.php" class="p-pic">
                    <img src="./../img/profile_photo/<?php echo empty($_SESSION['profile_pic']) ? 'blankprofile.jpg' : $_SESSION['profile_pic']; ?>" alt="Profile">
                </a>
                <?php endif ?>
            </div>
        </nav>
    </header>
    <div class="container mt-4">    
    <!-- Cart message at the top, before the product display -->
    <?php if (!empty($cart_message)): ?>
        <div class="row mb-3">
            <div class="col-12">
                <?php echo $cart_message; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Left column for product images -->
        <div class="col-md-6 mb-4">
            <!-- Main product image -->
            <div class="main-image-container mb-3">
                <img src="./../img/<?php echo $p_mimage; ?>" class="img-fluid" id="mainProductImage" alt="<?php echo $_SESSION['p_title']; ?>">
            </div>
            <!-- Thumbnail gallery -->
            <div class="thumbnail-gallery">
                <div class="row">
                    <?php 
                    // Display main image as first thumbnail
                    echo '<div class="col-3 mb-2"><img src="./../img/' . $p_mimage . '" class="img-thumbnail thumbnail-active" onclick="changeImage(\'./../img/' . $p_mimage. '\', this)"></div>';
                    
                    foreach ($images as $image) {
                        echo '<div class="col-3 mb-2"><img src="./../img/' . $image . '" class="img-thumbnail" onclick="changeImage(\'./../img/' . $image . '\', this)"></div>';
                    }
                    ?>
                </div>    
            </div>
        </div>
        
        <!-- Right column for product details -->
        <div class="col-md-6">
            <div class="product-details">
                <!-- Product title -->
                <h2 class="product-title"><?php echo $p_brand; ?></h2>
                <br>
                <!-- Rest of your product details code -->
                    <!-- Vendor and SKU info -->
                    <div class="product-meta mb-2">
                        <div class="mb-1"><span class="meta-label">Vendor:</span> <?php echo $_SESSION['p_title']; ?></div>
                        <div class="mb-1"><span class="meta-label">SKU:</span> <?php echo $p_id; ?></div>
                        <div><span class="meta-label">Availability:</span> In Stock</div>
                    </div>
                    <br>
                    <!-- Price -->
                    <div class="product-price mb-3">
                        <h3>$ <?php echo number_format($p_price, 2); ?></h3>
                        <input type="hidden" id="product-base-price" value="<?php echo $p_price; ?>"><br>
                    </div> 
                    
                    <!-- Form for adding to cart -->
                    <form method="post" action="">
                        <!-- Size selection -->
                        <!-- Replace the current size selection HTML with this -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div><span class="meta-label">Size:</span> <span id="selected-size"><?php echo !empty($available_sizes) ? $available_sizes[0] : 'S'; ?></span></div>
                            </div>
                            <div class="size-options">
                                <?php foreach($all_sizes as $size): ?>
                                    <?php 
                                    $isAvailable = in_array($size, $available_sizes);
                                    $activeClass = ($size == (!empty($available_sizes) ? $available_sizes[0] : 'S')) ? 'size-active' : '';
                                    $unavailableClass = !$isAvailable ? 'size-unavailable' : '';
                                    ?>
                                    <div class="size-box <?php echo $activeClass . ' ' . $unavailableClass; ?>" 
                                        data-size="<?php echo $size; ?>" 
                                        onclick="selectSize('<?php echo $size; ?>', this)">
                                        <?php echo $size; ?>
                                        <?php if(!$isAvailable): ?>
                                            <div class="size-cross"></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Hidden input to store selected size -->
                            <input type="hidden" name="size" id="size-input" value="<?php echo !empty($available_sizes) ? $available_sizes[0] : 'S'; ?>">
                        </div><br>
                        
                        <!-- Subtotal -->
                        <div class="mb-3">
                            <div><span class="meta-label">Subtotal:</span> <span id="subtotal">$ <?php echo number_format($p_price, 2); ?></span></div>
                        </div>
                        
                        <!-- Quantity -->
                        <div class="mb-4">
                            <label class="meta-label mb-2">Quantity:</label>
                            <div class="d-flex align-items-center">
                                <div class="input-group quantity-selector" style="width: 130px;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="decrementQuantity()">−</button>
                                    <input type="number" name="qty" class="form-control text-center" id="quantity" value="1" min="1" onchange="updateSubtotal()">
                                    <button type="button" class="btn btn-outline-secondary" onclick="incrementQuantity()">+</button>
                                </div>
                                
                                <!-- Action buttons next to quantity -->
                                <div class="ms-3">
                                    <button type="submit" name="addToCart" class="btn btn-primary" id="addToCartBtn">ADD TO CART</button>
                                </div>
                                <div class="ms-2">
                                    <button type="button" class="btn btn-outline-secondary btn-icon" id="shareBtn">
                                        <i class="fa-solid fa-share-nodes"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Buy Now Button -->
                    <div class="mb-4">
                        <a href="javascript:void(0);" onclick="buyNow();" class="btn btn-outline-dark w-100" id="buyNowBtn">BUY IT NOW</a>
                    </div>
                    
                    <!-- Shipping & delivery info -->
                    <div class="shipping-info mb-4">
                        <div class="shipping-option d-flex mb-2">
                            <div class="shipping-icon me-2">
                                <i class="fa-solid fa-truck"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Cash On Delivery</div>
                                <div class="text-muted">Cash On Delivery Island wide</div>
                            </div>
                        </div>
                        
                        <div class="shipping-option d-flex mb-2">
                            <div class="shipping-icon me-2">
                                <i class="fa-solid fa-store"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Exchange From Physical Outlets <i class="fa-solid fa-circle-info small ms-1"></i></div>
                                <a href="#" class="small">Learn More.</a>
                            </div>
                        </div>
                        
                        <div class="shipping-option d-flex">
                            <div class="shipping-icon me-2">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Delivery Within 2 - 3 Business Days <i class="fa-solid fa-circle-info small ms-1"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab navigation for Description and Shipping -->
        <div class="row mt-4">
            <div class="col-12">
                <ul class="nav nav-tabs product-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">Description</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab">Shipping & Return</button>
                    </li>
                </ul>
                <div class="tab-content py-4" id="productTabContent">
                    <div class="tab-pane fade show active" id="description" role="tabpanel">
                        <ul class="product-features">
                            <li>Dress</li>
                            <li>Name : <?php echo $_SESSION['p_title']; ?></li>
                            <li>Brand : <?php echo $p_brand; ?></li>
                            <li>Material : <?php echo $p_material; ?></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="shipping" role="tabpanel">
                        <h5>Shipping Policy</h5>
                        <p>Details about shipping and returns<a href="https://www.bigcommerce.com/articles/ecommerce/privacy-policy/"> policy here.</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer" id="contact">
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

    document.getElementById('shareBtn').addEventListener('click', function() {
        // Share functionality
        if (navigator.share) {
            navigator.share({
                title: '<?php echo $_SESSION["p_title"]; ?>',
                url: window.location.href
            })
            .catch(console.error);
        } else {
            // Fallback for browsers that don't support Web Share API
            alert('Share this link: ' + window.location.href);
        }
    });

    function buyNow() {
        // Get current quantity and size
        const quantity = parseInt(document.getElementById('quantity').value);
        const size = document.getElementById('size-input').value;
        
        // Store size in session before redirecting
        fetch('store-purchase-data.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=buyNow&p_id=<?php echo $p_id; ?>&quantity=' + quantity + '&size=' + size
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                window.location.href = './product-purchase.php?p_id=<?php echo $p_id; ?>&qty=' + quantity + '&size=' + encodeURIComponent(size);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            // Fallback direct redirect if AJAX fails
            window.location.href = './product-purchase.php?p_id=<?php echo $p_id; ?>&qty=' + quantity + '&size=' + encodeURIComponent(size);
        });
    }

    // Function to change the main product image when a thumbnail is clicked
    function changeImage(imagePath, thumbnailElement) {
        document.getElementById('mainProductImage').src = imagePath;
        
        // Remove active class from all thumbnails
        const thumbnails = document.querySelectorAll('.img-thumbnail');
        thumbnails.forEach(thumb => thumb.classList.remove('thumbnail-active'));
        
        // Add active class to clicked thumbnail
        thumbnailElement.classList.add('thumbnail-active');
    }
    
    // Function to select size
    function selectSize(size, element) {
        // First check if this size is available (not crossed out)
        if (element.classList.contains('size-unavailable')) {
            return; // Don't select unavailable sizes
        }
        
        // Remove active class from all sizes
        const sizeBoxes = document.querySelectorAll('.size-box');
        sizeBoxes.forEach(box => box.classList.remove('size-active'));
        
        // Add active class to selected size
        element.classList.add('size-active');
        
        // Update the displayed selected size
        document.getElementById('selected-size').textContent = size;
        
        // Update the hidden input for the form
        document.getElementById('size-input').value = size;
    }

    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        // Handle alerts
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 1s';
                setTimeout(() => {
                    alert.remove();
                }, 1000);
            }, 5000);
        });
    });
    
    // Function to update subtotal based on quantity
    function updateSubtotal() {
        const basePrice = parseFloat(document.getElementById('product-base-price').value);
        const quantity = parseInt(document.getElementById('quantity').value);
        
        if (isNaN(quantity) || quantity < 1) {
            document.getElementById('quantity').value = 1;
            const subtotal = basePrice;
            document.getElementById('subtotal').textContent = '$ ' + subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        } else {
            const subtotal = basePrice * quantity;
            document.getElementById('subtotal').textContent = '$ ' + subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    }
    
    // Functions to handle quantity changes
    function incrementQuantity() {
        const quantityInput = document.getElementById('quantity');
        quantityInput.value = parseInt(quantityInput.value) + 1;
        updateSubtotal();
    }
    
    function decrementQuantity() {
        const quantityInput = document.getElementById('quantity');
        if (parseInt(quantityInput.value) > 1) {
            quantityInput.value = parseInt(quantityInput.value) - 1;
            updateSubtotal();
        }
    }
</script>
</body>
</html>