<?php
include "./../php_files/conn.php";

// Function to handle profile image upload
function uploadProfileImage($user_id, $conn) {
    $upload_message = '';
    
    if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowed_ext = array("jpg", "jpeg", "png", "gif");
        $file_name = $_FILES['profile_pic']['name'];
        $file_size = $_FILES['profile_pic']['size'];
        $file_tmp = $_FILES['profile_pic']['tmp_name'];
        $tmp = explode('.', $file_name);
        $file_ext = strtolower(end($tmp));
        
        // Validate file extension
        if(in_array($file_ext, $allowed_ext) === false) {
            return '<div class="alert alert-danger" role="alert">Extension not allowed. Please choose a JPG, JPEG, PNG or GIF file.</div>';
        }
        
        // Validate file size (max 5MB)
        if($file_size > 5242880) {
            return '<div class="alert alert-danger" role="alert">File size must be less than 5MB</div>';
        }
        
        // Generate a unique file name to avoid overwriting
        $new_file_name = "profile_" . $user_id . "_" . time() . "." . $file_ext;
        $upload_path = "./../img/profile_photo/" . $new_file_name;
        
        if(move_uploaded_file($file_tmp, $upload_path)) {
            // Update database with new profile picture
            $sql_update_pic = "UPDATE user SET profile_pic = ? WHERE id = ?";
            $stmt_update_pic = $conn->prepare($sql_update_pic);
            $stmt_update_pic->bind_param("si", $new_file_name, $user_id);
            
            if($stmt_update_pic->execute()) {
                // $_SESSION['profile_pic'] = $new_file_name;
                return '<div class="alert alert-success" role="alert">Profile picture updated successfully!</div>';
            } else {
                return '<div class="alert alert-danger" role="alert">Error updating profile picture in database!</div>';
            }
            
            $stmt_update_pic->close();
        } else {
            return '<div class="alert alert-danger" role="alert">Failed to upload image!</div>';
        }
    }
    
    return $upload_message;
}

session_start();

$password_message = '';
$upload_message = '';

// Get user ID from session or GET parameter
$u_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Validate user ID to prevent SQL injection
if ($u_id <= 0) {
    echo "Invalid user ID";
    header("Location: ./signin.php");
    exit;
}

// Handle profile image upload - ONLY PROCESS WHEN FORM IS SUBMITTED
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_profile_pic'])) {
    $upload_message = uploadProfileImage($u_id, $conn);
}

// Combined query to get user and order details
$sql = "SELECT u.*, o.order_id, o.total_price, o.shipping_address, 
                       o.shipping_cost, o.product_id,
                       p.p_title, p.p_mimage
                FROM user u
                LEFT JOIN successful_orders o ON u.id = o.user_id
                LEFT JOIN product_details p ON o.product_id = p.p_id
                WHERE u.id = ?";

   $stmt = $conn->prepare($sql);
   $stmt->bind_param("i", $u_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        $u_id = $user['id'];
        $u_name = $user['name'];
        $u_email = $user['email'];
        $u_password = $user['password'];
        $u_mobile_no = $user['mobile_no'];
        $u_pic = $user['profile_pic'];
        $_SESSION['profile_pic'] = $u_pic;
        $total_price = $user['total_price'];
        $shipping_address = $user['shipping_address'];
        $shipping_cost = $user['shipping_cost'];
        $p_id = $user['product_id'];
        $p_title = $user['p_title'];
        $p_mimage = $user['p_mimage'];
    } else {
        echo "User not found.";
        exit;
    }

    $stmt->close();
 

$message = ''; // Variable to store the success or error message
$shipping_address_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_mobile = $_POST['mobile_no'];
    
    // Check if new password is provided
    if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate password
        if (strlen($new_password) < 8) {
            $password_message = '<div class="alert alert-danger" role="alert">Password must be at least 8 characters long!</div>';
        } elseif ($new_password !== $confirm_password) {
            $password_message = '<div class="alert alert-danger" role="alert">Passwords do not match!</div>';
        } else {
            // Hash password for security
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update user with new password
            $sql_update = "UPDATE user SET name=?, email=?, password=?, mobile_no=? WHERE id=?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ssssi", $new_name, $new_email, $hashed_password, $new_mobile, $u_id);
        }
    } else {
        // Update user without changing password
        $sql_update = "UPDATE user SET name=?, email=?, mobile_no=? WHERE id=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $new_name, $new_email, $new_mobile, $u_id);
    }

    if ($stmt_update->execute()) {
        $message = '<div class="alert alert-success" role="alert">Profile updated successfully!</div>';
        // Update local variables with new values
        $u_name = $new_name;
        $u_email = $new_email;
        $u_mobile_no = $new_mobile;
    } else {
        $message = '<div class="alert alert-danger" role="alert">Error updating profile!</div>';
    }

    $stmt_update->close();
}

// Get user orders
$orders_sql = "SELECT o.*, p.p_title, p.p_mimage 
               FROM successful_orders o 
               JOIN product_details p ON o.product_id = p.p_id 
               WHERE o.user_id = ?";

$stmt_orders = $conn->prepare($orders_sql);
$stmt_orders->bind_param("i", $u_id);
$stmt_orders->execute();
$orders_result = $stmt_orders->get_result();
$orders = [];

while ($order = $orders_result->fetch_assoc()) {
    $orders[] = $order;
}

$stmt_orders->close();


// Update shipping address
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_address'])) {
    $new_address = $_POST['shipping_address'];
    
    $sql_update_address = "UPDATE successful_orders SET shipping_address=? WHERE user_id=?";
    $stmt_update_address = $conn->prepare($sql_update_address);
    $stmt_update_address->bind_param("si", $new_address, $u_id);

    if ($stmt_update_address->execute()) {
        $shipping_address_message= '<div class="alert alert-success" role="alert">Shipping address updated successfully!</div>';
        $shipping_address = $new_address;
    } else {
        $shipping_address_message = '<div class="alert alert-danger" role="alert">Error updating shipping address!</div>';
    }

    $stmt_update_address->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    // Start transaction for data consistency
    $conn->begin_transaction();
    
    try {
        // First delete related records from successful_orders
        $sql_delete_orders = "DELETE FROM successful_orders WHERE user_id=?";
        $stmt_delete_orders = $conn->prepare($sql_delete_orders);
        $stmt_delete_orders->bind_param("i", $u_id);
        $stmt_delete_orders->execute();
        $stmt_delete_orders->close();

        //Second delete related records from cart
        $sql_delete_cart = "DELETE FROM cart WHERE user_email=?";
        $stmt_delete_cart = $conn->prepare($sql_delete_cart);
        $stmt_delete_cart->bind_param("i", $u_email);
        $stmt_delete_cart->execute();
        $stmt_delete_cart->close();
        
        // Then delete the user
        $sql_delete = "DELETE FROM user WHERE id=?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $u_id);
        $stmt_delete->execute();
        $stmt_delete->close();
        
        // Commit the transaction
        $conn->commit();
        
        // Clear session
        session_destroy();
        
        // Redirect to signup page
        header("Location: ./../home.php");
        exit;
    } catch (Exception $e) {
        // Rollback in case of error
        $conn->rollback();
        echo "Error deleting account: " . $e->getMessage();
    }
}

$conn->close();

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clothing E-Commerce Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./../css/profile.css">
    <link rel="stylesheet" href="./../css/home.css">
</head>
<body>
    <header style="background-color: #F0EAD6;">
        <nav class="nav">
            <div class="logo"><a href="./../home.php"><img src="./../img/Logo.png" alt="DexterStyles Logo"></a></div>
            <div class="nav-actions">
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <a href="./profile.php" class="p-pic">
                    <img src="./../img/profile_photo/<?php echo empty($u_pic) ? 'blankprofile.jpg' : $u_pic; ?>" alt="Profile">
                </a>
                <?php endif ?>

            </div>
        </nav>
    </header>

    <div class="main-container">
        <div class="profile-sidebar">
            <div class="text-center">
            <img src="./../img/profile_photo/<?php echo empty($u_pic) ? 'blankprofile.jpg' : $u_pic; ?>" alt="Profile Picture" class="profile-pic" id="pro">
                <h5 class="mt-3" id="ad"><?php echo $u_name; ?></h5>
                <p class="text-white-50" id="ae"><?php echo $u_email; ?></p>
            </div>
            <div class="menu-item active" onclick="showSection('personal-info')">
                <i class="fas fa-user"></i> Personal Information
            </div> 
            <div class="menu-item" onclick="showSection('orders')">
                <i class="fas fa-shopping-bag"></i> Orders
            </div> 
            <div class="menu-item" onclick="showSection('shipping')">
                <i class="fas fa-truck"></i> Shipping Address
            </div>
            <div class="menu-item" onclick="showSection('payment')">
                <i class="fas fa-credit-card"></i> Payment
            </div>
            <div class="menu-item" onclick="showSection('settings')">
                <i class="fas fa-cog"></i> Settings
            </div>
            <div class="menu-item text-danger" onclick="showLogoutPopup()" id="my5">
                <i class="fas fa-sign-out-alt"></i> Log Out
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="profile-content">
            <!-- Personal Information Section  -->
            <div id="personal-info" class="content-section">
                <h3 class="mb-4"><i class="fas fa-user-circle me-2"></i>Personal Information</h3>
                
                <!-- Profile Image Upload Form -->
                <div class="mb-4">
                    <div class="upload-message-container">
                        <?php echo $upload_message; ?>
                    </div>
                    <form method="POST" action="" enctype="multipart/form-data" class="text-center mb-4">
                        <div class="profile-pic-container text-center mb-3">
                            <!-- Make the image itself clickable to trigger file upload -->
                            <label for="profile_pic" style="cursor: pointer; display: inline-block;">
                                <img src="./../img/profile_photo/<?php echo empty($u_pic) ? 'blankprofile.jpg' : $u_pic; ?>" alt="Profile Picture" class="profile-pic-large">
                                <div class="mt-2 text-primary small">
                                    <i class="fas fa-camera me-1"></i>Click to change
                                </div>
                            </label>
                            <input type="file" id="profile_pic" name="profile_pic" class="form-control d-none" accept="image/*" onchange="showPreview(this)">
                        </div>
                        
                        <div id="image-preview-container" class="mt-3 d-none">
                            <img id="image-preview" src="#" alt="Preview" class="img-thumbnail mb-2" style="max-height: 200px;">
                            <div class="d-flex justify-content-center gap-2">
                                <button type="submit" name="upload_profile_pic" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i>Save Image
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="cancelImageUpload()">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                  
                <!-- Display success/error message -->
                <div id="message-container">
                    <?php echo $message; ?>
                </div>
                <div id="password-message-container">
                    <?php echo $password_message; ?>
                </div>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $u_name; ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $u_email; ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="newPassword" name="new_password" 
                                minlength="8" placeholder="Leave blank to keep current password">
                            <span class="input-group-text password-toggle" onclick="togglePasswordVisibility('newPassword', 'newToggleIcon')">
                                <i class="fas fa-eye" id="newToggleIcon"></i>
                            </span>
                        </div>
                        <small class="form-text text-muted">Password must be at least 8 characters long</small>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password"
                                placeholder="Confirm your new password">
                            <span class="input-group-text password-toggle" onclick="togglePasswordVisibility('confirmPassword', 'confirmToggleIcon')">
                                <i class="fas fa-eye" id="confirmToggleIcon"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="mobile_no" class="form-label">Mobile Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" class="form-control" id="mobile_no" name="mobile_no" value="<?php echo $u_mobile_no; ?>">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" name="save" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                        <button type="button" name="cancel" class="cancel-btn" onclick="cancelEdit()">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                    </div>
                </form>
            </div>
                
            <!-- Orders Section -->
            <div id="orders" class="content-section" style="display:none;">
                <h3 class="text-center mb-4"><i class="fas fa-shopping-bag me-2"></i>Your Orders</h3>
                <?php if (count($orders) > 0): ?>
                    <div class="row">
                        <?php foreach ($orders as $order): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card order-card" onclick="window.location.href='product_details.php?p_id=<?php echo $order['product_id']; ?>'">
                                    <div class="card-body">
                                        <!-- Image centered at the top -->
                                        <div class="text-center mb-3">
                                            <?php if (!empty($order['p_mimage'])): ?>
                                                <img src="./../img/<?php echo htmlspecialchars($order['p_mimage']); ?>" alt="Product" class="img-fluid">
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Order information below the image -->
                                        <div class="text-center">
                                            <h5 class="card-title"><?php echo $order['p_title']; ?></h5>
                                            <div class="badge bg-info mb-2">Order ID: <?php echo $order['order_id']; ?></div>
                                            <div class="d-flex justify-content-center gap-3">
                                                <div class="badge bg-success">Price: $<?php echo $order['total_price']; ?></div>
                                                <div class="badge bg-secondary">Shipping: $<?php echo $order['shipping_cost']; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-4x mb-3 text-muted"></i>
                        <p class="lead">No orders found. Start shopping to see your orders here!</p>
                        <a href="./product.php" class="btn btn-primary mt-3">Go Shopping</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Shipping Address Section -->
            <div id="shipping" class="content-section" style="display:none;">
                <h3 class="mb-4"><i class="fas fa-truck me-2"></i>Shipping Address</h3>

                <!-- Display success/error message -->
                <div id="shipping_address_message-container">
                    <?php echo $shipping_address_message; ?>
                </div>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="shipping_address" class="form-label">
                            <strong><?php echo $u_name; ?>'s</strong> Shipping Address:
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" rows="4"><?php echo $shipping_address; ?></textarea>
                        </div>
                    </div>
                    <button type="submit" name="save_address" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Address
                    </button>
                </form>
            </div>

            <!-- Payment Methods Section -->
            <div id="payment" class="content-section" style="display:none;">
                <h3 class="mb-4"><i class="fas fa-credit-card me-2"></i>Payment Methods</h3>
                <div class="payment-card">
                    <div class="mb-4">
                        <label class="form-label text-muted"><i class="fas fa-credit-card me-2"></i>Card Number</label>
                        <h5 class="mb-0">XXXX-XXXX-XXXX-XXXX</h5>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted"><i class="fas fa-money-check me-2"></i>Payment Type</label>
                        <h5 class="mb-0">Online</h5>
                    </div>

                    <div>
                        <label class="form-label text-muted"><i class="fas fa-wallet me-2"></i>Payment Methods</label>
                        <div class="payment-methods d-flex align-items-center mt-2">
                            <img src="./../img/visa.png" alt="Visa" class="img-fluid">
                            <img src="./../img/paypal.png" alt="PayPal" class="img-fluid">
                            <img src="./../img/master.png" alt="Mastercard" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
                
            <!-- Settings Section -->
            <div id="settings" class="content-section" style="display:none;">
                <h3 class="mb-4"><i class="fas fa-cog me-2"></i>Settings</h3>
                
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Notifications</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="emailNotif" checked>
                            <label class="form-check-label" for="emailNotif">
                                <i class="fas fa-envelope me-2"></i>Email Notifications
                            </label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="smsNotif">
                            <label class="form-check-label" for="smsNotif">
                                <i class="fas fa-sms me-2"></i>SMS Notifications
                            </label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="popupNotif" checked>
                            <label class="form-check-label" for="popupNotif">
                                <i class="fas fa-bell me-2"></i>Popup Notifications
                            </label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="orderNotif" checked>
                            <label class="form-check-label" for="orderNotif">
                                <i class="fas fa-shopping-cart me-2"></i>Order Updates
                            </label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="promoNotif">
                            <label class="form-check-label" for="promoNotif">
                                <i class="fas fa-tag me-2"></i>Promotional Offers
                            </label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="newsletterNotif">
                            <label class="form-check-label" for="newsletterNotif">
                                <i class="fas fa-newspaper me-2"></i>Newsletter
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="danger-zone mt-4">
                    <h4 class="text-danger mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h4>
                    <p class="text-muted">Once you delete your account, there is no going back. Please be certain.</p>
                    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                        <button type="submit" name="delete" class="btn btn-danger">
                            <i class="fas fa-trash-alt me-2"></i>Delete Account
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Logout Overlay (Hidden by default) -->
            <div id="logoutOverlay" class="logout-overlay position-fixed top-0 start-0 w-100 h-100 d-none justify-content-center align-items-center">
                <div class="logout-popup">
                    <img src="./../img/profile_photo/<?php echo empty($u_pic) ? 'blankprofile.jpg' : $u_pic; ?>" alt="Profile Picture" class="rounded-circle mb-3">
                    <h5><?php echo $u_name; ?></h5>
                    <p class="text-muted">Are you sure you want to log out?</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button id="confirmLogout" onclick="logoutUser()">
                            <i class="fas fa-sign-out-alt me-2"></i>Confirm Logout
                        </button>
                        <button id="cancelLogout" onclick="hideLogoutPopup()">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
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
                    <li><a href="#home">Home</a></li>
                    <li><a href="#shop">Shop</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="#facebook">Facebook</a>
                    <a href="#instagram">Instagram</a>
                    <a href="#twitter">Twitter</a>
                </div>
            </div>
        </div>
        <p class="footer-bottom">© 2025 DexterStyles. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to toggle password visibility
        function togglePasswordVisibility(inputId, iconId) {
            const passwordField = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Function to show specific content section
        function showSection(sectionId) {
            // Hide all content sections
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => {
                section.style.display = 'none';
            });
            
            // Remove active class from all menu items
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.classList.remove('active');
            });
            
            // Show the selected section
            document.getElementById(sectionId).style.display = 'block';
            
            // Add active class to the clicked menu item
            event.currentTarget.classList.add('active');
        }

        // Function to cancel edit and reload the page
        function cancelEdit() {
            window.location.reload();
        }

        // Function to show logout popup
        function showLogoutPopup() {
            const logoutOverlay = document.getElementById('logoutOverlay');
            logoutOverlay.classList.remove('d-none');
            logoutOverlay.classList.add('d-flex');
        }

        // Function to hide logout popup
        function hideLogoutPopup() {
            const logoutOverlay = document.getElementById('logoutOverlay');
            logoutOverlay.classList.remove('d-flex');
            logoutOverlay.classList.add('d-none');
        }

        // Function to handle user logout
        function logoutUser() {
            // You can redirect to logout.php or implement your logout logic
            window.location.href = './../php_files/logout.php';
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
            
            // Initialize tooltips if available in Bootstrap
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltips.forEach(tooltip => {
                    new bootstrap.Tooltip(tooltip);
                });
            }
            
            // Add animation to order cards
            const orderCards = document.querySelectorAll('.order-card');
            orderCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        // Confirm before deleting account
        document.querySelectorAll('form[name="delete"]').forEach(form => {
            form.addEventListener('submit', function(event) {
                const confirmDelete = confirm('Are you sure you want to delete your account? This action cannot be undone.');
                if (!confirmDelete) {
                    event.preventDefault();
                }
            });
        });

        // Add smooth scroll effect
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Add this to your existing JavaScript section
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation for password change
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                
                // Only validate if passwords are provided (skip if both empty)
                if (newPassword || confirmPassword) {
                    // Check password length
                    if (newPassword.length < 8) {
                        event.preventDefault();
                        showPasswordError('Password must be at least 8 characters long!');
                        return;
                    }
                    
                    // Check if passwords match
                    if (newPassword !== confirmPassword) {
                        event.preventDefault();
                        showPasswordError('Passwords do not match!');
                        return;
                    }
                }
            });

            // Function to show password errors
            function showPasswordError(message) {
                const errorContainer = document.getElementById('password-message-container');
                errorContainer.innerHTML = `<div class="alert alert-danger" role="alert">${message}</div>`;
                
                // Auto-hide the error after 5 seconds
                setTimeout(() => {
                    const alert = errorContainer.querySelector('.alert');
                    if (alert) {
                        alert.style.opacity = '0';
                        alert.style.transition = 'opacity 1s';
                        setTimeout(() => {
                            alert.remove();
                        }, 1000);
                    }
                }, 5000);
                
                // Scroll to error message
                errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        // Function to show image preview before upload
        function showPreview(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const previewContainer = document.getElementById('image-preview-container');
                    const previewImage = document.getElementById('image-preview');
                    
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Function to cancel image upload
        function cancelImageUpload() {
            const fileInput = document.getElementById('profile_pic');
            const previewContainer = document.getElementById('image-preview-container');
            
            // Clear the file input
            fileInput.value = '';
            
            // Hide the preview container
            previewContainer.classList.add('d-none');
        }

        // Auto-hide upload message after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            // Handle upload messages
            const uploadMessages = document.querySelectorAll('.upload-message-container .alert');
            uploadMessages.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 1s';
                    setTimeout(() => {
                        alert.remove();
                    }, 1000);
                }, 5000);
            });
        });
    </script>

</body>
</html>