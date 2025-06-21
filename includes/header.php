<?php
// Check if $relativePath is defined, otherwise set a default
if (!isset($relativePath)) {
    $relativePath = '';
}

// Use absolute paths for includes to avoid path issues
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

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
    <style>
        /* Import Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

        :root {
            --header-bg: #181f2c;
            --header-border: #232b3e;
            --header-shadow: 0 2px 8px rgba(0,0,0,0.07);
            --header-link: #fff;
            --header-link-hover: #4facfe;
            --header-link-active: #00f2fe;
            --header-link-muted: #bfc8e6;
            --header-brand: #fff;
            --header-brand-accent: #a084e8;
            --header-link-underline: #4facfe;
            --header-radius: 16px;
            --font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
        }

        body {
            font-family: var(--font-family);
            line-height: 1.6;
            color: var(--text-primary);
            background: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .navbar {
            background: var(--header-bg);
            border-bottom: 2px solid var(--header-border);
            box-shadow: var(--header-shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%; /* changed from 100vw */
            left: 0;
            margin: 0;
            border-radius: 0;
        }
        .navbar > .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 5rem;
            position: relative;
        }

        .navbar-brand {
            font-size: 2rem;
            font-weight: 800;
            color: var(--header-brand);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            letter-spacing: -0.5px;
            transition: color 0.2s;
        }
        .navbar-brand span {
            color: var(--header-brand-accent);
        }
        .navbar-brand::before {
            content: "📚";
            font-size: 2rem;
            margin-right: 0.5rem;
        }
        .navbar-brand:hover {
            color: var(--header-link-hover);
        }

        .navbar-toggler {
            display: none;
            background: var(--header-link-hover);
            border: none;
            cursor: pointer;
            padding: 0.75rem;
            border-radius: var(--header-radius);
            transition: background 0.2s;
        }
        .navbar-toggler:hover {
            background: var(--header-link-active);
        }
        .hamburger {
            width: 24px;
            height: 18px;
            position: relative;
        }
        .hamburger span {
            display: block;
            position: absolute;
            height: 2px;
            width: 100%;
            background: var(--header-link);
            border-radius: 2px;
            opacity: 1;
            left: 0;
            transform: rotate(0deg);
            transition: 0.3s;
        }
        .hamburger span:nth-child(1) { top: 0px; }
        .hamburger span:nth-child(2) { top: 8px; }
        .hamburger span:nth-child(3) { top: 16px; }

        .navbar-collapse {
            display: flex;
            align-items: center;
            flex-grow: 1;
            justify-content: space-between;
            margin-left: 3rem;
        }

        .navbar-nav {
            display: flex;
            list-style: none;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            color: var(--header-link);
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: var(--header-radius);
            transition: color 0.2s, background 0.2s;
            background: none;
            font-size: 1rem;
            position: relative;
        }
        .nav-link:hover,
        .nav-link:focus {
            color: var(--header-link-hover);
        }
        .nav-link.active {
            color: var(--header-link-active);
            border-bottom: 2px solid var(--header-link-underline);
            background: none;
        }
        .nav-link::after {
            content: '';
            display: block;
            width: 0;
            height: 2px;
            background: var(--header-link-underline);
            transition: width 0.2s;
            position: absolute;
            left: 0;
            bottom: 8px;
        }
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        /* Home button: keep it simple and white, no underline, no color change */
        .nav-item:first-child .nav-link,
        .nav-item:first-child .nav-link.active,
        .nav-item:first-child .nav-link:hover,
        .nav-item:first-child .nav-link:focus {
            color: #fff !important;
            border-bottom: none !important;
            background: none !important;
        }
        .nav-item:first-child .nav-link::after {
            display: none !important;
        }

        .search-form {
            display: flex;
            max-width: 350px;
            margin: 0 1.5rem;
            position: relative;
        }
        .search-input {
            width: 100%;
            padding: 0.7rem 1.25rem;
            padding-right: 3.5rem;
            border: 1px solid var(--header-border);
            border-radius: 999px;
            outline: none;
            background: #232b3e;
            color: #fff;
            font-size: 1rem;
            font-weight: 500;
            transition: border 0.2s;
        }
        .search-input::placeholder {
            color: var(--header-link-muted);
        }
        .search-input:focus {
            border-color: var(--header-link-hover);
        }
        .search-button {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--header-link-hover);
            border: none;
            color: #fff;
            cursor: pointer;
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background 0.2s;
        }
        .search-button:hover {
            background: var(--header-link-active);
        }
        .search-button::before {
            content: "🔍";
            font-size: 1rem;
        }

        .icon-cart::before {
            content: "🛒";
            font-size: 1.2rem;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--header-link-active);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 50px;
            min-width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }

        .user-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: var(--header-link-hover);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            margin-right: -0.25rem;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            transition: background 0.2s;
        }
        .user-avatar:hover {
            background: var(--header-link-active);
        }

        .dropdown {
            position: relative;
        }
        .dropdown-toggle {
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: var(--header-radius);
            background: none;
            color: var(--header-link);
            border: none;
        }
        .dropdown-toggle:hover {
            color: var(--header-link-hover);
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 0.75rem;
            background: #232b3e;
            border: 1px solid var(--header-border);
            border-radius: var(--header-radius);
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            min-width: 200px;
            overflow: hidden;
            z-index: 1000;
        }
        .dropdown-menu.show {
            display: block;
        }
        .dropdown-item {
            display: block;
            padding: 0.875rem 1.25rem;
            color: var(--header-link);
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
            font-weight: 500;
            border-bottom: 1px solid #232b3e;
        }
        .dropdown-item:last-child {
            border-bottom: none;
        }
        .dropdown-item:hover {
            background: var(--header-link-hover);
            color: #fff;
        }

        .caret-down {
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid var(--header-link);
            transition: transform 0.2s;
        }
        .dropdown-toggle:hover .caret-down,
        .dropdown-toggle.active .caret-down {
            transform: rotate(180deg);
        }

        .position-relative { position: relative; }
        .d-flex { display: flex; }
        .align-items-center { align-items: center; }
        .me-auto { margin-right: auto; }
        .gap-2 { gap: 0.5rem; }

        .content-wrapper {
            flex: 1 0 auto;
            width: 100%;
            margin: 0 auto;
            padding: 0;
        }
        .content-area {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2.5rem 0 2.5rem 0;
        }

        /* Footer styles */
        .footer {
            width: 100%; /* changed from 100vw */
            left: 0;
            background: linear-gradient(135deg, #181f2c 0%, #232b3e 100%);
            color: #fff;
            padding: 0;
            margin: 0;
            border-top: 4px solid #a084e8;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.07);
            flex-shrink: 0;
        }
        .footer .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem 1.5rem 1.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: space-between;
        }
        .footer .footer-brand {
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .footer .footer-brand span {
            color: #a084e8;
        }
        .footer .footer-brand::before {
            content: "📚";
            font-size: 2rem;
            margin-right: 0.5rem;
        }
        .footer .footer-desc {
            max-width: 320px;
            font-size: 1rem;
            color: #bfc8e6;
            margin-bottom: 1.5rem;
        }
        .footer .footer-section {
            min-width: 180px;
            margin-bottom: 1rem;
        }
        .footer .footer-section h4 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #a084e8;
            border-bottom: 1px solid #232b3e;
            padding-bottom: 0.5rem;
        }
        .footer .footer-links,
        .footer .footer-support {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer .footer-links li,
        .footer .footer-support li {
            margin-bottom: 0.7rem;
        }
        .footer .footer-links a,
        .footer .footer-support a {
            color: #bfc8e6;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .footer .footer-links a:hover,
        .footer .footer-support a:hover {
            color: #fff;
        }
        .footer-bottom {
            border-top: 1px solid #232b3e;
            text-align: center;
            color: #bfc8e6;
            font-size: 0.95rem;
            padding: 1rem 0 0.5rem 0;
            margin-top: 1rem;
        }
        .footer .footer-social {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        .footer .footer-social a {
            color: #bfc8e6;
            font-size: 1.2rem;
            transition: color 0.2s;
            text-decoration: none;
        }
        .footer .footer-social a:hover {
            color: #a084e8;
        }

        @media (max-width: 992px) {
            .footer .container {
                flex-direction: column;
                gap: 1.5rem;
                padding: 2rem 1rem 1rem 1rem;
            }
            .content-area {
                padding: 2rem 0 2rem 0;
            }
        }
        @media (max-width: 576px) {
            .footer .container {
                padding: 1.5rem 0.5rem 0.5rem 0.5rem;
            }
            .footer .footer-brand {
                font-size: 1.5rem;
            }
            .footer .footer-brand::before {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-container">
                <a class="navbar-brand" href="<?php echo $relativePath; ?>index.php">BookTrading</a>
                
                <button class="navbar-toggler" type="button" id="navbarToggler">
                    <div class="hamburger">
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
                                    <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/user/dashboard.php') !== false ? 'active' : ''; ?>" href="<?php echo $relativePath; ?>user/dashboard.php">Dashboard</a>
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
                            <input class="search-input" type="search" name="keyword" placeholder="Search amazing books..." autocomplete="off">
                            <button class="search-button" type="submit"></button>
                        </form>
                    <?php endif; ?>

                    <ul class="navbar-nav">
                        <?php if (!isAdmin()): ?>
                            <li class="nav-item">
                                <a href="<?php echo $relativePath; ?>cart.php" class="nav-link position-relative">
                                    <span class="icon-cart"></span>
                                    <?php 
                                    $cartCount = 0;
                                    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                                        foreach ($_SESSION['cart'] as $item) {
                                            $cartCount += $item['quantity'];
                                        }
                                    }
                                    if ($cartCount > 0): 
                                    ?>
                                    <span id="cart-count" class="cart-badge">
                                        <?php echo $cartCount; ?>
                                    </span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (isLoggedIn()): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                                        </div>
                                        <span><?php echo $_SESSION['user_name']; ?></span>
                                        <div class="caret-down"></div>
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
                                <a class="nav-link" href="<?php echo $relativePath; ?>login.php">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $relativePath; ?>register.php">Register</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="content-wrapper">
        <div class="content-area">
            <!-- Page content will be inserted here -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile navigation toggle
            const navbarToggler = document.getElementById('navbarToggler');
            const navbarCollapse = document.getElementById('navbarCollapse');
            
            if (navbarToggler && navbarCollapse) {
                navbarToggler.addEventListener('click', function() {
                    navbarCollapse.classList.toggle('show');
                });
            }
            
            // User dropdown functionality
            const dropdownToggle = document.querySelector('.dropdown-toggle');
            const userDropdown = document.getElementById('userDropdown');
            
            if (dropdownToggle && userDropdown) {
                dropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    userDropdown.classList.toggle('show');
                    this.classList.toggle('active');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.dropdown')) {
                        if (userDropdown && userDropdown.classList.contains('show')) {
                            userDropdown.classList.remove('show');
                            dropdownToggle.classList.remove('active');
                        }
                    }
                });
            }
            
            // Close mobile menu when clicking on a link
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 992) {
                        navbarCollapse.classList.remove('show');
                    }
                });
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 992 && 
                    !e.target.closest('.navbar-container') && 
                    navbarCollapse && navbarCollapse.classList.contains('show')) {
                    navbarCollapse.classList.remove('show');
                }
            });

            // Add floating animation to elements
            const floatingElements = document.querySelectorAll('.nav-link, .search-input, .user-avatar');
            floatingElements.forEach(el => {
                el.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                el.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
        </div>
    </div>
    <!-- End of content area -->

