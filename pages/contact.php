<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="style2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./../css/home.css">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
        }

        .back-image {
            background-image: url('./../img/back.jpg');
            background-size: cover;
            background-position: center;
            height: %;
            color: rgb(255, 255, 255);
            padding: 50px 0;
            background-attachment: fixed;
        }

        .image {
            width: 100%;
            max-width: 1200px;
            height: 300px;
            object-fit: cover;
            display: block;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 40px;
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .image {
                height: 200px; /* Adjust for smaller screens */
            }
        }

        .container {
            background: rgba(182, 167, 167, 0.7);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 70%;
            margin: 0 auto;
        }

        .contact-info {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(180, 155, 155, 0.2);
            margin-bottom: 20px;
            color: #333;
        }

        .icon-frame {
            border: 2px solid #000;
            padding: 5px;
            border-radius: 100%;
            transition: transform 0.3s, background-color 0.3s, border-color 0.3s;
        }

        .icon-frame:hover {
            transform: scale(1.1);
            border-color: #007bff;
        }

        .social-icons img {
            width: 40px;
            height: 40px;
            background-color: black;
        }

        .social-icons {
            background-color: gray;
            padding: 10px;
        }

        h5, h6 {
            color: #000;
        }

        .flex-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .container {
                width: 90%;
            }

            .contact-info {
                width: 100%;
            }
        }

        .social-icon {
            display: inline-block;
            border-radius: 50%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .icon-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            transition: 0.3s ease;
        }

        /* Hover Effects */
        .social-icon:hover {
            transform: scale(1.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        /* Optional color tints */
        .facebook:hover .icon-img {
            background-color: #3b5998;
        }

        .whatsapp:hover .icon-img {
            background-color: #25D366;
        }

        .youtube:hover .icon-img {
            background-color: #FF0000;
        }


        .contact-heading {
            font-size: 3rem;
            font-weight: 1000;
            background: linear-gradient(90deg, #99FFFF, #ffffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            padding: 5px 10px;
            border-radius: 8px;
            text-transform: uppercase;
        }

        /* Floating label for inputs */
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .form-control-label {
            position: absolute;
            left: 10px;
            top: 10px;
            transition: 0.3s ease;
        }

        .form-control:focus + .form-control-label,
        .form-control:not(:placeholder-shown) + .form-control-label {
            top: -10px;
            font-size: 12px;
            color: #007bff;
        }

        /* Accordion Style for FAQ */
        .accordion-button:not(.collapsed) {
            background-color: #007bff;
            color: white;
        }

        .accordion-button.collapsed {
            background-color: #f8f9fa;
            color: #007bff;
        }
        .page-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            flex: 1;
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <header style="background-color: #F0EAD6;">
        <nav class="nav">
            <div class="logo"><a href="./../home.php"><img src="./../img/Logo.png" alt="DexterStyles Logo"></a></div>
            <ul class="nav-menu">
                <li><a href="./../home.php">Home</a></li>
                <li><a href="./product.php">Shop</a></li>
                <li><a href="./aboutus.php">About</a></li>
                <li><a href="./contact.php">Contact</a></li>
            </ul>
            <div class="nav-actions">
                <a href="./view_cart.php" class="cart-icon">🛒</a>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <a href="./profile.php" class="p-pic">
                    <img src="./../img/profile_photo/<?php echo empty($_SESSION['profile_pic']) ? 'blankprofile.jpg' : $_SESSION['profile_pic']; ?>" alt="Profile">
                </a>
                <?php endif; ?>

            </div>
        </nav>
    </header>
    <div class="image">
        <img src="./../img/covers1.png" alt="Cover Photo" class="image">
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-6 contact-info p-4">
                <h4 class="text-center">Have questions or need help?</h4>
                <p class="text-center">
                Have questions or need help with your shopping experience? Our support team is here for you! Just fill out the form below, and we’ll respond quickly to ensure you have a smooth shopping experience.
                </p>
                <br>
                <h4 class="text-center font-weight-bold mb-3">Contact Information</h4>
                <div class="mb-2">
                    <img src="./../img/phone.png" alt="Call" style="width: 30px;">
                    <strong>Call Us:</strong> <span>+37 456432798</span>
                </div>
                <div class="mb-2">
                    <img src="./../img/email.png" alt="Email" style="width: 30px;">
                    <strong>Email:</strong> <span>teamdexter.com</span>
                </div>
                <div class="mb-2">
                    <img src="./../img/phone.png" alt="Call" style="width: 30px;">
                    <strong>Alternative:</strong> <span>+94 779988453</span>
                </div>
                <div class="mb-2">
                    <img src="./../img/location.png" alt="Location" style="width: 30px;">
                    <strong>Location:</strong> <span>Kamburupitiya, Matara</span>
                </div>
                <div class="d-flex justify-content-center gap-3 mt-3">
                    <a href="https://web.facebook.com/" class="social-icon facebook"><img src="img/facebook.png" alt="Facebook" class="icon-img"></a>
                    <a href="https://web.whatsapp.com/" class="social-icon whatsapp"><img src="img/whatsapp.png" alt="WhatsApp" class="icon-img"></a>
                    <a href="https://www.youtube.com/" class="social-icon youtube"><img src="img/youtube.png" alt="YouTube" class="icon-img"></a>
                </div>

            </div>

            <div class="col-md-6 p-4">
                <h3 class="text-center mb-4">Get In Touch</h3>
                <form method="post" action="sendmail.php" onsubmit="return validateForm()">
                    <div class="mb-3 position-relative">
                        <input type="text" class="form-control" name="fname" id="firstName" placeholder=" " value="<?php echo htmlspecialchars($_POST['fname'] ?? ''); ?>">
                        <label for="firstName" class="form-control-label">First Name</label>
                        <?php if (isset($_SESSION['errors']['fname'])): ?>
                            <div class="text-danger"><?php echo $_SESSION['errors']['fname']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3 position-relative">
                        <input type="text" class="form-control" name="lname" id="lastName" placeholder=" " value="<?php echo htmlspecialchars($_POST['lname'] ?? ''); ?>">
                        <label for="lastName" class="form-control-label">Last Name</label>
                        <?php if (isset($_SESSION['errors']['lname'])): ?>
                            <div class="text-danger"><?php echo $_SESSION['errors']['lname']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3 position-relative">
                        <input type="email" class="form-control" name="mail" id="email" placeholder=" " value="<?php echo htmlspecialchars($_POST['mail'] ?? ''); ?>">
                        <label for="email" class="form-control-label">Email Address</label>
                        <?php if (isset($_SESSION['errors']['mail'])): ?>
                            <div class="text-danger"><?php echo $_SESSION['errors']['mail']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3 position-relative">
                        <input type="tel" class="form-control" name="phone" id="phone" placeholder=" " value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                        <label for="phone" class="form-control-label">Phone Number</label>
                        <?php if (isset($_SESSION['errors']['phone'])): ?>
                            <div class="text-danger"><?php echo $_SESSION['errors']['phone']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3 position-relative">
                        <textarea class="form-control" id="message" name="msg" rows="4" placeholder=""><?php echo htmlspecialchars($_POST['msg'] ?? ''); ?></textarea>
                        <label for="message" class="form-control-label">Message</label>
                        <?php if (isset($_SESSION['errors']['msg'])): ?>
                            <div class="text-danger"><?php echo $_SESSION['errors']['msg']; ?></div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" name="send" class="btn btn-primary w-100">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Location Modal -->

    <!-- Find Location Button -->
    <div class="d-flex justify-content-center mt-4 mb-4">
                    <button type="button" class="btn btn-success px-4 py-2" data-bs-toggle="modal" data-bs-target="#locationModal">
                        <span id="toggleIcon">+</span> View Our Location
                    </button>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg rounded-4 border-0">
        
        <!-- Modal Header -->
        <div class="modal-header bg-gradient text-white rounded-top-4" style="background: linear-gradient(135deg, #4e73df, #1cc88a);">
            <h5 class="modal-title fw-bold" id="locationModalLabel">🌍 Our Locations</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body bg-light-subtle">
            <div class="row g-4">
            <!-- Location Card Template -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 rounded-4 hover-shadow transition-all">
                <div class="card-body text-center p-4">
                    <h5 class="card-title fw-semibold">Kamburupitiya</h5>
                    <p class="card-text text-muted small">
                    Located in Matara District, Southern Province.<br>
                    No:1289,Galle Road,Matara <br>
                    🕘 Open weekdays: <strong>9 AM – 6 PM</strong>
                    </p>
                </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 rounded-4 hover-shadow transition-all">
                <div class="card-body text-center p-4">
                    <h5 class="card-title fw-semibold">Colombo</h5>
                    <p class="card-text text-muted small">
                    Capital of the Western Province.<br>
                    4 Mile Post Avenue, 03,Colombo <br>
                    🕘 Open weekdays: <strong>9 AM – 6 PM</strong>
                    </p>
                </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 rounded-4 hover-shadow transition-all">
                <div class="card-body text-center p-4">
                    <h5 class="card-title fw-semibold">Kandy</h5>
                    <p class="card-text text-muted small">
                    Cultural capital in Central Province.<br>
                    No 166/2, Kulugammana,Kandy <br>
                    🕘 Open weekdays: <strong>9 AM – 6 PM</strong>
                    </p>
                </div>
                </div>
            </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer bg-white border-top-0">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
        </div>

        </div>
    </div>
    </div>


    <!-- FAQ Accordion -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Frequently Asked Questions</h2>
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="faq1">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqContent1" aria-expanded="true" aria-controls="faqContent1">
                        How do I track my order?
                    </button>
                </h2>
                <div id="faqContent1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Go to 'My Orders' and enter your tracking ID.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faq2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqContent2" aria-expanded="false" aria-controls="faqContent2">
                        What is your return policy?
                    </button>
                </h2>
                <div id="faqContent2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        You can return items within 30 days of purchase.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faq3">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqContent3" aria-expanded="false" aria-controls="faqContent3">
                        Do you offer international shipping?
                    </button>
                </h2>
                <div id="faqContent3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes, we ship worldwide.
                    </div>
                </div>
            </div>
        </div>
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
                    <li><a href="./home.php">Home</a></li>
                    <li><a href="./pages/product.php">Shop</a></li>
                    <li><a href="./pages/aboutus.php">About</a></li>
                    <li><a href="./pages/contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="www.facebook.com">Facebook</a>
                    <a href="www.instagram.com">Instagram</a>
                    <a href="www.twitter.com">Twitter</a>
                </div>
            </div>
        </div>
        <p class="footer-bottom">© 2025 DexterStyles. All rights reserved.</p>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    var messageText = "<?= $_SESSION['STATUS'] ?? ''; ?>";
    if (messageText !== '') {
        Swal.fire({
            title: "Notification",
            text: messageText,
            icon: "success"
        }).then(() => {
            <?php unset($_SESSION['STATUS']); ?>
        });
    }

    document.getElementById('faqSearch').addEventListener('input', function() {
        let searchQuery = this.value.toLowerCase();
        document.querySelectorAll('#faqList .card').forEach(card => {
            let question = card.querySelector('.card-title').innerText.toLowerCase();
            card.style.display = question.includes(searchQuery) ? 'block' : 'none';
        });
    });
</script>

</body>
</html>
