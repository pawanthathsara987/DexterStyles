<?php
// Include database connection 
   include('root_dex.php');

   if (isset($_GET['p_id'])) {
    $p_id = $_GET['p_id']; // Convert to integer for security

    // temparary
    //  $p_id = isset($_GET['p_id']) ? $_GET['p_id'] : "1"; 


    // Fetch product data from database
   $sql = "SELECT p.*, c.c_name, pi.img_path 
        FROM product_details p 
        JOIN category c ON p.c_id = c.c_id 
        LEFT JOIN product_images pi ON p.p_id = pi.p_id 
        WHERE p.p_id = ? "; 
       
       $stmt = $conn->prepare($sql);

    //temparary
    // if ($stmt) {

      $stmt->bind_param("i", $p_id); //"s" because id is a string
     $stmt->execute();
     $result = $stmt->get_result();

   
    // Check if product exists
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        $p_id = $product['p_id'];
        $p_title = $product['p_title'];
        $p_description = $product['p_description'];
        $p_brand = $product['p_brand'];
        $p_price = $product['p_price'];
        $p_qty = $product['p_qty'];
        $p_material = $product['p_material'];
        $p_mimage = $product['p_mimage'];
        $catogory_id = $product['c_id'];
        $c_name = $product['c_name'];
        // $img_path = $product['img_path'];

    } else {
        echo "Product not found.";
    }

    $stmt->close();
} 
else {
    echo "No product ID provided.";
}

$images = [];
$sql_images = "SELECT img_path FROM product_images WHERE p_id = ?";
$stmt_images = $conn->prepare($sql_images);
if ($stmt_images) {
    $stmt_images->bind_param("s", $p_id);
    $stmt_images->execute();
    $result_images = $stmt_images->get_result();

    while ($row = $result_images->fetch_assoc()) {
        $images[] = $row['img_path'];
    }
    $stmt_images->close();
}

// For demonstration - available sizes (in a real scenario, this would come from database)
$sizes = ['S', 'M', 'L', 'XL', '2XL', '3XL'];
// Updated to match your image - all sizes available
$available_sizes = ['S', 'M', 'L', 'XL', '2XL', '3XL']; 

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $p_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="oneproduct.css">
</head>
<body>
    <div class="container mt-4">
        <!-- Breadcrumb navigation  -->
         <nav aria-label="breadcrumb" class="small mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="productdetails.php?c_id=<?php echo $p_id; ?>"><?php echo $c_name; ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $p_title; ?></li>
            </ol>
        </nav>
        <div class="row">
            <!-- Left column for product images -->
            <div class="col-md-6 mb-4">
                <!-- Main product image -->
                <div class="main-image-container mb-3">
                   <img src="<?php echo $p_mimage; ?>" class="img-fluid" id="mainProductImage" alt="<?php echo $p_title; ?>">
                </div>
                <!-- Thumbnail gallery -->
                <div class="thumbnail-gallery">
                    <div class="row">
                        <?php 
                        // Display main image as first thumbnail
                          echo '<div class="col-3 mb-2"><img src="' . $p_mimage . '" class="img-thumbnail thumbnail-active" onclick="changeImage(\'' . $p_mimage. '\', this)"></div>';
                        
                        foreach ($images as $image) {
                                 echo '<div class="col-3 mb-2"><img src="' . $image . '" class="img-thumbnail" onclick="changeImage(\'' . $image . '\', this)"></div>';
                               //echo '<div class="col-3 mb-2"><img src="' . $image . '" class="img-thumbnail" onclick="changeImage(\'' . $image . '\', this)"></div>';
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
                    <!-- Vendor and SKU info -->
                    <div class="product-meta mb-2">
                        <div class="mb-1"><span class="meta-label">Vendor:</span> <?php echo $p_title; ?></div>
                        <div class="mb-1"><span class="meta-label">SKU:</span><?php echo $p_id; ?></div>
                        <div><span class="meta-label">Availability:</span> In Stock</div>
                    </div>
                    <br>
                    <!-- Price -->
                    <div class="product-price mb-3">
                        <h3>Rs <?php echo number_format($p_price, 2); ?></h3>
                        <input type="hidden" id="product-base-price" value="<?php echo $p_price; ?>"><br>

                    </div> 

                    <!-- Size selection -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div><span class="meta-label">Size:</span> <span id="selected-size">S</span></div>
                        </div>
                        <div class="size-options">
                            <?php foreach($sizes as $size): ?>
                                <?php 
                                $isAvailable = in_array($size, $available_sizes);
                                $activeClass = ($size == 'S') ? 'size-active' : '';
                                $unavailableClass = !$isAvailable ? 'size-unavailable' : '';
                                ?>
                                <div class="size-box <?php echo $activeClass . ' ' . $unavailableClass; ?>" data-size="<?php echo $size; ?>" onclick="selectSize('<?php echo $size; ?>', this)">
                                    <?php if(!$isAvailable): ?>
                                        <div class="size-cross"></div>
                                    <?php endif; ?>
                                    <?php echo $size; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div><br>
                    <!-- Subtotal -->
                    <div class="mb-3">
                        <div><span class="meta-label">Subtotal:</span> <span id="subtotal">Rs <?php echo number_format($p_price, 2); ?></span></div>
                    </div>
                    
                    <!-- Quantity -->
                    <div class="mb-4">
                        <label class="meta-label mb-2">Quantity:</label>
                        <div class="d-flex align-items-center">
                            <div class="input-group quantity-selector" style="width: 130px;">
                                <button class="btn btn-outline-secondary" type="button" onclick="decrementQuantity()">−</button>
                                <input type="number" class="form-control text-center" id="quantity" value="1" min="1" onchange="updateSubtotal()">
                                <button class="btn btn-outline-secondary" type="button" onclick="incrementQuantity()">+</button>
                            </div>
                            
                            <!-- Action buttons next to quantity -->
                            <div class="ms-3">
                                <!-- <button class="btn btn-primary" id="addToCartBtn">ADD TO CART</button> -->
                                <a href="add_to_cart.php?p_id=<?php echo $p_id; ?>" class="btn btn-primary" id="addToCartBtn">ADD TO CART</a>


                            </div>
                            <div class="ms-2">
                                <button class="btn btn-outline-secondary btn-icon" id="wishlistBtn">
                                    <i class="fa-regular fa-heart"></i>
                                </button>
                            </div>
                            <div class="ms-2">
                                <button class="btn btn-outline-secondary btn-icon" id="shareBtn">
                                    <i class="fa-solid fa-share-nodes"></i>
                                </button>
                                
                            </div>
                        </div>
                    </div>
                    
                    <!-- Buy Now Button -->
                    <div class="mb-4">
                        <!-- <button class="btn btn-outline-dark w-100" id="buyNowBtn">BUY IT NOW</button> -->
                        <a href="BuyProduct.php?p_id=<?php echo $p_id; ?>" class="btn btn-outline-dark w-100" id="buyNowBtn">BUY IT NOW</a>
                    </div><br>
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
                            <li>Name : <?php echo $p_title; ?></li>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="oneproduct.js"></script>
                   
</body>
</html>