<?php
require_once 'includes/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get book ID from URL
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get book details
$book = getBookById($bookId);

// If book not found or quantity is 0, redirect to homepage
if (!$book || $book['quantity'] <= 0) {
    redirect('index.php');
}

// Get user details
$userId = $_SESSION['user_id'];
$user = getUserById($userId);

// Handle checkout process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = sanitize($_POST['payment_method']);
    $totalAmount = $book['price'];
    
    // Check book quantity again before processing (in case of concurrent orders)
    $freshBookData = getBookById($bookId);
    if ($freshBookData['quantity'] <= 0) {
        $error = 'Sorry, this book is no longer available.';
    } else {
        // Insert order into database
        $query = "INSERT INTO orders (user_id, total_amount, payment_method, status) 
                  VALUES ($userId, $totalAmount, '$paymentMethod', 'pending')";
        
        if (mysqli_query($conn, $query)) {
            $orderId = mysqli_insert_id($conn);
            
            // Insert order item
            $query = "INSERT INTO order_items (order_id, book_id, quantity, price) 
                      VALUES ($orderId, $bookId, 1, $totalAmount)";
            
            if (mysqli_query($conn, $query)) {
                // Immediately update book quantity if available
                $freshBookData = getBookById($bookId);
                if ($freshBookData['quantity'] > 0) {
                    updateBookQuantity($bookId, 1);
                } else {
                    $error = 'Sorry, this book is no longer available.';
                }
                
                // If payment method is cash on delivery, redirect to success page
                if ($paymentMethod === 'cash') {
                    $_SESSION['success_message'] = 'Order placed successfully! Your order will be delivered soon.';
                    redirect('payment_success.php?order_id=' . $orderId);
                } else {
                    // For Khalti payment, initialize payment and redirect
                    $_SESSION['order_id'] = $orderId;
                    $_SESSION['book_id'] = $bookId;
                    $_SESSION['amount'] = $totalAmount;
                    redirect('khalti_payment.php');
                }
            } else {
                $error = 'Error adding order item: ' . mysqli_error($conn);
            }
        } else {
            $error = 'Error placing order: ' . mysqli_error($conn);
        }
    }
}
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Checkout</h3>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <img src="assets/images/<?php echo $book['image']; ?>" class="img-fluid rounded" alt="<?php echo $book['title']; ?>">
                    </div>
                    <div class="col-md-8">
                        <h4><?php echo $book['title']; ?></h4>
                        <p>by <?php echo $book['author']; ?></p>
                        <p><strong>Price:</strong> Rs. <?php echo $book['price']; ?></p>
                        <p><strong>Quantity Available:</strong> <?php echo $book['quantity']; ?></p>
                    </div>
                </div>
                
                <form action="checkout.php?id=<?php echo $bookId; ?>" method="POST">
                    <div class="mb-3">
                        <h5>Shipping Information</h5>
                        <p><strong>Name:</strong> <?php echo $user['name']; ?></p>
                        <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                        <p><strong>Phone:</strong> <?php echo $user['phone']; ?></p>
                        <p><strong>Address:</strong> <?php echo $user['address']; ?></p>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Payment Method</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" checked>
                            <label class="form-check-label" for="cash">
                                Cash on Delivery
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="khalti" value="khalti">
                            <label class="form-check-label" for="khalti">
                                Pay with Khalti
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Place Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>