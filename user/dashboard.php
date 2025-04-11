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
                <a href="<?php echo $relativePath; ?>index.php" class="list-group-item list-group-item-action">Browse Books</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <!-- Books Sent for Selling Section -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Books Sent for Selling
            </div>
            <div class="card-body">
                <?php if (empty($sentBooks)): ?>
                    <p>You haven't sent any books for selling yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Price</th>
                                    <th>Submitted Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sentBooks as $book): ?>
                                    <tr>
                                        <td><?php echo $book['title']; ?></td>
                                        <td><?php echo $book['author']; ?></td>
                                        <td>Rs. <?php echo number_format($book['price'], 2); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($book['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Books Sold Section -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Books Sold
            </div>
            <div class="card-body">
                <?php if (empty($soldBooks)): ?>
                    <p>You haven't sold any books yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Price</th>
                                    <th>Sold Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($soldBooks as $book): ?>
                                    <tr>
                                        <td><?php echo $book['title']; ?></td>
                                        <td><?php echo $book['author']; ?></td>
                                        <td>Rs. <?php echo number_format($book['price'], 2); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($book['sold_date'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>
