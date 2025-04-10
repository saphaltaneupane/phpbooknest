<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if request is POST and has the required data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON data from POST request
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    // Check if required data is present
    if (isset($data['token']) && isset($data['amount']) && isset($data['purchase_order_id'])) {
        $token = $data['token'];
        $amount = $data['amount'];
        $purchaseOrderId = $data['purchase_order_id'];
        
        // Khalti verification URL
        $url = "https://dev.khalti.com/api/v2/payment/verify/";
        
        // Data to send to Khalti
        $payload = json_encode([
            'token' => $token,
            'amount' => $amount
        ]);
        
        // Initialize cURL
        $ch = curl_init();
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Key b42d1cff70d84d759d823a75f0ac17d5',
            'Content-Type: application/json'
        ]);
        
        // Execute cURL
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Close cURL
        curl_close($ch);
        
        // Check if verification was successful
        if ($statusCode === 200) {
            $responseData = json_decode($response, true);
            
            // Get order ID from purchase order ID
            $orderId = substr($purchaseOrderId, strrpos($purchaseOrderId, '-') + 1);
            
            // Update order payment status
            $transactionId = $responseData['idx'];
            $query = "UPDATE orders SET payment_status = 'completed', transaction_id = '$transactionId' WHERE id = $orderId";
            mysqli_query($conn, $query);
            
            // Get book ID from order items
            $query = "SELECT book_id FROM order_items WHERE order_id = $orderId";
            $result = mysqli_query($conn, $query);
            $orderItem = mysqli_fetch_assoc($result);
            $bookId = $orderItem['book_id'];
            
            // Update book status
            $query = "UPDATE books SET status = 'sold' WHERE id = $bookId";
            mysqli_query($conn, $query);
            
            // Send success response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'transaction_id' => $transactionId
            ]);
        } else {
            // Send error response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Payment verification failed'
            ]);
        }
    } else {
        // Send error response for missing data
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Missing required data'
        ]);
    }
} else {
    // Send error response for invalid request method
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>