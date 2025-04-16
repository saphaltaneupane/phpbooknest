<?php
// Adjust the path for includes based on the current directory
$relativePath = '../';
require_once $relativePath . 'includes/header.php';

// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    redirect($relativePath . 'login.php');
}
?>

<style>
    /* Simple CSS styles */
    .admin-nav {
        background-color: #f8f9fa;
        padding: 15px;
        margin-bottom: 20px;
        border-bottom: 1px solid #ddd;
    }
    
    .nav-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .brand {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
        display: block;
    }
    
    .nav-menu {
        list-style: none;
        padding: 0;
    }
    
    .nav-menu li {
        display: inline-block;
        margin-right: 15px;
    }
    
    .nav-link {
        text-decoration: none;
        color: #333;
    }
    
    .nav-link.active {
        font-weight: bold;
        color: #007bff;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }
    
    /* Simple mobile menu */
    @media (max-width: 768px) {
        .nav-menu li {
            display: block;
            margin-bottom: 10px;
        }
    }
</style>

<nav class="admin-nav">
    <div class="nav-container">
        <a class="brand" href="dashboard.php">Admin Panel</a>
        <ul class="nav-menu">
            <li>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">Dashboard</a>
            </li>
            <li>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>" href="users.php">Manage Users</a>
            </li>
            <li>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'books.php' ? 'active' : ''; ?>" href="books.php">Manage Books</a>
            </li>
            <li>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'add_book.php' ? 'active' : ''; ?>" href="add_book.php">Add New Book</a>
            </li>
            <li>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : ''; ?>" href="orders.php">Manage Orders</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2>Welcome to the Admin Dashboard</h2>
    <p>Use the navigation bar above to manage users, books, and orders.</p>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>