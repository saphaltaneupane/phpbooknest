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
    die("Error updating purchase order ID: " . mysqli_error($conn)); // Add error handling
}
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
                
                <div id="payment-loading" class="spinner d-none"></div>
                
                <button id="payment-button" class="btn btn-primary">Pay with Khalti</button>
                
                <div class="mt-3">
                    <a href="user/orders.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/khalti-checkout.iffe.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log("Khalti Payment Initialized"); // Debugging log

        // Debugging: Log the public key
        const publicKey = "test_public_key_dc74b8a5a6b54c7c8f2b7a9c5e8b9a6d"; // Replace with your Khalti public key
        console.log("Using public key:", publicKey);

        // Amount in paisa (Khalti requires amount in paisa)
        const amount = <?php echo $amount * 100; ?>; // Convert Rs to paisa
        const purchaseOrderId = '<?php echo $purchaseOrderId; ?>';
        const purchaseOrderName = '<?php echo $book['title']; ?>';
        const returnUrl = 'payment_success.php?order_id=<?php echo $orderId; ?>';

        // Initialize Khalti checkout
        const config = {
            publicKey: publicKey,
            productIdentity: purchaseOrderId,
            productName: purchaseOrderName,
            productUrl: window.location.href,
            eventHandler: {
                onSuccess(payload) {
                    console.log("Payment successful:", payload); // Debugging log
                    window.location.href = returnUrl + "&token=" + payload.token + "&amount=" + payload.amount;
                },
                onError(error) {
                    console.error("Payment error:", error); // Debugging log
                    alert("Payment failed. Please try again.");
                },
                onClose() {
                    console.log("Khalti widget closed."); // Debugging log
                }
            }
        };

        const checkout = new KhaltiCheckout(config);

        // Add click event to payment button
        document.getElementById('payment-button').addEventListener('click', function() {
            console.log("Pay with Khalti button clicked"); // Debugging log
            checkout.show({ amount: amount });
        });
    });
</script>
<?php require_once 'includes/footer.php'; ?>