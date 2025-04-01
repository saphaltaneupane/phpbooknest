<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookStore - Online Book Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Custom styles */
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .dark-mode {
                background-color: #181818;
                color: #e2e2e2;
            }
            .dark-card {
                background-color: #2d2d2d;
            }
            .dark-border {
                border-color: #3d3d3d;
            }
        }
    </style>
    <script>
        // Check for dark mode preference
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark-mode');
        }
        
        // Listen for changes to color scheme
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
            if (event.matches) {
                document.documentElement.classList.add('dark-mode');
            } else {
                document.documentElement.classList.remove('dark-mode');
            }
        });
    </script>
</head>
<body class="min-h-screen flex flex-col">
    <header class="bg-indigo-700 text-white shadow-md">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="index.php" class="text-xl font-bold">BookStore</a>
                
                <div class="hidden md:block flex-grow mx-10">
                    <form action="index.php" method="GET" class="flex items-center">
                        <input type="hidden" name="controller" value="book">
                        <input type="hidden" name="action" value="search">
                        <input type="text" name="query" placeholder="Search books..." class="w-full px-4 py-2 rounded-l-md text-black text-base">
                        <button type="submit" class="bg-indigo-800 px-4 py-2 rounded-r-md">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                
                <nav>
                    <ul class="flex space-x-4">
                        <li><a href="index.php" class="hover:text-indigo-200"><i class="fas fa-home mr-1"></i> Home</a></li>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <li><a href="index.php?controller=admin&action=dashboard" class="hover:text-indigo-200"><i class="fas fa-tachometer-alt mr-1"></i> Admin</a></li>
                            <?php endif; ?>
                            
                            <li><a href="index.php?controller=order&action=history" class="hover:text-indigo-200"><i class="fas fa-shopping-bag mr-1"></i> Orders</a></li>
                            <li><a href="index.php?controller=user&action=profile" class="hover:text-indigo-200"><i class="fas fa-user mr-1"></i> Profile</a></li>
                            <li><a href="index.php?controller=user&action=logout" class="hover:text-indigo-200"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a></li>
                        <?php else: ?>
                            <li><a href="index.php?controller=user&action=login" class="hover:text-indigo-200"><i class="fas fa-sign-in-alt mr-1"></i> Login</a></li>
                            <li><a href="index.php?controller=user&action=register" class="hover:text-indigo-200"><i class="fas fa-user-plus mr-1"></i> Register</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            
            <!-- Mobile search -->
            <div class="mt-2 md:hidden">
                <form action="index.php" method="GET" class="flex items-center">
                    <input type="hidden" name="controller" value="book">
                    <input type="hidden" name="action" value="search">
                    <input type="text" name="query" placeholder="Search books..." class="w-full px-4 py-2 rounded-l-md text-black text-base">
                    <button type="submit" class="bg-indigo-800 px-4 py-2 rounded-r-md">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>
    
    <main class="container mx-auto px-4 py-6 flex-grow">