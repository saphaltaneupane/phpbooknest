<?php
require_once 'includes/header.php';

// Check if order ID is provided
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Redirect if no order ID
if ($orderId <= 0) {
    redirect('index.php');
}

// Get order details
$query = "SELECT o.*, u.name as user_name, u.email, u.phone, u.address 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          WHERE o.id = $orderId";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    redirect('index.php');
}

$order = mysqli_fetch_assoc($result);

// Verify user has access to this order (must be the order owner or an admin)
if (!isAdmin() && $_SESSION['user_id'] != $order['user_id']) {
    redirect('index.php');
}

// Get order items
$query = "SELECT oi.*, b.title, b.author FROM order_items oi 
          JOIN books b ON oi.book_id = b.id 
          WHERE oi.order_id = $orderId";
$result = mysqli_query($conn, $query);
$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}

// Calculate totals
$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

// Format date
$orderDate = date('F j, Y', strtotime($order['created_at']));
$invoiceDate = date('F j, Y');
?>

<style>
    /* Invoice Styles */
    .invoice-container {
        max-width: 800px;
        margin: 20px auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        padding: 40px;
    }
    
    /* Print Specific Styles */
    @media print {
        body {
            background-color: #fff;
            font-size: 12pt;
        }
        
        .navbar, .footer, .no-print {
            display: none !important;
        }
        
        .invoice-container {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
            box-shadow: none;
            border: none;
        }
        
        .invoice-actions {
            display: none;
        }
        
        @page {
            margin: 0.5cm;
        }
    }

    /* Header Styles */
    .invoice-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 40px;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 20px;
    }
    
    .invoice-logo {
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }
    
    .invoice-company {
        text-align: right;
    }
    
    .invoice-company h3 {
        margin: 0;
        font-size: 20px;
        color: #333;
    }
    
    .invoice-company p {
        margin: 5px 0;
        color: #666;
        font-size: 14px;
    }
    
    /* Invoice Details */
    .invoice-details {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }
    
    .invoice-details-left h4, .invoice-details-right h4 {
        font-size: 16px;
        margin-bottom: 10px;
        color: #333;
    }
    
    .invoice-details-left p, .invoice-details-right p {
        margin: 5px 0;
        color: #666;
        font-size: 14px;
    }
    
    .invoice-id, .invoice-date {
        color: #333;
        font-weight: bold;
    }
    
    /* Items Table */
    .invoice-items {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }
    
    .invoice-items th {
        background-color: #f5f5f5;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #ddd;
    }
    
    .invoice-items td {
        padding: 12px;
        border-bottom: 1px solid #eee;
        color: #666;
    }
    
    .invoice-items tr:last-child td {
        border-bottom: none;
    }
    
    .invoice-items .text-right {
        text-align: right;
    }
    
    /* Item Quantity Styling */
    .item-quantity {
        text-align: center;
    }
    
    /* Totals Section */
    .invoice-totals {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 40px;
    }
    
    .totals-table {
        width: 300px;
    }
    
    .totals-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    
    .totals-row:last-child {
        border-top: 2px solid #ddd;
        border-bottom: none;
        padding-top: 12px;
        font-weight: bold;
        font-size: 16px;
    }
    
    /* Payment Status */
    .payment-status {
        margin-bottom: 30px;
    }
    
    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        color: white;
        font-weight: 600;
        font-size: 13px;
    }
    
    .status-completed {
        background-color: #28a745;
    }
    
    .status-pending {
        background-color: #ffc107;
        color: #212529;
    }
    
    .status-failed {
        background-color: #dc3545;
    }
    
    /* Thank You Note */
    .invoice-footer {
        text-align: center;
        margin-top: 40px;
        padding-top: 20px;
        border-top: 2px solid #f0f0f0;
        color: #666;
    }
    
    .thank-you {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }
    
    /* Print and Download Buttons */
    .invoice-actions {
        display: flex;
        justify-content: center;
        margin-top: 30px;
    }
    
    .btn-print, .btn-download, .btn-back {
        padding: 10px 20px;
        margin: 0 10px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    
    .btn-print {
        background-color: #6c63ff;
        color: white;
        border: none;
    }
    
    .btn-download {
        background-color: #28a745;
        color: white;
        border: none;
    }
    
    .btn-back {
        background-color: #f8f9fa;
        color: #333;
        border: 1px solid #ddd;
    }
    
    .btn-print i, .btn-download i, .btn-back i {
        margin-right: 8px;
    }
    
    .btn-print:hover {
        background-color: #5652db;
    }
    
    .btn-download:hover {
        background-color: #218838;
    }
    
    .btn-back:hover {
        background-color: #e9ecef;
    }
    
    /* Dark Mode */
    @media (prefers-color-scheme: dark) {
        .invoice-container {
            background-color: #2c2c2c;
            color: #f0f0f0;
        }
        
        .invoice-logo, .invoice-company h3 {
            color: #f0f0f0;
        }
        
        .invoice-company p, .invoice-details-left p, .invoice-details-right p {
            color: #aaa;
        }
        
        .invoice-details-left h4, .invoice-details-right h4 {
            color: #f0f0f0;
        }
        
        .invoice-id, .invoice-date {
            color: #f0f0f0;
        }
        
        .invoice-items th {
            background-color: #3a3a3a;
            color: #f0f0f0;
            border-bottom: 2px solid #444;
        }
        
        .invoice-items td {
            border-bottom: 1px solid #444;
            color: #ccc;
        }
        
        .totals-row {
            border-bottom: 1px solid #444;
        }
        
        .totals-row:last-child {
            border-top: 2px solid #444;
        }
        
        .invoice-header, .invoice-footer {
            border-color: #444;
        }
        
        .thank-you {
            color: #f0f0f0;
        }
        
        .btn-back {
            background-color: #3a3a3a;
            color: #f0f0f0;
            border: 1px solid #444;
        }
        
        .btn-back:hover {
            background-color: #444;
        }
        
        /* For printing in dark mode */
        @media print {
            body {
                background-color: white;
                color: black;
            }
            
            .invoice-container, .invoice-logo, .invoice-company h3,
            .invoice-details-left h4, .invoice-details-right h4,
            .invoice-id, .invoice-date, .thank-you {
                color: black;
            }
            
            .invoice-company p, .invoice-details-left p, 
            .invoice-details-right p, .invoice-items td {
                color: #333;
            }
        }
    }
</style>

<div class="invoice-container">
    <!-- Invoice Header -->
    <div class="invoice-header">
        <div class="invoice-logo">
            BookTrading
        </div>
        <div class="invoice-company">
            <h3>Online Book Trading System</h3>
            <p>123 Book Street, Kathmandu</p>
            <p>Phone: +977 1234567890</p>
            <p>Email: contact@booktrading.com</p>
        </div>
    </div>
    
    <!-- Invoice Details -->
    <div class="invoice-details">
        <div class="invoice-details-left">
            <h4>Bill To:</h4>
            <p><strong><?php echo $order['user_name']; ?></strong></p>
            <p><?php echo $order['email']; ?></p>
            <p><?php echo $order['phone']; ?></p>
            <p><?php echo $order['address']; ?></p>
        </div>
        <div class="invoice-details-right">
            <h4>Invoice Details:</h4>
            <p>Invoice #: <span class="invoice-id">INV-<?php echo str_pad($orderId, 6, '0', STR_PAD_LEFT); ?></span></p>
            <p>Order #: <span class="invoice-id"><?php echo $orderId; ?></span></p>
            <p>Date: <span class="invoice-date"><?php echo $invoiceDate; ?></span></p>
            <p>Order Date: <span class="invoice-date"><?php echo $orderDate; ?></span></p>
        </div>
    </div>
    
    <!-- Payment Status -->
    <div class="payment-status">
        <p>Payment Method: <strong><?php echo ucfirst($order['payment_method']); ?></strong></p>
        <p>
            Payment Status: 
            <span class="status-badge status-<?php echo $order['payment_status']; ?>">
                <?php echo ucfirst($order['payment_status']); ?>
            </span>
        </p>
        <p>
            Order Status: 
            <span class="status-badge status-<?php echo $order['status'] === 'completed' ? 'completed' : ($order['status'] === 'pending' ? 'pending' : 'failed'); ?>">
                <?php echo ucfirst($order['status']); ?>
            </span>
        </p>
        <?php if (!empty($order['transaction_id'])): ?>
            <p>Transaction ID: <strong><?php echo $order['transaction_id']; ?></strong></p>
        <?php endif; ?>
    </div>
    
    <!-- Invoice Items -->
    <table class="invoice-items">
        <thead>
            <tr>
                <th width="50%">Item</th>
                <th>Price</th>
                <th class="item-quantity">Quantity</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <strong><?php echo $item['title']; ?></strong><br>
                        <small>by <?php echo $item['author']; ?></small>
                    </td>
                    <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                    <td class="item-quantity"><?php echo $item['quantity']; ?></td>
                    <td class="text-right">Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Invoice Totals -->
    <div class="invoice-totals">
        <div class="totals-table">
            <div class="totals-row">
                <span>Subtotal</span>
                <span>Rs. <?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div class="totals-row">
                <span>Shipping</span>
                <span>Free</span>
            </div>
            <div class="totals-row">
                <span>Total</span>
                <span>Rs. <?php echo number_format($subtotal, 2); ?></span>
            </div>
        </div>
    </div>
    
    <!-- Invoice Footer -->
    <div class="invoice-footer">
        <p class="thank-you">Thank you for your business!</p>
        <p>If you have any questions about this invoice, please contact our customer service at support@booktrading.com</p>
    </div>
    
    <!-- Print and Back Buttons -->
    <div class="invoice-actions no-print">
        <button class="btn-print" onclick="window.print();">
            <i class="bi bi-printer"></i> Print Invoice
        </button>
        <a href="<?php echo isAdmin() ? 'admin/orders.php' : 'user/orders.php'; ?>" class="btn-back">
            <i class="bi bi-arrow-left"></i> Back to Orders
        </a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>