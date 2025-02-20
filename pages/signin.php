<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up & Sign In</title>
    <link rel="stylesheet" href="./../css/signin.css">
</head>
<body>
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

    <script src="./../js/signin.js"></script>
</body>
</html>