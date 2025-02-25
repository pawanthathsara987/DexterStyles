<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up & Sign In - DexterStyles</title>
    <link rel="stylesheet" href="./../css/home.css">
    <link rel="stylesheet" href="./../css/signin.css">
    <style>
        button {
            width: 100%;
            padding: 12px;
            background: #1877f2;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #166fe5;
        }

        .separator {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }

        .separator::before,
        .separator::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: #ddd;
        }

        .separator::before {
            left: 0;
        }

        .separator::after {
            right: 0;
        }

        .separator span {
            background: #fff;
            padding: 0 10px;
            color: #666;
        }

        .google-login-btn {
            width: 100%;
            padding: 12px 20px;
            background:rgb(198, 205, 216);
            color: black;
            border: 1px solid black;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .google-login-btn:hover {
            background:rgb(237, 239, 241);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .google-login-btn:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .google-icon {
            width: 24px;
            height: 24px;
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo"><img src="path-to-dexterstyles-logo.png" alt="DexterStyles Logo"></div>
            <ul class="nav-menu">
                <li><a href="/index.html">Home</a></li>
                <li><a href="/#shop">Shop</a></li>
                <li><a href="/#about">About</a></li>
                <li><a href="/#contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <div class="wrapper">
        <div class="container" id="container">
            <div class="form-container sign-up-container">
                <form>
                    <h1>Create Account</h1>
                    <div class="form-group">
                        <input type="text" placeholder="Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" placeholder="Password" required>
                    </div>
                    <button type="submit">Sign Up</button>
                </form>
            </div>

            <div class="form-container sign-in-container">
                <form>
                    <h1>Sign In</h1>
                    <div class="form-group">
                        <input type="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" placeholder="Password" required>
                    </div>
                    <button type="submit">Sign In</button>
                    <div class="separator">
                        <span>OR</span>
                    </div>
                    <button type="button" class="google-login-btn">
                        <img src="./../img/google.png" alt="Google Icon" class="google-icon">
                        Sign in with Google
                    </button>
                </form>
            </div>

            <div class="welcome-container">
                <div>
                    <div class="welcome-text welcome-signup">
                        <h1>Welcome!</h1>
                        <p>Join our community to get started.</p>
                        <div class="toggle-link">
                            <a onclick="toggleForm()">Already have an account? Sign In</a>
                        </div>
                    </div>
                    <div class="welcome-text welcome-signin">
                        <h1>Welcome Back!</h1>
                        <p>Sign in to access your account.</p>
                        <div class="toggle-link">
                            <a onclick="toggleForm()">Don't have an account? Sign Up</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleForm() {
            const container = document.getElementById('container');
            container.classList.toggle('active');
        }
    </script>
</body>
</html>