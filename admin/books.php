<?php
// Adjust the path for includes based on the current directory
$relativePath = '../';
require_once $relativePath . 'includes/header.php';

// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    redirect($relativePath . 'login.php');
}

// Get all books
$books = getAllBooks();

// Update book status to 'available' if quantity > 0 but status is not 'available'
foreach ($books as &$book) {
    if ($book['quantity'] > 0 && $book['status'] !== 'available') {
        $updateQuery = "UPDATE books SET status = 'available' WHERE id = " . $book['id'];
        mysqli_query($conn, $updateQuery);
        $book['status'] = 'available'; // Update local copy for display
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
    
    .sidebar-menu {
        border: 1px solid #ddd;
        border-top: none;
    }
    
    .sidebar-menu a {
        display: block;
        padding: 10px;
        border-bottom: 1px solid #ddd;
        text-decoration: none;
        color: #333;
    }
    
    .sidebar-menu a:last-child {
        border-bottom: none;
    }
    
    .sidebar-menu a:hover {
        background-color: #f5f5f5;
    }
    
    .sidebar-menu a.active {
        background-color: #0066cc;
        color: white;
    }
    
    /* Main content styles */
    .main-content {
        flex: 1;
        min-width: 300px;
    }
    
    /* Button styles */
    .btn {
        display: inline-block;
        padding: 6px 12px;
        margin-bottom: 15px;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    
    .btn-primary {
        background-color: #0066cc;
        color: white;
    }
    
    .btn-success {
        background-color: #28a745;
        color: white;
    }
    
    .btn-danger {
        background-color: #dc3545;
        color: white;
    }
    
    .btn-sm {
        padding: 4px 8px;
        font-size: 0.9em;
    }
    
    .btn.disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }
    
    /* Table styles */
    .books-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .books-table th, .books-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    
    .books-table th {
        background-color: #f5f5f5;
    }
    
    .books-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    
    /* Badge styles */
    .badge {
        display: inline-block;
        padding: 3px 7px;
        font-size: 0.75em;
        font-weight: bold;
        border-radius: 10px;
        color: white;
    }
    
    .badge-success {
        background-color: #28a745;
    }
    
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .badge-secondary {
        background-color: #6c757d;
    }
    
    /* Alert */
    .alert {
        padding: 10px;
        background-color: #d1ecf1;
        border: 1px solid #bee5eb;
        color: #0c5460;
        margin: 10px 0;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            margin-right: 0;
        }
        
        .books-table {
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
        <div class="sidebar-menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="users.php">Manage Users</a>
            <a href="books.php" class="active">Manage Books</a>
            <a href="add_book.php">Add New Book</a>
            <a href="orders.php">Manage Orders</a>
        </div>
    </div>
    
    <div class="main-content">
        <h2>Manage Books</h2>
        
        <div>
            <a href="add_book.php" class="btn btn-primary">Add New Book</a>
        </div>
        
        <?php if (empty($books)): ?>
            <div class="alert">No books found.</div>
        <?php else: ?>
            <div>
                <table class="books-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Type</th>
                            <th>Added By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><?php echo $book['id']; ?></td>
                                <td><?php echo $book['title']; ?></td>
                                <td><?php echo $book['author']; ?></td>
                                <td>Rs. <?php echo $book['price']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $book['status'] === 'available' ? 'success' : ($book['status'] === 'pending' ? 'warning' : 'secondary'); ?>">
                                        <?php echo ucfirst($book['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $book['is_old'] ? 'Used' : 'New'; ?></td>
                                <td><?php echo $book['added_by_name'] ? $book['added_by_name'] : 'Admin'; ?></td>
                                <td>
                                    <a href="edit_book.php?id=<?php echo $book['id']; ?>" 
                                       class="btn btn-primary btn-sm <?php echo ($book['is_old'] && $book['status'] === 'sold') ? 'disabled' : ''; ?>">
                                       Edit
                                    </a>
                                    
                                    <?php if ($book['status'] === 'pending' && $book['is_old']): ?>
                                        <a href="approve_book.php?id=<?php echo $book['id']; ?>&action=approve" 
                                           class="btn btn-success btn-sm" 
                                           onclick="return confirm('Are you sure you want to approve this book?')">Approve</a>
                                    <?php endif; ?>
                                    
                                    <a href="delete_book.php?id=<?php echo $book['id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>