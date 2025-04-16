<?php
// Adjust the path for includes based on the current directory
$relativePath = '../';
require_once $relativePath . 'includes/header.php';

// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    redirect($relativePath . 'login.php');
}

// Get all users
$users = getAllUsers();
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
        
        <?php if (empty($users)): ?>
            <div class="alert">No users found.</div>
        <?php else: ?>
            <div class="table-container">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Joined On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['name']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['phone']; ?></td>
                                <td><?php echo $user['address'] ? $user['address'] : '-'; ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>