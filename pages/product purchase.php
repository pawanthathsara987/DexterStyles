<?php
// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "your_database_name");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['full_name'];
    $phone = $_POST['phone_number'];
    $province = $_POST['province'];
    $district = $_POST['district'];
    $city = $_POST['city'];
    $street = $_POST['street'];
    $landmark = $_POST['landmark'];
    $address = $_POST['address'];
    $label = $_POST['label'];
    $item_total = 1950;
    $delivery_fee = 199;
    $total = $item_total + $delivery_fee;

    $stmt = $conn->prepare("INSERT INTO orders 
        (full_name, phone_number, province, district, city, street_address, landmark, full_address, delivery_label, item_total, delivery_fee, total) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssdd", $name, $phone, $province, $district, $city, $street, $landmark, $address, $label, $item_total, $delivery_fee, $total);
    $stmt->execute();

    echo "<script>alert('Order placed successfully!');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Product Purchase Page</title>
  <link rel="stylesheet" href="../css/product purchase.css">
</head>
<body>

<div class="container">
  <!-- Left Form Section -->
  <div class="form-section">
    <h2>Delivery Information</h2>
    <form method="POST">
      <input type="text" name="full_name" placeholder="First and Last Name" required>
      <input type="text" name="phone_number" placeholder="Phone Number" required>

      <select name="province" required>
        <option value="">Choose Province</option>
        <option>Western</option>
        <option>Southern</option>
        <option>Central</option>
      </select>
      <select name="district" required>
        <option value="">Choose District</option>
        <option>Colombo</option>
        <option>Galle</option>
        <option>Kandy</option>
      </select>

      <input type="text" name="city" placeholder="City" required>
      <input type="text" name="address" placeholder="Full Address" class="full-width" required>

      <div class="full-width">
        <label>Select payment method:</label>
        <div class="label-buttons">
          <button type="button" > COD</button>
          <button type="button" > Visa/Master Card</button>
        </div>
        <input type="hidden" name="label" id="deliveryLabel" required>
      </div>

      <button type="submit" class="btn-save full-width">Save</button>
    </form>
  </div>

  <!-- Right Summary Section -->
  <div class="summary-section">
    <h2>Invoice</h2>
    <p><span>Items Total</span><span>Rs. 1,950</span></p>
    <p><span>Delivery Fee</span><span>Rs. 199</span></p>
    <hr>
    <p class="total"><span>Total</span><span>Rs. 2,149</span></p>
    <p style="font-size: 12px;">VAT included, where applicable</p>
    <button class="btn-pay" disabled>Proceed to Pay</button>
  </div>
</div>

<script>
  function selectLabel(value) {
    document.getElementById('deliveryLabel').value = value;
    alert("Delivery label set to: " + value);
  }
</script>

</body>
</html>
