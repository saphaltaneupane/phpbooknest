<?php
require_once 'includes/header.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $bookId = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;
    
    // Remove item from cart
    if ($action === 'remove' && $bookId > 0) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['book_id'] === $bookId) {
                unset($_SESSION['cart'][$key]);
                // Re-index array
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                break;
            }
        }
        // Redirect to remove query params
        redirect('cart.php');
    }
    
    // Clear entire cart
    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        redirect('cart.php');
    }
}

// Update quantities
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    $quantities = $_POST['quantity'];
    
    foreach ($quantities as $bookId => $quantity) {
        $bookId = (int)$bookId;
        $quantity = (int)$quantity;
        
        if ($quantity <= 0) {
            // Remove item if quantity is 0 or negative
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['book_id'] === $bookId) {
                    unset($_SESSION['cart'][$key]);
                    break;
                }
            }
        } else {
            // Get book details to check available quantity
            $query = "SELECT quantity FROM books WHERE id = $bookId";
            $result = mysqli_query($conn, $query);
            
            if (mysqli_num_rows($result) > 0) {
                $book = mysqli_fetch_assoc($result);
                
                // Update quantity, but don't exceed available stock
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['book_id'] === $bookId) {
                        $item['quantity'] = min($quantity, $book['quantity']);
                        break;
                    }
                }
            }
        }
    }
    
    // Re-index array
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    
    // Set success message
    $_SESSION['cart_message'] = 'Cart updated successfully';
    
    // Redirect to remove query params
    redirect('cart.php');
}

// Calculate totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

// Total (subtotal + any additional fees)
$total = $subtotal;

// Format amounts
$formattedSubtotal = number_format($subtotal, 2);
$formattedTotal = number_format($total, 2);
?>

<style>
    /* Cart page styles */
    .cart-container {
        max-width: 1000px;
        margin: 0 auto;
        
    }
    
    .cart-header {
        margin-bottom: 2rem;
    }
    
    .cart-title {
        font-size: 2rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }
    
    .cart-subtitle {
        color: #666;
        font-size: 1rem;
    }
    
    .cart-message {
        margin-bottom: 1.5rem;
    }
    
    .cart-empty {
        background-color:rgb(232, 230, 238);
        padding: 3rem;
        text-align: center;
        border-radius: 8px;
        margin-bottom: 2rem;
    }
    
    .cart-empty-icon {
        font-size: 4rem;
        color: #ccc;
        margin-bottom: 1rem;
    }
    
    .cart-empty-text {
        font-size: 1.2rem;
        color: #666;
        margin-bottom: 1.5rem;
    }
    
    .cart-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 2rem;
    }
    
    .cart-table th {
        background-color: #f5f5f5;
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #ddd;
    }
    
    .cart-table td {
        padding: 15px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    
    .cart-product {
        display: flex;
        align-items: center;
    }
.
.cart-product-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        margin-right: 15px;
        border-radius: 4px;
    }
    
    .cart-product-title {
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .cart-product-author {
        color: #666;
        font-size: 0.9rem;
    }
    
    .cart-quantity {
        max-width: 100px;
    }
    
    .cart-remove {
        color: #dc3545;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .cart-remove:hover {
        color: #bd2130;
    }
    
    .cart-footer {
        background-color:rgb(249, 249, 249);
        padding: 20px;
        border-radius: 8px;
    }
    
    .cart-subtotal, .cart-total {
        background-color:rgb(243, 243, 248);
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
    }
    
    .cart-total {
        background-color:rgb(253, 253, 255);
        font-weight: 600;
        font-size: 1.1rem;
        border-top: 1px solid #ddd;
        margin-top: 10px;
        padding-top: 15px;
    }
    
    .cart-actions {
        margin-top: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .cart-update-btn {
        background-color:rgb(68, 68, 154);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .cart-update-btn:hover {
        background-color: #5652db;
    }
    
    .cart-clear {
        color: #666;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .cart-clear:hover {
        color: #333;
        text-decoration: underline;
    }
    
    .cart-checkout-btn {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s;
        display: block;
        width: 100%;
        text-align: center;
        margin-top: 1.5rem;
        text-decoration: none;
    }
    
    .cart-checkout-btn:hover {
        background-color: #218838;
    }
    
    .continue-shopping {
        display: inline-block;
        margin-top: 1rem;
        color: #6c63ff;
        text-decoration: none;
    }
    
    .continue-shopping:hover {
        text-decoration: underline;
    }
    
    @media (max-width: 768px) {
        .cart-table thead {
            display: none;
        }
        
        .cart-table, .cart-table tbody, .cart-table tr, .cart-table td {
            display: block;
            width: 100%;
        }
        
        .cart-table tr {
            margin-bottom: 1.5rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
        }
        
        .cart-table td {
            border: none;
            padding: 10px 0;
            text-align: right;
            position: relative;
            padding-left: 50%;
        }
        
        .cart-table td::before {
            content: attr(data-label);
            position: absolute;
            left: 0;
            width: 45%;
            padding-right: 10px;
            text-align: left;
            font-weight: 600;
        }
        
        .cart-product {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .cart-product-image {
            margin-bottom: 10px;
            margin-right: 0;
        }
    }
    
    /* Dark mode */
    @media (prefers-color-scheme: dark) {
        .cart-title {
            color: black;
        }
        
        .cart-subtitle, .cart-empty-text {
            color: #adb5bd;
        }
        
        .cart-empty {
            background-color: #343a40;
        }
        
        .cart-empty-icon {
            color: #495057;
        }
        
        .cart-table th {
            background-color: #343a40;
            color: #f8f9fa;
            border-color: #495057;
        }
        
        .cart-table td {
            border-color: #495057;
        }
        
        .cart-product-a
uthor {
            color: #adb5bd;
        }
        
        .cart-footer {
            background-color: #343a40;
        }
        
        .cart-total {
            border-color: #495057;
        }
        
        .cart-clear {
            color: #adb5bd;
        }
        
        .cart-clear:hover {
            color: #f8f9fa;
        }
    }
</style>

<div class="cart-container">
    <div class="cart-header">
        <h1 class="cart-title">Your Shopping Cart</h1>
        <p class="cart-subtitle">Review your items before proceeding to checkout</p>
    </div>
    
    <?php if (isset($_SESSION['cart_message'])): ?>
        <div class="alert alert-success cart-message">
            <?php echo $_SESSION['cart_message']; ?>
        </div>
        <?php unset($_SESSION['cart_message']); ?>
    <?php endif; ?>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <div class="cart-empty">
            <div class="cart-empty-icon">
                <i class="bi bi-cart-x"></i>
            </div>
            <p class="cart-empty-text">Your cart is empty</p>
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <form action="cart.php" method="POST">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <tr>
                            <td data-label="Product">
                                <div class="cart-product">
                                    <img src="assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>" class="cart-product-image">
                                    <div>
                                        <h5 class="cart-product-title"><?php echo $item['title']; ?></h5>
                                        <p class="cart-product-author">by <?php echo $item['author']; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Price">Rs. <?php echo number_format($item['price'], 2); ?></td>
                            <td data-label="Quantity">
                                <input type="text" name="quantity[<?php echo $item['book_id']; ?>]" value="<?php echo $item['quantity']; ?>" class="cart-quantity" readonly>
                            </td>
                            <td data-label="Total">Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            <td data-label="Action">
                                <a href="cart.php?action=remove&book_id=<?php echo $item['book_id']; ?>" class="cart-remove">
                                    <i class="bi bi-trash"></i> Remove
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="cart-footer">
                <div class="cart-subtotal">
                    <span>Subtotal</span>
                    <span>Rs. <?php echo $formattedSubtotal; ?></span>
                </div>
                <div class="cart-total">
                    <span>Total</span>
                    <span>Rs. <?php echo $formattedTotal; ?></span>
                </div>
            </div>
            
            <div class="cart-actions">
                <div>
                    <button type="submit" name="update_cart" class="cart-update-btn">Update Cart</button>
                    <a href="cart.php?action=clear" class="cart-clear">Clear Cart</a>
                </div>
                <a href="index.php" class="continue-shopping">
                    <i class="bi bi-arrow-left"></i> Continue Shopping
                </a>
            </div>
            
            <?php if (isLoggedIn()): ?>
                <a href="checkout.php" class="cart-checkout-btn">Proceed to Checkout</a>
            <?php else: ?>
                <div class="login-instruction">
                    <p>Please log in or register to complete your purchase.</p>
                    <p>Your cart items will be saved for you.</p>
                    <div class="btn-group">
                        <a href="login.php?redirect=checkout" class="btn login-btn">Login</a>
                        <a href="register.php?redirect=checkout" class="btn register-btn">Register</a>
                    </div>
                </div>
            <?php endif; ?>
        </form>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>