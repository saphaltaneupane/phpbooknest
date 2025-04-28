<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Default response
$response = [
    'success' => false,
    'message' => 'An error occurred',
    'cartCount' => 0
];

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get book ID and quantity from POST data
    $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Validate input
    if ($bookId <= 0 || $quantity <= 0) {
        $response['message'] = 'Invalid book ID or quantity';
    } else {
        // Get book details
        $query = "SELECT * FROM books WHERE id = $bookId AND status = 'available'";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $book = mysqli_fetch_assoc($result);
            
            // Check if book has enough quantity
            if ($book['quantity'] >= $quantity) {
                // Initialize cart if it doesn't exist
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }
                
                // Check if book is already in cart
                $found = false;
                
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['book_id'] === $bookId) {
                        // Update quantity (ensure it doesn't exceed available stock)
                        $newQuantity = $item['quantity'] + $quantity;
                        
                        if ($newQuantity <= $book['quantity']) {
                            $item['quantity'] = $newQuantity;
                            $response['success'] = true;
                            $response['message'] = 'Cart updated successfully';
                        } else {
                            $response['message'] = 'Cannot add more of this item (exceeds available stock)';
                        }
                        
                        $found = true;
                        break;
                    }
                }
                
                // If book wasn't found in cart, add it
                if (!$found) {
                    $_SESSION['cart'][] = [
                        'book_id' => $bookId,
                        'title' => $book['title'],
                        'author' => $book['author'],
                        'price' => $book['price'],
                        'image' => $book['image'],
                        'quantity' => $quantity
                    ];
                    
                    $response['success'] = true;
                    $response['message'] = 'Item added to cart successfully';
                }
                
                // Count total items in cart
                $cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));
                $response['cartCount'] = $cartCount;
            } else {
                $response['message'] = 'Not enough stock available';
            }
        } else {
            $response['message'] = 'Book not found or not available';
        }
    }
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>