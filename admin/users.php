<?php
// Adjust the path for includes based on the current directory
$relativePath = '../';
require_once $relativePath . 'includes/header.php';

// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    redirect($relativePath . 'login.php');
}

// Handle user deletion
if (isset($_POST['delete_user']) && !empty($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    
    // Call function to delete user (you'll need to implement this in your functions)
    if (deleteUser($userId)) {
        $successMessage = "User deleted successfully.";
    } else {
        $errorMessage = "Failed to delete user.";
    }
    
    // Refresh user list after deletion
    $users = getAllUsers();
} else {
    // Get all users
    $users = getAllUsers();
}
?>

<style>
    /* Basic layout */
    .admin-container {
        display: flex;
        flex-wrap: wrap;
        width: 100%;
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
    
    /* Table styles */
    .users-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .users-table th, .users-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    
    .users-table th {
        background-color: #f5f5f5;
    }
    
    .users-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    
    /* Alert */
    .alert {
        padding: 10px;
        margin: 10px 0;
    }
    
    .alert-info {
        background-color: #d1ecf1;
        border: 1px solid #bee5eb;
        color: #0c5460;
    }
    
    .alert-success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
    
    /* Delete button */
    .btn-delete {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 3px;
        cursor: pointer;
    }
    
    .btn-delete:hover {
        background-color: #c82333;
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
        <div class="sidebar-menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="users.php" class="active">Manage Users</a>
            <a href="books.php">Manage Books</a>
            <a href="add_book.php">Add New Book</a>
            <a href="orders.php">Manage Orders</a>
        </div>
    </div>
    
    <div class="main-content">
        <h2>Manage Users</h2>
        
        <?php if (isset($successMessage)): ?>
            <div class="alert alert-success"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        
        <?php if (empty($users)): ?>
            <div class="alert alert-info">No users found.</div>
        <?php else: ?>
            <div class="table-container">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Joined On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['name']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['phone']; ?></td>
                                <td><?php echo $user['address'] ? $user['address'] : '-'; ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="btn-delete">Delete</button>
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

<?php require_once $relativePath . 'includes/footer.php'; ?>