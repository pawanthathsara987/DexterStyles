<?php
// Include database connection
include('root.php');

// Get product ID from the URL (passed as a query parameter)
$product_id = isset($_GET['pid']) ? (int)$_GET['pid'] : 1;

if ($product_id > 0) {
    // Fetch product details from the database
    $sql = "SELECT * FROM products WHERE id = $product_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the product details
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found!";
        exit();
    }
} else {
    echo "Invalid product ID!";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-image {
            max-width: 80%;
            height: 50%;
        }
        .product-gallery {
            display: flex;
            overflow-x: auto;
            gap: 10px;
        }
        .product-gallery img {
            max-width: 100px;
            cursor: pointer;
        }
        .product-detail {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <!-- Product Left Section: Main Image and Additional Images -->
        <div class="row">
            <div class="col-md-6">
                <!-- Main Product Image -->
                <div class="product-detail">
                    <img src="G:\PROJECTS\DexterStyles\Images\1.jpg" class="product-image" id="mainImage">
                    <!-- <?php echo $product['image']; ?>' -->
                </div>
                <!-- Additional Images (Slider) -->
                <div class="product-gallery">
                    <?php
                    // Handle additional images (comma-separated list)
                    $additional_images = explode(',', $product['additional_images']);
                    foreach ($additional_images as $image) {
                        echo "<img src='2.jpg' class='product-thumbnail' onclick='changeImage(\"images/$image\")'>";
                    }
                    ?>
                </div>
            </div>

            <!-- Product Right Section: Details, Price, Quantity, and Cart Options -->
            <div class="col-md-6">
                <div class="product-detail">
                    <h2><?php echo $product['product_name']; ?></h2>
                    <p><h5>Price:</h5>RS.<?php echo number_format($product['price'], 2); ?></p></br>
                    <p><h5>Description:</h5> <?php echo $product['description']; ?></p><br>
                    <label><h5>Quantity:</h5></label>   
                    <input type="number" id="quantity" name="quantity" value="1" min="1" class="form-control mb-3" style="width: 100px;">
                    <br>
                    <!-- Add to Cart and Buy Now Buttons -->
                    <button class="btn btn-primary mb-3">Add to Cart</button>
                    <button class="btn btn-success">Buy Now</button>
                </div>
            </div>
        </div>
    </div>
    <!-- for="quantity" -->

    <script>
        // Function to change the main product image when a thumbnail is clicked
        function changeImage(imagePath) {
            document.getElementById('mainImage').src = imagePath;
        }
    </script>
</body>
</html>
