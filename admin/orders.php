<?php
// Adjust the path for includes based on the current directory
$relativePath = '../';
require_once $relativePath . 'includes/header.php';

// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    redirect($relativePath . 'login.php');
}

// Get all orders with proper pagination
$limit = 10; // Number of orders per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total orders for pagination
$countQuery = "SELECT COUNT(*) as total FROM orders";
$countResult = mysqli_query($conn, $countQuery);
$totalOrders = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalOrders / $limit);

// Get orders for current page
$query = "SELECT o.*, u.name as user_name FROM orders o 
          JOIN users u ON o.user_id = u.id 
          ORDER BY o.created_at DESC
          LIMIT $offset, $limit";
$result = mysqli_query($conn, $query);
$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

// Handle form submission for status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = (int)$_POST['order_id'];
    $updated = false;
    $errorMsg = '';
    
    // Update order status if provided
    if (isset($_POST['status'])) {
        $status = sanitize($_POST['status']);
        $query = "UPDATE orders SET status = '$status' WHERE id = $orderId";
        
        if (mysqli_query($conn, $query)) {
            $updated = true;
            // Update book status to 'sold' if order status is 'completed'
            if ($status === 'completed') {
                updateBookStatusToSold($orderId);
            }
        } else {
            $errorMsg .= 'Error updating order status: ' . mysqli_error($conn) . '<br>';
        }
    }
    
    // Update payment status if provided
    if (isset($_POST['payment_status'])) {
        $paymentMethod = sanitize($_POST['payment_method']);
        $paymentStatus = sanitize($_POST['payment_status']);
        
        // Only update if payment method is not Khalti
        if ($paymentMethod !== 'khalti') {
            $query = "UPDATE orders SET payment_status = '$paymentStatus' WHERE id = $orderId";
            
            if (mysqli_query($conn, $query)) {
                $updated = true;
            } else {
                $errorMsg .= 'Error updating payment status: ' . mysqli_error($conn);
            }
        }
    }
    
    // Set session messages
    if ($updated) {
        $_SESSION['success_message'] = 'Order information updated successfully!';
    } else if (!empty($errorMsg)) {
        $_SESSION['error_message'] = $errorMsg;
    }
    
    // Redirect to refresh the page
    redirect('orders.php' . ($page > 1 ? "?page=$page" : ''));
}
?>

<div class="container-fluid py-4">
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
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Manage Orders</h2>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success"><?php echo $_SESSION['success_message']; ?></div>
                        <?php unset($_SESSION['success_message']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; ?></div>
                        <?php unset($_SESSION['error_message']); ?>
                    <?php endif; ?>
                    
                    <?php if (empty($orders)): ?>
                        <div class="alert alert-info">No orders found.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
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
                                            <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td><?php echo ucfirst(htmlspecialchars($order['payment_method'])); ?></td>
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
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Order pagination">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page-1; ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page+1; ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modals -->
<?php foreach ($orders as $order): ?>
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
                            <p><strong>User:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
                            <p><strong>Order Date:</strong> <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
                            <p><strong>Total Amount:</strong> Rs. <?php echo number_format($order['total_amount'], 2); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Payment Method:</strong> <?php echo ucfirst(htmlspecialchars($order['payment_method'])); ?></p>
                            <p><strong>Payment Status:</strong> <?php echo ucfirst($order['payment_status']); ?></p>
                            <p><strong>Order Status:</strong> <?php echo ucfirst($order['status']); ?></p>
                        </div>
                    </div>
                    
                    <h6 class="mt-4 mb-3">Order Items</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Book</th>
                                    <th>Author</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $items = getOrderItems($order['id']);
                                foreach ($items as $item): 
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                                        <td><?php echo htmlspecialchars($item['author']); ?></td>
                                        <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['quantity'] ?? 1; ?></td>
                                        <td>Rs. <?php echo number_format(($item['price'] * ($item['quantity'] ?? 1)), 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (!empty($order['transaction_id'])): ?>
                        <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($order['transaction_id']); ?></p>
                    <?php endif; ?>
                    
                    <form action="orders.php<?php echo $page > 1 ? "?page=$page" : ''; ?>" method="POST" class="mt-4">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <input type="hidden" name="payment_method" value="<?php echo $order['payment_method']; ?>">
                        
                        <!-- Update Payment Status -->
                        <div class="mb-3">
                            <label for="paymentStatus<?php echo $order['id']; ?>" class="form-label">Update Payment Status</label>
                            <?php if ($order['payment_method'] === 'khalti'): ?>
                                <select class="form-select" id="paymentStatus<?php echo $order['id']; ?>" name="payment_status" disabled>
                                    <option value="<?php echo $order['payment_status']; ?>" selected><?php echo ucfirst($order['payment_status']); ?> (Khalti payments cannot be modified)</option>
                                </select>
                                <small class="text-muted">Payment status for Khalti transactions cannot be modified</small>
                            <?php else: ?>
                                <select class="form-select" id="paymentStatus<?php echo $order['id']; ?>" name="payment_status">
                                    <option value="pending" <?php echo $order['payment_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="completed" <?php echo $order['payment_status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="failed" <?php echo $order['payment_status'] === 'failed' ? 'selected' : ''; ?>>Failed</option>
                                </select>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Update Order Status -->
                        <div class="mb-3">
                            <label for="status<?php echo $order['id']; ?>" class="form-label">Update Order Status</label>
                            <select class="form-select" id="status<?php echo $order['id']; ?>" name="status">
                                <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php require_once $relativePath . 'includes/footer.php'; ?>