<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNest - <?php echo isset($page_title) ? $page_title : 'Online Book Store'; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if (isset($is_admin) && $is_admin): ?>
    <link rel="stylesheet" href="assets/css/admin.css">
    <?php endif; ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-book-open mr-2"></i>BookNest
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['user_type'] === 'admin'): ?>
                        <!-- Admin Navigation -->
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item <?php echo $active_page === 'dashboard' ? 'active' : ''; ?>">
                                <a class="nav-link" href="index.php?page=admin_dashboard">Dashboard</a>
                            </li>
                            <li class="nav-item <?php echo $active_page === 'manage_books' ? 'active' : ''; ?>">
                                <a class="nav-link" href="index.php?page=admin_manage_books">Manage Books</a>
                            </li>
                            <li class="nav-item <?php echo $active_page === 'manage_users' ? 'active' : ''; ?>">
                                <a class="nav-link" href="index.php?page=admin_manage_users">Manage Users</a>
                            </li>
                        </ul>
                    <?php else: ?>
                        <!-- User Navigation -->
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item <?php echo $active_page === 'dashboard' ? 'active' : ''; ?>">
                                <a class="nav-link" href="index.php?page=dashboard">Home</a>
                            </li>
                            <li class="nav-item <?php echo $active_page === 'books' ? 'active' : ''; ?>">
                                <a class="nav-link" href="index.php?page=books">Browse Books</a>
                            </li>
                            <li class="nav-item <?php echo $active_page === 'my_books' ? 'active' : ''; ?>">
                                <a class="nav-link" href="index.php?page=my_books">My Books</a>
                            </li>
                            <li class="nav-item <?php echo $active_page === 'add_used_book' ? 'active' : ''; ?>">
                                <a class="nav-link" href="index.php?page=add_used_book">Sell A Book</a>
                            </li>
                            <li class="nav-item <?php echo $active_page === 'purchase_history' ? 'active' : ''; ?>">
                                <a class="nav-link" href="index.php?page=purchase_history">Purchase History</a>
                            </li>
                        </ul>
                        <form class="form-inline my-2 my-lg-0" action="index.php" method="GET">
                            <input type="hidden" name="page" value="search">
                            <input class="form-control mr-sm-2" type="search" name="keyword" placeholder="Search books..." aria-label="Search">
                            <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Search</button>
                        </form>
                    <?php endif; ?>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user-circle mr-1"></i><?php echo $_SESSION['username']; ?>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="index.php?page=profile">Profile</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="index.php?page=logout">Logout</a>
                            </div>
                        </li>
                    </ul>
                <?php else: ?>
                    <!-- Guest Navigation -->
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item <?php echo $active_page === 'home' ? 'active' : ''; ?>">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item <?php echo $active_page === 'login' ? 'active' : ''; ?>">
                            <a class="nav-link" href="index.php?page=login">Login</a>
                        </li>
                        <li class="nav-item <?php echo $active_page === 'register' ? 'active' : ''; ?>">
                            <a class="nav-link" href="index.php?page=register">Register</a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>