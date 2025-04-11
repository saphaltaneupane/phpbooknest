<?php
require_once 'includes/header.php';

// Get order ID from URL
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$status = isset($_GET['status']) ? $_GET['status'] : 'success';
$message = isset($_GET['message']) ? $_GET['message'] : '';

// Get order details
$query = "SELECT o.*, u.name as user_name FROM orders o 
          JOIN users u ON o.user_id = u.id 
          WHERE o.id = $orderId";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    redirect('index.php');
}

$order = mysqli_fetch_assoc($result);

// Get order items
$query = "SELECT oi.*, b.title, b.author FROM order_items oi 
          JOIN books b ON oi.book_id = b.id 
          WHERE oi.order_id = $orderId";
$result = mysqli_query($conn, $query);
$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}

// Display verification result for debugging (remove in production)
$debugInfo = '';
if (isset($_SESSION['verification_result']) && isAdmin()) {
    $debugInfo = '<div class="alert alert-info">
        <h5>Debug Info (Admin Only):</h5>
        <pre>' . print_r($_SESSION['verification_result'], true) . '</pre>
    </div>';
    unset($_SESSION['verification_result']);
}
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-<?php echo $status === 'success' ? 'success' : 'danger'; ?> text-white">
                <h3 class="mb-0">
                    <?php echo $status === 'success' ? 'Payment Successful!' : 'Payment Failed'; ?>
                </h3>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success_message']; ?></div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                
                <?php if ($message): ?>
                    <div class="alert alert-danger"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if (isAdmin()): ?>
                    <?php echo $debugInfo; ?>
                <?php endif; ?>
                
                <?php if ($status === 'success'): ?>
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 60px;"></i>
                        <h4 class="mt-3">Thank you for your order!</h4>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Order Details</h5>
                        <p><strong>Order ID:</strong> #<?php echo $orderId; ?></p>
                        <p><strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
                        <p><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                        <p><strong>Payment Status:</strong> <?php echo ucfirst($order['payment_status']); ?></p>
                        <p><strong>Total Amount:</strong> Rs. <?php echo $order['total_amount']; ?></p>
                        <?php if (!empty($order['transaction_id'])): ?>
                            <p><strong>Transaction ID:</strong> <?php echo $order['transaction_id']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Items</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Book</th>
                                    <th>Author</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?php echo $item['title']; ?></td>
                                        <td><?php echo $item['author']; ?></td>
                                        <td>Rs. <?php echo $item['price']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center mb-4">
                        <i class="fas fa-times-circle text-danger" style="font-size: 60px;"></i>
                        <h4 class="mt-3">Payment was not successful</h4>
                        <p>Please try again or choose a different payment method.</p>
                    </div>
                <?php endif; ?>
                
                <div class="text-center">
                    <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="user/orders.php" class="btn btn-outline-primary">View Orders</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>