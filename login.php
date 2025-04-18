<?php
require_once 'includes/header.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
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
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            // Redirect based on user role
            if ($user['is_admin'] == 1) {
                redirect('admin/dashboard.php');
            } else {
                redirect('user/dashboard.php');
            }
        } else {
            $errors['general'] = 'Invalid email or password';
        }
    }
}
?>

<style>
    /* Login Page Styles */
    .login-container {
        width: 100%;
        max-width: 450px;
        margin: 50px auto;
        padding: 0 15px;
    }
    
    .login-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .login-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
    }
    
    .login-header {
        background-color: #6c63ff;
        color: white;
        padding: 20px;
        text-align: center;
    }
    
    .login-header h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
    }
    
    .login-body {
        padding: 30px;
    }
    
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 6px;
        font-weight: 500;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        font-weight: 500;
        margin-bottom: 8px;
        color: #495057;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        font-size: 16px;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.25);
    }
    
    .form-control.is-invalid {
        border-color: #dc3545;
    }
    
    .error-feedback {
        display: block;
        color: #dc3545;
        font-size: 14px;
        margin-top: 5px;
    }
    
    .login-btn {
        width: 100%;
        background-color: #6c63ff;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(108, 99, 255, 0.25);
    }
    
    .login-btn:hover {
        background-color: #5652db;
        transform: translateY(-2px);
        box-shadow: 0 6px 10px rgba(108, 99, 255, 0.3);
    }
    
    .login-footer {
        text-align: center;
        margin-top: 20px;
    }
    
    .login-footer p {
        color: #6c757d;
        margin: 0;
    }
    
    .login-footer a {
        color: #6c63ff;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .login-footer a:hover {
        text-decoration: underline;
        color: #5652db;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .login-container {
            margin: 30px auto;
        }
        
        .login-body {
            padding: 25px 20px;
        }
    }
</style>

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

<?php require_once 'includes/footer.php'; ?>