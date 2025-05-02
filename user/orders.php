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

<style>
    /* Pure CSS Styles for Orders Page */
    :root {
        --primary-color: #6c63ff;
        --primary-dark: #5652db;
        --secondary-color: #ff9d72;
        --success-color: #4caf50;
        --warning-color: #ff9800;
        --danger-color: #f44336;
        --info-color: #2196f3;
        --light-color: #f8f9ff;
        --dark-color: #2c2c54;
        --gray-light: #f0f2f9;
        --gray-medium: #e0e0e0;
        --gray-dark: #a0a0a0;
        --text-primary: #333333;
        --text-secondary: #666666;
        --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
        --radius: 12px;
        --radius-sm: 6px;
        --transition: all 0.3s ease;
    }
    
    .orders-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .orders-title {
        color: var(--dark-color);
        margin-bottom: 1.5rem;
        font-size: 1.75rem;
        font-weight: 600;
        position: relative;
        padding-left: 15px;
    }
    
    .orders-title::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 5px;
        background-color: var(--primary-color);
        border-radius: 3px;
    }
    
    /* Empty Orders Message */
    .empty-message {
        background-color: rgba(33, 150, 243, 0.1);
        border: 1px solid rgba(33, 150, 243, 0.3);
        color: var(--info-color);
        padding: 15px 20px;
        border-radius: var(--radius-sm);
        margin-bottom: 20px;
    }
    
    /* Table Styles */
    .table-wrapper {
        overflow-x: auto;
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        margin-bottom: 25px;
    }
    
    .orders-table {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap;
        font-size: 0.95rem;
        background-color: white;
    }
    
    .orders-table thead {
        background-color: var(--gray-light);
    }
    
    .orders-table th {
        text-align: left;
        padding: 14px 16px;
        font-weight: 600;
        color: var(--dark-color);
        border-bottom: 2px solid var(--gray-medium);
    }
    
    .orders-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--gray-light);
        color: var(--text-secondary);
    }
    
    .orders-table tbody tr:nth-child(even) {
        background-color: var(--gray-light);
    }
    
    .orders-table tbody tr:hover {
        background-color: rgba(108, 99, 255, 0.05);
    }
    
    /* Status Badges */
    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: capitalize;
        color: white;
    }
    
    .status-success {
        background-color: var(--success-color);
    }
    
    .status-warning {
        background-color: var(--warning-color);
    }
    
    .status-danger {
        background-color: var(--danger-color);
    }
    
    .status-info {
        background-color: var(--info-color);
    }
    
    /* Back Button */
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--gray-dark) 0%, var(--gray-medium) 100%);
        color: var(--text-primary);
        padding: 10px 20px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
        border: none;
        cursor: pointer;
        margin-top: 20px;
    }
    
    .back-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        background: linear-gradient(135deg, var(--gray-medium) 0%, var(--gray-dark) 100%);
    }
    
    /* Responsive Table */
    @media screen and (max-width: 768px) {
        .orders-table {
            display: block;
        }
        
        .orders-table thead {
            display: none;
        }
        
        .orders-table tbody {
            display: block;
        }
        
        .orders-table tr {
            display: block;
            margin-bottom: 1rem;
            border-radius: var(--radius-sm);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        
        .orders-table td {
            display: flex;
            justify-content: space-between;
            text-align: right;
            padding: 12px 15px;
            border-bottom: 1px solid var(--gray-light);
        }
        
        .orders-table td::before {
            content: attr(data-label);
            font-weight: 600;
            float: left;
            text-align: left;
            color: var(--dark-color);
        }
        
        .orders-table tbody tr:hover {
            transform: translateY(-2px);
        }
    }
    
    @media screen and (max-width: 576px) {
        .orders-container {
            padding: 0 15px;
        }
        
        .orders-title {
            font-size: 1.5rem;
            margin-bottom: 1.2rem;
        }
    }
</style>

<div class="orders-container">
    <h2 class="orders-title">My Orders</h2>
    
    <?php if (empty($orders)): ?>
        <div class="empty-message">You haven't placed any orders yet.</div>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Payment Method</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td data-label="Order ID">#<?php echo $order['id']; ?></td>
                            <td data-label="Date"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td data-label="Total">Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                            <td data-label="Payment Method"><?php echo ucfirst($order['payment_method']); ?></td>
                            <td data-label="Payment Status">
                                <span class="status-badge status-<?php echo $order['payment_status'] === 'completed' ? 'success' : ($order['payment_status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                    <?php echo ucfirst($order['payment_status']); ?>
                                </span>
                            </td>
                            <td data-label="Order Status">
                                <span class="status-badge status-<?php echo $order['status'] === 'completed' ? 'success' : ($order['status'] === 'pending' ? 'warning' : ($order['status'] === 'cancelled' ? 'danger' : 'info')); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td data-label="Actions">
                                <a href="<?php echo $relativePath; ?>payment_success.php?order_id=<?php echo $order['id']; ?>&status=success" class="action-button view-button">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="<?php echo $relativePath; ?>invoice.php?id=<?php echo $order['id']; ?>" class="action-button invoice-button">
                                    <i class="bi bi-file-earmark-text"></i> Invoice
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    
    <div>
        <a href="dashboard.php" class="back-button">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>