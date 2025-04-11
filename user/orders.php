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
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Book Name</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Payment Status</th>
                            <th>Order Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>
                                    <?php 
                                    $items = getOrderItems($order['id']);
                                    echo $items[0]['title'];
                                    if (count($items) > 1) {
                                        echo ' + '.(count($items)-1).' more';
                                    }
                                    ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
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
                            </tr>
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