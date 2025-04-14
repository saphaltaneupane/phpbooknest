<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

$errors = [];
$name = $email = $phone = $address = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = sanitize($_POST['address']);
    
    // Validate input
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!isValidEmail($email)) {
        $errors['email'] = 'Invalid email format';
    } else {
        // Check if email already exists
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            $errors['email'] = 'Email already exists';
        }
    }
    
    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required';
    } elseif (!isValidPhone($phone)) {
        $errors['phone'] = 'Phone number must be 10 digits';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters';
    }
    
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }
    
    // If no errors, register the user
    if (empty($errors)) {
        // Insert user into database
        $query = "INSERT INTO users (name, email, phone, password, address)
                  VALUES ('$name', '$email', '$phone', '$password', '$address')";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_message'] = 'Registration successful! Please login.';
            redirect('login.php');
        } else {
            $errors['general'] = 'Registration failed: ' . mysqli_error($conn);
        }
    }
}

// Include header
require_once 'includes/header.php';
?>

<style>
    /* Internal CSS for Registration Page */
    .register-container {
        padding: 40px 0;
        background-color: #f8f9fa;
        min-height: calc(100vh - 250px);
    }
    
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.3s ease-in-out;
        margin-bottom: 30px;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
    }
    
    .card-header {
        background-color: #007bff !important;
        padding: 18px 25px;
        border-bottom: none;
    }
    
    .card-header h3 {
        font-weight: 600;
        letter-spacing: 0.3px;
        font-size: 1.5rem;
    }
    
    .card-body {
        padding: 30px 25px;
    }
    
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }
    
    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #dce1e6;
        transition: all 0.25s ease;
        background-color: #f9fbfd;
        color: #333;
        font-size: 0.95rem;
    }
    
    .form-control:focus {
        box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.15);
        border-color: #007bff;
        background-color: #fff;
    }
    
    .form-control.is-invalid {
        border-color: #dc3545;
        background-color: #fff;
    }
    
    .invalid-feedback {
        font-size: 0.85rem;
        margin-top: 5px;
        font-weight: 500;
    }
    
    .form-text {
        color: #6c757d;
        font-size: 0.85rem;
        margin-top: 5px;
    }
    
    textarea.form-control {
        min-height: 100px;
    }
    
    .btn-primary {
        background-color: #007bff;
        border: none;
        padding: 12px 20px;
        font-weight: 500;
        letter-spacing: 0.5px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
        transition: all 0.3s ease;
        font-size: 1rem;
    }
    
    .btn-primary:hover, .btn-primary:focus {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 123, 255, 0.3);
        background-color: #0069d9;
    }
    
    .btn-primary:active {
        transform: translateY(0);
    }
    
    .alert {
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        border: none;
        font-weight: 500;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
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
    
    /* Field Icons - Uncomment if you want to add icons to fields */
    /*
    .input-group-text {
        background-color: transparent;
        border-right: none;
        color: #007bff;
    }
    
    .input-group .form-control {
        border-left: none;
    }
    */
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .register-container {
            padding: 20px 15px;
        }
        
        .card-body {
            padding: 25px 20px;
        }
        
        .card-header {
            padding: 15px 20px;
        }
    }
</style>

<div class="register-container">
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Create an Account</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
                        <?php endif; ?>
                        
                        <form action="register.php" method="POST" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo $name; ?>" required>
                                    <?php if (isset($errors['name'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo $email; ?>" required>
                                    <?php if (isset($errors['email'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" id="phone" name="phone" value="<?php echo $phone; ?>" placeholder="10-digit phone number" required>
                                <?php if (isset($errors['phone'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['phone']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" required>
                                    <div class="form-text">Password must be at least 6 characters long.</div>
                                    <?php if (isset($errors['password'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" id="confirm_password" name="confirm_password" required>
                                    <?php if (isset($errors['confirm_password'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['confirm_password']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="address" class="form-label">Delivery Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter your complete delivery address"><?php echo $address; ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary w-100">Register</button>
                            </div>
                            
                            <div class="text-center">
                                <p>Already have an account? <a href="login.php">Login</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>