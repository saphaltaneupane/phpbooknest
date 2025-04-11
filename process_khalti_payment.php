<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verify Khalti payment using the lookup API
 * 
 * @param string $pidx Payment ID from Khalti
 * @return array|false Response from Khalti or false on failure
 */
function verifyKhaltiPayment($pidx) {
    // Khalti lookup URL
    $url = "https://dev.khalti.com/api/v2/epayment/lookup/";
    
    // Data to send to Khalti
    $payload = json_encode([
        'pidx' => $pidx
    ]);
    
    // Initialize cURL
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Key ' . KHALTI_SECRET_KEY,
        'Content-Type: application/json'
    ]);
    
    // Execute cURL
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Close cURL
    curl_close($ch);
    
    // Check if verification was successful
    if ($statusCode === 200) {
        return json_decode($response, true);
    }
    
    return false;
}

/**
 * Update order status based on payment verification
 * 
 * @param int $orderId Order ID
 * @param string $transactionId Transaction ID from Khalti
 * @param string $status Payment status (completed, failed, etc.)
 * @return bool Success status
 */
function updateOrderStatus($orderId, $transactionId, $status) {
    global $conn;
    
    // Update order payment status
    $query = "UPDATE orders SET payment_status = '$status', transaction_id = '$transactionId' WHERE id = $orderId";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        return false;
    }
    
    // If payment is completed, update book status
    if ($status === 'completed') {
        // Get book ID from order items
        $query = "SELECT book_id FROM order_items WHERE order_id = $orderId";
        $result = mysqli_query($conn, $query);
        
        if ($orderItem = mysqli_fetch_assoc($result)) {
            $bookId = $orderItem['book_id'];
            
            // Update book status
            $query = "UPDATE books SET status = 'sold' WHERE id = $bookId";
            return mysqli_query($conn, $query);
        }
    }
    
    return true;
}

// Process payment verification
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['pidx'])) {
    $pidx = $_GET['pidx'];
    $status = $_GET['status'] ?? '';
    $transactionId = $_GET['transaction_id'] ?? '';
    $purchaseOrderId = $_GET['purchase_order_id'] ?? '';
    
    // Extract order ID from purchase order ID
    $orderId = substr($purchaseOrderId, strrpos($purchaseOrderId, '-') + 1);
    
    // Verify with Khalti
    $verificationResult = verifyKhaltiPayment($pidx);
    
    if ($verificationResult) {
        // Store verification result in session for debugging
        $_SESSION['verification_result'] = $verificationResult;
        
        // Check payment status
        if ($verificationResult['status'] === 'Completed') {
            // Payment successful
            updateOrderStatus($orderId, $transactionId, 'completed');
            
            // Redirect to success page
            header("Location: payment_success.php?order_id=$orderId&status=success");
            exit;
        } else {
            // Payment failed or pending
            $message = urlencode("Payment status: " . $verificationResult['status']);
            header("Location: payment_success.php?order_id=$orderId&status=failed&message=$message");
            exit;
        }
    } else {
        // Verification failed
        $message = urlencode("Payment verification failed. Please contact support.");
        header("Location: payment_success.php?order_id=$orderId&status=failed&message=$message");
        exit;
    }
} else {
    // Invalid request
    header("Location: index.php");
    exit;
}
?>