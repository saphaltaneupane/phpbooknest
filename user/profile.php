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

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Edit Profile</h3>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">Profile updated successfully!</div>
                <?php endif; ?>
                
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
                <?php endif; ?>
                
                <form action="profile.php" method="POST" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="<?php echo $user['email']; ?>" disabled>
                        <small class="text-muted">Email cannot be changed</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>
                        <?php if (isset($errors['phone'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['phone']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo $user['address']; ?></textarea>
                    </div>
                    
                    <hr>
                    <h5>Change Password (Optional)</h5>
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control <?php echo isset($errors['current_password']) ? 'is-invalid' : ''; ?>" id="current_password" name="current_password">
                        <?php if (isset($errors['current_password'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['current_password']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control <?php echo isset($errors['new_password']) ? 'is-invalid' : ''; ?>" id="new_password" name="new_password">
                        <?php if (isset($errors['new_password'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['new_password']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" id="confirm_password" name="confirm_password">
                        <?php if (isset($errors['confirm_password'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['confirm_password']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="dashboard.php" class="btn btn-secondary">Back</a>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>