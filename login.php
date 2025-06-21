<?php
require_once 'includes/header.php';

// Redirect if already logged in
if (isLoggedIn()) {
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
    
    // Only allow certain pages for redirect
    if ($redirect === 'checkout') {
        redirect('checkout.php');
    } else {
        redirect('index.php');
    }
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }
    
    // If no errors, attempt login
    if (empty($errors)) {
        $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Save cart items before setting session variables
            $cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            // Restore cart items
            $_SESSION['cart'] = $cartItems;
            
            // Redirect based on user role or redirect parameter
            if ($user['is_admin'] == 1) {
                redirect('admin/dashboard.php');
            } else {
                $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
                
                // Only allow certain pages for redirect
                if ($redirect === 'checkout') {
                    redirect('checkout.php');
                } else {
                    // Redirect to home page instead of dashboard
                    redirect('index.php');
                }
            }
        } else {
            $errors['general'] = 'Invalid email or password';
        }
    }
}
?>

<style>
/* Login Page Styles */

body {
    min-height: 100vh;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', 'Arial', sans-serif;
    /* background removed */
}

.main-content-flex {
    /* min-height: calc(100vh - 70px); */ /* remove this */
    display: flex;
    justify-content: center;
    padding-top: 32px;
    padding-bottom: 32px;
}

.login-container {
    width: 100%;
    max-width: 380px;
    margin: 0 auto;
    padding: 0 10px;
}

.login-card {
    background-color: #fff;
    border-radius: 18px;
    box-shadow: 0 8px 32px rgba(60, 72, 88, 0.18);
    overflow: hidden;
    transition: box-shadow 0.2s;
    border: 1px solid #ececec;
}

.login-header {
    background: #fff;
    color: #23293a;
    padding: 16px 0 6px 0; /* reduced vertical padding */
    text-align: center;
    border-bottom: 1px solid #f0f0f0;
}

.login-header h2 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    letter-spacing: 1px;
}

.login-body {
    padding: 24px 22px 18px 22px; /* slightly reduced padding */
}

.alert {
    padding: 12px 15px;
    margin-bottom: 18px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 15px;
}

.alert-danger {
    background-color: #ffeaea;
    color: #d32f2f;
    border: 1px solid #ffd6d6;
}

.alert-success {
    background-color: #eaffea;
    color: #388e3c;
    border: 1px solid #c6f7d0;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    margin-bottom: 7px;
    color: #23293a;
    font-size: 15px;
}

.form-control {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #d1d9e6;
    border-radius: 6px;
    font-size: 15px;
    background-color: #f7fafd;
    color: #23293a;
    transition: border-color 0.2s, background 0.2s;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #6c63ff;
    background-color: #fff;
}

.form-control.is-invalid {
    border-color: #d32f2f;
    background: #fff6f6;
}

.error-feedback {
    display: block;
    color: #d32f2f;
    font-size: 13px;
    margin-top: 4px;
}

.login-btn {
    width: 100%;
    background: linear-gradient(90deg, #6c63ff 60%, #5a54c7 100%);
    color: #fff;
    border: none;
    padding: 13px 0;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 5px;
    box-shadow: 0 2px 8px rgba(108, 99, 255, 0.08);
    transition: background 0.2s, box-shadow 0.2s;
}

.login-btn:hover {
    background: linear-gradient(90deg, #5a54c7 60%, #6c63ff 100%);
    box-shadow: 0 4px 16px rgba(108, 99, 255, 0.13);
}

.login-footer {
    text-align: center;
    margin-top: 20px;
}

.login-footer p {
    color: #6c63ff;
    margin: 0;
    font-size: 15px;
}

.login-footer a {
    color: #5a54c7;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s;
}

.login-footer a:hover {
    text-decoration: underline;
    color: #23293a;
}

/* Responsive adjustments */
@media (max-width: 600px) {
    .main-content-flex {
        min-height: 80vh;
    }
    .login-container {
        margin: 0 auto;
    }
    .login-body {
        padding: 18px 8px 10px 8px;
    }
}
</style>

<div class="main-content-flex">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2>Login</h2>
            </div>
            <div class="login-body">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success_message']; ?></div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
                <?php endif; ?>
                
                <form action="login.php" method="POST" novalidate>
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                               id="email" name="email" value="<?php echo $email; ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <span class="error-feedback"><?php echo $errors['email']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                               id="password" name="password" required>
                        <?php if (isset($errors['password'])): ?>
                            <span class="error-feedback"><?php echo $errors['password']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="login-btn">Login</button>
                    </div>
                    
                    <div class="login-footer">
                        <p>Don't have an account? <a href="register.php">Register</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>