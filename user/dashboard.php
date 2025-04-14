<?php
// Adjust the path for includes based on the current directory
$relativePath = '../';
require_once $relativePath . 'includes/header.php';

// Redirect if not logged in or is admin
if (!isLoggedIn() || isAdmin()) {
    redirect($relativePath . 'login.php');
}

// Get user ID
$userId = $_SESSION['user_id'];

// Get user details
$user = getUserById($userId);

// Get books sold by the user
$soldBooks = [];
$query = "SELECT b.*, o.created_at as sold_date FROM books b 
          JOIN order_items oi ON b.id = oi.book_id 
          JOIN orders o ON oi.order_id = o.id 
          WHERE b.added_by = $userId AND b.status = 'sold'";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $soldBooks[] = $row;
}

// Get books sent for selling by the user
$sentBooks = [];
$query = "SELECT * FROM books WHERE added_by = $userId AND status IN ('pending', 'submitted')";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $sentBooks[] = $row;
}
?>

<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                User Profile
            </div>
            <div class="card-body">
                <h5 class="card-title"><?php echo $user['name']; ?></h5>
                <p class="card-text"><strong>Email:</strong> <?php echo $user['email']; ?></p>
                <p class="card-text"><strong>Phone:</strong> <?php echo $user['phone']; ?></p>
                <a href="profile.php" class="btn btn-outline-primary btn-sm">Edit Profile</a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                Quick Links
            </div>
            <div class="list-group list-group-flush">
                <a href="orders.php" class="list-group-item list-group-item-action">My Orders</a>
                <a href="add_book.php" class="list-group-item list-group-item-action">Sell Old Book</a>
                <a href="kept_books.php" class="list-group-item list-group-item-action">My Kept Books</a>
                <a href="<?php echo $relativePath; ?>index.php" class="list-group-item list-group-item-action">Browse Books</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3>Welcome to Your Dashboard</h3>
            </div>
            <div class="card-body">
                <p>Use the quick links on the left to navigate through your account options.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>
