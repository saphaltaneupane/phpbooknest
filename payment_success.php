<?php
require_once 'includes/header.php';

// Clear any payment session data to prevent loops
if (isset($_SESSION['khalti_payment'])) {
    unset($_SESSION['khalti_payment']);
}

// Get parameters from URL
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$status = isset($_GET['status']) ? $_GET['status'] : 'failed';
$message = isset($_GET['message']) ? $_GET['message'] : '';

// Redirect if no order ID
if ($orderId <= 0) {
    redirect('index.php');
}

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

// Get debug info if available
$debugInfo = '';
if (isset($_SESSION['khalti_response']) && isAdmin()) {
    $debugInfo = '<div class="alert alert-info mt-4">
        <h5>Debug Info (Admin Only):</h5>
        <pre style="max-height: 200px; overflow-y: auto;">' . print_r($_SESSION['khalti_response'], true) . '</pre>
    </div>';
    unset($_SESSION['khalti_response']);
}
?>

<style>
    /* Main container styling */
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
    
    .col-md-8 {
        flex: 0 0 100%;
        max-width: 100%;
        padding-right: 15px;
        padding-left: 15px;
        position: relative;
        width: 100%;
    }
    
    @media (min-width: 768px) {
        .col-md-8 {
            flex: 0 0 66.666667%;
            max-width: 66.666667%;
        }
    }
    
    /* Card styling */
    .card {
        background-color: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
        display: flex;
        flex-direction: column;
    }
    
    .card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }
    
    .bg-success {
        background-color: #28a745 !important;
    }
    
    .bg-danger {
        background-color: #dc3545 !important;
    }
    
    .text-white {
        color: #fff !important;
    }
    
    .mb-0 {
        margin-bottom: 0 !important;
    }
    
    .card-body {
        flex: 1 1 auto;
        padding: 1.25rem;
    }
    
    /* Alert styling */
    .alert {
        position: relative;
        padding: 0.75rem 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        border-radius: 0.25rem;
    }
    
    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }
    
    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }
    
    .alert-info {
        color: #0c5460;
        background-color: #d1ecf1;
        border-color: #bee5eb;
    }
    
    .mt-4 {
        margin-top: 1.5rem;
    }
    
    /* Typography */
    h3, h5 {
        margin-top: 0;
        margin-bottom: 0.5rem;
        font-weight: 500;
        line-height: 1.2;
    }
    
    h3 {
        font-size: 1.75rem;
    }
    
    h5 {
        font-size: 1.25rem;
    }
    
    p {
        margin-top: 0;
        margin-bottom: 1rem;
    }
    
    strong {
        font-weight: 700;
    }
    
    /* Spacing */
    .mb-3 {
        margin-bottom: 1rem;
    }
    
    .mb-4 {
        margin-bottom: 1.5rem;
    }
    
    /* Table styling */
    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
        border-collapse: collapse;
    }
    
    .table-bordered {
        border: 1px solid #dee2e6;
    }
    
    .table th,
    .table td {
        padding: 0.75rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }
    
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
    }
    
    /* Custom styling to match the screenshot */
    .table thead th {
        background-color: #6c63ff;
        color: white;
        font-weight: 600;
        text-align: left;
        border-color: #554fd8;
    }
    
    .table tbody td {
        background-color: #ffffff;
        color: #333333;
        border-color: #dee2e6;
    }
    
    /* Supporting dark mode */
    @media (prefers-color-scheme: dark) {
        .card {
            background-color: #343a40;
            color: #f8f9fa;
        }
        
        .table {
            color: #f8f9fa;
        }
        
        .table tbody td {
            background-color: #343a40;
            color: #f8f9fa;
            border-color: #495057;
        }
        
        .table thead th {
            border-color: #554fd8;
        }
        
        .table-bordered {
            border-color: #495057;
        }
        
        .table-bordered th,
        .table-bordered td {
            border-color: #495057;
        }
    }
    
    /* Button styling */
    .text-center {
        text-align: center !important;
    }
    
    .btn {
        display: inline-block;
        font-weight: 400;
        color: #212529;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
        user-select: none;
        background-color: transparent;
        border: 1px solid transparent;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: 0.25rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        text-decoration: none;
        margin: 0 0.25rem;
    }
    
    .btn-primary {
        color: #fff;
        background-color: #6c63ff;
        border-color: #6c63ff;
    }
    
    .btn-primary:hover {
        background-color: #5652db;
        border-color: #5652db;
    }
    
    .btn-info {
        color: #fff;
        background-color: #17a2b8;
        border-color: #17a2b8;
    }
    
    .btn-info:hover {
        background-color: #138496;
        border-color: #117a8b;
    }
    
    .btn-outline-primary {
        color: #6c63ff;
        border-color: #6c63ff;
    }
    
    .btn-outline-primary:hover {
        color: #fff;
        background-color: #6c63ff;
        border-color: #6c63ff;
    }
    
    .bi {
        display: inline-block;
        vertical-align: -0.125em;
        fill: currentColor;
        margin-right: 0.25rem;
    }
</style>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-<?php echo $status === 'success' ? 'success' : 'danger'; ?> text-white">
                    <h3 class="mb-0">
                        <?php echo $status === 'success' ? 'Payment Successful!' : 'Payment Failed'; ?>
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $status === 'success' ? 'success' : 'danger'; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php echo $debugInfo; ?>
                    
                    <div class="mb-4">
                        <h5>Order Details</h5>
                        <p><strong>Order ID:</strong> #<?php echo $orderId; ?></p>
                        <p><strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
                        <p><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                        <p><strong>Payment Status:</strong> <?php echo ucfirst($order['payment_status']); ?></p>
                        <p><strong>Order Status:</strong> <?php echo ucfirst($order['status']); ?></p>
                        <p><strong>Total Amount:</strong> Rs. <?php echo number_format($order['total_amount'], 2); ?></p>
                        <?php if (!empty($order['transaction_id'])): ?>
                            <p><strong>Transaction ID:</strong> <?php echo $order['transaction_id']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Items Purchased</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Book</th>
                                    <th>Author</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                                        <td><?php echo htmlspecialchars($item['author']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-4">
                        <?php if ($status === 'success'): ?>
                            <a href="invoice.php?id=<?php echo $orderId; ?>" class="btn btn-info mb-3">
                                <i class="bi bi-file-earmark-text"></i> View Invoice
                            </a>
                            <br>
                        <?php elseif ($order['payment_method'] === 'khalti' && $order['payment_status'] !== 'completed'): ?>
                            <a href="khalti_payment.php?retry=<?php echo $orderId; ?>" class="btn btn-primary mb-3">
                                Try Payment Again
                            </a>
                            <br>
                        <?php endif; ?>
                        
                        <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                        <?php if (isLoggedIn()): ?>
                            <a href="user/orders.php" class="btn btn-outline-primary">View All Orders</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>