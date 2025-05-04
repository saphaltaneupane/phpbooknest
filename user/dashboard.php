<?php
// Adjust the path for includes based on the current directory
$relativePath = '../';
require_once $relativePath . 'includes/header.php';

// Redirect if not logged in or is admin
if (!isLoggedIn() || isAdmin()) {
    redirect($relativePath . 'index.php');
}

// Get user ID
$userId = $_SESSION['user_id'];

// Get user details
$user = getUserById($userId);

// SQL queries will still be here for backend functionality even though we're not displaying results
$soldBooks = [];
$query = "SELECT b.*, o.created_at as sold_date FROM books b 
          JOIN order_items oi ON b.id = oi.book_id 
          JOIN orders o ON oi.order_id = o.id 
          WHERE b.added_by = $userId AND b.status = 'sold'";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $soldBooks[] = $row;
}

$sentBooks = [];
$query = "SELECT * FROM books WHERE added_by = $userId AND status IN ('pending', 'submitted')";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $sentBooks[] = $row;
}
?>

<style>
    /* Pure CSS Dashboard Styles */
    :root {
        --primary-color: #6c63ff;
        --primary-dark: #5652db;
        --primary-light: #817dff;
        --secondary-color: #ff9d72;
        --accent-color: #ff6584;
        --light-color: #f8f9ff;
        --dark-color: #2c2c54;
        --gray-light: #f0f2f9;
        --gray-medium: #e0e0e0;
        --text-primary: #333333;
        --text-secondary: #666666;
        --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 5px 15px rgba(0, 0, 0, 0.08);
        --shadow-lg: 0 10px 20px rgba(0, 0, 0, 0.1);
        --radius: 12px;
        --radius-sm: 6px;
        --transition: all 0.3s ease;
    }
    
    .dashboard-wrapper {
        display: grid;
        grid-template-columns: 1fr 3fr;
        gap: 24px;
        padding-bottom: 20px;
    }
    
    /* Sidebar Styles */
    .dashboard-sidebar {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    
    .profile-card {
        background-color: white;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
    }
    
    .profile-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }
    
    .profile-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #8367ff 100%);
        color: white;
        padding: 24px;
        position: relative;
    }
    
    .profile-header::after {
        content: '';
        position: absolute;
        bottom: -20px;
        right: -20px;
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    
    .profile-name {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0 0 6px 0;
        letter-spacing: -0.5px;
    }
    
    .profile-body {
        padding: 20px;
    }
    
    .profile-info {
        margin-bottom: 20px;
    }
    
    .info-row {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        color: var(--text-secondary);
    }
    
    .info-icon {
        width: 24px;
        margin-right: 10px;
        color: var(--primary-color);
    }
    
    .edit-profile-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--primary-color) 0%, #5652db 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
        border: none;
        cursor: pointer;
    }
    
    .edit-profile-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(108, 99, 255, 0.3);
    }
    
    /* Navigation Card */
    .nav-card {
        background-color: white;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }
    
    .nav-header {
        background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
        color: white;
        padding: 16px 20px;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .nav-links {
        display: flex;
        flex-direction: column;
    }
    
    .nav-link {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        text-decoration: none;
        color: var(--text-primary);
        border-bottom: 1px solid var(--gray-light);
        transition: var(--transition);
    }
    
    .nav-link:last-child {
        border-bottom: none;
    }
    
    .nav-link:hover {
        background-color: var(--gray-light);
        transform: translateX(5px);
    }
    
    .nav-icon {
        margin-right: 12px;
        color: var(--primary-color);
        transition: var(--transition);
        width: 20px;
        text-align: center;
    }
    
    .nav-link:hover .nav-icon {
        transform: scale(1.2);
    }
    
    /* Main Content */
    .dashboard-main {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    
    .welcome-banner {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        border-radius: var(--radius);
        padding: 30px;
        position: relative;
        overflow: hidden;
    }
    
    .welcome-banner::before {
        content: '';
        font-family: "bootstrap-icons";
        content: "\F77D"; /* Bootstrap Icons book emoji */
        position: absolute;
        bottom: 20px;
        right: 30px;
        font-size: 4rem;
        opacity: 0.2;
    }
    
    .welcome-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0 0 12px 0;
    }
    
    .welcome-text {
        font-size: 1rem;
        opacity: 0.9;
        max-width: 80%;
        margin: 0;
        line-height: 1.5;
    }
    
    /* Activity Cards */
    .activity-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .activity-card {
        background-color: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        padding: 30px 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }
    
    .activity-card:nth-child(1) {
        border-top: 4px solid var(--primary-color);
    }
    
    .activity-card:nth-child(2) {
        border-top: 4px solid var(--secondary-color);
    }
    
    .activity-card:nth-child(3) {
        border-top: 4px solid var(--accent-color);
    }
    
    .activity-card:hover {
        transform: translateY(-7px);
        box-shadow: var(--shadow-lg);
    }
    
    .activity-icon {
        font-size: 36px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        position: relative;
    }
    
    .activity-card:nth-child(1) .activity-icon {
        color: var(--primary-color);
        background-color: rgba(108, 99, 255, 0.1);
    }
    
    .activity-card:nth-child(2) .activity-icon {
        color: var(--secondary-color);
        background-color: rgba(255, 157, 114, 0.1);
    }
    
    .activity-card:nth-child(3) .activity-icon {
        color: var(--accent-color);
        background-color: rgba(255, 101, 132, 0.1);
    }
    
    .activity-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0 0 10px 0;
        color: var(--text-primary);
    }
    
    .activity-text {
        color: var(--text-secondary);
        margin: 0 0 24px 0;
        font-size: 0.9rem;
        line-height: 1.5;
    }
    
    .activity-button {
        display: inline-block;
        padding: 10px 24px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
        font-size: 0.95rem;
    }
    
    .activity-card:nth-child(1) .activity-button {
        background-color: var(--primary-color);
        color: white;
    }
    
    .activity-card:nth-child(2) .activity-button {
        background-color: var(--secondary-color);
        color: white;
    }
    
    .activity-card:nth-child(3) .activity-button {
        background-color: var(--accent-color);
        color: white;
    }
    
    .activity-button:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }
    
    /* Responsive Styles */
    @media screen and (max-width: 992px) {
        .dashboard-wrapper {
            grid-template-columns: 1fr;
        }
        
        .dashboard-sidebar {
            order: 2;
        }
        
        .dashboard-main {
            order: 1;
        }
    }
    
    @media screen and (max-width: 768px) {
        .activity-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .activity-card:last-child {
            grid-column: span 2;
            max-width: 80%;
            margin: 0 auto;
        }
    }
    
    @media screen and (max-width: 576px) {
        .welcome-banner::before {
            display: none;
        }
        
        .welcome-text {
            max-width: 100%;
        }
        
        .activity-grid {
            grid-template-columns: 1fr;
        }
        
        .activity-card:last-child {
            grid-column: auto;
            max-width: 100%;
        }
        
        .welcome-title {
            font-size: 1.5rem;
        }
    }
</style>

<div class="dashboard-wrapper">
    <!-- Sidebar Section -->
    <div class="dashboard-sidebar">
        <!-- User Profile Card -->
        <div class="profile-card">
            <div class="profile-header">
                <h2 class="profile-name"><?php echo $user['name']; ?></h2>
            </div>
            <div class="profile-body">
                <div class="profile-info">
                    <div class="info-row">
                        <i class="bi bi-envelope info-icon"></i>
                        <?php echo $user['email']; ?>
                    </div>
                    <div class="info-row">
                        <i class="bi bi-telephone info-icon"></i>
                        <?php echo $user['phone']; ?>
                    </div>
                </div>
                <a href="profile.php" class="edit-profile-button">
                    <i class="bi bi-pencil"></i> Edit Profile
                </a>
            </div>
        </div>
        
        <!-- Navigation Card -->
        <div class="nav-card">
            <div class="nav-header">
                Quick Links
            </div>
            <div class="nav-links">
                <a href="orders.php" class="nav-link">
                    <i class="bi bi-bag nav-icon"></i> My Orders
                </a>
                <a href="add_book.php" class="nav-link">
                    <i class="bi bi-book nav-icon"></i> Sell Old Book
                </a>
                <a href="kept_books.php" class="nav-link">
                    <i class="bi bi-bookmark nav-icon"></i> My Kept Books
                </a>
                <a href="<?php echo $relativePath; ?>index.php" class="nav-link">
                    <i class="bi bi-search nav-icon"></i> Browse Books
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main Content Area -->
    <div class="dashboard-main">
        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <h1 class="welcome-title">Welcome to Your Dashboard, <?php echo $user['name']; ?>!</h1>
            <p class="welcome-text">Manage your book trading activities, track orders, and explore new titles from your personalized dashboard.</p>
        </div>
        
        <!-- Activity Cards -->
        <div class="activity-grid">
            <div class="activity-card">
                <div class="activity-icon">
                    <i class="bi bi-book"></i>
                </div>
                <h3 class="activity-title">Start Selling</h3>
                <p class="activity-text">List your unused books and earn money from their sale.</p>
                <a href="add_book.php" class="activity-button">Add Book</a>
            </div>
            
            <div class="activity-card">
                <div class="activity-icon">
                    <i class="bi bi-bag"></i>
                </div>
                <h3 class="activity-title">View Orders</h3>
                <p class="activity-text">Check your purchase history and track deliveries.</p>
                <a href="orders.php" class="activity-button">See Orders</a>
            </div>
            
            <div class="activity-card">
                <div class="activity-icon">
                    <i class="bi bi-bookmark"></i>
                </div>
                <h3 class="activity-title">Saved Books</h3>
                <p class="activity-text">Browse your collection of kept books and references.</p>
                <a href="kept_books.php" class="activity-button">View Collection</a>
            </div>
        </div>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>