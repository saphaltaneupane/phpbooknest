<?php
// Adjust the path for includes based on the current directory
$relativePath = '../';
require_once $relativePath . 'includes/header.php';

// Redirect if not logged in or is admin
if (!isLoggedIn() || isAdmin()) {
    redirect($relativePath . 'login.php');
}

// Get user ID
$userId = $_SESSION['user_id'];

// Get user details
$user = getUserById($userId);

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validate input
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    }
    
    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required';
    } elseif (!isValidPhone($phone)) {
        $errors['phone'] = 'Phone number must be 10 digits';
    }
    
    // If changing password
    if (!empty($currentPassword) || !empty($newPassword) || !empty($confirmPassword)) {
        if (empty($currentPassword)) {
            $errors['current_password'] = 'Current password is required';
        } elseif ($currentPassword !== $user['password']) {
            $errors['current_password'] = 'Current password is incorrect';
        }
        
        if (empty($newPassword)) {
            $errors['new_password'] = 'New password is required';
        } elseif (strlen($newPassword) < 6) {
            $errors['new_password'] = 'Password must be at least 6 characters';
        }
        
        if ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
    }
    
    // If no errors, update profile
    if (empty($errors)) {
        $updateFields = "name = '$name', phone = '$phone', address = '$address'";
        
        // If changing password
        if (!empty($newPassword)) {
            $updateFields .= ", password = '$newPassword'";
        }
        
        $query = "UPDATE users SET $updateFields WHERE id = $userId";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['user_name'] = $name;
            $success = true;
            $user = getUserById($userId);
        } else {
            $errors['general'] = 'Error updating profile: ' . mysqli_error($conn);
        }
    }
}
?>

<style>
    /* Custom CSS Variables */
    :root {
        --primary-color: #6c63ff;
        --primary-dark: #5652db;
        --primary-light: #817dff;
        --secondary-color: #ff9d72;
        --success-color: #4caf50;
        --danger-color: #f44336;
        --light-color: #f8f9ff;
        --white-color: #ffffff;
        --dark-color: #2c2c54;
        --gray-light: #f0f2f9;
        --gray-medium: #e0e0e0;
        --gray-dark: #a0a0a0;
        --text-primary: #333333;
        --text-secondary: #666666;
        --text-muted: #999999;
        --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 5px 15px rgba(0, 0, 0, 0.08);
        --radius: 12px;
        --radius-sm: 6px;
        --transition: all 0.3s ease;
    }
    
    /* Main Profile Container */
    .profile-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    /* Profile Card */
    .profile-card {
        background-color: white;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        margin-bottom: 30px;
    }
    
    .profile-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #8367ff 100%);
        color: white;
        padding: 20px;
    }
    
    .profile-title {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    .profile-body {
        padding: 25px;
    }
    
    /* Form Styles */
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--text-primary);
    }
    
    .form-input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid var(--gray-medium);
        border-radius: var(--radius-sm);
        font-size: 16px;
        transition: var(--transition);
        background-color: white;
    }
    
    .form-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.2);
    }
    
    .form-input:disabled {
        background-color: var(--gray-light);
        cursor: not-allowed;
    }
    
    .form-input.error {
        border-color: var(--danger-color);
    }
    
    .form-input.error:focus {
        box-shadow: 0 0 0 3px rgba(244, 67, 54, 0.2);
    }
    
    .error-text {
        color: var(--danger-color);
        font-size: 0.85rem;
        margin-top: 5px;
        display: block;
    }
    
    .helper-text {
        color: var(--text-muted);
        font-size: 0.85rem;
        margin-top: 5px;
        display: block;
    }
    
    /* Textarea */
    .form-textarea {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid var(--gray-medium);
        border-radius: var(--radius-sm);
        font-size: 16px;
        min-height: 100px;
        resize: vertical;
        transition: var(--transition);
        background-color: white;
    }
    
    .form-textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.2);
    }
    
    /* Alert Messages */
    .alert {
        padding: 15px;
        border-radius: var(--radius-sm);
        margin-bottom: 20px;
        position: relative;
    }
    
    .alert-success {
        background-color: rgba(76, 175, 80, 0.1);
        border: 1px solid rgba(76, 175, 80, 0.5);
        color: var(--success-color);
    }
    
    .alert-error {
        background-color: rgba(244, 67, 54, 0.1);
        border: 1px solid rgba(244, 67, 54, 0.5);
        color: var(--danger-color);
    }
    
    /* Buttons */
    .button-group {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 25px;
    }
    
    .button {
        padding: 12px 24px;
        border-radius: 30px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        border: none;
        transition: var(--transition);
        font-size: 1rem;
        display: inline-block;
    }
    
    .button-primary {
        background-color: var(--primary-color);
        color: white;
    }
    
    .button-primary:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(108, 99, 255, 0.3);
    }
    
    .button-secondary {
        background-color: var(--gray-medium);
        color: var(--text-primary);
    }
    
    .button-secondary:hover {
        background-color: var(--gray-dark);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    /* Divider */
    .divider {
        height: 1px;
        background-color: var(--gray-medium);
        margin: 25px 0;
        border: none;
    }
    
    .section-title {
        font-size: 1.25rem;
        margin-top: 0;
        margin-bottom: 20px;
        color: var(--text-primary);
    }
    
    /* Responsive Adjustments */
    @media screen and (max-width: 768px) {
        .profile-container {
            padding: 0 15px;
        }
        
        .profile-body {
            padding: 20px;
        }
        
        .button-group {
            flex-direction: column-reverse;
            gap: 15px;
        }
        
        .button {
            width: 100%;
            text-align: center;
        }
    }
</style>

<div class="profile-container">
    <div class="profile-card">
        <div class="profile-header">
            <h3 class="profile-title">Edit Profile</h3>
        </div>
        <div class="profile-body">
            <?php if ($success): ?>
                <div class="alert alert-success">Profile updated successfully!</div>
            <?php endif; ?>
            
            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-error"><?php echo $errors['general']; ?></div>
            <?php endif; ?>
            
            <form action="profile.php" method="POST" novalidate>
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-input <?php echo isset($errors['name']) ? 'error' : ''; ?>" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <span class="error-text"><?php echo $errors['name']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-input" id="email" value="<?php echo $user['email']; ?>" disabled>
                    <span class="helper-text">Email cannot be changed</span>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-input <?php echo isset($errors['phone']) ? 'error' : ''; ?>" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>
                    <?php if (isset($errors['phone'])): ?>
                        <span class="error-text"><?php echo $errors['phone']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-textarea" id="address" name="address" rows="3"><?php echo $user['address']; ?></textarea>
                </div>
                
                <hr class="divider">
                <h5 class="section-title">Change Password (Optional)</h5>
                
                <div class="form-group">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-input <?php echo isset($errors['current_password']) ? 'error' : ''; ?>" id="current_password" name="current_password">
                    <?php if (isset($errors['current_password'])): ?>
                        <span class="error-text"><?php echo $errors['current_password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-input <?php echo isset($errors['new_password']) ? 'error' : ''; ?>" id="new_password" name="new_password">
                    <?php if (isset($errors['new_password'])): ?>
                        <span class="error-text"><?php echo $errors['new_password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-input <?php echo isset($errors['confirm_password']) ? 'error' : ''; ?>" id="confirm_password" name="confirm_password">
                    <?php if (isset($errors['confirm_password'])): ?>
                        <span class="error-text"><?php echo $errors['confirm_password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="button-group">
                    <a href="dashboard.php" class="button button-secondary">Back</a>
                    <button type="submit" class="button button-primary">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>