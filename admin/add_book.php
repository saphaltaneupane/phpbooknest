<?php
// Adjust the path for includes based on the current directory
$relativePath = '../';
require_once $relativePath . 'includes/header.php';

// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    redirect($relativePath . 'login.php');
}

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
        $query = "INSERT INTO books (title, author, description, price, image, quantity, is_old, status) 
                  VALUES ('$title', '$author', '$description', '$price', '$targetFile', 1, 0, 'available')";
        
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
    /* Basic layout */
    .admin-container {
        display: flex;
        flex-wrap: wrap;
    }
    
    /* Sidebar styles */
    .sidebar {
        width: 250px;
        margin-right: 20px;
        margin-bottom: 20px;
    }
    
    .sidebar-header {
        background-color: #0066cc;
        color: white;
        padding: 10px;
        font-weight: bold;
    }
    
    .menu-list {
        border: 1px solid #ddd;
    }
    
    .menu-link {
        display: block;
        padding: 10px;
        text-decoration: none;
        color: #333;
        border-bottom: 1px solid #ddd;
    }
    
    .menu-link:last-child {
        border-bottom: none;
    }
    
    .menu-link:hover {
        background-color: #f5f5f5;
    }
    
    .menu-link.active {
        background-color: #0066cc;
        color: white;
    }
    
    /* Main content styles */
    .main-content {
        flex: 1;
        min-width: 300px;
    }
    
    .card {
        border: 1px solid #ddd;
        margin-bottom: 20px;
    }
    
    .card-header {
        background-color: #0066cc;
        color: white;
        padding: 10px 15px;
    }
    
    .card-body {
        padding: 15px;
    }
    
    /* Form styles */
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    
    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .form-control.invalid {
        border-color: #dc3545;
    }
    
    .form-text {
        display: block;
        margin-top: 5px;
        font-size: 0.9em;
        color: #666;
    }
    
    .error-feedback {
        display: block;
        color: #dc3545;
        margin-top: 5px;
        font-size: 0.9em;
    }
    
    /* Alert styles */
    .alert {
        padding: 10px 15px;
        margin-bottom: 15px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    
    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
    
    /* Button styles */
    .btn {
        display: inline-block;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
    }
    
    .btn-primary {
        background-color: #0066cc;
        color: white;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }
    
    /* Utility */
    .button-row {
        display: flex;
        justify-content: space-between;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            margin-right: 0;
        }
    }
</style>

<div class="admin-container">
    <div class="sidebar">
        <div class="sidebar-header">
            Admin Panel
        </div>
        <div class="menu-list">
            <a href="dashboard.php" class="menu-link">Dashboard</a>
            <a href="users.php" class="menu-link">Manage Users</a>
            <a href="books.php" class="menu-link">Manage Books</a>
            <a href="add_book.php" class="menu-link active">Add New Book</a>
            <a href="orders.php" class="menu-link">Manage Orders</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="card">
            <div class="card-header">
                <h3>Add New Book</h3>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">Book added successfully!</div>
                <?php endif; ?>
                
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
                <?php endif; ?>
                
                <form action="add_book.php" method="POST" enctype="multipart/form-data" novalidate>
                    <div class="form-group">
                        <label for="title" class="form-label">Book Title</label>
                        <input type="text" class="form-control <?php echo isset($errors['title']) ? 'invalid' : ''; ?>" id="title" name="title" value="<?php echo isset($title) ? $title : ''; ?>" required>
                        <?php if (isset($errors['title'])): ?>
                            <div class="error-feedback"><?php echo $errors['title']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="author" class="form-label">Author</label>
                        <input type="text" class="form-control <?php echo isset($errors['author']) ? 'invalid' : ''; ?>" id="author" name="author" value="<?php echo isset($author) ? $author : ''; ?>" required>
                        <?php if (isset($errors['author'])): ?>
                            <div class="error-feedback"><?php echo $errors['author']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo isset($description) ? $description : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="price" class="form-label">Price (in Rs.)</label>
                        <input type="number" class="form-control <?php echo isset($errors['price']) ? 'invalid' : ''; ?>" id="price" name="price" value="<?php echo isset($price) ? $price : ''; ?>" min="1" step="1" required>
                        <?php if (isset($errors['price'])): ?>
                            <div class="error-feedback"><?php echo $errors['price']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="image" class="form-label">Book Image</label>
                        <input type="file" class="form-control <?php echo isset($errors['image']) ? 'invalid' : ''; ?>" id="image" name="image">
                        <small class="form-text">Upload an image of the book (optional). Max size: 5MB. Supported formats: JPG, JPEG, PNG, GIF.</small>
                        <?php if (isset($errors['image'])): ?>
                            <div class="error-feedback"><?php echo $errors['image']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="button-row">
                        <a href="books.php" class="btn btn-secondary">Back</a>
                        <button type="submit" class="btn btn-primary">Add Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>