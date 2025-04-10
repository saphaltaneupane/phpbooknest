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
    $quantity = sanitize($_POST['quantity']);
    $status = sanitize($_POST['status']);
    
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
    
    if (empty($quantity)) {
        $errors['quantity'] = 'Quantity is required';
    } elseif (!is_numeric($quantity) || $quantity <= 0) {
        $errors['quantity'] = 'Quantity must be a positive number';
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
                  price = '$price', image = '$targetFile', quantity = $quantity, status = '$status' 
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

<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Admin Panel
            </div>
            <div class="list-group list-group-flush">
                <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="users.php" class="list-group-item list-group-item-action">Manage Users</a>
                <a href="books.php" class="list-group-item list-group-item-action active">Manage Books</a>
                <a href="add_book.php" class="list-group-item list-group-item-action">Add New Book</a>
                <a href="orders.php" class="list-group-item list-group-item-action">Manage Orders</a>
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
                    <div class="alert alert-success">Book updated successfully!</div>
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
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control <?php echo isset($errors['quantity']) ? 'is-invalid' : ''; ?>" id="quantity" name="quantity" value="<?php echo $book['quantity']; ?>" min="1" step="1" required>
                        <?php if (isset($errors['quantity'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['quantity']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="available" <?php echo $book['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                            <option value="pending" <?php echo $book['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="sold" <?php echo $book['status'] === 'sold' ? 'selected' : ''; ?>>Sold</option>
                        </select>
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