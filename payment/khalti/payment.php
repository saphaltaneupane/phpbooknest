<?php
// Session configuration must happen before session_start() or inclusion of any file
// that might start the session
$sessionStarted = (session_status() == PHP_SESSION_ACTIVE);
if (!$sessionStarted) {
    // Only set these if session hasn't started yet
    ini_set('session.cookie_lifetime', 3600); // 1 hour
    ini_set('session.gc_maxlifetime', 3600);
    session_start();
}

require_once '../../includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../../login.php?redirect=payment/khalti/payment.php');
}

// Only regenerate session ID if headers haven't been sent yet
if (!headers_sent() && $sessionStarted) {
    session_regenerate_id();
}

// Handle retry attempts
if (isset($_GET['retry'])) {
    $orderId = (int)$_GET['retry'];
    
    // Get order details to recreate payment
    $query = "SELECT * FROM orders WHERE id = $orderId AND user_id = {$_SESSION['user_id']}";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);
        
        // Only allow retries for pending Khalti payments
        if ($order['payment_method'] === 'khalti' && $order['payment_status'] !== 'completed') {
            // Set session variables for new payment
            $_SESSION['order_id'] = $orderId;
            $_SESSION['amount'] = $order['total_amount'];
        } else {
            redirect('../../payment_success.php?order_id=' . $orderId);
        }
    } else {
        redirect('../../user/orders.php');
    }
}

// Redirect if no order in session
if (!isset($_SESSION['order_id']) || !isset($_SESSION['amount'])) {
    redirect('../../index.php');
}

$orderId = $_SESSION['order_id'];
$amount = $_SESSION['amount'];

// Get order details
$query = "SELECT o.*, u.name as user_name FROM orders o
          JOIN users u ON o.user_id = u.id
          WHERE o.id = $orderId";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    redirect('../../index.php');
}

$order = mysqli_fetch_assoc($result);

// Get all items in the order
$query = "SELECT oi.*, b.title, b.author, b.image FROM order_items oi
          JOIN books b ON oi.book_id = b.id
          WHERE oi.order_id = $orderId";
$result = mysqli_query($conn, $query);
$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}

// Create order name for Khalti
$orderName = count($items) > 1 
    ? $items[0]['title'] . ' and ' . (count($items) - 1) . ' more items'
    : $items[0]['title'];

// Get user details
$user = getUserById($_SESSION['user_id']);
?>

<style>
    /* Container styles */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }
    
    .mt-5 {
        margin-top: 3rem;
    }
    
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }
    
    .justify-content-center {
        justify-content: center;
    }
    
    .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
        padding: 0 15px;
        position: relative;
    }
    
    @media (min-width: 768px) {
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    
    /* Card styles */
    .card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }
    
    .card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        background-color: #6c63ff;
        color: white;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    /* Typography */
    h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    h4 {
        font-size: 1.3rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    h5 {
        font-size: 1.1rem;
        margin-top: 1rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }
    
    .mb-0 {
        margin-bottom: 0 !important;
    }
    
    .mb-4 {
        margin-bottom: 1.5rem !important;
    }
    
    .mb-3 {
        margin-bottom: 1rem !important;
    }
    
    .text-center {
        text-align: center !important;
    }
    
    .lead {
        font-size: 1.15rem;
        font-weight: 400;
    }
    
    /* Table styles */
    .table-responsive {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .table {
        width: 100%;
        margin-bottom: 1rem;
        border-collapse: collapse;
    }
    
    .table-bordered {
        border: 1px solid #dee2e6;
    }
    
    .table th,
    .table td {
        padding: 0.75rem;
        vertical-align: top;
        text-align: left;
    }
    
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
    }
    
    /* Match the purple header color in the screenshot */
    .table thead th {
        background-color: #6c63ff;
        color: white;
        font-weight: 600;
        border-color: #5d54e0;
    }
    
    .table tbody td {
        background-color: #ffffff;
        color: #333333;
        font-weight: 500;
    }
    
    /* Button styles */
    .btn {
        display: inline-block;
        font-weight: 500;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        user-select: none;
        border: 1px solid transparent;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: 0.25rem;
        transition: all 0.15s ease-in-out;
        text-decoration: none;
    }
    
    .btn-primary {
        color: #fff;
        background-color: #6c63ff;
        border-color: #6c63ff;
    }
    
    .btn-primary:hover {
        background-color: #5a52e0;
        border-color: #5a52e0;
    }
    
    .btn-lg {
        padding: 0.5rem 1rem;
        font-size: 1.25rem;
        line-height: 1.5;
        border-radius: 0.3rem;
    }
    
    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
        background-color: transparent;
    }
    
    .btn-outline-secondary:hover {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }
    
    /* Loading indicator */
    .d-none {
        display: none !important;
    }
    
    .spinner-border {
        display: inline-block;
        width: 2rem;
        height: 2rem;
        vertical-align: text-bottom;
        border: 0.25em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-border 0.75s linear infinite;
    }
    
    .text-primary {
        color: #6c63ff !important;
    }
    
    .visually-hidden {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
    
    .mt-2 {
        margin-top: 0.5rem !important;
    }
    
    .mt-3 {
        margin-top: 1rem !important;
    }
    
    /* Animation for loading spinner */
    @keyframes spinner-border {
        to { transform: rotate(360deg); }
    }
    
    /* Icon styling */
    .bi {
        display: inline-block;
        vertical-align: -0.125em;
        margin-right: 0.25rem;
    }
    
    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .card {
            background-color: #2d2d2d;
            color: #f8f9fa;
        }
        
        .table tbody td {
            background-color: #2d2d2d;
            color: #f8f9fa;
            border-color: #495057;
        }
        
        .table-bordered,
        .table-bordered th,
        .table-bordered td {
            border-color: #495057;
        }
        
        .btn-outline-secondary {
            color: #adb5bd;
            border-color: #adb5bd;
        }
        
        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: #f8f9fa;
        }
    }
</style>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Complete Payment</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h4><?php echo htmlspecialchars($orderName); ?></h4>
                        <p class="lead">Amount: Rs. <?php echo number_format($amount, 2); ?></p>
                    </div>
                    
                    <?php if (count($items) > 0): ?>
                        <div class="mb-4">
                            <h5>Order Items:</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Book</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                                <td><?php echo $item['quantity']; ?></td>
                                                <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Loading indicator -->
                    <div id="payment-loading" class="text-center d-none mb-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Connecting to payment gateway...</p>
                    </div>
                    
                    <!-- Payment form -->
                    <form id="payment-form" action="../khalti/initiate.php" method="post" class="text-center">
                        <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                        <input type="hidden" name="amount" value="<?php echo $amount; ?>">
                        <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($user['name']); ?>">
                        <input type="hidden" name="customer_email" value="<?php echo htmlspecialchars($user['email']); ?>">
                        <input type="hidden" name="customer_phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                        
                        <button type="submit" id="pay-button" class="btn btn-primary btn-lg">
                            <i class="bi bi-credit-card"></i> Pay with Khalti
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="../../cart.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const payForm = document.getElementById('payment-form');
        const payButton = document.getElementById('pay-button');
        const loadingIndicator = document.getElementById('payment-loading');
        
        payForm.addEventListener('submit', function() {
            // Show loading indicator
            loadingIndicator.classList.remove('d-none');
            payButton.disabled = true;
        });
    });
</script>

<?php require_once '../../includes/footer.php'; ?>