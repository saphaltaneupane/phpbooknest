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
    <link rel="stylesheet" href="<?php echo $relativePath; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.22.0.0.0/khalti-checkout.iffe.js"></script>
    <style>
        /* Vibrant Color Palette and Base Styles */
        :root {
            --primary-color: #6c63ff;
            --secondary-color: #ff9d72;
            --accent-color: #ff6584;
            --success-color: #4caf50;
            --light-color: #f8f9ff;
            --dark-color: #2c2c54;
            --gray-light: #f0f2f9;
            --gray-medium: #e0e0e0;
            --gray-dark: #a0a0a0;
            --text-primary: #333333;
            --text-secondary: #666666;
            --font-primary: 'Poppins', 'Segoe UI', Tahoma, sans-serif;
            --radius: 12px;
            --radius-sm: 6px;
            --transition: all 0.3s ease;
        }
        
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 400;
            src: local('Poppins Regular'), local('Poppins-Regular'), 
                 url(https://fonts.gstatic.com/s/poppins/v15/pxiEyp8kv8JHgFVrJJfecg.woff2) format('woff2');
            font-display: swap;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: var(--font-primary);
            line-height: 1.6;
            color: var(--text-primary);
            background-color: var(--light-color);
            min-height: 100vh;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Stylish Colorful Navigation */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #8367ff 100%);
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }
        
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            letter-spacing: -0.5px;
            transition: var(--transition);
            display: flex;
            align-items: center;
        }
        
        .navbar-brand:hover {
            transform: translateY(-2px);
        }
        
        .navbar-brand::before {
            content: "📚";
            margin-right: 10px;
            font-size: 1.6rem;
        }
        
        .navbar-toggler {
            display: none;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            width: 40px;
            height: 40px;
            position: relative;
            transition: var(--transition);
        }
        
        .navbar-toggler:hover {
            transform: scale(1.1);
        }
        
        .navbar-collapse {
            display: flex;
            align-items: center;
            flex-grow: 1;
            justify-content: space-between;
            margin-left: 40px;
        }
        
        .navbar-nav {
            display: flex;
            list-style: none;
            align-items: center;
        }
        
        .nav-item {
            margin: 0 5px;
            position: relative;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 10px 15px;
            font-weight: 500;
            display: block;
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }
        
        .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        /* Dropdown Menu */
        .dropdown-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 180px;
            border: 1px solid var(--gray-medium);
            z-index: 1000;
            border-radius: var(--radius-sm);
            padding: 10px 0;
            right: 0;
            top: 50px;
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.3s, transform 0.3s;
        }
        
        .dropdown-menu.show {
            opacity: 1;
            transform: translateY(0);
            display: block;
        }
        
        .dropdown-item {
            color: var(--text-primary);
            padding: 10px 20px;
            text-decoration: none;
            display: block;
            transition: var(--transition);
        }
        
        .dropdown-item:hover {
            background-color: var(--gray-light);
            color: var(--primary-color);
        }
        
        /* Custom Icons */
        .icon-bars {
            position: relative;
            width: 24px;
            height: 24px;
        }
        
        .icon-bars span {
            display: block;
            position: absolute;
            height: 3px;
            width: 100%;
            background: white;
            border-radius: 3px;
            opacity: 1;
            left: 0;
            transform: rotate(0deg);
            transition: .25s ease-in-out;
        }
        
        .icon-bars span:nth-child(1) {
            top: 6px;
        }
        
        .icon-bars span:nth-child(2) {
            top: 14px;
        }
        
        .icon-bars span:nth-child(3) {
            top: 22px;
        }
        
        .icon-caret-down {
            display: inline-block;
            margin-left: 8px;
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid currentColor;
            transition: var(--transition);
        }
        
        .dropdown-toggle:hover .icon-caret-down,
        .dropdown-toggle.active .icon-caret-down {
            transform: rotate(180deg);
        }
        
        /* Stylish Search Form */
        .search-form {
            display: flex;
            flex: 0 0 300px;
            margin: 0 20px;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 10px 15px;
            padding-right: 40px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 30px;
            outline: none;
            transition: var(--transition);
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 0.9rem;
        }
        
        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .search-input:focus {
            border-color: rgba(255, 255, 255, 0.8);
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .search-button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.8);
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }
        
        .search-button:hover {
            color: white;
        }
        
        .search-icon {
            position: relative;
            width: 16px;
            height: 16px;
        }
        
        .search-icon::before {
            content: '';
            position: absolute;
            width: 10px;
            height: 10px;
            border: 2px solid currentColor;
            border-radius: 50%;
            top: 0;
            left: 0;
        }
        
        .search-icon::after {
            content: '';
            position: absolute;
            width: 2px;
            height: 7px;
            background-color: currentColor;
            transform: rotate(-45deg);
            bottom: 0;
            right: 3px;
        }
        
        /* User Avatar */
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 8px;
            font-size: 14px;
            transition: var(--transition);
        }
        
        .nav-link:hover .user-avatar {
            transform: scale(1.1);
        }
        
        /* Card-like Content Area */
        .content-area {
            margin-top: 30px;
            margin-bottom: 30px;
            background-color: white;
            border-radius: var(--radius);
            border: 1px solid var(--gray-medium);
            padding: 25px;
        }
        
        /* Card Grid System */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .card {
            background-color: white;
            border-radius: var(--radius);
            border: 1px solid var(--gray-medium);
            overflow: hidden;
            transition: var(--transition);
        }
        
        .card:hover {
            border-color: var(--primary-color);
            transform: translateY(-3px);
        }
        
        .card-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background-color: var(--gray-light);
        }
        
        .card-body {
            padding: 15px;
        }
        
        .card-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: var(--dark-color);
        }
        
        .card-text {
            color: var(--text-secondary);
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        .card-footer {
            padding: 15px;
            border-top: 1px solid var(--gray-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #5652db;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #ff8a55;
            transform: translateY(-2px);
        }
        
        .btn-outline {
            border: 2px solid var(--gray-medium);
            background-color: transparent;
            color: var(--text-primary);
        }
        
        .btn-outline:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        /* Utility Classes */
        .mt-4 {
            margin-top: 1.5rem;
        }
        
        .me-auto {
            margin-right: auto;
        }
        
        .d-flex {
            display: flex;
        }
        
        .align-items-center {
            align-items: center;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .navbar-container {
                height: auto;
                padding: 15px 0;
            }
            
            .navbar-toggler {
                display: block;
            }
            
            .navbar-collapse {
                display: none;
                flex-direction: column;
                width: 100%;
                margin-left: 0;
                margin-top: 15px;
                align-items: flex-start;
            }
            
            .navbar-collapse.show {
                display: flex;
            }
            
            .navbar-nav {
                flex-direction: column;
                width: 100%;
                align-items: flex-start;
            }
            
            .nav-item {
                margin: 5px 0;
                width: 100%;
            }
            
            .nav-link {
                width: 100%;
            }
            
            .search-form {
                margin: 15px 0;
                width: 100%;
                flex: 1 1 auto;
            }
            
            .dropdown-menu {
                position: static;
                width: 100%;
                margin-top: 5px;
                margin-bottom: 10px;
                transform: none;
                border: none;
                border-left: 3px solid var(--primary-color);
                border-radius: 0;
            }
            
            .card-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.5rem;
            }
            
            .content-area {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container navbar-container">
            <a class="navbar-brand" href="<?php echo $relativePath; ?>index.php">BookTrading</a>
            <button class="navbar-toggler" type="button" id="navbarToggler">
                <div class="icon-bars">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>
            <div class="navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto">
                    <?php if (!isLoggedIn() || !isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>" href="<?php echo $relativePath; ?>index.php">Home</a>
                        </li>
                        <?php if (isLoggedIn()): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/user/dashboard.php') !== false ? 'active' : ''; ?>" href="<?php echo $relativePath; ?>user/dashboard.php">User Dashboard</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (isLoggedIn() && isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/admin/dashboard.php') !== false ? 'active' : ''; ?>" href="<?php echo $relativePath; ?>admin/dashboard.php">Admin</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <?php if (basename($_SERVER['PHP_SELF']) === 'index.php' && !isAdmin()): ?>
                    <form class="search-form" action="<?php echo $relativePath; ?>search.php" method="GET">
                        <input class="search-input" type="search" name="keyword" placeholder="Search books...">
                        <button class="search-button" type="submit">
                            <div class="search-icon"></div>
                        </button>
                    </form>
                <?php endif; ?>
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a href="<?php echo $relativePath; ?>cart.php" class="nav-link position-relative">
                                <i class="bi bi-cart3" style="font-size: 1.2rem;"></i>
                                <?php 
                                $cartCount = 0;
                                if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                                    foreach ($_SESSION['cart'] as $item) {
                                        $cartCount += $item['quantity'];
                                    }
                                }
                                if ($cartCount > 0): 
                                ?>
                                <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $cartCount; ?>
                                </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></div>
                                    <?php echo $_SESSION['user_name']; ?>
                                    <div class="icon-caret-down"></div>
                                </div>
                            </a>
                            <ul class="dropdown-menu" id="userDropdown">
                                <?php if (!isAdmin()): ?>
                                    <li><a class="dropdown-item" href="<?php echo $relativePath; ?>user/profile.php">Profile</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?php echo $relativePath; ?>logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'login.php' ? 'active' : ''; ?>" href="<?php echo $relativePath; ?>login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'register.php' ? 'active' : ''; ?>" href="<?php echo $relativePath; ?>register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="content-area">
            <!-- Content goes here -->
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile navigation toggle
            const navbarToggler = document.getElementById('navbarToggler');
            const navbarCollapse = document.getElementById('navbarCollapse');
            
            if (navbarToggler && navbarCollapse) {
                navbarToggler.addEventListener('click', function() {
                    navbarCollapse.classList.toggle('show');
                    
                    // Animate hamburger icon (optional animation)
                    const spans = this.querySelectorAll('span');
                    spans.forEach(span => span.classList.toggle('active'));
                });
            }
            
            // User dropdown menu
            const dropdownToggle = document.querySelector('.dropdown-toggle');
            const userDropdown = document.getElementById('userDropdown');
            
            if (dropdownToggle && userDropdown) {
                // Toggle dropdown on click
                dropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    userDropdown.classList.toggle('show');
                    this.classList.toggle('active');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.dropdown-toggle') && !e.target.closest('#userDropdown')) {
                        if (userDropdown.classList.contains('show')) {
                            userDropdown.classList.remove('show');
                            dropdownToggle.classList.remove('active');
                        }
                    }
                });
            }
            
            // Add active class to current page nav link
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                const linkPath = link.getAttribute('href');
                if (linkPath && currentPath.includes(linkPath) && linkPath !== '/index.php') {
                    link.classList.add('active');
                }
            });
        });
    </script>