<?php
require_once 'includes/header.php';

// Redirect if not logged in or no order in session
if (!isLoggedIn() || !isset($_SESSION['order_id']) || !isset($_SESSION['amount'])) {
    redirect('index.php');
}

$orderId = $_SESSION['order_id'];
$amount = $_SESSION['amount'];

// Get order details to display
$query = "SELECT o.*, oi.book_id FROM orders o
          JOIN order_items oi ON o.id = oi.order_id
          WHERE o.id = $orderId
          LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    redirect('index.php');
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

// Generate a unique purchase order ID
$purchaseOrderId = 'ORD-' . time() . '-' . $orderId;

// Update order with purchase order ID
$query = "UPDATE orders SET purchase_order_id = '$purchaseOrderId' WHERE id = $orderId";
if (!mysqli_query($conn, $query)) {
    die("Error updating purchase order ID: " . mysqli_error($conn));
}

// Get user details
$user = getUserById($_SESSION['user_id']);

// Create order name for Khalti
$orderName = count($items) > 1 
    ? $items[0]['title'] . ' and ' . (count($items) - 1) . ' more items'
    : $items[0]['title'];
?>

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Khalti Payment</h3>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h4><?php echo $orderName; ?></h4>
                    <p>Total Amount: Rs. <?php echo $amount; ?></p>
                    
                    <?php if (count($items) > 1): ?>
                        <div class="mt-3">
                            <h5>Order Items:</h5>
                            <ul class="list-group">
                                <?php foreach ($items as $item): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo $item['title']; ?>
                                        <span class="badge bg-primary rounded-pill"><?php echo $item['quantity']; ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Loading spinner -->
                <div id="payment-loading" class="spinner-border text-primary d-none" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                
                <!-- Payment form -->
                <form id="payment-form" action="initiate_khalti_payment.php" method="post" class="text-center">
                    <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                    <input type="hidden" name="amount" value="<?php echo $amount; ?>">
                    <input type="hidden" name="purchase_order_id" value="<?php echo $purchaseOrderId; ?>">
                    <input type="hidden" name="purchase_order_name" value="<?php echo $orderName; ?>">
                    <input type="hidden" name="customer_name" value="<?php echo $user['name']; ?>">
                    <input type="hidden" name="customer_email" value="<?php echo $user['email']; ?>">
                    <input type="hidden" name="customer_phone" value="<?php echo $user['phone']; ?>">
                    
                    <button type="submit" id="payment-button" class="btn btn-primary">
                        Pay with Khalti
                    </button>
                </form>
                
                <div class="mt-3 text-center">
                    <a href="user/orders.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentForm = document.getElementById('payment-form');
        const paymentButton = document.getElementById('payment-button');
        const loadingSpinner = document.getElementById('payment-loading');
        
        paymentForm.addEventListener('submit', function() {
            // Show loading spinner and disable button when form is submitted
            paymentButton.disabled = true;
            loadingSpinner.classList.remove('d-none');
        });
    });
</script>
<?php require_once 'includes/footer.php'; ?>