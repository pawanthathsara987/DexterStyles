<?php
// Start session with secure settings
ini_set('session.cookie_httponly', 1);
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}
session_start();

include './../php_files/conn.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get and sanitize category from URL parameter
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : 'all';

// Set default filters with proper validation
$min_price = isset($_GET['min_price']) ? max(0, intval($_GET['min_price'])) : 0;
$max_price = isset($_GET['max_price']) ? min(1000, max(0, intval($_GET['max_price']))) : 50;
$sizes = isset($_GET['sizes']) && is_array($_GET['sizes']) ? $_GET['sizes'] : [];

// Sanitize each size value
$sanitized_sizes = [];
foreach ($sizes as $size) {
    // Only allow valid sizes
    if (in_array($size, ['s', 'm', 'l', 'xl'])) {
        $sanitized_sizes[] = $conn->real_escape_string($size);
    }
}
$sizes = $sanitized_sizes;

// Sanitize model_type
$model_type = isset($_GET['model_type']) ? $conn->real_escape_string($_GET['model_type']) : '';

// Define available sizes for filter options
$available_sizes = ['s', 'm', 'l', 'xl'];

// Define available sort options - MODIFIED: keep only price sorting options
$available_sort_options = [
    'price-low' => 'Price: Low to High',
    'price-high' => 'Price: High to Low'
];

// Get sort parameter with default to price-low
$order_by = isset($_GET['sort']) && array_key_exists($_GET['sort'], $available_sort_options) 
    ? $conn->real_escape_string($_GET['sort']) 
    : 'price-low';

// Prepared statement for getting all unique brands
$brand_query = "SELECT DISTINCT p_brand FROM product_details ORDER BY p_brand";
$available_models = [];

$brand_stmt = $conn->prepare($brand_query);
if ($brand_stmt) {
    $brand_stmt->execute();
    $brand_result = $brand_stmt->get_result();
    
    // Populate array with brands from database
    if ($brand_result->num_rows > 0) {
        while($brand_row = $brand_result->fetch_assoc()) {
            $available_models[] = $brand_row['p_brand'];
        }
    }
    $brand_stmt->close();
}

// Prepare base SQL query with placeholders
$sql = "SELECT * FROM product_details WHERE 1=1";
$params = [];
$types = "";

// Add category filter using prepared statement
if ($category && $category != 'all') {
    // First get the category ID
    $cat_stmt = $conn->prepare("SELECT c_id FROM category WHERE c_name = ?");
    if ($cat_stmt) {
        $cat_stmt->bind_param("s", $category);
        $cat_stmt->execute();
        $cat_result = $cat_stmt->get_result();
        
        if ($cat_result->num_rows > 0) {
            $cat_row = $cat_result->fetch_assoc();
            $sql .= " AND c_id = ?";
            $params[] = $cat_row['c_id'];
            $types .= "i"; // integer
        }
        $cat_stmt->close();
    }
}

// Add price range filter
if ($min_price > 0 || $max_price < 50) {
    $sql .= " AND p_price BETWEEN ? AND ?";
    $params[] = $min_price;
    $params[] = $max_price;
    $types .= "ii"; // two integers
}

// Add size filter
if (!empty($sizes)) {
    $size_conditions = [];
    foreach ($sizes as $size) {
        $size_conditions[] = "p_size LIKE ?";
        $params[] = "%$size%";
        $types .= "s"; // string
    }
    $sql .= " AND (" . implode(" OR ", $size_conditions) . ")";
}

// Add brand filter
if (!empty($model_type)) {
    $sql .= " AND p_brand = ?";
    $params[] = $model_type;
    $types .= "s"; // string
}

// Add order by clause - SIMPLIFIED
switch ($order_by) {
    case 'price-high':
        $sql .= " ORDER BY p_price DESC";
        break;
    case 'price-low':
    default:
        $sql .= " ORDER BY p_price ASC";
        break;
}

// Pagination
$items_per_page = 12;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

// Count total products for pagination
$count_sql = str_replace("SELECT *", "SELECT COUNT(*) as total", $sql);
$count_stmt = $conn->prepare($count_sql);
if ($count_stmt) {
    if (!empty($types)) {
        $count_stmt->bind_param($types, ...$params);
    }
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $count_row = $count_result->fetch_assoc();
    $total_products = $count_row['total'];
    $count_stmt->close();
} else {
    $total_products = 0;
}

// Add limit and offset to the main query
$sql .= " LIMIT ? OFFSET ?";
$params[] = $items_per_page;
$params[] = $offset;
$types .= "ii"; // two more integers

// Execute the main query with prepared statement
$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DexterStyles - <?php echo htmlspecialchars(ucfirst($category)); ?> Collection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./../css/product.css">
    <link rel="stylesheet" href="./../css/home.css">
    <!-- Add Content Security Policy header -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self'; style-src 'self' https://cdnjs.cloudflare.com; font-src 'self' https://cdnjs.cloudflare.com; img-src 'self';">
</head>
<body>
    <header style="background-color: #F0EAD6;">
        <nav class="nav">
            <div class="logo"><a href="../home.php"><img src="../img/Logo.png" alt="DexterStyles Logo"></a></div>
            <ul class="nav-menu">
                <li><a href="../home.php">Home</a></li>
                <li><a href="./product.php" class="active">Shop</a></li>
                <li><a href="./aboutus.php">About</a></li>
                <li><a href="./contact.php">Contact</a></li>
            </ul>
            <div class="nav-actions">
                <a href="./view_cart.php" class="cart-icon">🛒</a>

                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <a href="./profile.php" class="p-pic">
                    <?php 
                    $profile_pic = empty($_SESSION['profile_pic']) ? 'blankprofile.jpg' : basename($_SESSION['profile_pic']);
                    ?>
                    <img src="../img/profile_photo/<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile">
                </a>
                <?php else: ?>
                    <a href="./signin.php" class="signin-btn">Sign In</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    
    <!-- Content Wrapper with Sidebar -->
    <div class="content-wrapper">
        <!-- Sidebar with Filters -->
        <aside class="sidebar">
            <div class="sidebar-heading">Shop by Category</div>
            
            <div class="sidebar-nav">
                <a href="product.php?category=men<?php echo isset($_GET['sort']) ? '&sort=' . htmlspecialchars($_GET['sort']) : ''; ?>" class="sidebar-nav-link <?php echo ($category == 'men') ? 'active' : ''; ?>">Men's Collection</a>
                <a href="product.php?category=women<?php echo isset($_GET['sort']) ? '&sort=' . htmlspecialchars($_GET['sort']) : ''; ?>" class="sidebar-nav-link <?php echo ($category == 'women') ? 'active' : ''; ?>">Women's Collection</a>
                <a href="product.php?category=kids<?php echo isset($_GET['sort']) ? '&sort=' . htmlspecialchars($_GET['sort']) : ''; ?>" class="sidebar-nav-link <?php echo ($category == 'kids') ? 'active' : ''; ?>">Kids Collection</a>
                <a href="product.php?category=new<?php echo isset($_GET['sort']) ? '&sort=' . htmlspecialchars($_GET['sort']) : ''; ?>" class="sidebar-nav-link <?php echo ($category == 'new') ? 'active' : ''; ?>">New Arrivals</a>
                <a href="product.php?category=sale<?php echo isset($_GET['sort']) ? '&sort=' . htmlspecialchars($_GET['sort']) : ''; ?>" class="sidebar-nav-link <?php echo ($category == 'sale') ? 'active' : ''; ?>">Sale Items</a>
            </div>
            
            <form action="product.php" method="GET" id="filter-form">
                <!-- Add CSRF token -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                <!-- Add current sort parameter to maintain sorting when filters are applied -->
                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($order_by); ?>">
                
                <div class="sidebar-section">
                    <h3>Price Range</h3>
                    <div class="price-range">
                        <div class="price-inputs">
                            <input type="number" name="min_price" id="min-price" class="price-input" placeholder="Min" value="<?php echo htmlspecialchars($min_price); ?>">
                            <input type="number" name="max_price" id="max-price" class="price-input" placeholder="Max" value="<?php echo htmlspecialchars($max_price); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="sidebar-section">
                    <h3>Sizes</h3>
                    <div class="filter-group">
                        <?php foreach ($available_sizes as $size): ?>
                        <div class="filter-item">
                            <input type="checkbox" name="sizes[]" id="size-<?php echo htmlspecialchars($size); ?>" value="<?php echo htmlspecialchars($size); ?>" <?php echo in_array($size, $sizes) ? 'checked' : ''; ?>>
                            <label for="size-<?php echo htmlspecialchars($size); ?>"><?php echo htmlspecialchars(strtoupper($size)); ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="sidebar-section">
                    <h3>Brand name</h3>
                    <div class="filter-group">
                        <select name="model_type" class="sort-select" style="width: 100%;">
                            <option value="">All Brand</option>
                            <?php foreach ($available_models as $model): ?>
                            <option value="<?php echo htmlspecialchars($model); ?>" <?php echo ($model_type == $model) ? 'selected' : ''; ?>><?php echo htmlspecialchars($model); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="apply-filters">Apply Filters</button>
            </form>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <section class="products-section">
                <div class="products-header">
                    <div class="products-title">
                        <?php 
                        if ($category == 'men') echo "Men's Fashion";
                        elseif ($category == 'women') echo "Women's Fashion";
                        elseif ($category == 'kids') echo "Kids Fashion";
                        else echo "All Products";
                        ?>
                        <span class="product-count">(<?php echo $total_products; ?> products)</span>
                    </div>
                    
                    <div class="products-sort">
    <span class="sort-label">Sort by:</span>
    <div class="sort-buttons">
        <?php
        // Create URLs for sorting
        $query_params = $_GET;
        
        // Set up low-to-high URL
        $query_params['sort'] = 'price-low';
        unset($query_params['page']); // Reset to page 1 when sorting changes
        $low_to_high_url = '?' . http_build_query($query_params);
        
        // Set up high-to-low URL
        $query_params['sort'] = 'price-high';
        $high_to_low_url = '?' . http_build_query($query_params);
        ?>
        <a href="<?php echo htmlspecialchars($low_to_high_url); ?>" class="sort-button <?php echo ($order_by == 'price-low') ? 'active' : ''; ?>">
            <i class="fas fa-sort-amount-down-alt"></i> Price: Low to High
        </a>
        <a href="<?php echo htmlspecialchars($high_to_low_url); ?>" class="sort-button <?php echo ($order_by == 'price-high') ? 'active' : ''; ?>">
            <i class="fas fa-sort-amount-down"></i> Price: High to Low
        </a>
    </div>
</div>
                </div>
                
                <div class="product-grid">
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            // Sanitize data before output
                            $title = htmlspecialchars($row['p_title']);
                            $brand = htmlspecialchars($row['p_brand']);
                            $price = number_format($row['p_price'], 2);
                            $image = htmlspecialchars(basename($row['p_mimage'])); // Use basename to prevent directory traversal
                            $product_id = (int)$row['p_id'];
                    ?>
                    <div class="product-card" data-product-id="<?php echo $product_id; ?>">
                        <a href="product_details.php?p_id=<?php echo $product_id; ?>" class="product-link">
                            <div class="product-image">
                                <img src="./../img/<?php echo $image; ?>" alt="<?php echo $title; ?>">
                                <div class="product-quick-view">Quick View</div>
                            </div>
                            <div class="product-info">
                                <div class="product-category"><?php echo $brand; ?></div>
                                <h3 class="product-title"><?php echo $title; ?></h3>
                                <div class="product-price">
                                    <div class="current-price">$<?php echo $price; ?></div>
                                    <?php if (isset($row['original_price']) && $row['original_price'] > $row['p_price']): 
                                        $original_price = number_format($row['original_price'], 2);
                                        $discount = round(($row['original_price'] - $row['p_price']) / $row['original_price'] * 100);
                                    ?>
                                    <div class="original-price">$<?php echo $original_price; ?></div>
                                    <div class="product-discount">-<?php echo $discount; ?>%</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php
                        }
                    } else {
                        echo "<p style='grid-column: 1 / -1; text-align: center; padding: 40px 0;'>No products found matching your criteria.</p>";
                    }
                    ?>
                </div>
                
                <!-- Pagination -->
                <div class="pagination">
                    <?php 
                    $total_pages = ceil($total_products / $items_per_page);
                    
                    // Build query string for pagination links
                    $query_params = $_GET;
                    unset($query_params['csrf_token']); // Remove CSRF token from URL
                    
                    // Show previous button if not on first page
                    if ($current_page > 1) {
                        $query_params['page'] = $current_page - 1;
                        $query_string = http_build_query($query_params);
                        echo "<a href='?$query_string' class='pagination-item prev'>&laquo; Prev</a>";
                    }
                    
                    // Determine page range to display
                    $page_range = 5; // Show 5 page numbers at a time
                    $start_page = max(1, $current_page - floor($page_range / 2));
                    $end_page = min($total_pages, $start_page + $page_range - 1);
                    
                    // Adjust start_page if end_page is at maximum
                    if ($end_page == $total_pages) {
                        $start_page = max(1, $total_pages - $page_range + 1);
                    }
                    
                    // Show page numbers
                    for ($i = $start_page; $i <= $end_page; $i++) {
                        $query_params['page'] = $i;
                        $query_string = http_build_query($query_params);
                        $active_class = ($current_page == $i) ? 'active' : '';
                        echo "<a href='?$query_string' class='pagination-item $active_class'>$i</a>";
                    }
                    
                    // Show next button if not on last page
                    if ($current_page < $total_pages) {
                        $query_params['page'] = $current_page + 1;
                        $query_string = http_build_query($query_params);
                        echo "<a href='?$query_string' class='pagination-item next'>Next &raquo;</a>";
                    }
                    ?>
                </div>
            </section>
        </main>
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
                    <li><a href="../home.php">Home</a></li>
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

    <script>
    // Generate CSRF token if it doesn't exist
    <?php if (!isset($_SESSION['csrf_token'])): ?>
    <?php $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); ?>
    <?php endif; ?>
    
    // Price slider interaction
    const priceSlider = document.getElementById('price-slider');
    const maxPriceInput = document.getElementById('max-price');
    
    priceSlider.addEventListener('input', function() {
        maxPriceInput.value = this.value;
    });
    
    maxPriceInput.addEventListener('input', function() {
        priceSlider.value = this.value;
    });
    
    // Quick view functionality
    document.querySelectorAll('.product-quick-view').forEach(function(quickView) {
        quickView.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productCard = this.closest('.product-card');
            const productId = productCard.dataset.productId;
            
            // Redirect to the product detail page with quickview parameter
            window.location.href = `product_detail.php?id=${productId}&quickview=1`;
        });
    });
    </script>
</body>
</html>