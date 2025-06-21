<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/functions.php'; // <-- Add this line to ensure isLoggedIn() is available

// Ensure database connection is available
$conn = mysqli_connect("localhost", "root", "", "booktrading");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Remove unavailable items from cart before proceeding
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $unavailableItems = [];
    foreach ($_SESSION['cart'] as $key => $item) {
        $bookId = $item['book_id'];
        $quantity = $item['quantity'];
        $query = "SELECT quantity, status FROM books WHERE id = $bookId";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $book = mysqli_fetch_assoc($result);
            if ($book['status'] !== 'available' || $book['quantity'] < $quantity) {
                $unavailableItems[] = $item['title'];
                unset($_SESSION['cart'][$key]);
            }
        } else {
            unset($_SESSION['cart'][$key]);
        }
    }
    if (!empty($unavailableItems)) {
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        $_SESSION['checkout_error'] = "The following items have been removed from your cart as they are no longer available: " . implode(", ", $unavailableItems);
    }
}

// Redirect to cart page if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    redirect('cart.php');
}

$error = null;

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php?redirect=checkout");
    exit();
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

require_once 'includes/header.php';

// Get user details
$userId = $_SESSION['user_id'];
$user = getUserById($userId);

// Calculate total
$totalAmount = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalAmount += $item['price'] * $item['quantity'];
}

// Handle checkout process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = sanitize($_POST['payment_method']);
    
    // Verify stock for each item before processing
    $stockError = false;
    
    foreach ($_SESSION['cart'] as $item) {
        $bookId = $item['book_id'];
        $requestedQuantity = $item['quantity'];
        
        // Get fresh book data
        $freshBookData = getBookById($bookId);
        
        if (!$freshBookData || $freshBookData['quantity'] < $requestedQuantity) {
            $stockError = true;
            $itemTitle = isset($freshBookData['title']) ? $freshBookData['title'] : $item['title'];
            $availableQuantity = isset($freshBookData['quantity']) ? $freshBookData['quantity'] : 0;
            
            $error = "Sorry, '$itemTitle' only has $availableQuantity copies available.";
            break;
        }
    }
    
    if (!$stockError) {
        // Insert order into database
        $query = "INSERT INTO orders (user_id, total_amount, payment_method, status) 
                  VALUES ($userId, $totalAmount, '$paymentMethod', 'pending')";
        
        if (mysqli_query($conn, $query)) {
            $orderId = mysqli_insert_id($conn);
            
            // Insert order items
            $insertError = false;
            
            foreach ($_SESSION['cart'] as $item) {
                $bookId = $item['book_id'];
                $itemQuantity = $item['quantity'];
                $itemPrice = $item['price'];
                
                $query = "INSERT INTO order_items (order_id, book_id, quantity, price) 
                          VALUES ($orderId, $bookId, $itemQuantity, $itemPrice)";
                
                if (!mysqli_query($conn, $query)) {
                    $insertError = true;
                    $error = 'Error adding order item: ' . mysqli_error($conn);
                    break;
                }
                
                // For Khalti payments, we'll update inventory after payment verification
                // Only update inventory immediately for Cash on Delivery
                if ($paymentMethod === 'cash') {
                    $freshBookData = getBookById($bookId);
                    if ($freshBookData && $freshBookData['quantity'] >= $itemQuantity) {
                        updateBookQuantity($bookId, $itemQuantity);
                    } else {
                        $insertError = true;
                        $error = 'Sorry, one or more books are no longer available in the requested quantity.';
                        break;
                    }
                }
            }
            
            // If no errors inserting items, process payment
            if (!$insertError) {
                // Redirect based on payment method
                if ($paymentMethod === 'cash') {
                    // Clear cart
                    $_SESSION['cart'] = [];
                    $_SESSION['success_message'] = 'Order placed successfully! Your order will be delivered soon.';
                    redirect('payment_success.php?order_id=' . $orderId);
                } else {
                    // For Khalti payment, initialize payment and redirect
                    $_SESSION['order_id'] = $orderId;
                    $_SESSION['amount'] = $totalAmount;

                    // Try new path first, fall back to original path
                    if (file_exists('payment/khalti/payment.php')) {
                        redirect('payment/khalti/payment.php');
                    } else {
                        $khaltiPaymentUrl = 'http://localhost/booktrading/khalti_payment.php'; // Use original file for now
                        redirect($khaltiPaymentUrl);
                    }
                }
            } else {
                // If error occurred after order was created, delete the order
                $deleteQuery = "DELETE FROM orders WHERE id = $orderId";
                mysqli_query($conn, $deleteQuery);
            }
        } else {
            $error = 'Error placing order: ' . mysqli_error($conn);
        }
    }
}
?>

<style>
    /* Main container styles */
    .checkout-container {
        padding: 30px 0;
        background-color: #f0f2f5;
        min-height: 80vh;
    }
    
    /* Card styles */
    .checkout-card {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        border: none;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 30px;
        background-color: #ffffff;
    }
    
    /* Header styles */
    .checkout-header {
        background: linear-gradient(135deg, #0056b3, #003d80);
        color: white;
        padding: 20px;
        font-weight: 600;
    }
    
    .checkout-header h3 {
        margin: 0;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    
    /* Card body styles */
    .checkout-body {
        padding: 25px;
        color: #333333;
    }
    
    /* Book image styles */
    .book-image-container {
        border: 1px solid #dee2e6;
        padding: 10px;
        border-radius: 8px;
        background-color: white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease;
    }
    
    .book-image-container:hover {
        transform: scale(1.03);
    }
    
    /* Book details styles */
    .book-details {
        padding-left: 20px;
    }
    
    .book-title {
        color: #0056b3;
        font-size: 1.5rem;
        margin-bottom: 5px;
        font-weight: 600;
    }
    
    .book-author {
        color: #495057;
        margin-bottom: 15px;
        font-style: italic;
    }
    
    /* Price info styles */
    .price-info {
        background-color: #e7f1ff;
        border-left: 4px solid #0056b3;
        padding: 15px;
        margin: 15px 0;
        border-radius: 4px;
        color: #0d2b4d;
    }
    
    .price-info p {
        margin-bottom: 8px;
        font-size: 1.05rem;
    }
    
    .price-info p:last-child {
        font-weight: 600;
        color: #0056b3;
    }
    
    /* Section title styles */
    .section-title {
        font-size: 1.25rem;
        color: #0056b3;
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 10px;
        margin: 25px 0 15px;
        font-weight: 600;
    }
    
    /* User info styles */
    .user-info {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        color: #333333;
    }
    
    .user-info p {
        margin-bottom: 10px;
        font-size: 1.05rem;
    }
    
    .user-info strong {
        color: #0d2b4d;
        margin-right: 5px;
    }
    
    /* Payment options styles */
    .payment-options {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    
    .payment-method-label {
        display: flex;
        align-items: center;
        padding: 10px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.2s ease;
        font-size: 1.05rem;
        color: #333333;
    }
    
    .payment-method-label:hover {
        background-color: #e7f1ff;
    }
    
    .form-check-input:checked + .payment-method-label {
        font-weight: 600;
        color: #0056b3;
    }
    
    /* Button styles */
    .order-button {
        background: linear-gradient(135deg, #0056b3, #003d80);
        color: white;
        padding: 14px;
        font-size: 1.15rem;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 20px;
        font-weight: 600;
        letter-spacing: 0.5px;
        width: 100%;
    }
    
    .order-button:hover {
        background: linear-gradient(135deg, #003d80, #00264d);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    /* Alert styles */
    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-weight: 500;
    }
    
    /* Quantity badge styles */
    .quantity-badge {
        background-color: #28a745;
        color: white;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.9rem;
        display: inline-block;
        margin-top: 10px;
        font-weight: 500;
    }
    
    .low-stock {
        background-color: #dc3545;
    }
</style>

<div class="checkout-container">
    <div class="checkout-header">
        <h1 class="checkout-title">Checkout</h1>
        <p class="checkout-subtitle">Complete your purchase</p>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['checkout_error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['checkout_error']; unset($_SESSION['checkout_error']); ?>
        </div>
    <?php endif; ?>
    
    <form action="checkout.php" method="POST">
        <div class="checkout-row">
            <div class="checkout-col checkout-col-7">
                <div class="checkout-section">
                    <h3 class="checkout-section-title">Shipping Information</h3>
                    <div class="user-details">
                        <div class="user-detail">
                            <span class="user-detail-label">Name:</span>
                            <span class="user-detail-value"><?php echo $user['name']; ?></span>
                        </div>
                        <div class="user-detail">
                            <span class="user-detail-label">Email:</span>
                            <span class="user-detail-value"><?php echo $user['email']; ?></span>
                        </div>
                        <div class="user-detail">
                            <span class="user-detail-label">Phone:</span>
                            <span class="user-detail-value"><?php echo $user['phone']; ?></span>
                        </div>
                        <div class="user-detail">
                            <span class="user-detail-label">Address:</span>
                            <span class="user-detail-value"><?php echo $user['address']; ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="checkout-section">
                    <h3 class="checkout-section-title">Payment Method</h3>
                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cash" class="payment-radio" checked>
                            <span class="payment-label">Cash on Delivery</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="khalti" class="payment-radio">
                            <span class="payment-label">Pay with Khalti</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="checkout-col checkout-col-5">
                <div class="checkout-section order-summary">
                    <h3 class="checkout-section-title">Order Summary</h3>
                    <div class="cart-items">
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <div class="cart-item">
                                <img src="assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>" class="cart-item-image">
                                <div class="cart-item-details">
                                    <h5 class="cart-item-title"><?php echo $item['title']; ?></h5>
                                    <p class="cart-item-author">by <?php echo $item['author']; ?></p>
                                    <p class="cart-item-price">Rs. <?php echo number_format($item['price'], 2); ?></p>
                                </div>
                                <div class="cart-item-quantity">×<?php echo $item['quantity']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="order-totals">
                        <div class="order-total-row">
                            <span class="order-total-label">Subtotal</span>
                            <span class="order-total-value">Rs. <?php echo number_format($totalAmount, 2); ?></span>
                        </div>
                        <div class="order-total-row">
                            <span class="order-total-label">Shipping</span>
                            <span class="order-total-value">Free</span>
                        </div>
                        <div class="order-total-row grand-total">
                            <span class="order-total-label">Total</span>
                            <span class="order-total-value">Rs. <?php echo number_format($totalAmount, 2); ?></span>
                        </div>
                    </div>
                    
                    <button type="submit" class="checkout-btn">Place Order</button>
                    <a href="cart.php" class="back-to-cart">
                        <i class="bi bi-arrow-left"></i> Back to Cart
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>