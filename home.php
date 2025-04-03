<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DexterStyles - Trendy Clothing for Every Style</title>
    <link rel="stylesheet" href="./css/home.css">
</head>
<body>
    <header>
        <nav class="nav">
            <div class="logo"><a href="./home.php"><img src="path-to-dexterstyles-logo.png" alt="DexterStyles Logo"></a></div>
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
    </main>
</body>
</html>