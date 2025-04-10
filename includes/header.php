<?php
// Check if $relativePath is defined, otherwise set a default
if (!isset($relativePath)) {
    $relativePath = '';
}

require_once $relativePath . 'config/db.php';
require_once $relativePath . 'includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Book Trading System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $relativePath; ?>assets/css/style.css">
    <script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.22.0.0.0/khalti-checkout.iffe.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $relativePath; ?>index.php">BookTrading</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $relativePath; ?>index.php">Home</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $relativePath; ?>admin/dashboard.php">Admin Dashboard</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $relativePath; ?>user/dashboard.php">My Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $relativePath; ?>user/add_book.php">Sell Old Book</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $relativePath; ?>user/orders.php">My Orders</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <form class="d-flex me-2" action="<?php echo $relativePath; ?>search.php" method="GET">
                    <input class="form-control me-2" type="search" name="keyword" placeholder="Search books...">
                    <button class="btn btn-light" type="submit">Search</button>
                </form>
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <?php echo $_SESSION['user_name']; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if (!isAdmin()): ?>
                                    <li><a class="dropdown-item" href="<?php echo $relativePath; ?>user/profile.php">Profile</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?php echo $relativePath; ?>logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $relativePath; ?>login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $relativePath; ?>register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">