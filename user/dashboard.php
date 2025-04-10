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

// Get user's orders
$orders = getUserOrders($userId);

// Get recommended books
$recommendedBooks = getTopRatedBooks(4);
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
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Recent Orders
            </div>
            <div class="card-body">
                <?php if (empty($orders)): ?>
                    <p>You haven't placed any orders yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        <td>Rs. <?php echo $order['total_amount']; ?></td>
                                        <td><?php echo ucfirst($order['payment_method']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $order['status'] === 'completed' ? 'success' : ($order['status'] === 'pending' ? 'warning' : 'info'); ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (count($orders) > 5): ?>
                        <div class="text-center mt-3">
                            <a href="orders.php" class="btn btn-outline-primary btn-sm">View All Orders</a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Recommended Books Section -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                Recommended For You
            </div>
            <div class="card-body">
                <?php if (empty($recommendedBooks)): ?>
                    <p>No book recommendations available.</p>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($recommendedBooks as $book): ?>
                            <div class="col-md-3 mb-3">
                                <div class="card book-card h-100">
                                    <img src="<?php echo $relativePath; ?>assets/images/<?php echo $book['image']; ?>" class="card-img-top book-image" alt="<?php echo $book['title']; ?>">
                                    <div class="card-body">
                                        <h6 class="card-title"><?php echo $book['title']; ?></h6>
                                        <p class="card-text book-price">Rs. <?php echo $book['price']; ?></p>
                                        <a href="<?php echo $relativePath; ?>book_details.php?id=<?php echo $book['id']; ?>" class="btn btn-primary btn-sm">View</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>