<?php
include "./../php_files/conn.php"; // Make sure this file connects to your database

session_start();
$user_email = 'lsandeepa13@gmail.com'; // Use session email if applicable

// Handle adding products to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['p_id'])) {
    $p_id = $_POST['p_id'];

    // Check if product is already in cart
    $sql_check = "SELECT * FROM cart WHERE user_email = ? AND p_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $user_email, $p_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // Product already in cart, just update quantity
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + 1;  // Increase quantity by 1
        $sql_update = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ii", $new_quantity, $row['cart_id']);
        $stmt_update->execute();
        echo "Product quantity updated.";
    } else {
        // Product not in cart, add new record
        $sql_insert = "INSERT INTO cart (user_email, p_id, quantity) VALUES (?, ?, 1)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ss", $user_email, $p_id);
        $stmt_insert->execute();
        echo "Product added to cart.";
    }

    // Close the statement and connection
    $stmt_check->close();
    $stmt_insert->close();
    $stmt_update->close();
    $conn->close();
}

// Fetch cart items
$sql = "SELECT c.cart_id, p.p_title, p.p_price, c.quantity, p.p_mimage 
        FROM cart c
        JOIN product_details p ON c.p_id = p.p_id
        WHERE c.user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .cart-container {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .cart-item {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .item-details {
            flex-grow: 1;
            padding: 0 15px;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
        }

        .quantity-selector button {
            background: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .quantity-selector input {
            width: 50px;
            text-align: center;
            border: 1px solid #ccc;
            margin: 0 5px;
            border-radius: 5px;
        }

        .remove-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .cart-summary {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
        }


        .checkout-btn {
            width: 100%;
            background: #28a745;
            color: white;
            padding: 10px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .checkout-btn:hover {
            background: #218838;
        }

        /* Ad Banner Styles */
        .ad-banner {
            text-align: center;
            margin-bottom: 30px;
        }

        .ad-banner img {
            width: 100%;
            max-width: 800px;  /* Set a max width for the ad */
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .ad-banner-bottom {
            text-align: center;
            margin-top: 30px;
        }

        .ad-banner-bottom img {
            width: 100%;
            max-width: 800px;  /* Set a max width for the ad */
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

    </style>
</head>
<body>

    <div class="cart-container">

    <div class="ad-banner">
        <img src="./../img/ad1.jpeg" alt="Advertisement" class="img-fluid">
    </div>

        <h2 class="text-center mb-4">Shopping Cart</h2>

        <?php while($row = $result->fetch_assoc()): ?>
            <div class="cart-item">
                <img src="./../img/<?php echo $row['p_mimage']; ?>" alt="Product">
                <div class="item-details">
                    <h5><?php echo $row['p_title']; ?></h5>
                    <p>Price: LKR <?php echo $row['p_price']; ?></p>
                </div>
                <div class="quantity-selector">
                    <button class="decrease-btn">-</button>
                    <input type="text" value="<?php echo $row['quantity']; ?>" class="quantity">
                    <button class="increase-btn">+</button>
                </div>
                <button class="remove-btn" data-cart-id="<?php echo $row['cart_id']; ?>">Remove</button>
            </div>
        <?php endwhile; ?>

        <div class="cart-summary shadow-sm p-4 rounded bg-white">
    <h4 class="mb-3 text-center">Order Summary</h4>
    <ul class="list-group list-group-flush mb-3">
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <strong>Total Price:</strong>
            <span id="total-price" class="text-primary fw-bold">LKR 0.00</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <strong>Discount (5%):</strong>
            <span id="discount-price" class="text-success fw-bold">LKR 0.00</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <strong>Final Price:</strong>
            <span id="final-price" class="text-danger fw-bold">LKR 0.00</span>
        </li>
    </ul>
    <button class="checkout-btn btn btn-success w-100 mt-2">Proceed to Checkout</button>
</div>


      <div class="ad-banner-bottom">
        <img src="./../img/ad2.jpeg" alt="Advertisement" class="img-fluid">
    </div>
    
    </div>
    <script>
    $(document).ready(function () {
        function updateTotal() {
            let total = 0;
            $(".cart-item").each(function () {
                let price = parseFloat($(this).find(".item-details p").text().replace("Price: LKR", ""));
                let quantity = parseInt($(this).find(".quantity").val());
                total += price * quantity;
            });

            let discount = total * 0.05;
            let final = total - discount;

            $("#total-price").text("LKR " + total.toFixed(2));
            $("#discount-price").text("LKR " + discount.toFixed(2));
            $("#final-price").text("LKR " + final.toFixed(2));
        }

        function sendUpdate(cartId, quantity) {
            $.post("update_cart.php", {
                cart_id: cartId,
                quantity: quantity
            }, function (response) {
                console.log(response);
                updateTotal();
            });
        }

        $(".increase-btn").click(function () {
            let input = $(this).siblings(".quantity");
            let current = parseInt(input.val());
            input.val(current + 1);
            let cartId = $(this).closest(".cart-item").find(".remove-btn").data("cart-id");
            sendUpdate(cartId, current + 1);
        });

        $(".decrease-btn").click(function () {
            let input = $(this).siblings(".quantity");
            let current = parseInt(input.val());
            if (current > 1) {
                input.val(current - 1);
                let cartId = $(this).closest(".cart-item").find(".remove-btn").data("cart-id");
                sendUpdate(cartId, current - 1);
            }
        });

        $(".quantity").on("change", function () {
            let newQty = parseInt($(this).val());
            if (isNaN(newQty) || newQty < 1) {
                newQty = 1;
                $(this).val(1);
            }
            let cartId = $(this).closest(".cart-item").find(".remove-btn").data("cart-id");
            sendUpdate(cartId, newQty);
        });

        $(".remove-btn").click(function () {
    const cartId = $(this).data("cart-id");

    if (confirm("Are you sure you want to remove this item from your cart?")) {
        $.post("remove_cart.php", { cart_id: cartId }, function (response) {
            alert(response);
            location.reload(); // Refresh cart after removal
        });
    }
});


        updateTotal();
    });
</script>


</body>
</html>
