<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DexterStyles - Trendy Clothing for Every Style</title>
    <link rel="stylesheet" href="./css/home.css">
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo"><img src="path-to-dexterstyles-logo.png" alt="DexterStyles Logo"></div>
            <ul class="nav-menu">
                <li><a href="#home">Home</a></li>
                <li><a href="#shop">Shop</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <div class="nav-actions">
                <a href="#search" class="search-icon">🔍</a>
                <a href="#cart" class="cart-icon">🛒</a>
                <button class="sign-in-btn" onclick="window.location.href='./pages/signin.php'">Sign In</button>
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
                <button class="cta-button">Shop Now</button>
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
                <div class="category-card" data-category="bags">
                    <img src="./img/bagsandshoes.jpg" alt="Bags">
                    <h3>Bags & Shoes</h3>
                </div>
            </div>
        </section>

        <section class="featured-products" id="shop">
            <h2>Featured Products</h2>
            <div class="product-grid">
                <div class="product-card">
                    <img src="https://via.placeholder.com/300x400?text=Product+1" alt="Product 1">
                    <h3>Chic Summer Dress</h3>
                    <p>$49.99</p>
                    <button class="add-to-cart">Add to Cart</button>
                </div>
                <div class="product-card">
                    <img src="https://via.placeholder.com/300x400?text=Product+2" alt="Product 2">
                    <h3>Stylish Jacket</h3>
                    <p>$89.99</p>
                    <button class="add-to-cart">Add to Cart</button>
                </div>
                <div class="product-card">
                    <img src="https://via.placeholder.com/300x400?text=Product+3" alt="Product 3">
                    <h3>Trendy Jeans</h3>
                    <p>$59.99</p>
                    <button class="add-to-cart">Add to Cart</button>
                </div>
            </div>
        </section>

        <section class="about" id="about">
            <h2>About DexterStyles</h2>
            <p>DexterStyles is your destination for cutting-edge fashion, blending quality craftsmanship with contemporary designs. We celebrate individuality and style, offering eco-friendly and trendy clothing for all.</p>
            <button class="learn-more">Learn More</button>
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

    <script>
        // Add click handlers for category cards (optional, for interactivity)
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', () => {
                const category = card.getAttribute('data-category');
                console.log(`Navigating to ${category} category...`);
                // Add actual navigation logic here (e.g., window.location.href = `/${category}`);
            });
        });
    </script>
</body>
</html>