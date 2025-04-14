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
    /* Internal CSS for Login Page */
    .login-container {
        padding: 50px 0;
        min-height: calc(100vh - 250px);
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
    }
    
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
    }
    
    .card-header {
        background-color: #007bff !important;
        padding: 20px;
        border-bottom: none;
    }
    
    .card-header h3 {
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .card-body {
        padding: 30px;
    }
    
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
    }
    
    .form-control {
        border-radius: 6px;
        padding: 12px 15px;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
        border-color: #007bff;
    }
    
    .form-control.is-invalid {
        border-color: #dc3545;
    }
    
    .invalid-feedback {
        font-size: 0.85rem;
        margin-top: 5px;
    }
    
    .btn-primary {
        background-color: #007bff;
        border: none;
        padding: 12px 20px;
        font-weight: 500;
        letter-spacing: 0.5px;
        border-radius: 6px;
        box-shadow: 0 4px 6px rgba(0, 123, 255, 0.25);
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover, .btn-primary:focus {
        transform: translateY(-2px);
        box-shadow: 0 6px 10px rgba(0, 123, 255, 0.3);
        background-color: #0069d9;
    }
    
    .btn-primary:active {
        transform: translateY(0);
    }
    
    .alert {
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 20px;
        border: none;
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
    
    .text-center p {
        margin-top: 15px;
        color: #6c757d;
    }
    
    .text-center a {
        color: #007bff;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .text-center a:hover {
        color: #0056b3;
        text-decoration: underline;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .login-container {
            padding: 30px 15px;
        }
        
        .card-body {
            padding: 25px 20px;
        }
    }
</style>

<div class="login-container">
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Login</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success"><?php echo $_SESSION['success_message']; ?></div>
                            <?php unset($_SESSION['success_message']); ?>
                        <?php endif; ?>
                        
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
                        <?php endif; ?>
                        
                        <form action="login.php" method="POST" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo $email; ?>" required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" required>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </div>
                            
                            <div class="text-center">
                                <p>Don't have an account? <a href="register.php">Register</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>