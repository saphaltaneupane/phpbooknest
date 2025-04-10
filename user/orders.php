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

// Get user's orders
$orders = getUserOrders($userId);
?>

<div class="row">
    <div class="col-12">
        <h2>My Orders</h2>
        
        <?php if (empty($orders)): ?>
            <div class="alert alert-info">You haven't placed any orders yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order ID</th>
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
        
        <div class="mt-4">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>