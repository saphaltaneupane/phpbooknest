<?php
// Adjust the path for includes based on the current directory
$relativePath = '../';
require_once $relativePath . 'includes/header.php';

// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    redirect($relativePath . 'login.php');
}

// Get all orders
$query = "SELECT o.*, u.name as user_name FROM orders o 
          JOIN users u ON o.user_id = u.id 
          ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $query);
$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = (int)$_POST['order_id'];
    $status = sanitize($_POST['status']);
    
    $query = "UPDATE orders SET status = '$status' WHERE id = $orderId";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = 'Order status updated successfully!';
        redirect('orders.php');
    } else {
        $error = 'Error updating order status: ' . mysqli_error($conn);
    }
}
?>

<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Admin Panel
            </div>
            <div class="list-group list-group-flush">
                <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="users.php" class="list-group-item list-group-item-action">Manage Users</a>
                <a href="books.php" class="list-group-item list-group-item-action">Manage Books</a>
                <a href="add_book.php" class="list-group-item list-group-item-action">Add New Book</a>
                <a href="orders.php" class="list-group-item list-group-item-action active">Manage Orders</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <h2>Manage Orders</h2>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (empty($orders)): ?>
            <div class="alert alert-info">No orders found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Payment Status</th>
                            <th>Order Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo $order['user_name']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td>Rs. <?php echo $order['total_amount']; ?></td>
                                <td><?php echo ucfirst($order['payment_method']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $order['payment_status'] === 'completed' ? 'success' : ($order['payment_status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                        <?php echo ucfirst($order['payment_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $order['status'] === 'completed' ? 'success' : ($order['status'] === 'pending' ? 'warning' : ($order['status'] === 'cancelled' ? 'danger' : 'info')); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#orderModal<?php echo $order['id']; ?>">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Order Details Modal -->
                            <div class="modal fade" id="orderModal<?php echo $order['id']; ?>" tabindex="-1" aria-labelledby="orderModalLabel<?php echo $order['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="orderModalLabel<?php echo $order['id']; ?>">Order #<?php echo $order['id']; ?> Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <p><strong>User:</strong> <?php echo $order['user_name']; ?></p>
                                                    <p><strong>Order Date:</strong> <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
                                                    <p><strong>Total Amount:</strong> Rs. <?php echo $order['total_amount']; ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                                                    <p><strong>Payment Status:</strong> <?php echo ucfirst($order['payment_status']); ?></p>
                                                    <p><strong>Order Status:</strong> <?php echo ucfirst($order['status']); ?></p>
                                                </div>
                                            </div>
                                            
                                            <h6>Order Items</h6>
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Book</th>
                                                        <th>Author</th>
                                                        <th>Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $items = getOrderItems($order['id']);
                                                    foreach ($items as $item): 
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $item['title']; ?></td>
                                                            <td><?php echo $item['author']; ?></td>
                                                            <td>Rs. <?php echo $item['price']; ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                            
                                            <?php if ($order['transaction_id']): ?>
                                                <p><strong>Transaction ID:</strong> <?php echo $order['transaction_id']; ?></p>
                                            <?php endif; ?>
                                            
                                            <form action="orders.php" method="POST">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Update Order Status</label>
                                                    <select class="form-select" id="status" name="status">
                                                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                        <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Update Status</button>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>