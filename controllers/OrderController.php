<?php
class OrderController {
    public function checkout($book_id) {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=user&action=login');
            exit;
        }
        
        if (!$book_id) {
            header('Location: index.php?controller=book&action=index');
            exit;
        }
        
        $book = Book::getBookById($book_id);
        
        if (!$book || $book['status'] !== 'available') {
            // Book not found or not available
            header('Location: index.php?controller=book&action=index');
            exit;
        }
        
        // Load the view
        include 'views/layout/header.php';
        include 'views/orders/checkout.php';
        include 'views/layout/footer.php';
    }
    
    public function processOrder() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=user&action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $book_id = $_POST['book_id'] ?? null;
            $payment_method = $_POST['payment_method'] ?? 'cash';
            
            if (!$book_id) {
                header('Location: index.php?controller=book&action=index');
                exit;
            }
            
            $book = Book::getBookById($book_id);
            
            if (!$book || $book['status'] !== 'available') {
                // Book not found or not available
                header('Location: index.php?controller=book&action=index');
                exit;
            }
            
            // Create order
            if ($payment_method === 'cash') {
                // Cash on delivery
                $order_id = Order::createOrder($_SESSION['user_id'], $book_id, 'cash', 'pending');
                
                if ($order_id) {
                    // Redirect to order history
                    header('Location: index.php?controller=order&action=history');
                    exit;
                }
            } else if ($payment_method === 'khalti') {
                // Khalti payment
                // Store book_id in session for later use after Khalti payment
                $_SESSION['checkout_book_id'] = $book_id;
                
                // Load Khalti payment view
                include 'views/layout/header.php';
                include 'views/orders/khalti_payment.php';
                include 'views/layout/footer.php';
                exit;
            }
        }
        
        // Redirect to home page if something went wrong
        header('Location: index.php');
        exit;
    }
    
    public function verifyKhaltiPayment() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? '';
            $amount = $_POST['amount'] ?? 0;
            $book_id = $_SESSION['checkout_book_id'] ?? null;
            
            if (!$token || !$amount || !$book_id) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid payment data']);
                exit;
            }
            
            // For testing purposes, we'll simulate a successful Khalti payment
            // In production, you would verify with Khalti API
            
            // Create order with Khalti payment
            $order_id = Order::createOrder($_SESSION['user_id'], $book_id, 'khalti', 'completed', $token);
            
            if ($order_id) {
                // Clear checkout session
                unset($_SESSION['checkout_book_id']);
                
                echo json_encode(['status' => 'success', 'order_id' => $order_id]);
                exit;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Order creation failed']);
                exit;
            }
        }
        
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        exit;
    }
    
    public function history() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=user&action=login');
            exit;
        }
        
        $orders = Order::getOrdersByUser($_SESSION['user_id']);
        
        // Load the view
        include 'views/layout/header.php';
        include 'views/orders/history.php';
        include 'views/layout/footer.php';
    }
}
?>