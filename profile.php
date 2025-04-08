<?php
include('root_dex.php'); 

session_start();

// Get user ID from session or GET parameter
$u_id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_SESSION['id']) ? $_SESSION['id'] : 0);

// Validate user ID to prevent SQL injection
if ($u_id <= 0) {
    echo "Invalid user ID";
    exit;
}


//Tempary
// if (!isset($_SESSION['id'])) {
//     $_SESSION['id'] = 2; 
// }

// $u_id = $_SESSION['id']; 



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

if (isset($_POST['save'])) {
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];
    $new_mobile = $_POST['mobile_no'];
    
    $sql_update = "UPDATE user SET name=?, email=?, password=?, mobile_no=? WHERE id=?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssi", $new_name, $new_email, $new_password, $new_mobile, $u_id);

    if ($stmt_update->execute()) {
        $message = '<div class="alert alert-success" role="alert">Profile updated successfully!</div>';
        // Update local variables with new values
        $u_name = $new_name;
        $u_email = $new_email;
        $u_password = $new_password;
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
if (isset($_POST['save_address'])) {
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

$conn->close();

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clothing E-Commerce Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="profile1.css ">
</head>
<body>
   <div class="d-flex">
        <div class="profile-sidebar">
            <div class="text-center">
                <img src="images/user.jpg" alt="Profile Picture" class="profile-pic" id="pro">
                <h5 id="ad"><?php echo $u_name; ?></h5>
                <p id="ae"><?php echo $u_email; ?></p>
            </div></br>
            <div class="menu-item active" onclick="showSection('personal-info')">Personal Information</div> 
                <div class="menu-item" onclick="showSection('orders')">Orders</div> 
                <div class="menu-item" onclick="showSection('shipping')">Shipping Address</div>
                <div class="menu-item" onclick="showSection('payment')">Payment</div>
                <div class="menu-item" onclick="showSection('settings')">Settings</div>
                <div class="menu-item text-danger" onclick="showSection('logout')" id="my5">Log Out</div>
            </div>

            <!-- Main Content Area -->
            <div class="profile-content p-4">

                <!-- Personal Information Section  -->
                <div id="personal-info" class="content-section">
                    <h3>Personal Information</h3><br>
                      
                <!-- Display success/error message -->
                  <div id="message-container">
                    <?php echo $message; ?>
                  </div>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $u_name; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $u_email; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" value="<?php echo $u_password; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="mobile_no" class="form-label">Mobile Number</label>
                            <input type="text" class="form-control" id="mobile_no" name="mobile_no" value="<?php echo $u_mobile_no; ?>">
                        </div>
                        <button type="submit" name="save" class="btn btn-primary" onclick="saveFunction()">Save Changes</button>
                        <button type="button" name="cancle" class="cancel-btn" onclick="cancelEdit()" >Cancel</button>
                    </form>
                </div>
                
                <!-- Orders Section -->
          <div id="orders" class="content-section">
               <h3><center>Your Orders</center></h3><br>
                   <?php if (count($orders) > 0): ?>
                       <div class="row">
                          <?php foreach ($orders as $order): ?>
                             <div class="col-md-6 mb-4">
                                  <div class="card order-card" onclick="window.location.href='oneproduct.php'">   
                                    <!-- ?id=<?php echo $p_id;?> -->

                                    <div class="card-body">
                                       <!-- Image centered at the top -->
                                         <div class="text-center mb-3">
                                         <?php if (!empty($order['p_mimage'])): ?>
                                        <img src="<?php echo htmlspecialchars($order['p_mimage']); ?>" alt="Product" class="img-fluid" style="max-width: 25%; height: auto;">
                                        <?php endif; ?>
                                    </div>
                            
                            <!-- Order information below the image -->
                            <div class="text-center">
                                <h5 class="card-title"><?php echo $order['p_title']; ?></h5>
                                <h7 class="card-text">Order ID: <?php echo $order['order_id']; ?></h7></br>
                                <h7 class="card-text">Price: Rs.<?php echo $order['total_price']; ?></h7><br>
                                <h7 class="card-text">Shipping: Rs.<?php echo $order['shipping_cost']; ?></h7>
                            
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No orders found.</p>
    <?php endif; ?>
</div>

     <!-- Shipping Address Section -->
     <div id="shipping" class="content-section" style="display:none;">
                <h3>Shipping Address</h3><br>

                <!-- Display success/error message -->
                <div id="shipping_address_message-container">
                    <?php echo $shipping_address_message; ?>
                </div>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="shipping_address" class="form-label"><?php echo $u_name; ?> Shipping Address is:</label><br>
                        <textarea class="form-control" id="shipping_address" name="shipping_address" rows="4"><?php echo $shipping_address; ?></textarea>
                    </div>
                    <button type="submit" name="save_address" class="btn btn-primary">Save Address</button>

                </form>
            </div>


    <!-- Centered box with medium size -->
    <div id="payment" class="content-section">
        <h3 class="text-center">Payment Methods</h3>
        <div class="form-lable" ><br>
            <label for="card_number" class="form-label">Card Number</label>
            <h6>XXXX-XXXX-XXXX-XXXX</h6><br>

            <label for="payment_type" class="form-label">Payment Type</label>
            <h6>Online</h6><br>

            <label for="payment_methods" class="form-label">Payment Methods</label>
            <h6>
                <img src="images/visa.png" alt="Visa" class="img-fluid">
                <img src="images/paypal.png" alt="PayPal" class="img-fluid" >
                <img src="images/card.png" alt="Mastercard" class="img-fluid" >
            </h6>
        </div>
    </div>
</div>

                
                <!-- Settings Section -->
                <div id="settings" class="content-section" style="display:none;">
                    <h3>Settings</h3>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="emailNotif">
                        <label class="form-check-label" for="emailNotif">Email Notifications</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="smsNotif">
                        <label class="form-check-label" for="smsNotif">SMS Notifications</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="smsNotif">
                        <label class="form-check-label" for="smsNotif">popup Notifications</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="smsNotif">
                        <label class="form-check-label" for="smsNotif">SMS Notifications</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="smsNotif">
                        <label class="form-check-label" for="smsNotif">SMS Notifications</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="smsNotif">
                        <label class="form-check-label" for="smsNotif">SMS Notifications</label>
                    </div>
                    
                    
                </div>
            </div>

              <!-- logout Section -->
      
  <!-- <div id="logoutBtn" class="content-section" style="display:none;">
        <div id="logoutOverlay" class="logout-overlay">
        <div class="logout-popup">
            <img src="images/user.jpg" alt="Profile Picture">
            <h5 id="popupUsername"><?php echo $u_name?></h5>
            <p>Are you sure you want to log out?</p>
            <button id="confirmLogout">Confirm Logout</button>
            <button id="cancelLogout">Cancel</button>
        </div>
    </div>
</div> -->

<div id="logout" class="content-section" style="display:none;">

<div id="logoutOverlay" class="logout-overlay">
    <div class="logout-popup">
        <img src="images/user.jpg" alt="Profile Picture">
        <h5><?php echo $u_name; ?></h5>
        <p>Are you sure you want to log out?</p>
        <button id="confirmLogout">Confirm Logout</button>
        <button id="cancelLogout">Cancel</button>
    </div>
</div>
</div>
 </div> 
<!-- </div> -->
 
   

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="profile1.js"></script>

</body>
</html>