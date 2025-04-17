<?php
session_start();

include './php_files/conn.php';

// Function to get featured products
function getFeaturedProducts($conn, $limit = 6) {
    $featuredProducts = [];
    
    // Query to select featured products
    $sql = "SELECT p.p_id, p.p_title, p.p_description, p.p_price, p.p_brand, 
                   p.p_size, p.p_material, p.p_mimage, c.c_name as category 
            FROM product_details p 
            JOIN category c ON p.c_id = c.c_id 
            ORDER BY RAND() 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $featuredProducts[] = $row;
    }
    
    $stmt->close();
    return $featuredProducts;
}

// Get featured products
$featuredProducts = getFeaturedProducts($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DexterStyles - Trendy Clothing for Every Style</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-DQvkBjpPgn7RC31MCQoOeC9TI2kdqa4+BSgNMNj8v77fdC77Kj5zpWFTJaaAoMbC" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/home.css">
    <style>
        .product-card {
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: scale(1.05);
        }
        .product-image-link {
            display: block;
            cursor: pointer;
        }
        .carousel-inner img {
            transition: opacity 0.3s;
        }
        .carousel-inner img:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <header style="background-color: #F0EAD6;">
        <nav class="nav">
            <div class="logo"><a href="./home.php"><img src="./img/Logo.png" alt="DexterStyles Logo"></a></div>
            <ul class="nav-menu">
                <li><a href="./home.php">Home</a></li>
                <li><a href="./pages/product.php">Shop</a></li>
                <li><a href="./pages/aboutus.php">About</a></li>
                <li><a href="./pages/contact.php">Contact</a></li>
            </ul>
            <div class="nav-actions">
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <a href="./pages/view_cart.php" class="cart-icon">🛒</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <a href="./pages/profile.php" class="p-pic">
                    <img src="./img/profile_photo/<?php echo empty($_SESSION['profile_pic']) ? 'blankprofile.jpg' : $_SESSION['profile_pic']; ?>" alt="Profile">
                </a>
                <?php else: ?>
                    <a href="./pages/signin.php" class="signin-btn">Sign In</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-image">
                <img src="./img/fashion-model.jpg" alt="DexterStyles Fashion Model">
            </div>
            <div class="hero-content">
                <h1>Discover Timeless Style with DexterStyles</h1>
                <p>Elevate your wardrobe with our exclusive collection of trendy clothing.</p>
                <button class="cta-button" onclick="window.location.href='./pages/product.php'">Shop Now</button>
            </div>
        </section>

        <section class="categories">
            <h2>Shop by Category</h2>
            <div class="category-grid">
                <div class="category-card" data-category="women">
                    <img src="./img/women.jpg" alt="Women Clothing">
                    <h3>Women</h3>
                </div>
                <div class="category-card" data-category="men">
                    <img src="./img/men.jpg" alt="Men Clothing">
                    <h3>Men</h3>
                </div>
                <div class="category-card" data-category="kids">
                    <img src="./img/kids.jpg" alt="Kids Clothing">
                    <h3>Kids</h3>
                </div>
            </div>
        </section>

        <section class="featured-products" id="shop">
            <h2>Featured Products</h2>
            <div class="product-grid">
                <?php foreach ($featuredProducts as $product): ?>
                    <?php 
                        $productId = $product['p_id'];
                        $imageNames = [];
                        // Use main image if available
                        if (!empty($product['p_mimage'])) {
                            $imageNames[] = "./img/" . $product['p_mimage'];
                        } else {
                            $imageNames[] = "./img/placeholder.jpg";
                        }
                    ?>
                    <div class="product-card">
                        <a href="./pages/product_details.php?p_id=<?php echo $productId; ?>" class="product-image-link">
                            <div id="carousel<?php echo $productId; ?>" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
                                <div class="carousel-inner">
                                    <?php foreach ($imageNames as $index => $imageName): ?>
                                        <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                                            <img src="<?php echo htmlspecialchars($imageName); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($product['p_title']); ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </a>
                        <h3><?php echo htmlspecialchars($product['p_title']); ?></h3>
                        <p>$<?php echo number_format($product['p_price'], 2); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="about" id="about">
            <h2>About DexterStyles</h2>
            <p>DexterStyles is your destination for cutting-edge fashion, blending quality craftsmanship with contemporary designs. We celebrate individuality and style, offering eco-friendly and trendy clothing for all.</p>
            <button class="learn-more" onclick="window.location.href='./pages/aboutus.php'">Learn More</button>
        </section>
    </main>

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
                    <li><a href="./home.php">Home</a></li>
                    <li><a href="./pages/product.php">Shop</a></li>
                    <li><a href="./pages/aboutus.php">About</a></li>
                    <li><a href="./pages/contact.php">Contact</a></li>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/js/bootstrap.bundle.min.js" integrity="sha384-YUe2LzesAfftltw+PEaao2tjU/QATaW/rOitAq67e0CT0Zi2VVRL0oC4+gAaeBKu" crossorigin="anonymous"></script>
    <script>
        // Add click handlers for category cards
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', () => {
                const category = card.getAttribute('data-category');
                window.location.href = `./pages/product.php?category=${category}`;
            });
        });
    </script>

    <?php $conn->close(); ?>
</body>
</html>