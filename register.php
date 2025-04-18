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
    /* Registration page styles with pure CSS (no Bootstrap) */
    * {
        box-sizing: border-box;
    }
    
    .register-container {
        width: 100%;
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }
    
    .register-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.3s ease-in-out;
        margin-bottom: 30px;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .register-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
    }
    
    .register-header {
        background-color: #6c63ff;
        padding: 18px 25px;
        color: white;
    }
    
    .register-header h3 {
        margin: 0;
        font-weight: 600;
        letter-spacing: 0.3px;
        font-size: 1.5rem;
    }
    
    .register-body {
        padding: 30px 25px;
    }
    
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        font-weight: 500;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .form-row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
    }
    
    .form-column {
        flex: 1 0 100%;
        padding: 0 10px;
        margin-bottom: 20px;
    }
    
    @media (min-width: 768px) {
        .form-column.half {
            flex: 0 0 50%;
        }
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }
    
    .form-control {
        width: 100%;
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #dce1e6;
        transition: all 0.25s ease;
        background-color: #f9fbfd;
        color: #333;
        font-size: 16px;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #6c63ff;
        box-shadow: 0 0 0 4px rgba(108, 99, 255, 0.15);
        background-color: #fff;
    }
    
    .form-control.is-invalid {
        border-color: #dc3545;
        background-color: #fff;
    }
    
    .form-text {
        display: block;
        color: #6c757d;
        font-size: 0.85rem;
        margin-top: 5px;
    }
    
    .error-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.85rem;
        margin-top: 5px;
        font-weight: 500;
    }
    
    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }
    
    .btn {
        display: inline-block;
        font-weight: 500;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        cursor: pointer;
        padding: 12px 20px;
        border-radius: 8px;
        border: none;
        font-size: 1rem;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .btn-primary {
        background-color: #6c63ff;
        color: white;
        box-shadow: 0 4px 10px rgba(108, 99, 255, 0.2);
    }
    
    .btn-primary:hover, .btn-primary:focus {
        background-color: #5652db;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(108, 99, 255, 0.3);
    }
    
    .btn-primary:active {
        transform: translateY(0);
    }
    
    .text-center {
        text-align: center;
    }
    
    .text-center p {
        margin-top: 15px;
        color: #6c757d;
    }
    
    .text-center a {
        color: #6c63ff;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .text-center a:hover {
        color: #5652db;
        text-decoration: underline;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .register-container {
            padding: 20px 15px;
        }
        
        .register-body {
            padding: 25px 20px;
        }
        
        .register-header {
            padding: 15px 20px;
        }
    }
</style>

<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <h3>Create an Account</h3>
        </div>
        <div class="register-body">
            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
            <?php endif; ?>
            
            <form action="register.php" method="POST" novalidate>
                <div class="form-row">
                    <div class="form-column half">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                               id="name" name="name" value="<?php echo $name; ?>" required>
                        <?php if (isset($errors['name'])): ?>
                            <span class="error-feedback"><?php echo $errors['name']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-column half">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                               id="email" name="email" value="<?php echo $email; ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <span class="error-feedback"><?php echo $errors['email']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" 
                           id="phone" name="phone" value="<?php echo $phone; ?>" placeholder="10-digit phone number" required>
                    <?php if (isset($errors['phone'])): ?>
                        <span class="error-feedback"><?php echo $errors['phone']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-row">
                    <div class="form-column half">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                               id="password" name="password" required>
                        <span class="form-text">Password must be at least 6 characters long.</span>
                        <?php if (isset($errors['password'])): ?>
                            <span class="error-feedback"><?php echo $errors['password']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-column half">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                               id="confirm_password" name="confirm_password" required>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <span class="error-feedback"><?php echo $errors['confirm_password']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="address" class="form-label">Delivery Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3" 
                              placeholder="Enter your complete delivery address"><?php echo $address; ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Register</button>
                </div>
                
                <div class="text-center">
                    <p>Already have an account? <a href="login.php">Login</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>