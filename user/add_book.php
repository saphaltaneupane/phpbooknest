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

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = sanitize($_POST['title']);
    $author = sanitize($_POST['author']);
    $description = sanitize($_POST['description']);
    $price = sanitize($_POST['price']);
    
    // Validate input
    if (empty($title)) {
        $errors['title'] = 'Title is required';
    }
    
    if (empty($author)) {
        $errors['author'] = 'Author is required';
    }
    
    if (empty($price)) {
        $errors['price'] = 'Price is required';
    } elseif (!is_numeric($price) || $price <= 0) {
        $errors['price'] = 'Price must be a positive number';
    }
    
    // Handle image upload
    $targetFile = 'default-book.jpg';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = $relativePath . 'assets/images/';
        $filename = basename($_FILES['image']['name']);
        $targetFile = time() . '_' . $filename;
        $uploadFile = $uploadDir . $targetFile;
        
        // Check if image file is an actual image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false) {
            $errors['image'] = 'File is not an image';
        }
        
        // Check file size (max 5MB)
        if ($_FILES['image']['size'] > 5000000) {
            $errors['image'] = 'File is too large (max 5MB)';
        }
        
        // Allow only certain file formats
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        if ($imageFileType !== 'jpg' && $imageFileType !== 'jpeg' && $imageFileType !== 'png' && $imageFileType !== 'gif') {
            $errors['image'] = 'Only JPG, JPEG, PNG & GIF files are allowed';
        }
        
        // If no errors, upload the file
        if (empty($errors['image'])) {
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $errors['image'] = 'Failed to upload image';
            }
        }
    }
    
    // If no errors, add book to database
    if (empty($errors)) {
        $query = "INSERT INTO books (title, author, description, price, image, added_by, is_old, status) 
                  VALUES ('$title', '$author', '$description', '$price', '$targetFile', $userId, 1, 'pending')";
        
        if (mysqli_query($conn, $query)) {
            $success = true;
            // Reset form data
            $title = $author = $description = $price = '';
        } else {
            $errors['general'] = 'Error adding book: ' . mysqli_error($conn);
        }
    }
}
?>

<style>
    /* Custom Pure CSS Styles */
    :root {
        --primary-color: #6c63ff;
        --primary-dark: #5652db;
        --primary-light: #817dff;
        --secondary-color: #ff9d72;
        --accent-color: #ff6584;
        --success-color: #4caf50;
        --error-color: #f44336;
        --light-color: #f8f9ff;
        --white-color: #ffffff;
        --dark-color: #2c2c54;
        --gray-light: #f0f2f9;
        --gray-medium: #e0e0e0;
        --gray-dark: #a0a0a0;
        --text-primary: #333333;
        --text-secondary: #666666;
        --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 5px 15px rgba(0, 0, 0, 0.08);
        --radius: 12px;
        --radius-sm: 6px;
        --transition: all 0.3s ease;
    }
    
    /* Form Container */
    .form-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .form-card {
        background-color: white;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        margin-bottom: 30px;
    }
    
    .form-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #8367ff 100%);
        color: white;
        padding: 20px;
    }
    
    .form-title {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    .form-body {
        padding: 25px;
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
        color: var(--error-color);
    }
    
    /* Form Elements */
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--text-primary);
    }
    
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid var(--gray-medium);
        border-radius: var(--radius-sm);
        font-size: 16px;
        transition: var(--transition);
        background-color: white !important;
        color: black !important;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.2);
        background-color: white !important;
        color: black !important;
    }
    
    .form-control-invalid {
        border-color: var(--error-color);
    }
    
    .form-control-invalid:focus {
        box-shadow: 0 0 0 3px rgba(244, 67, 54, 0.2);
    }
    
    textarea.form-control {
        min-height: 120px;
        resize: vertical;
        background-color: white !important;
        color: black !important;
    }
    
    .help-text {
        display: block;
        margin-top: 5px;
        font-size: 0.85rem;
        color: var(--text-secondary);
    }
    
    .error-message {
        display: block;
        color: var(--error-color);
        font-size: 0.85rem;
        margin-top: 5px;
    }
    
    /* Buttons */
    .form-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 30px;
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
        display: inline-flex;
        align-items: center;
        justify-content: center;
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
    
    /* File upload control */
    .file-upload-wrapper {
        position: relative;
    }
    
    .file-upload-wrapper input[type="file"] {
        padding: 12px 15px;
        cursor: pointer;
    }
    
    /* Responsive Adjustments */
    @media screen and (max-width: 768px) {
        .form-container {
            padding: 0 15px;
        }
        
        .form-body {
            padding: 20px;
        }
        
        .form-buttons {
            flex-direction: column;
            gap: 15px;
        }
        
        .button {
            width: 100%;
        }
    }
</style>

<div class="form-container">
    <div class="form-card">
        <div class="form-header">
            <h3 class="form-title">Sell Your Old Book</h3>
        </div>
        <div class="form-body">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    Your book has been submitted successfully! It will be available for purchase after admin approval.
                </div>
            <?php endif; ?>
            
            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-error"><?php echo $errors['general']; ?></div>
            <?php endif; ?>
            
            <form action="add_book.php" method="POST" enctype="multipart/form-data" novalidate>
                <div class="form-group">
                    <label for="title" class="form-label">Book Title</label>
                    <input type="text" class="form-control <?php echo isset($errors['title']) ? 'form-control-invalid' : ''; ?>" 
                           id="title" name="title" value="<?php echo isset($title) ? $title : ''; ?>" required>
                    <?php if (isset($errors['title'])): ?>
                        <span class="error-message"><?php echo $errors['title']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="author" class="form-label">Author</label>
                    <input type="text" class="form-control <?php echo isset($errors['author']) ? 'form-control-invalid' : ''; ?>" 
                           id="author" name="author" value="<?php echo isset($author) ? $author : ''; ?>" required>
                    <?php if (isset($errors['author'])): ?>
                        <span class="error-message"><?php echo $errors['author']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo isset($description) ? $description : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price" class="form-label">Price (in Rs.)</label>
                    <input type="number" class="form-control <?php echo isset($errors['price']) ? 'form-control-invalid' : ''; ?>" 
                           id="price" name="price" value="<?php echo isset($price) ? $price : ''; ?>" min="1" step="1" required>
                    <?php if (isset($errors['price'])): ?>
                        <span class="error-message"><?php echo $errors['price']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="image" class="form-label">Book Image</label>
                    <div class="file-upload-wrapper">
                        <input type="file" class="form-control <?php echo isset($errors['image']) ? 'form-control-invalid' : ''; ?>" 
                               id="image" name="image">
                    </div>
                    <span class="help-text">Upload an image of the book (optional). Max size: 5MB. Supported formats: JPG, JPEG, PNG, GIF.</span>
                    <?php if (isset($errors['image'])): ?>
                        <span class="error-message"><?php echo $errors['image']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-buttons">
                    <a href="dashboard.php" class="button button-secondary">Back</a>
                    <button type="submit" class="button button-primary">Submit Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>