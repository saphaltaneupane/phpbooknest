<?php
// Adjust the path for includes based on the current directory
$relativePath = '../';
require_once $relativePath . 'includes/header.php';

// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    redirect($relativePath . 'login.php');
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>" href="users.php">Manage Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'books.php' ? 'active' : ''; ?>" href="books.php">Manage Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'add_book.php' ? 'active' : ''; ?>" href="add_book.php">Add New Book</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : ''; ?>" href="orders.php">Manage Orders</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h2>Welcome to the Admin Dashboard</h2>
    <p>Use the navigation bar above to manage users, books, and orders.</p>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>