<?php
require_once 'includes/header.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get order ID from URL parameter
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$status = isset($_GET['status']) ? $_GET['status'] : 'success';
$message = isset($_GET['message']) ? $_GET['message'] : '';

// If payment was successful, clear the cart
if ($status === 'success') {
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
}

// Get order details if order ID is provided
if ($orderId > 0) {
    $query = "SELECT * FROM orders WHERE id = $orderId";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);
    }
}
?>

<style>
    /* Thank you page styles */
    .thankyou-container {
        max-width: 700px;
        margin: 40px auto;
        text-align: center;
        padding: 20px;
    }
    
    .thankyou-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        padding: 40px 30px;
        transition: transform 0.3s ease;
    }
    
    .thankyou-icon {
        font-size: 70px;
        margin-bottom: 20px;
    }
    
    .icon-success {
        color: #28a745;
    }
    
    .icon-pending {
        color: #ffc107;
    }
    
    .icon-failed {
        color: #dc3545;
    }
    
    .thankyou-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #333;
    }
    
    .thankyou-message {
        font-size: 18px;
        color: #666;
        margin-bottom: 30px;
        line-height: 1.6;
    }
    
    .order-number {
        font-weight: bold;
        color: #5D5CDE;
    }
    
    .buttons-container {
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 25px;
    }
    
    .btn {
        display: inline-block;
        font-weight: 500;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        cursor: pointer;
        padding: 12px 24px;
        border-radius: 6px;
        font-size: 16px;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .btn-primary {
        background-color: #5D5CDE;
        color: white;
        border: none;
    }
    
    .btn-primary:hover {
        background-color: #4a49b7;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(93, 92, 222, 0.3);
    }
    
    .btn-outline {
        background-color: transparent;
        color: #5D5CDE;
        border: 2px solid #5D5CDE;
    }
    
    .btn-outline:hover {
        background-color: #f0f0ff;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(93, 92, 222, 0.15);
    }
    
    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .thankyou-card {
            background-color: #2c2c2c;
        }
        
        .thankyou-title {
            color: #f5f5f5;
        }
        
        .thankyou-message {
            color: #cccccc;
        }
        
        .btn-outline {
            color: #7d76ff;
            border-color: #7d76ff;
        }
        
        .btn-outline:hover {
            background-color: rgba(93, 92, 222, 0.1);
        }
    }
</style>

<div class="thankyou-container">
    <div class="thankyou-card">
        <?php if ($status === 'pending'): ?>
            <div class="thankyou-icon icon-pending">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <h1 class="thankyou-title">Payment Pending</h1>
        <?php elseif ($status === 'failed'): ?>
            <div class="thankyou-icon icon-failed">
                <i class="bi bi-exclamation-circle"></i>
            </div>
            <h1 class="thankyou-title">Payment Failed</h1>
        <?php else: ?>
            <div class="thankyou-icon icon-success">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h1 class="thankyou-title">Thank You for Your Order!</h1>
        <?php endif; ?>
        
        <?php if ($orderId > 0): ?>
            <p class="thankyou-message">
                Your order <span class="order-number">#<?php echo $orderId; ?></span> has been placed. 
                <?php if ($status === 'success'): ?>
                    Your payment has been confirmed and your order is being processed.
                <?php elseif ($status === 'pending'): ?>
                    Your payment is being processed. We'll update your order once payment is confirmed.
                <?php else: ?>
                    There was an issue with your payment. Please try again or contact customer support.
                <?php endif; ?>
            </p>
        <?php else: ?>
            <p class="thankyou-message">
                Your order has been placed.
                We'll process it shortly and notify you when it's ready.
            </p>
        <?php endif; ?>
        
        <?php if (!empty($message)): ?>
            <p class="thankyou-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        
        <div class="buttons-container">
            <a href="index.php" class="btn btn-outline">
                <i class="bi bi-house"></i> Go to Homepage
            </a>
            <?php if (isLoggedIn()): ?>
                <a href="user/orders.php" class="btn btn-primary">
                    <i class="bi bi-bag-check"></i> View My Orders
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>