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

<style>
    /* Reset and Base Styles */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        color: #333;
        background-color: #f9f9f9;
    }
    
    /* Layout */
    .container {
        padding: 15px;
        max-width: 100%;
    }
    
    .admin-layout {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    /* Sidebar */
    .sidebar {
        width: 250px;
        flex-shrink: 0;
    }
    
    .sidebar-box {
        background-color: #fff;
        border: 1px solid #ddd;
        margin-bottom: 20px;
    }
    
    .sidebar-header {
        background-color: #0066cc;
        color: white;
        padding: 10px 15px;
        font-weight: bold;
    }
    
    .sidebar-menu {
        list-style: none;
    }
    
    .sidebar-menu a {
        display: block;
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
        border-bottom: 1px solid #eee;
    }
    
    .sidebar-menu a:hover {
        background-color: #f5f5f5;
    }
    
    .sidebar-menu a.active {
        background-color: #0066cc;
        color: white;
    }
    
    /* Main Content Area */
    .main-content {
        flex: 1;
        min-width: 0; /* Important for flexbox to respect table width */
    }
    
    .content-box {
        background-color: #fff;
        border: 1px solid #ddd;
    }
    
    .content-header {
        padding: 15px;
        border-bottom: 1px solid #ddd;
    }
    
    .content-header h2 {
        margin: 0;
        font-size: 20px;
    }
    
    .content-body {
        padding: 15px;
    }
    
    /* Table Styles */
    .orders-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    
    .orders-table th, 
    .orders-table td {
        border: 1px solid #ddd;
        padding: 8px 12px;
        text-align: left;
    }
    
    .orders-table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
    
    .orders-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    
    .orders-table tr:hover {
        background-color: #f1f1f1;
    }
    
    /* Table container with horizontal scroll for small screens */
    .table-responsive {
        overflow-x: auto;
        margin-bottom: 15px;
        width: 100%;
    }
    
    /* Status Badges */
    .badge {
        display: inline-block;
        padding: 3px 7px;
        font-size: 12px;
        font-weight: bold;
        border-radius: 3px;
        color: white;
    }
    
    .badge-success {
        background-color: #28a745;
    }
    
    .badge-warning {
        background-color: #ffc107;
        color: #333;
    }
    
    .badge-danger {
        background-color: #dc3545;
    }
    
    .badge-info {
        background-color: #17a2b8;
    }
    
    /* Buttons */
    .btn {
        display: inline-block;
        padding: 6px 12px;
        font-size: 14px;
        text-align: center;
        cursor: pointer;
        text-decoration: none;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    
    .btn-sm {
        padding: 4px 8px;
        font-size: 12px;
    }
    
    .btn-primary {
        color: #fff;
        background-color: #0066cc;
    }
    
    .btn-secondary {
        color: #fff;
        background-color: #6c757d;
    }
    
    /* Alerts */
    .alert {
        padding: 12px 15px;
        margin-bottom: 15px;
        border: 1px solid transparent;
        border-radius: 4px;
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
    
    /* Pagination */
    .pagination {
        display: flex;
        list-style: none;
        justify-content: center;
        margin: 20px 0;
    }
    
    .pagination li {
        margin: 0 2px;
    }
    
    .pagination a {
        display: block;
        padding: 6px 12px;
        text-decoration: none;
        color: #0066cc;
        border: 1px solid #ddd;
    }
    
    .pagination li.active a {
        background-color: #0066cc;
        color: white;
        border-color: #0066cc;
    }
    
    .pagination li.disabled a {
        color: #6c757d;
        cursor: not-allowed;
        background-color: #fff;
    }
    
    /* Modal Styles */
    .modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        border-radius: 5px;
        z-index: 1001;
        width: 90%;
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .modal-header {
        padding: 15px;
        border-bottom: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-title {
        margin: 0;
        font-size: 18px;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #999;
    }
    
    .modal-body {
        padding: 15px;
    }
    
    .modal-footer {
        padding: 15px;
        border-top: 1px solid #ddd;
        text-align: right;
    }
    
    /* Form Styles */
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    
    .form-select, .form-input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .form-text {
        font-size: 12px;
        color: #6c757d;
        margin-top: 4px;
    }
    
    /* Utility Classes */
    .mb-15 {
        margin-bottom: 15px;
    }
    
    .details-row {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }
    
    .details-col {
        flex: 1;
        min-width: 250px;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .admin-layout {
            flex-direction: column;
        }
        
        .sidebar {
            width: 100%;
        }
    }
</style>

<div class="container">
    <div class="admin-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-box">
                <div class="sidebar-header">
                    Admin Panel
                </div>
                <ul class="sidebar-menu">
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="users.php">Manage Users</a></li>
                    <li><a href="books.php">Manage Books</a></li>
                    <li><a href="add_book.php">Add New Book</a></li>
                    <li><a href="orders.php" class="active">Manage Orders</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="content-box">
                <div class="content-header">
                    <h2>Manage Orders</h2>
                </div>
                <div class="content-body">
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
                            <table class="orders-table">
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
                                                <span class="badge badge-<?php echo $order['payment_status'] === 'completed' ? 'success' : ($order['payment_status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                                    <?php echo ucfirst($order['payment_status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo $order['status'] === 'completed' ? 'success' : ($order['status'] === 'pending' ? 'warning' : ($order['status'] === 'cancelled' ? 'danger' : 'info')); ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" onclick="openModal('orderModal<?php echo $order['id']; ?>')">
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
                            <ul class="pagination">
                                <li class="<?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a href="?page=<?php echo $page-1; ?>">&laquo;</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="<?php echo $i === $page ? 'active' : ''; ?>">
                                        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="<?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                    <a href="?page=<?php echo $page+1; ?>">&raquo;</a>
                                </li>
                            </ul>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal backdrop -->
<div class="modal-backdrop" id="modalBackdrop"></div>

<!-- Order Details Modals -->
<?php foreach ($orders as $order): ?>
    <div class="modal" id="orderModal<?php echo $order['id']; ?>">
        <div class="modal-header">
            <h5 class="modal-title">Order #<?php echo $order['id']; ?> Details</h5>
            <button type="button" class="modal-close" onclick="closeModal('orderModal<?php echo $order['id']; ?>')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="details-row">
                <div class="details-col">
                    <p><strong>User:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
                    <p><strong>Order Date:</strong> <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
                    <p><strong>Total Amount:</strong> Rs. <?php echo number_format($order['total_amount'], 2); ?></p>
                </div>
                <div class="details-col">
                    <p><strong>Payment Method:</strong> <?php echo ucfirst(htmlspecialchars($order['payment_method'])); ?></p>
                    <p><strong>Payment Status:</strong> <?php echo ucfirst($order['payment_status']); ?></p>
                    <p><strong>Order Status:</strong> <?php echo ucfirst($order['status']); ?></p>
                </div>
            </div>
            
            <h6 style="margin-top: 20px; margin-bottom: 10px; font-weight: bold;">Order Items</h6>
            <div class="table-responsive mb-15">
                <table class="orders-table">
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
                <p class="mb-15"><strong>Transaction ID:</strong> <?php echo htmlspecialchars($order['transaction_id']); ?></p>
            <?php endif; ?>
            
            <form action="orders.php<?php echo $page > 1 ? "?page=$page" : ''; ?>" method="POST" class="mb-15">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <input type="hidden" name="payment_method" value="<?php echo $order['payment_method']; ?>">
                
                <!-- Update Payment Status -->
                <div class="form-group">
                    <label for="paymentStatus<?php echo $order['id']; ?>" class="form-label">Update Payment Status</label>
                    <?php if ($order['payment_method'] === 'khalti'): ?>
                        <select class="form-select" id="paymentStatus<?php echo $order['id']; ?>" name="payment_status" disabled>
                            <option value="<?php echo $order['payment_status']; ?>" selected><?php echo ucfirst($order['payment_status']); ?> (Khalti payments cannot be modified)</option>
                        </select>
                        <small class="form-text">Payment status for Khalti transactions cannot be modified</small>
                    <?php else: ?>
                        <select class="form-select" id="paymentStatus<?php echo $order['id']; ?>" name="payment_status">
                            <option value="pending" <?php echo $order['payment_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="completed" <?php echo $order['payment_status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="failed" <?php echo $order['payment_status'] === 'failed' ? 'selected' : ''; ?>>Failed</option>
                        </select>
                    <?php endif; ?>
                </div>
                
                <!-- Update Order Status -->
                <div class="form-group">
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
            <button type="button" class="btn btn-secondary" onclick="closeModal('orderModal<?php echo $order['id']; ?>')">Close</button>
        </div>
    </div>
<?php endforeach; ?>

<script>
    // Modal functions
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
        document.getElementById('modalBackdrop').style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent scrolling behind modal
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
        document.getElementById('modalBackdrop').style.display = 'none';
        document.body.style.overflow = ''; // Restore scrolling
    }
    
    // Close modal when clicking outside
    document.getElementById('modalBackdrop').addEventListener('click', function() {
        var modals = document.getElementsByClassName('modal');
        for (var i = 0; i < modals.length; i++) {
            modals[i].style.display = 'none';
        }
        this.style.display = 'none';
        document.body.style.overflow = '';
    });
</script>

<?php require_once $relativePath . 'includes/footer.php'; ?>