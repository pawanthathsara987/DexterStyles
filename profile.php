<?php
session_start();

//get id 

if (!isset($_SESSION['Ad_id'])) {
    $_SESSION['Ad_id'] = 2; // Temporary fix: Replace with a valid admin ID
}

include('root.php');

$admin_id = $_SESSION['Ad_id']; 

// Debugging: Check if session is set
// echo "Admin ID from Session: " . $admin_id;
//  <img src="C:\xampp\htdocs\dexter\images\editprofilebackgrount.jpg" >


// Fetch admin details from the database
$sql = "SELECT * FROM adminDetails WHERE Ad_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

$background_image = !empty($admin['background_image']) ? $admin['background_image'] : 'C:\xampp\htdocs\dexter\images\editprofilebackgrount.jpg';

$message = ''; // Variable to store the success or error message

// If the form is submitted, update the records
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Ad_name = $_POST['Admin_name'];
    $Ad_email = $_POST['A_email'];
    $Ad_address = $_POST['A_address'];
    $Ad_phone_number = $_POST['A_phone_number'];
    $Ad_date_of_birth = $_POST['A_date_of_birth'];
    $Ad_gender = $_POST['A_gender'];
    $Ad_city = $_POST['A_city'];
    $Ad_district = $_POST['A_district'];
    $Ad_state = $_POST['A_state'];

    if(isset($_POST['save'])){
        $sql_update = "UPDATE adminDetails SET Admin_name=?, A_email=?, A_address=?, A_phone_number=?, A_date_of_birth=?, A_gender=?, A_city=?, A_district=?, A_state=? WHERE Ad_id=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssssssssi", $Ad_name, $Ad_email, $Ad_address, $Ad_phone_number, $Ad_date_of_birth, $Ad_gender, $Ad_city, $Ad_district, $Ad_state, $admin_id);
        
        if ($stmt_update->execute()) {
            $message = '<div class="alert alert-success" role="alert">Profile updated successfully!</div>';

        } else {
            
            $message = '<div class="alert alert-danger" role="alert">Error updating profile!</div>';
        }
    }

    
   
}
?>
    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clothing E-Commerce Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="profile(css).css ">
</head>
<body>
    

    <!-- Include Header -->

    <div id="header"></div>
    
    <div class="d-flex">
        <div class="profile-sidebar">
            <div class="text-center">
                <img src="C:\xampp\htdocs\dexter\images\user.jpg" alt="Profile Picture">
                <!-- <h5 name="admin_name" id="ad">John Doe</h5>
                <p name="admin_email" id="ae">john.doe@example.com</p> -->
                <h5 id="ad"><?php echo $admin['Admin_name']; ?></h5>
                <p id="ae"><?php echo $admin['A_email']; ?></p>
            </div></br>

           
            <!-- <div class="menu-item" id="my1" onclick="showFavorites()">My Favourite</div>  -->
            <div class="menu-item" id="my2" onclick="editProfile()">Edit Profile</div> 
            <div class="menu-item" id="my3" onclick="showCardDetails()">Card Details</div> 
            <div class="menu-item" id="my4" onclick="showSettings()">Settings</div>
            <div class="menu-item text-danger" id="my5" name="logout" onclick="logout()" >Log Out</div>
        </div> 
        
    </div> 
  

    <div class="editprofile-container" id = "editProfileForm" >
        <h2>Edit Profile</h2>

         <!-- Display the message here if available -->
        <?php echo $message; ?>
        
        <form method="POST" action="" name = "editProfileForm" >
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="Admin_name" value="<?php echo $admin['Admin_name']; ?>" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="A_email" value="<?php echo $admin['A_email']; ?>" required>
            </div>
            <div class="form-group">
                <label>Address:</label>
                <input type="text" name="A_address" value="<?php echo $admin['A_address']; ?>">
            </div>
            <div class="form-group">
                <label>Phone Number:</label>
                <input type="text" name="A_phone_number" value="<?php echo $admin['A_phone_number']; ?>">
            </div>
            <div class="form-group">
                <label>Date of Birth:</label>
                <input type="date" name="A_date_of_birth" value="<?php echo $admin['A_date_of_birth']; ?>">
            </div>
            <div class="form-group">
                <label>Gender:</label>
                <select name="A_gender">
                    <option value="Male" <?php if($admin['A_gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if($admin['A_gender'] == 'Female') echo 'selected'; ?>>Female</option>
                </select>
            </div>
            <div class="form-group">
                <label>City:</label>
                <input type="text" name="A_city" value="<?php echo $admin['A_city']; ?>">
            </div>
            <div class="form-group">
                <label>District:</label>
                <input type="text" name="A_district" value="<?php echo $admin['A_district']; ?>">
            </div>
            <div class="form-group">
                <label>State:</label>
                <input type="text" name="A_state" value="<?php echo $admin['A_state']; ?>">
            </div>
            <div class="form-buttons">
                <button type="submit" class="save-btn" onclick="saveFunction()" name= "save" >Save Changes</button>
                <button type="button" class="cancel-btn" onclick="cancelEdit()" name="cancle">Cancel</button>
            
            </div>
        </form>
    </div>

<!-- 
    <div class="card" id="creditcard" onclick="displayCardDetails()">
        <img src="C:\xampp\htdocs\dexter\images\credit_cards.jpg" alt="Card Image" class="card-image">
      
    </div>

     Card Details (to be shown on click) 
    <div id="card-details" class="card-details" >
         <div class="left">
            <img src="C:\xampp\htdocs\dexter\images\credit_cards.jpg" alt="Card Image" class="card-image">  
        </div> 
        <div class="right">
            <p><h3 id="cardholder-name">Cardholder Name:</h3>W.J.doe</p>
            <p><h5>Card Type:</h5> <lable id="card-type">Visa</lable></p>
            <p><h5>Account Number:</h5> <span id="account-number">**** **** **** *234</span></p>
        </div>
    </div> -->

    <!-- Include Footer -->
       <div id="footer"></div>

    <script src="script.js"></script> 

    

</body>
</html>