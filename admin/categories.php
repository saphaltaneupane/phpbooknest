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
$categoryToEdit = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new category
    if (isset($_POST['add_category'])) {
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        
        // Validate input
        if (empty($name)) {
            $errors['name'] = 'Category name is required';
        } else {
            // Check if category already exists
            $checkQuery = "SELECT * FROM categories WHERE name = '$name'";
            $checkResult = mysqli_query($conn, $checkQuery);
            
            if (mysqli_num_rows($checkResult) > 0) {
                $errors['name'] = 'A category with this name already exists';
            }
        }
        
        // If no errors, add category
        if (empty($errors)) {
            $insertQuery = "INSERT INTO categories (name, description) VALUES ('$name', '$description')";
            
            if (mysqli_query($conn, $insertQuery)) {
                $success = 'Category added successfully!';
                // Reset form data
                $name = $description = '';
            } else {
                $errors['general'] = 'Error adding category: ' . mysqli_error($conn);
            }
        }
    }
    
    // Update existing category
    if (isset($_POST['update_category'])) {
        $categoryId = (int)$_POST['category_id'];
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        
        // Validate input
        if (empty($name)) {
            $errors['edit_name'] = 'Category name is required';
        } else {
            // Check if category already exists with this name (excluding current category)
            $checkQuery = "SELECT * FROM categories WHERE name = '$name' AND id != $categoryId";
            $checkResult = mysqli_query($conn, $checkQuery);
            
            if (mysqli_num_rows($checkResult) > 0) {
                $errors['edit_name'] = 'A category with this name already exists';
            }
        }
        
        // If no errors, update category
        if (empty($errors)) {
            $updateQuery = "UPDATE categories SET name = '$name', description = '$description' WHERE id = $categoryId";
            
            if (mysqli_query($conn, $updateQuery)) {
                $success = 'Category updated successfully!';
            } else {
                $errors['general'] = 'Error updating category: ' . mysqli_error($conn);
            }
        }
    }
    
    // Delete category
    if (isset($_POST['delete_category'])) {
        $categoryId = (int)$_POST['category_id'];
        
        // Check if there are books in this category
        $checkBooksQuery = "SELECT COUNT(*) as count FROM books WHERE category_id = $categoryId";
        $checkResult = mysqli_query($conn, $checkBooksQuery);
        $bookCount = mysqli_fetch_assoc($checkResult)['count'];
        
        if ($bookCount > 0) {
            $errors['delete'] = "Cannot delete category: There are $bookCount books assigned to this category. Please reassign these books first.";
        } else {
            // Delete the category
            $deleteQuery = "DELETE FROM categories WHERE id = $categoryId";
            
            if (mysqli_query($conn, $deleteQuery)) {
                $success = 'Category deleted successfully!';
            } else {
                $errors['general'] = 'Error deleting category: ' . mysqli_error($conn);
            }
        }
    }
}

// Get category for editing
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $categoryId = (int)$_GET['edit'];
    $query = "SELECT * FROM categories WHERE id = $categoryId";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $categoryToEdit = mysqli_fetch_assoc($result);
    }
}

// Get all categories
$query = "SELECT c.*, (SELECT COUNT(*) FROM books WHERE category_id = c.id) as book_count 
          FROM categories c ORDER BY c.name";
$result = mysqli_query($conn, $query);
$categories = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
}
?>

<style>
/* Global styles and reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    color: #333;
    background-color: #f8f9fa;
}

/* Basic layout */
.admin-container {
    display: flex;
    flex-wrap: wrap;
    max-width: 1200px;
    margin: 0 auto;
    padding: 15px;
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
    padding: 12px 15px;
    font-weight: bold;
    border-radius: 4px 4px 0 0;
}

.menu-list {
    border: 1px solid #ddd;
    border-radius: 0 0 4px 4px;
    background-color: #fff;
}

.menu-link {
    display: block;
    padding: 12px 15px;
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
    border-radius: 4px;
    margin-bottom: 20px;
    background-color: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #0066cc;
    color: white;
    padding: 12px 15px;
    border-radius: 4px 4px 0 0;
}

.card-header h3 {
    margin: 0;
    font-size: 1.25rem;
}

.card-body {
    padding: 15px;
}

/* Form styles */
.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    color: #333;
    background-color: #fff;
}

.form-control.invalid {
    border-color: #dc3545;
}

.form-control:focus {
    outline: none;
    border-color: #0066cc;
    box-shadow: 0 0 0 3px rgba(0,102,204,0.1);
}

textarea.form-control {
    min-height: 100px;
    resize: vertical;
}

.form-text {
    display: block;
    margin-top: 5px;
    font-size: 0.875rem;
    color: #666;
}

.error-feedback {
    display: block;
    color: #dc3545;
    margin-top: 5px;
    font-size: 0.875rem;
}

/* Alert styles */
.alert {
    padding: 12px 15px;
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
    padding: 10px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-size: 16px;
    font-weight: 400;
    text-align: center;
}

.btn-primary {
    background-color: #0066cc;
    color: white;
}

.btn-primary:hover {
    background-color: #0055aa;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
}

.btn-sm {
    padding: 6px 10px;
    font-size: 14px;
}

/* Table styles */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.table th,
.table td {
    padding: 12px 15px;
    text-align: left;
    border: 1px solid #ddd;
    color: #000 !important; /* Force black text color */
}

.table th {
    background-color: #f8f9fa;
    color: #333;
    font-weight: bold;
}

.table tr:nth-child(even) {
    background-color: #f8f9fa;
}

.table tr:hover {
    background-color: #f1f1f1;
}

/* Badge styles */
.badge {
    display: inline-block;
    padding: 3px 7px;
    font-size: 12px;
    font-weight: bold;
    border-radius: 10px;
    color: white;
}

.badge-primary {
    background-color: #0066cc;
}

.badge-secondary {
    background-color: #6c757d;
}

.badge-success {
    background-color: #28a745;
}

.badge-danger {
    background-color: #dc3545;
}

/* Responsive */
@media (max-width: 768px) {
    .admin-container {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        margin-right: 0;
    }

    .table {
        display: block;
        overflow-x: auto;
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
            <a href="add_book.php" class="menu-link">Add New Book</a>
            <a href="categories.php" class="menu-link active">Manage Categories</a>
            <a href="orders.php" class="menu-link">Manage Orders</a>
        </div>
    </div>

    <div class="main-content">
        <div class="card">
            <div class="card-header">
                <h3><?php echo $categoryToEdit ? 'Edit Category' : 'Add New Category'; ?></h3>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
                <?php endif; ?>

                <form action="categories.php<?php echo $categoryToEdit ? '?edit=' . $categoryToEdit['id'] : ''; ?>" method="POST" novalidate>
                    <?php if ($categoryToEdit): ?>
                        <input type="hidden" name="category_id" value="<?php echo $categoryToEdit['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control <?php echo isset($errors['name']) || isset($errors['edit_name']) ? 'invalid' : ''; ?>" 
                               id="name" name="name" 
                               value="<?php echo $categoryToEdit ? $categoryToEdit['name'] : (isset($name) ? $name : ''); ?>" 
                               required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="error-feedback"><?php echo $errors['name']; ?></div>
                        <?php endif; ?>
                        <?php if (isset($errors['edit_name'])): ?>
                            <div class="error-feedback"><?php echo $errors['edit_name']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo $categoryToEdit ? $categoryToEdit['description'] : (isset($description) ? $description : ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <?php if ($categoryToEdit): ?>
                            <button type="submit" name="update_category" class="btn btn-primary">Update Category</button>
                            <a href="categories.php" class="btn btn-secondary">Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Manage Categories</h3>
            </div>
            <div class="card-body">
                <?php if (isset($errors['delete'])): ?>
                    <div class="alert alert-danger"><?php echo $errors['delete']; ?></div>
                <?php endif; ?>

                <?php if (empty($categories)): ?>
                    <div class="alert alert-danger">No categories found. Please add some categories.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Books</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td><?php echo htmlspecialchars($category['description'] ?? ''); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $category['book_count'] > 0 ? 'primary' : 'secondary'; ?>">
                                            <?php echo $category['book_count']; ?> book(s)
                                        </span>
                                    </td>
                                    <td>
                                        <a href="categories.php?edit=<?php echo $category['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                        
                                        <form method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                            <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                            <button type="submit" name="delete_category" class="btn btn-danger btn-sm" <?php echo $category['book_count'] > 0 ? 'disabled title="Cannot delete: Category has books assigned to it"' : ''; ?>>
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>