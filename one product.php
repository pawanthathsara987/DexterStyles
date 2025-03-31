<?php 
// Include database connection 
include('root.php');

// Get product ID from the URL (passed as a query parameter) 
$product_id = isset($_GET['pid']) ? (int)$_GET['pid'] : 1;

if ($product_id > 0) {
    // Fetch product details from the database
    $sql = "SELECT * FROM products WHERE pid = $product_id";
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
    <title><?php echo $product['product_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container mt-4">
        <!-- Breadcrumb navigation -->
        <nav aria-label="breadcrumb" class="small mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="products.php">Women</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $product['product_name']; ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Left column for product images -->
            <div class="col-md-6 mb-4">
                <!-- Main product image -->
                <div class="main-image-container mb-3">
                    <img src="<?php echo $product['pimage']; ?>" class="img-fluid" id="mainProductImage" alt="<?php echo $product['product_name']; ?>">
                </div>
                
                <!-- Thumbnail gallery -->
                <div class="thumbnail-gallery">
                    <div class="row">
                        <?php 
                        // Display main image as first thumbnail
                        echo '<div class="col-3 mb-2"><img src="' . $product['pimage'] . '" class="img-thumbnail thumbnail-active" onclick="changeImage(\'' . $product['additional_images'] . '\', this)"></div>';
                        
                        // Display additional images
                        if(!empty($product['additional_images'])) {
                            $additional_images = explode(',', $product['additional_images']);
                            foreach ($additional_images as $image) {
                                echo '<div class="col-3 mb-2"><img src="' . $image . '" class="img-thumbnail" onclick="changeImage(\'' . $image . '\', this)"></div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <!-- Right column for product details -->
            <div class="col-md-6">
                <div class="product-details">
                    <!-- Product title -->
                    <h2 class="product-title"><?php echo $product['product_name']; ?></h2>
                    
                    <!-- Vendor and SKU info -->
                    <div class="product-meta mb-2">
                        <div class="mb-1"><span class="meta-label">Vendor:</span> Tendenza</div>
                        <div class="mb-1"><span class="meta-label">SKU:</span> TW14505</div>
                        <div><span class="meta-label">Availability:</span> In Stock</div>
                    </div>
                    
                    <!-- Price -->
                    <div class="product-price mb-3">
                        <h3>Rs <?php echo number_format($product['price'], 2); ?></h3>
                        <input type="hidden" id="product-base-price" value="<?php echo $product['price']; ?>">
                        
                        <!-- EMI payment options -->
                        <div class="emi-options mb-2">
                            <small>or pay in 3 x Rs 596.66 with</small>
                            <span class="ms-1 emi-logo">KOKO</span>
                            <i class="fa-solid fa-circle-info small ms-1"></i>
                        </div>
                        <div class="emi-options mb-3">
                            <small>or 3 x Rs 596.66 with</small>
                            <span class="ms-1 emi-logo-alt">simpl/pay</span>
                        </div>
                    </div>
                    
                    <!-- Size selection with horizontal lines -->
                    <div class="size-section mb-4">
                        <div class="horizontal-line"></div>
                        <div class="d-flex justify-content-between align-items-center py-3">
                            <div><span class="meta-label">Size:</span> <span id="selected-size">S</span></div>
                        </div>
                        <div class="size-options-container">
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
                        </div>
                        <div class="horizontal-line mt-3"></div>
                    </div>
                    
                    <!-- Subtotal -->
                    <div class="mb-3">
                        <div><span class="meta-label">Subtotal:</span> <span id="subtotal">Rs <?php echo number_format($product['price'], 2); ?></span></div>
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
                                <button class="btn btn-primary" id="addToCartBtn">ADD TO CART</button>
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
                        <button class="btn btn-outline-dark w-100" id="buyNowBtn">BUY IT NOW</button>
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
                            <li>Batwing Sleeve</li>
                            <li>Tie & Dye</li>
                            <li>Casual Wear</li>
                            <li>Material : Cotton</li>
                            <li>Material Composition : 100% Cotton</li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="shipping" role="tabpanel">
                        <h5>Shipping Policy</h5>
                        <p>Details about shipping and returns policy here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to change the main product image when a thumbnail is clicked
        function changeImage(imagePath, thumbnailElement) {
            document.getElementById('mainProductImage').src = imagePath;
            
            // Remove active class from all thumbnails
            const thumbnails = document.querySelectorAll('.img-thumbnail');
            thumbnails.forEach(thumb => {
                thumb.classList.remove('thumbnail-active');
            });
            
            // Add active class to clicked thumbnail
            thumbnailElement.classList.add('thumbnail-active');
        }
        
        // Function to select size
        function selectSize(size, element) {
            // Check if size is unavailable
            if (element.classList.contains('size-unavailable')) {
                return;
            }
            
            // Update displayed selected size
            document.getElementById('selected-size').textContent = size;
            
            // Remove active class from all size boxes
            const sizeBoxes = document.querySelectorAll('.size-box');
            sizeBoxes.forEach(box => {
                box.classList.remove('size-active');
            });
            
            // Add active class to selected size box
            element.classList.add('size-active');
        }
        
        // Function to update subtotal based on quantity
        function updateSubtotal() {
            const basePrice = parseFloat(document.getElementById('product-base-price').value);
            const quantity = parseInt(document.getElementById('quantity').value);
            
            if (isNaN(quantity) || quantity < 1) {
                document.getElementById('quantity').value = 1;
                const subtotal = basePrice;
                document.getElementById('subtotal').textContent = 'Rs ' + subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            } else {
                const subtotal = basePrice * quantity;
                document.getElementById('subtotal').textContent = 'Rs ' + subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
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