<?php
require_once 'includes/header.php';

// Redirect if not logged in or no order in session
if (!isLoggedIn() || !isset($_SESSION['order_id']) || !isset($_SESSION['book_id']) || !isset($_SESSION['amount'])) {
    redirect('index.php');
}

$orderId = $_SESSION['order_id'];
$bookId = $_SESSION['book_id'];
$amount = $_SESSION['amount'];

// Get book details
$book = getBookById($bookId);

// Generate a unique purchase order ID
$purchaseOrderId = 'ORD-' . time() . '-' . $orderId;

// Update order with purchase order ID
$query = "UPDATE orders SET purchase_order_id = '$purchaseOrderId' WHERE id = $orderId";
if (!mysqli_query($conn, $query)) {
    die("Error updating purchase order ID: " . mysqli_error($conn));
}

// Get user details
$user = getUserById($_SESSION['user_id']);
?>

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Khalti Payment</h3>
            </div>
            <div class="card-body text-center">
                <div class="mb-4">
                    <h4><?php echo $book['title']; ?></h4>
                    <p>Total Amount: Rs. <?php echo $amount; ?></p>
                </div>
                
                <!-- Loading spinner -->
                <div id="payment-loading" class="spinner-border text-primary d-none" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                
                <!-- Payment form -->
                <form id="payment-form" action="initiate_khalti_payment.php" method="post">
                    <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                    <input type="hidden" name="book_id" value="<?php echo $bookId; ?>">
                    <input type="hidden" name="amount" value="<?php echo $amount; ?>">
                    <input type="hidden" name="purchase_order_id" value="<?php echo $purchaseOrderId; ?>">
                    <input type="hidden" name="purchase_order_name" value="<?php echo $book['title']; ?>">
                    <input type="hidden" name="customer_name" value="<?php echo $user['name']; ?>">
                    <input type="hidden" name="customer_email" value="<?php echo $user['email']; ?>">
                    <input type="hidden" name="customer_phone" value="<?php echo $user['phone']; ?>">
                    
                    <button type="submit" id="payment-button" class="btn btn-primary">
                        Pay with Khalti
                    </button>
                </form>
                
                <div class="mt-3">
                    <a href="user/orders.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log("Khalti Payment Page Loaded");
        
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