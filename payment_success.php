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

<style>
    /* Main container styles */
    .payment-container {
        padding: 40px 0;
        background-color: #f0f3f8;
        min-height: 80vh;
    }
    
    /* Card styles */
    .payment-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        background-color: #ffffff;
    }
    
    /* Header styles based on payment status */
    .success-header {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 25px;
    }
    
    .failed-header {
        background: linear-gradient(135deg, #dc3545, #fd7e14);
        color: white;
        padding: 25px;
    }
    
    .card-header h3 {
        margin: 0;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }
    
    /* Card body styles */
    .payment-body {
        padding: 30px;
        color: #333333;
    }
    
    /* Success icon styles */
    .success-icon {
        font-size: 80px;
        color: #28a745;
        margin-bottom: 15px;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }
    
    /* Failed icon styles */
    .failed-icon {
        font-size: 80px;
        color: #dc3545;
    }
    
    /* Section styles */
    .order-section {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
        border-left: 4px solid #007bff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .order-section h5 {
        color: #007bff;
        font-weight: 600;
        margin-bottom: 15px;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 10px;
        font-size: 1.25rem;
    }
    
    .order-info p {
        margin-bottom: 12px;
        font-size: 1.05rem;
        color: #343a40;
        display: flex;
        align-items: center;
    }
    
    .order-info strong {
        color: #495057;
        display: inline-block;
        width: 150px;
        font-weight: 600;
    }
    
    /* Table styles */
    .items-table {
        width: 100%;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 0;
    }
    
    .items-table thead {
        background-color: #e9ecef;
    }
    
    .items-table th {
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        padding: 12px 15px;
        font-size: 1rem;
    }
    
    .items-table td {
        padding: 12px 15px;
        vertical-align: middle;
        color: #212529;
        font-size: 1rem;
        border-bottom: 1px solid #dee2e6;
    }
    
    .text-right {
        text-align: right;
    }
    
    .text-center {
        text-align: center;
    }
    
    .font-weight-bold {
        font-weight: 700;
    }
    
    .items-table tbody tr:hover {
        background-color: #f1f5f9;
    }
    
    .items-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    /* Button styles */
    .action-buttons {
        margin-top: 30px;
    }
    
    .shop-button {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        padding: 12px 25px;
        font-weight: 600;
        border: none;
        border-radius: 5px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-right: 10px;
    }
    
    .shop-button:hover {
        background: linear-gradient(135deg, #0056b3, #004085);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        color: white;
        text-decoration: none;
    }
    
    .orders-button {
        background-color: white;
        color: #007bff;
        border: 2px solid #007bff;
        padding: 10px 25px;
        font-weight: 600;
        border-radius: 5px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .orders-button:hover {
        background-color: #f0f7ff;
        color: #0056b3;
        border-color: #0056b3;
        transform: translateY(-2px);
        text-decoration: none;
    }
    
    /* Message styles */
    .thank-you-message {
        font-size: 1.4rem;
        color: #343a40;
        margin-bottom: 20px;
        font-weight: 600;
    }
    
    .error-message {
        font-size: 1.2rem;
        color: #6c757d;
    }
    
    /* Order ID badge */
    .order-id {
        font-family: monospace;
        background-color: #e9ecef;
        padding: 3px 8px;
        border-radius: 4px;
        color: #495057;
        margin-left: 5px;
        font-weight: 600;
    }
    
    /* Transaction ID */
    .transaction-id {
        font-family: monospace;
        word-break: break-all;
        color: #28a745;
        font-weight: 500;
    }
    
    /* Alert styles */
    .alert {
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .alert-success {
        background-color: #d4edda;
        border-left: 4px solid #28a745;
        color: #155724;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        border-left: 4px solid #dc3545;
        color: #721c24;
    }
    
    /* Status badge */
    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 15px;
        color: white;
        font-weight: 600;
        font-size: 0.85rem;
        background-color: #28a745;
    }
    
    /* Book title styling */
    .book-title {
        font-weight: 600;
        color: white;
    }
    
    /* Book author styling */
    .book-author {
        color: #6c757d;
        font-style: italic;
    }
    
    /* Total price styling */
    .total-price {
        font-weight: 700;
        color: #0056b3;
        font-size: 1.1rem;
    }
    
    /* Total row styling */
    .total-row {
        background-color: #e9ecef;
    }
    
    .total-row td {
        font-weight: 700;
    }
</style>

<div class="payment-container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="payment-card card">
                <div class="card-header <?php echo $status === 'success' ? 'success-header' : 'failed-header'; ?>">
                    <h3 class="mb-0">
                        <?php echo $status === 'success' ? 'Payment Successful!' : 'Payment Failed'; ?>
                    </h3>
                </div>
                <div class="payment-body card-body">
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
                            <i class="fas fa-check-circle success-icon"></i>
                            <h4 class="thank-you-message">Thank you for your order!</h4>
                            <p class="text-muted">Your order has been received and is now being processed.</p>
                        </div>
                        
                        <div class="order-section order-info mb-4">
                            <h5>Order Details</h5>
                            <p><strong>Order ID:</strong> <span class="order-id">#<?php echo $orderId; ?></span></p>
                            <p><strong>Customer:</strong> <?php echo $order['user_name']; ?></p>
                            <p><strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
                            <p><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                            <p><strong>Payment Status:</strong> 
                                <span class="status-badge">
                                    <?php echo ucfirst($order['payment_status']); ?>
                                </span>
                            </p>
                            <p><strong>Total Amount:</strong> <span class="total-price">Rs. <?php echo number_format($order['total_amount'], 2); ?></span></p>
                            <?php if (!empty($order['transaction_id'])): ?>
                                <p><strong>Transaction ID:</strong> <span class="transaction-id"><?php echo $order['transaction_id']; ?></span></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="order-section mb-4">
                            <h5>Items Purchased</h5>
                            <div class="table-responsive">
                                <table class="table items-table">
                                    <thead>
                                        <tr>
                                            <th>Book Title</th>
                                            <th>Author</th>
                                            <th class="text-right">Price</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td class="book-title"><?php echo $item['title']; ?></td>
                                                <td class="book-author"><?php echo $item['author']; ?></td>
                                                <td class="text-right">Rs. <?php echo number_format($item['price'], 2); ?></td>
                                                <td class="text-center"><?php echo $item['quantity']; ?></td>
                                                <td class="text-right">Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr class="total-row">
                                            <td colspan="4" class="text-right font-weight-bold">Total</td>
                                            <td class="text-right total-price">Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center mb-4">
                            <i class="fas fa-times-circle failed-icon"></i>
                            <h4 class="thank-you-message">Payment was not successful</h4>
                            <p class="error-message">We encountered an issue processing your payment. Please try again or choose a different payment method.</p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center action-buttons">
                        <a href="index.php" class="shop-button">Continue Shopping</a>
                        <?php if (isLoggedIn()): ?>
                            <a href="user/orders.php" class="orders-button">View My Orders</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>