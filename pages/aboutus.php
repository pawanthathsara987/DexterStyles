<?php
session_start();

include './../php_files/conn.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - DexterStyles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-DQvkBjpPgn7RC31MCQoOeC9TI2kdqa4+BSgNMNj8v77fdC77Kj5zpWFTJaaAoMbC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/aboutus.css">
</head>
<body>
    <header style="background-color: #F0EAD6;">
        <nav class="nav">
            <div class="logo"><a href="../home.php"><img src="../img/Logo.png" alt="DexterStyles Logo"></a></div>
            <ul class="nav-menu">
                <li><a href="../home.php">Home</a></li>
                <li><a href="./product.php">Shop</a></li>
                <li><a href="./aboutus.php">About</a></li>
                <li><a href="./contact.php">Contact</a></li>
            </ul>
            <div class="nav-actions">
                <a href="./view_cart.php" class="cart-icon">🛒</a>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <a href="./profile.php" class="p-pic">
                    <img src="../img/profile_photo/<?php echo empty($_SESSION['profile_pic']) ? 'blankprofile.jpg' : $_SESSION['profile_pic']; ?>" alt="Profile">
                </a>
                <?php else: ?>
                    <a href="./signin.php" class="signin-btn">Sign In</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <section class="about-hero parallax">
            <div class="hero-slideshow">
                <div class="hero-slide"></div>
                <div class="hero-slide"></div>
                <div class="hero-slide"></div>
            </div>
            <div id="particles-js"></div>
            <div class="hero-content">
                <h1>About DexterStyles</h1>
                <p class="main-tagline">Where passion for fashion meets timeless craftsmanship.</p>
                <p class="sub-tagline">Join us on a journey to redefine style.</p>
                <a href="#our-story" class="hero-cta">Learn Our Story</a>
            </div>
        </section>

        <section class="section our-story" id="our-story">
            <h2>Our Story</h2>
            <p>Launched in 2020, DexterStyles emerged from a dream to blend elegance with sustainability. What began as a small collective of visionary designers has grown into a brand that celebrates individuality through carefully curated collections. Each piece reflects our love for fashion and commitment to quality.</p>
        </section>

        <section class="section our-mission">
            <h2>Our Mission</h2>
            <p>We aim to inspire confidence through fashion that’s sustainable, inclusive, and innovative. DexterStyles is dedicated to creating clothing that not only looks good but feels good—crafted with care for both people and the planet.</p>
        </section>

        <section class="section our-values">
            <h2>Our Values</h2>
            <div class="values-grid">
                <div class="value-card">
                    <i class="fas fa-leaf"></i>
                    <h3>Sustainability</h3>
                    <p>Using eco-friendly materials and ethical practices to protect our planet.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-star"></i>
                    <h3>Quality</h3>
                    <p>Designing durable, stylish pieces that stand the test of time.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-heart"></i>
                    <h3>Inclusivity</h3>
                    <p>Embracing all styles, sizes, and stories with open hearts.</p>
                </div>
            </div>
        </section>

        <section class="team-section">
            <h2>Meet Our Team</h2>
            <div id="teamCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="team-card">
                            <img src="../img/team/team-member1.jpg" alt="Jane Doe">
                            <h3>Jane Doe</h3>
                            <p>Founder & Creative Director</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="team-card">
                            <img src="../img/team/team-member2.jpg" alt="John Smith">
                            <h3>John Smith</h3>
                            <p>Head Designer</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="team-card">
                            <img src="../img/team/team-member3.jpg" alt="Emily Brown">
                            <h3>Emily Brown</h3>
                            <p>Marketing Manager</p>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#teamCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#teamCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </section>

        <section class="cta-section">
            <h2>Explore Your Style</h2>
            <p>Join us in celebrating fashion that’s bold, sustainable, and uniquely you.</p>
            <a href="./product.php" class="cta-button">Shop Now</a>
        </section>
    </main>

    <footer class="footer">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/js/bootstrap.bundle.min.js" integrity="sha384-YUe2LzesAfftltw+PEaao2tjU/QATaW/rOitAq67e0CT0Zi2VVRL0oC4+gAaeBKu" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script>
        // Team Carousel
        document.addEventListener('DOMContentLoaded', () => {
            const teamCarousel = document.querySelector('#teamCarousel');
            new bootstrap.Carousel(teamCarousel, {
                interval: 4000,
                ride: 'carousel'
            });

            // Particles.js for subtle sparkles
            particlesJS('particles-js', {
                particles: {
                    number: { value: 50, density: { enable: true, value_area: 800 } },
                    color: { value: '#F0EAD6' },
                    shape: { type: 'circle' },
                    opacity: { value: 0.5, random: true },
                    size: { value: 3, random: true },
                    line_linked: { enable: false },
                    move: {
                        enable: true,
                        speed: 1,
                        direction: 'none',
                        random: true,
                        straight: false,
                        out_mode: 'out'
                    }
                },
                interactivity: {
                    detect_on: 'canvas',
                    events: {
                        onhover: { enable: true, mode: 'repulse' },
                        onclick: { enable: true, mode: 'push' },
                        resize: true
                    },
                    modes: {
                        repulse: { distance: 100, duration: 0.4 },
                        push: { particles_nb: 4 }
                    }
                },
                retina_detect: true
            });

            // Parallax Scroll Effect
            const hero = document.querySelector('.about-hero');
            window.addEventListener('scroll', () => {
                const scrollPosition = window.pageYOffset;
                hero.style.backgroundPositionY = `${scrollPosition * 0.5}px`;
            });
        });
    </script>
</body>
</html>