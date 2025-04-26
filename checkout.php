<?php
require_once 'includes/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get book ID and quantity from URL
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

// Make sure quantity is at least 1
if ($quantity < 1) {
    $quantity = 1;
}

// Get book details
$book = getBookById($bookId);

// If book not found or quantity is 0, redirect to homepage
if (!$book || $book['quantity'] <= 0) {
    redirect('index.php');
}

// Make sure requested quantity doesn't exceed available quantity
if ($quantity > $book['quantity']) {
    $quantity = $book['quantity'];
}

// Get user details
$userId = $_SESSION['user_id'];
$user = getUserById($userId);

// Calculate total price based on quantity
$totalPrice = $book['price'] * $quantity;

// Handle checkout process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = sanitize($_POST['payment_method']);
    
    // Check book quantity again before processing (in case of concurrent orders)
    $freshBookData = getBookById($bookId);
    if ($freshBookData['quantity'] < $quantity) {
        $error = 'Sorry, the requested quantity is no longer available. Only ' . $freshBookData['quantity'] . ' books are in stock.';
        $quantity = $freshBookData['quantity'];
        $totalPrice = $book['price'] * $quantity;
    } else {
        // Insert order into database
        $query = "INSERT INTO orders (user_id, total_amount, payment_method, status) 
                  VALUES ($userId, $totalPrice, '$paymentMethod', 'pending')";
        
        if (mysqli_query($conn, $query)) {
            $orderId = mysqli_insert_id($conn);
            
            // Insert order item with specified quantity
            $query = "INSERT INTO order_items (order_id, book_id, quantity, price) 
                      VALUES ($orderId, $bookId, $quantity, " . $book['price'] . ")";
            
            if (mysqli_query($conn, $query)) {
                // Immediately update book quantity if available
                $freshBookData = getBookById($bookId);
                if ($freshBookData['quantity'] >= $quantity) {
                    updateBookQuantity($bookId, $quantity);
                } else {
                    $error = 'Sorry, this book is no longer available in the requested quantity.';
                }
                
                // If payment method is cash on delivery, redirect to success page
                if ($paymentMethod === 'cash') {
                    $_SESSION['success_message'] = 'Order placed successfully! Your order will be delivered soon.';
                    redirect('payment_success.php?order_id=' . $orderId);
                } else {
                    // For Khalti payment, initialize payment and redirect
                    $_SESSION['order_id'] = $orderId;
                    $_SESSION['book_id'] = $bookId;
                    $_SESSION['amount'] = $totalPrice;
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
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="checkout-card">
                <div class="checkout-header">
                    <h3 class="mb-0">Checkout</h3>
                </div>
                <div class="checkout-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="book-image-container">
                                <img src="assets/images/<?php echo $book['image']; ?>" class="img-fluid rounded" alt="<?php echo $book['title']; ?>">
                            </div>
                        </div>
                        <div class="col-md-8 book-details">
                            <h4 class="book-title"><?php echo $book['title']; ?></h4>
                            <p class="book-author">by <?php echo $book['author']; ?></p>
                            
                            <div class="price-info">
                                <p><strong>Price per copy:</strong> Rs. <?php echo number_format($book['price'], 2); ?></p>
                                <p><strong>Quantity:</strong> <?php echo $quantity; ?></p>
                                <p class="mb-0"><strong>Total Price:</strong> Rs. <?php echo number_format($totalPrice, 2); ?></p>
                            </div>
                            
                            <span class="quantity-badge <?php echo ($book['quantity'] < 5) ? 'low-stock' : ''; ?>">
                                <?php echo $book['quantity']; ?> copies available
                            </span>
                        </div>
                    </div>
                    
                    <form action="checkout.php?id=<?php echo $bookId; ?>&quantity=<?php echo $quantity; ?>" method="POST">
                        <h5 class="section-title">Shipping Information</h5>
                        <div class="user-info mb-4">
                            <p><strong>Name:</strong> <?php echo $user['name']; ?></p>
                            <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                            <p><strong>Phone:</strong> <?php echo $user['phone']; ?></p>
                            <p class="mb-0"><strong>Address:</strong> <?php echo $user['address']; ?></p>
                        </div>
                        
                        <h5 class="section-title">Payment Method</h5>
                        <div class="payment-options mb-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" checked>
                                <label class="form-check-label payment-method-label" for="cash">
                                    Cash on Delivery
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="khalti" value="khalti">
                                <label class="form-check-label payment-method-label" for="khalti">
                                    Pay with Khalti
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="order-button">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>