<?php
// Adjust the path for includes based on the current directory
$relativePath = '../';
require_once $relativePath . 'includes/header.php';

// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    redirect($relativePath . 'login.php');
}

// Get book ID from URL
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get book details
$book = getBookById($bookId);

// If book not found, redirect to books page
if (!$book) {
    redirect('books.php');
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = sanitize($_POST['title']);
    $author = sanitize($_POST['author']);
    $description = sanitize($_POST['description']);
    $price = sanitize($_POST['price']);
    $status = sanitize($_POST['status']);
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : $book['quantity'];
    if ($quantity < 0) {
        $quantity = 0;
    }
    
    // FIXED: Auto-update status to 'available' if quantity was increased from 0
    if ($book['quantity'] == 0 && $quantity > 0) {
        $status = 'available';  // Automatically set to available when restocking
    }
    
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
    $targetFile = $book['image'];
    
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
    
    // If no errors, update book in database
    if (empty($errors)) {
        $query = "UPDATE books SET title = '$title', author = '$author', description = '$description', 
                  price = '$price', image = '$targetFile', status = '$status', quantity = $quantity
                  WHERE id = $bookId";
        
        if (mysqli_query($conn, $query)) {
            $success = true;
            // Get updated book details
            $book = getBookById($bookId);
        } else {
            $errors['general'] = 'Error updating book: ' . mysqli_error($conn);
        }
    }
}
?>

<style>
    /* General styles */
    .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -15px;
    }
    .col-md-3 {
        width: 25%;
        padding: 0 15px;
        box-sizing: border-box;
    }
    .col-md-9 {
        width: 75%;
        padding: 0 15px;
        box-sizing: border-box;
    }
    @media (max-width: 767px) {
        .col-md-3, .col-md-9 {
            width: 100%;
        }
    }
    
    /* Card styles */
    .card {
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #fff;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .card-header {
        padding: 15px;
        border-bottom: 1px solid #ddd;
    }
    .bg-primary {
        background-color: #4a6da7;
    }
    .text-white {
        color: white;
    }
    .mb-0 {
        margin-bottom: 0;
    }
    .mb-4 {
        margin-bottom: 20px;
    }
    .card-body {
        padding: 15px;
    }
    
    /* Navigation */
    .list-group {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .list-group-flush {
        border-radius: 0;
    }
    .list-group-item {
        display: block;
        padding: 12px 15px;
        text-decoration: none;
        color: #333;
        border-bottom: 1px solid #ddd;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    .list-group-item.active {
        background-color: #4a6da7;
        color: white;
        border-color: #4a6da7;
    }
    
    /* Alerts */
    .alert {
        padding: 12px 15px;
        margin-bottom: 15px;
        border-radius: 4px;
    }
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    /* Form elements */
    .mb-3 {
        margin-bottom: 15px;
    }
    .form-label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color:black;
    }
    .form-control {
        display: block;
        width: 100%;
        padding: 8px 12px;
        font-size: 16px;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 4px;
        box-sizing: border-box;
    }
    .form-control:focus {
        border-color: #80bdff;
        outline: 0;
    }
    .form-select {
        display: block;
        width: 100%;
        padding: 8px 12px;
        font-size: 16px;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 4px;
        box-sizing: border-box;
    }
    textarea.form-control {
        height: auto;
        min-height: 100px;
    }
    .is-invalid {
        border-color: #dc3545;
    }
    .invalid-feedback {
        display: block;
        color: #dc3545;
        margin-top: 5px;
        font-size: 14px;
    }
    .text-muted {
        color: #6c757d;
        font-size: 14px;
        margin-top: 5px;
        display: block;
    }
    .img-thumbnail {
        padding: 4px;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        max-width: 100%;
        height: auto;
    }
    
    /* Buttons */
    .d-flex {
        display: flex;
    }
    .justify-content-between {
        justify-content: space-between;
    }
    .btn {
        display: inline-block;
        font-weight: 400;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        user-select: none;
        border: 1px solid transparent;
        padding: 8px 16px;
        font-size: 16px;
        line-height: 1.5;
        border-radius: 4px;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-primary {
        color: #fff;
        background-color: #4a6da7;
        border-color: #4a6da7;
    }
    .btn-primary:hover {
        background-color: #3a5a8f;
        border-color: #3a5a8f;
    }
    .btn-secondary {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }
    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }
</style>

<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Admin Panel
            </div>
            <div class="list-group list-group-flush">
                <a href="dashboard.php" class="list-group-item">Dashboard</a>
                <a href="users.php" class="list-group-item">Manage Users</a>
                <a href="books.php" class="list-group-item active">Manage Books</a>
                <a href="add_book.php" class="list-group-item">Add New Book</a>
                <a href="orders.php" class="list-group-item">Manage Orders</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Edit Book</h3>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        Book updated successfully!
                        <?php if ($book['quantity'] > 0 && $book['status'] === 'available'): ?>
                            The book is now available on the homepage.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
                <?php endif; ?>
                
                <form action="edit_book.php?id=<?php echo $bookId; ?>" method="POST" enctype="multipart/form-data" novalidate>
                    <div class="mb-3">
                        <label for="title" class="form-label">Book Title</label>
                        <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo $book['title']; ?>" required>
                        <?php if (isset($errors['title'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['title']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="author" class="form-label">Author</label>
                        <input type="text" class="form-control <?php echo isset($errors['author']) ? 'is-invalid' : ''; ?>" id="author" name="author" value="<?php echo $book['author']; ?>" required>
                        <?php if (isset($errors['author'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['author']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo $book['description']; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="price" class="form-label">Price (in Rs.)</label>
                        <input type="number" class="form-control <?php echo isset($errors['price']) ? 'is-invalid' : ''; ?>" id="price" name="price" value="<?php echo $book['price']; ?>" min="1" step="1" required>
                        <?php if (isset($errors['price'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['price']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="available" <?php echo $book['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                        <option value="pending" <?php echo $book['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="sold" <?php echo $book['status'] === 'sold' ? 'selected' : ''; ?>>Sold</option>
                    </select>
                    <small class="text-muted">
                        Note: When restocking a sold-out book (increasing quantity from 0),
                        the status will automatically be changed to "Available".
                    </small>
                </div>
                
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo $book['quantity']; ?>" min="0" required>
                    <small class="text-muted">Set to 0 to mark as unavailable/sold out</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Current Image</label>
                    <div>
                        <img src="<?php echo $relativePath; ?>assets/images/<?php echo $book['image']; ?>" class="img-thumbnail" style="max-width: 200px;" alt="<?php echo $book['title']; ?>">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="image" class="form-label">Change Image</label>
                    <input type="file" class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" id="image" name="image">
                    <small class="text-muted">Upload a new image for the book (optional). Max size: 5MB. Supported formats: JPG, JPEG, PNG, GIF.</small>
                    <?php if (isset($errors['image'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['image']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="books.php" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Update Book</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>